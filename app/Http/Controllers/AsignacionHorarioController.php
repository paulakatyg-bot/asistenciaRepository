<?php

namespace App\Http\Controllers;

use App\Models\AsignacionHorario;
use App\Models\Empleado;
use App\Models\Horario;
use Illuminate\Http\Request;

class AsignacionHorarioController extends Controller
{
    public function index(Request $request)
    {
        $asignaciones = AsignacionHorario::with(['empleado', 'horario'])
            ->orderByDesc('fecha_inicio')
            ->paginate(10);

        return view('asignacion_horarios.index', compact('asignaciones'));
    }

    public function create()
    {
        $empleados = Empleado::orderBy('nombres')->get();
        $horarios = Horario::orderBy('nombre')->get();

        return view('asignacion_horarios.create',
            compact('empleados', 'horarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'horario_id' => 'required|exists:horarios,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        // 🔥 Evitar traslapes de asignaciones activas
        $existe = AsignacionHorario::where('empleado_id', $request->empleado_id)
            ->where(function ($query) use ($request) {
                $query->whereNull('fecha_fin')
                      ->orWhere('fecha_fin', '>=', $request->fecha_inicio);
            })
            ->exists();

        if ($existe) {
            return back()->withErrors([
                'empleado_id' => 'El empleado ya tiene un horario activo en ese rango.'
            ])->withInput();
        }

        AsignacionHorario::create($request->all());

        return redirect()->route('asignacion_horarios.index')
            ->with('success', 'Horario asignado correctamente');
    }

    public function edit(AsignacionHorario $asignacion_horario)
    {
        $empleados = Empleado::orderBy('nombres')->get();
        $horarios = Horario::orderBy('nombre')->get();

        return view('asignacion_horarios.edit',
            compact('asignacion_horario','empleados','horarios'));
    }

    public function update(Request $request, AsignacionHorario $asignacion_horario)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'horario_id' => 'required|exists:horarios,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        $asignacion_horario->update($request->all());

        return redirect()->route('asignacion_horarios.index')
            ->with('success', 'Asignación actualizada correctamente');
    }

    public function destroy(AsignacionHorario $asignacion_horario)
    {
        $asignacion_horario->delete();

        return redirect()->route('asignacion_horarios.index')
            ->with('success', 'Asignación eliminada correctamente');
    }
}