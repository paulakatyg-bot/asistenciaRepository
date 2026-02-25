<?php

namespace App\Http\Controllers;

use App\Models\HorarioTurno;
use App\Models\Horario;
use Illuminate\Http\Request;

class HorarioTurnoController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->busqueda;

        $turnos = HorarioTurno::with('horario')
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where('dia_semana', 'like', "%$busqueda%");
            })
            ->orderBy('horario_id')
            ->orderBy('dia_semana')
            ->orderBy('numero_turno')
            ->paginate(10);

        return view('horario_turnos.index', compact('turnos'));
    }

    public function create()
    {
        $horarios = Horario::orderBy('nombre')->get();
        return view('horario_turnos.create', compact('horarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'horario_id' => 'required|exists:horarios,id',
            'dia_semana' => 'required|integer|min:1|max:7',
            'numero_turno' => 'required|integer|min:1',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'minutos_tolerancia' => 'required|integer|min:0'
        ]);

        HorarioTurno::create($request->all());

        return redirect()->route('horario_turnos.index')
            ->with('success', 'Turno creado correctamente');
    }

    public function edit(HorarioTurno $horario_turno)
    {
        $horarios = Horario::orderBy('nombre')->get();
        return view('horario_turnos.edit', compact('horario_turno', 'horarios'));
    }

    public function update(Request $request, HorarioTurno $horario_turno)
    {
        $request->validate([
            'horario_id' => 'required|exists:horarios,id',
            'dia_semana' => 'required|integer|min:1|max:7',
            'numero_turno' => 'required|integer|min:1',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'minutos_tolerancia' => 'required|integer|min:0'
        ]);

        $horario_turno->update($request->all());

        return redirect()->route('horario_turnos.index')
            ->with('success', 'Turno actualizado correctamente');
    }

    public function destroy(HorarioTurno $horario_turno)
    {
        $horario_turno->delete();

        return redirect()->route('horario_turnos.index')
            ->with('success', 'Turno eliminado correctamente');
    }
}