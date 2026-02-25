<?php

namespace App\Http\Controllers;

use App\Models\ExcepcionEmpleado;
use App\Models\Empleado;
use Illuminate\Http\Request;

class ExcepcionEmpleadoController extends Controller
{
    public function index()
    {
        $excepciones = ExcepcionEmpleado::with('empleado')
            ->orderByDesc('fecha_inicio')
            ->paginate(10);

        return view('excepcion_empleados.index', compact('excepciones'));
    }

    public function create()
    {
        $empleados = Empleado::orderBy('nombres')->get();

        return view('excepcion_empleados.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'minutos_extra_entrada' => 'nullable|integer|min:0',
            'minutos_extra_salida' => 'nullable|integer|min:0',
            'motivo' => 'nullable|string|max:255'
        ]);

        ExcepcionEmpleado::create($request->all());

        return redirect()->route('excepcion_empleados.index')
            ->with('success', 'Excepción registrada correctamente');
    }

    public function edit(ExcepcionEmpleado $excepcion_empleado)
    {
        $empleados = Empleado::orderBy('nombres')->get();

        return view('excepcion_empleados.edit',
            compact('excepcion_empleado', 'empleados'));
    }

    public function update(Request $request, ExcepcionEmpleado $excepcion_empleado)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'minutos_extra_entrada' => 'nullable|integer|min:0',
            'minutos_extra_salida' => 'nullable|integer|min:0',
            'motivo' => 'nullable|string|max:255'
        ]);

        $excepcion_empleado->update($request->all());

        return redirect()->route('excepcion_empleados.index')
            ->with('success', 'Excepción actualizada correctamente');
    }

    public function destroy(ExcepcionEmpleado $excepcion_empleado)
    {
        $excepcion_empleado->delete();

        return redirect()->route('excepcion_empleados.index')
            ->with('success', 'Excepción eliminada correctamente');
    }
}