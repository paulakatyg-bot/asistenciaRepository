<?php

namespace App\Http\Controllers;

use App\Models\MarcacionCruda;
use App\Models\AsistenciaDiaria;
use App\Models\AsignacionHorario;
use App\Models\Empleado;
use App\Models\Calendario;
use App\Models\TipoTickeo;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProcesarAsistenciaController extends Controller
{
    public function index(Request $request)
    {
        $fecha_hasta = $request->filled('fecha_hasta') ? Carbon::parse($request->fecha_hasta) : Carbon::today();
        $fecha_desde = $request->filled('fecha_desde') ? Carbon::parse($request->fecha_desde) : $fecha_hasta->copy()->subDays(29);

        $todosLosEmpleados = Empleado::orderBy('nombres')->get();
        $empleadoId = $request->input('empleado_id') ?? $todosLosEmpleados->first()?->id;

        // Obtener eventos del calendario para el rango
        $eventosCalendario = Calendario::whereBetween('fecha', [
            $fecha_desde->format('Y-m-d'), 
            $fecha_hasta->format('Y-m-d')
        ])->get()->keyBy(fn($item) => $item->fecha->format('Y-m-d'));

        $empleadoElegido = null;
        $asistenciasReales = collect();

        if ($empleadoId) {
            $empleadoElegido = Empleado::with(['grupoBeneficio', 'asignacionesHorarios.horario.turnos'])
                ->find($empleadoId);

            $asistenciasReales = AsistenciaDiaria::where('empleado_id', $empleadoId)
                ->whereBetween('fecha', [$fecha_desde->format('Y-m-d'), $fecha_hasta->format('Y-m-d')])
                ->get()
                ->groupBy(fn($item) => $item->fecha->format('Y-m-d'));
        }

        $periodo = [];
        $iterador = $fecha_desde->copy();
        while ($iterador <= $fecha_hasta) {
            $periodo[] = $iterador->copy();
            $iterador->addDay();
        }
        $periodo = array_reverse($periodo);
        $tiposTickeo = TipoTickeo::orderBy('nombre')->get();

        return view('asistencias.index', [
            'empleado' => $empleadoElegido,
            'empleados' => $todosLosEmpleados,
            'asistenciasReales' => $asistenciasReales,
            'eventosCalendario' => $eventosCalendario,
            'periodo' => $periodo,
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
            'tiposTickeo' => $tiposTickeo
        ]);
    }

    public function procesar()
    {
        $marcaciones = MarcacionCruda::where('procesado', 0)
            ->orderBy('fecha_hora')
            ->get();

        if ($marcaciones->isEmpty()) {
            return back()->with('success', 'No hay marcaciones pendientes');
        }

        $grupos = $marcaciones->groupBy(fn($item) => $item->empleado_id . '-' . $item->fecha_hora->format('Y-m-d'));

        foreach ($grupos as $grupo) {
            $primerMarca = $grupo->first();
            $empleado = Empleado::with('grupoBeneficio')->find($primerMarca->empleado_id);
            if (!$empleado) continue;

            $fechaCarbon = $primerMarca->fecha_hora->copy()->startOfDay();
            $fechaStr = $fechaCarbon->format('Y-m-d');

            // 1. PRIORIDAD: CALENDARIO (FERIADOS)
            $diaCalendario = Calendario::where('fecha', $fechaStr)->first();
            if ($diaCalendario && $diaCalendario->tipo_dia === 'FERIADO') {
                AsistenciaDiaria::updateOrCreate(
                    ['empleado_id' => $empleado->id, 'fecha' => $fechaStr],
                    [
                        'estado_dia' => 'FERIADO',
                        'minutos_tarde' => 0,
                        'observaciones' => 'Feriado: ' . $diaCalendario->descripcion,
                        'tipo_registro' => 'SISTEMA'
                    ]
                );
                $grupo->each->update(['procesado' => 1]);
                continue; 
            }

            // 2. HORARIO PROGRAMADO
            $asignacion = AsignacionHorario::where('empleado_id', $empleado->id)
                ->where('fecha_inicio', '<=', $fechaStr)
                ->where(fn($q) => $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fechaStr))
                ->first();

            if (!$asignacion || !$asignacion->horario) continue;

            $turnos = $asignacion->horario->turnos()
                ->where('dia_semana', $fechaCarbon->dayOfWeekIso)
                ->orderBy('numero_turno')->get();

            if ($turnos->isEmpty()) continue;

            // 3. CLASIFICAR MARCACIONES POR CERCANÍA
            $reales = $this->clasificarMarcaciones($fechaCarbon, $turnos, $grupo);

            // 4. CALCULAR ASISTENCIA
            $analisis = $this->calcularAsistencia($empleado, $fechaCarbon, $turnos, $reales);
            
            // Si es un día ESPECIAL (pero no feriado), anotamos en observaciones
            if ($diaCalendario && $diaCalendario->tipo_dia === 'ESPECIAL') {
                $analisis['observaciones'] = 'Especial: ' . $diaCalendario->descripcion;
            }

            AsistenciaDiaria::updateOrCreate(
                ['empleado_id' => $empleado->id, 'fecha' => $fechaStr],
                array_merge($analisis, ['tipo_registro' => 'MAQUINA'])
            );

            $grupo->each->update(['procesado' => 1]);
        }

        return back()->with('success', 'Procesamiento finalizado con éxito.');
    }

    public function actualizacionManual(Request $request, $id)
    {
        $asistencia = AsistenciaDiaria::findOrFail($id);
        $empleado = Empleado::with('grupoBeneficio')->find($asistencia->empleado_id);
        
        $request->validate([
            'observaciones' => 'required|min:5'
        ]);

        // 1. Obtener turnos para recalcular
        $asignacion = AsignacionHorario::where('empleado_id', $empleado->id)
            ->where('fecha_inicio', '<=', $asistencia->fecha->format('Y-m-d'))
            ->where(fn($q) => $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $asistencia->fecha->format('Y-m-d')))
            ->first();

        if (!$asignacion) return back()->with('error', 'Sin horario asignado.');
        $turnos = $asignacion->horario->turnos()->where('dia_semana', $asistencia->fecha->dayOfWeekIso)->get();

        // 2. CORRECCIÓN: Nombres de variables coincidentes con el HTML (tipo_e1_id, etc.)
        // Si hay un tipo seleccionado, usamos la hora programada para anular el atraso.
        $marcacionesNuevas = [
            'entrada_1_real' => $request->tipo_e1_id ? $asistencia->entrada_1_prog : $request->entrada_1_real,
            'salida_1_real'  => $request->tipo_s1_id ? $asistencia->salida_1_prog : $request->salida_1_real,
            'entrada_2_real' => $request->tipo_e2_id ? $asistencia->entrada_2_prog : $request->entrada_2_real,
            'salida_2_real'  => $request->tipo_s2_id ? $asistencia->salida_2_prog : $request->salida_2_real,
        ];

        // 3. Recalcular estado y minutos tarde
        $analisis = $this->calcularAsistencia($empleado, $asistencia->fecha, $turnos, $marcacionesNuevas);

        // 4. CORRECCIÓN: Guardar con los nombres de columna correctos de tu BD
        $asistencia->update(array_merge($analisis, [
            'tipo_e1_id'    => $request->tipo_e1_id,
            'tipo_s1_id'    => $request->tipo_s1_id,
            'tipo_e2_id'    => $request->tipo_e2_id,
            'tipo_s2_id'    => $request->tipo_s2_id,
            'observaciones' => $request->observaciones,
            'tipo_registro' => 'MANUAL'
        ]));

        return back()->with('success', 'Registro regularizado correctamente.');
    }

    public function reprocesar(Request $request)
    {
        $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
        ]);

        $fechaDesde = Carbon::parse($request->fecha_desde)->startOfDay();
        $fechaHasta = Carbon::parse($request->fecha_hasta)->endOfDay();
        
        // Marcar como no procesadas las marcaciones del rango
        MarcacionCruda::whereBetween('fecha_hora', [$fechaDesde, $fechaHasta])->update(['procesado' => 0]);
        
        // Eliminar registros de asistencia para forzar recalculo
        $queryAsistencia = AsistenciaDiaria::whereBetween('fecha', [$fechaDesde->format('Y-m-d'), $fechaHasta->format('Y-m-d')]);
        if ($request->filled('empleado_id')) {
            $queryAsistencia->where('empleado_id', $request->empleado_id);
        }
        $queryAsistencia->delete();

        return $this->procesar();
    }

    private function calcularAsistencia($empleado, $fechaCarbon, $turnos, $reales)
    {
        $totalMinutosTarde = 0;
        $asistenciaCompleta = true;
        $extraEntrada = $empleado->grupoBeneficio->minutos_tolerancia_extra_entrada ?? 0;
        $extraSalida = $empleado->grupoBeneficio->minutos_tolerancia_extra_salida ?? 0;

        $datos = [
            'entrada_1_prog' => null, 'salida_1_prog' => null,
            'entrada_2_prog' => null, 'salida_2_prog' => null,
            'entrada_1_real' => $reales['entrada_1_real'] ?? null,
            'salida_1_real'  => $reales['salida_1_real'] ?? null,
            'entrada_2_real' => $reales['entrada_2_real'] ?? null,
            'salida_2_real'  => $reales['salida_2_real'] ?? null,
        ];

        foreach ($turnos as $t) {
            $n = $t->numero_turno;
            $datos["entrada_{$n}_prog"] = $t->hora_inicio;
            $datos["salida_{$n}_prog"] = $t->hora_fin;

            $eR = $datos["entrada_{$n}_real"];
            $sR = $datos["salida_{$n}_real"];

            if (!$eR || !$sR) {
                $asistenciaCompleta = false;
                continue;
            }

            $hProgE = $fechaCarbon->copy()->setTimeFrom(Carbon::parse($t->hora_inicio));
            $hProgS = $fechaCarbon->copy()->setTimeFrom(Carbon::parse($t->hora_fin));
            $hRealE = $fechaCarbon->copy()->setTimeFrom(Carbon::parse($eR));
            $hRealS = $fechaCarbon->copy()->setTimeFrom(Carbon::parse($sR));

            if ($hRealS->lt($hProgS->subMinutes($extraSalida))) {
                $asistenciaCompleta = false;
            }

            $tolerancia = ($t->minutos_tolerancia ?? 0) + $extraEntrada;
            $dif = $hProgE->diffInMinutes($hRealE, false);
            if ($dif > $tolerancia) $totalMinutosTarde += ($dif - $tolerancia);
        }

        $datos['estado_dia'] = !$asistenciaCompleta ? 'INASISTENCIA' : ($totalMinutosTarde > 0 ? 'TARDE' : 'NORMAL');
        $datos['minutos_tarde'] = $totalMinutosTarde;

        return $datos;
    }

    private function clasificarMarcaciones($fechaCarbon, $turnos, $grupoMarcaciones)
    {
        $puntos = [];
        foreach ($turnos as $t) {
            $puntos[] = ['tipo' => "entrada_{$t->numero_turno}_real", 'h' => $fechaCarbon->copy()->setTimeFrom(Carbon::parse($t->hora_inicio))];
            $puntos[] = ['tipo' => "salida_{$t->numero_turno}_real", 'h' => $fechaCarbon->copy()->setTimeFrom(Carbon::parse($t->hora_fin))];
        }

        $reales = [];
        foreach ($grupoMarcaciones as $m) {
            $mejorDist = null; $mejorTipo = null;
            foreach ($puntos as $p) {
                $dist = abs($m->fecha_hora->diffInSeconds($p['h']));
                if (is_null($mejorDist) || $dist < $mejorDist) {
                    $mejorDist = $dist; $mejorTipo = $p['tipo'];
                }
            }
            if (!isset($reales[$mejorTipo])) $reales[$mejorTipo] = $m->fecha_hora->format('H:i:s');
        }
        return $reales;
    }

    public function exportarPdf(Request $request)
    {
        $desde = $request->fecha_desde;
        $hasta = $request->fecha_hasta;
        $empId = $request->empleado_id;

        $query = AsistenciaDiaria::with('empleado')->whereBetween('fecha', [$desde, $hasta]);
        if ($empId) $query->where('empleado_id', $empId);

        $asistencias = $query->orderBy('fecha', 'asc')->get();
        $empleado = $empId ? Empleado::find($empId) : null;

        return Pdf::loadView('asistencias.pdf', compact('asistencias', 'desde', 'hasta', 'empleado'))
            ->setPaper('a4', 'portrait')->download("Asistencia_{$desde}_al_{$hasta}.pdf");
    }
}