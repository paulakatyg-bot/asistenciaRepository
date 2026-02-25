<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Models\MarcacionCruda;
use Illuminate\Support\Facades\DB;

class MarcacionCrudaController extends Controller
{
    public function create()
    {
        return view('marcaciones.importar');
    }

    public function store(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:txt,csv'
        ]);

        $archivo = $request->file('archivo');
        $nombreArchivo = time().'_'.$archivo->getClientOriginalName();
        $ruta = $archivo->getRealPath();

        $lineas = file($ruta);

        $insertados = 0;

        DB::beginTransaction();

        try {

            foreach ($lineas as $linea) {

                $linea = trim($linea);

                if (empty($linea)) continue;

                // separar por espacios múltiples o tab
                $columnas = preg_split('/\s+/', $linea);

                if (count($columnas) < 3) continue;

                $codigoBiometrico = $columnas[0];
                $fechaHora = $columnas[1] . ' ' . $columnas[2];

                $empleado = Empleado::where('codigo_biometrico', $codigoBiometrico)->first();

                if (!$empleado) {
                    continue; // si no existe empleado lo ignora
                }

                $existe = MarcacionCruda::where('empleado_id', $empleado->id)
                    ->where('fecha_hora', $fechaHora)
                    ->exists();

                if (!$existe) {
                    MarcacionCruda::create([
                        'empleado_id' => $empleado->id,
                        'fecha_hora' => $fechaHora,
                        'archivo_origen' => $nombreArchivo,
                        'procesado' => false
                    ]);

                    $insertados++;
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar archivo');
        }

        return redirect()
            ->route('marcaciones.create')
            ->with('success', "Archivo importado. Registros insertados: $insertados");
    }
}