<?php

namespace App\Http\Controllers;

use App\Models\MarcacionCruda;
use App\Models\AsistenciaDiaria;
use App\Models\AsignacionHorario;
use App\Models\Empleado;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProcesarAsistenciaController extends Controller
{
    public function index(Request $request)
    {
        $query = AsistenciaDiaria::with(['empleado.grupoBeneficio']);

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }
        if ($request->filled('empleado_id')) {
            $query->where('empleado_id', $request->empleado_id);
        }

        $asistencias = $query->orderByDesc('fecha')->paginate(20);
        $empleados = Empleado::orderBy('nombres')->get();

        return view('asistencias.index', compact('asistencias', 'empleados'));
    }

    /**
     * Procesa marcaciones crudas pendientes
     */
    public function procesar()
    {
        $marcaciones = MarcacionCruda::where('procesado', 0)
            ->orderBy('fecha_hora')
            ->get();

        if ($marcaciones->isEmpty()) {
            return back()->with('success', 'No hay marcaciones pendientes');
        }

        $grupos = $marcaciones->groupBy(function ($item) {
            return $item->empleado_id . '-' . $item->fecha_hora->format('Y-m-d');
        });

        foreach ($grupos as $grupo) {
            $primerMarca = $grupo->first();
            $empleado = Empleado::with('grupoBeneficio')->find($primerMarca->empleado_id);
            if (!$empleado) continue;

            $fechaCarbon = $primerMarca->fecha_hora->copy()->startOfDay();
            
            // 1. Obtener Horario Programado
            $asignacion = AsignacionHorario::where('empleado_id', $empleado->id)
                ->where('fecha_inicio', '<=', $fechaCarbon->format('Y-m-d'))
                ->where(function ($q) use ($fechaCarbon) {
                    $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fechaCarbon->format('Y-m-d'));
                })->first();

            if (!$asignacion || !$asignacion->horario) continue;

            $turnos = $asignacion->horario->turnos()
                ->where('dia_semana', $fechaCarbon->dayOfWeekIso)
                ->orderBy('numero_turno')
                ->get();

            if ($turnos->isEmpty()) continue;

            // 2. Definir puntos de anclaje para asignar marcaciones por cercanía
            $puntosProgramados = [];
            foreach ($turnos as $turno) {
                $puntosProgramados[] = ['tipo' => "entrada_" . $turno->numero_turno, 'hora' => $fechaCarbon->copy()->setTimeFrom(Carbon::parse($turno->hora_inicio))];
                $puntosProgramados[] = ['tipo' => "salida_" . $turno->numero_turno, 'hora' => $fechaCarbon->copy()->setTimeFrom(Carbon::parse($turno->hora_fin))];
            }

            $marcacionesReales = [
                'entrada_1_real' => null, 'salida_1_real' => null,
                'entrada_2_real' => null, 'salida_2_real' => null,
            ];

            foreach ($grupo as $marca) {
                $mejorDistancia = null;
                $mejorTipo = null;

                foreach ($puntosProgramados as $punto) {
                    $distancia = abs($marca->fecha_hora->diffInSeconds($punto['hora']));
                    if (is_null($mejorDistancia) || $distancia < $mejorDistancia) {
                        $mejorDistancia = $distancia;
                        $mejorTipo = $punto['tipo'] . "_real";
                    }
                }

                if (is_null($marcacionesReales[$mejorTipo])) {
                    $marcacionesReales[$mejorTipo] = $marca->fecha_hora->format('H:i:s');
                }
            }

            // 3. Calcular usando la lógica unificada
            $analisis = $this->calcularAsistencia($empleado, $fechaCarbon, $turnos, $marcacionesReales);

            // 4. Guardar
            AsistenciaDiaria::updateOrCreate(
                ['empleado_id' => $empleado->id, 'fecha' => $fechaCarbon->format('Y-m-d')],
                array_merge($analisis, ['tipo_registro' => 'MAQUINA'])
            );

            $grupo->each->update(['procesado' => 1]);
        }

        return back()->with('success', 'Procesamiento finalizado con éxito.');
    }

    /**
     * Actualización manual desde el modal
     */
    public function actualizacionManual(Request $request, $id)
    {
        $asistencia = AsistenciaDiaria::findOrFail($id);
        $empleado = Empleado::with('grupoBeneficio')->find($asistencia->empleado_id);
        
        $request->validate(['observaciones' => 'required|min:5']);

        $marcacionesNuevas = [
            'entrada_1_real' => $request->entrada_1_real,
            'salida_1_real'  => $request->salida_1_real,
            'entrada_2_real' => $request->entrada_2_real,
            'salida_2_real'  => $request->salida_2_real,
        ];

        // Recuperar turnos programados para esa fecha
        $asignacion = AsignacionHorario::where('empleado_id', $empleado->id)
            ->where('fecha_inicio', '<=', $asistencia->fecha->format('Y-m-d'))
            ->where(function ($q) use ($asistencia) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $asistencia->fecha->format('Y-m-d'));
            })->first();

        $turnos = $asignacion->horario->turnos()
            ->where('dia_semana', $asistencia->fecha->dayOfWeekIso)
            ->get();

        // Calcular de nuevo con los datos manuales
        $analisis = $this->calcularAsistencia($empleado, $asistencia->fecha, $turnos, $marcacionesNuevas);

        $asistencia->update(array_merge($analisis, [
            'observaciones' => $request->observaciones,
            'tipo_registro' => 'MANUAL'
        ]));

        return back()->with('success', 'Registro actualizado y recalculado correctamente.');
    }

    /**
     * LÓGICA UNIFICADA DE CÁLCULO (Turnos + Beneficios)
     */
    private function calcularAsistencia($empleado, $fechaCarbon, $turnos, $reales)
    {
        $totalMinutosTarde = 0;
        $asistenciaCompleta = true;
        
        // Obtener beneficios extras si existen
        $extraEntrada = $empleado->grupoBeneficio->minutos_tolerancia_extra_entrada ?? 0;
        $extraSalida = $empleado->grupoBeneficio->minutos_tolerancia_extra_salida ?? 0;

        $datosParaGuardar = [
            'entrada_1_prog' => null, 'salida_1_prog' => null,
            'entrada_2_prog' => null, 'salida_2_prog' => null,
            'entrada_1_real' => $reales['entrada_1_real'],
            'salida_1_real'  => $reales['salida_1_real'],
            'entrada_2_real' => $reales['entrada_2_real'],
            'salida_2_real'  => $reales['salida_2_real'],
        ];

        foreach ($turnos as $t) {
            $n = $t->numero_turno;
            $datosParaGuardar["entrada_{$n}_prog"] = $t->hora_inicio;
            $datosParaGuardar["salida_{$n}_prog"] = $t->hora_fin;

            $eRealStr = $reales["entrada_{$n}_real"];
            $sRealStr = $reales["salida_{$n}_real"];

            if (!$eRealStr || !$sRealStr) {
                $asistenciaCompleta = false;
                continue;
            }

            $hEntradaProg = $fechaCarbon->copy()->setTimeFrom(Carbon::parse($t->hora_inicio));
            $hSalidaProg = $fechaCarbon->copy()->setTimeFrom(Carbon::parse($t->hora_fin));
            $hEntradaReal = $fechaCarbon->copy()->setTimeFrom(Carbon::parse($eRealStr));
            $hSalidaReal = $fechaCarbon->copy()->setTimeFrom(Carbon::parse($sRealStr));

            // 1. Validar Salida Temprana (considerando beneficio de salida)
            // Si sale antes de la (hora_prog - extraSalida), es inasistencia.
            if ($hSalidaReal->lt($hSalidaProg->subMinutes($extraSalida))) {
                $asistenciaCompleta = false;
            }

            // 2. Calcular Tardanza (Tolerancia Turno + Tolerancia Grupo)
            $toleranciaTotal = ($t->minutos_tolerancia ?? 0) + $extraEntrada;
            $minutosDiferencia = $hEntradaProg->diffInMinutes($hEntradaReal, false);
            
            if ($minutosDiferencia > $toleranciaTotal) {
                $totalMinutosTarde += ($minutosDiferencia - $toleranciaTotal);
            }
        }

        $estado = 'NORMAL';
        if (!$asistenciaCompleta) $estado = 'INASISTENCIA';
        elseif ($totalMinutosTarde > 0) $estado = 'TARDE';

        $datosParaGuardar['minutos_tarde'] = $totalMinutosTarde;
        $datosParaGuardar['estado_dia'] = $estado;

        return $datosParaGuardar;
    }

    public function reprocesar(Request $request)
    {
        $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
        ]);
        $fechaDesde = Carbon::parse($request->fecha_desde)->startOfDay();
        $fechaHasta = Carbon::parse($request->fecha_hasta)->endOfDay();
        
        MarcacionCruda::whereBetween('fecha_hora', [$fechaDesde, $fechaHasta])->update(['procesado' => 0]);
        
        $queryAsistencia = AsistenciaDiaria::whereBetween('fecha', [$fechaDesde->format('Y-m-d'), $fechaHasta->format('Y-m-d')]);
        if ($request->filled('empleado_id')) {
            $queryAsistencia->where('empleado_id', $request->empleado_id);
        }
        $queryAsistencia->delete();

        return $this->procesar();
    }
    public function exportarPdf(Request $request)
    {
        $desde = $request->fecha_desde;
        $hasta = $request->fecha_hasta;
        $empId = $request->empleado_id;

        $query = AsistenciaDiaria::with('empleado')
            ->whereBetween('fecha', [$desde, $hasta]);

        if ($empId) {
            $query->where('empleado_id', $empId);
        }

        $asistencias = $query->orderBy('fecha', 'asc')->get();
        $empleado = $empId ? Empleado::find($empId) : null;

        // Cargar la vista y pasar los datos
        $pdf = Pdf::loadView('asistencias.pdf', compact('asistencias', 'desde', 'hasta', 'empleado'))
                ->setPaper('a4', 'portrait');

        return $pdf->download("Asistencia_{$desde}_al_{$hasta}.pdf");
    }
}