<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->busqueda;

        $horarios = Horario::when($busqueda, function ($query) use ($busqueda) {
                $query->where('nombre', 'like', "%$busqueda%");
            })
            ->orderBy('nombre')
            ->paginate(10);

        return view('horarios.index', compact('horarios'));
    }

    public function create()
    {
        return view('horarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:100',
            'horas_semanales' => 'required|numeric|min:0'
        ]);

        Horario::create($request->all());

        return redirect()->route('horarios.index')
            ->with('success', 'Horario creado correctamente');
    }

    public function edit(Horario $horario)
    {
        return view('horarios.edit', compact('horario'));
    }

    public function update(Request $request, Horario $horario)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:100',
            'horas_semanales' => 'required|numeric|min:0'
        ]);

        $horario->update($request->all());

        return redirect()->route('horarios.index')
            ->with('success', 'Horario actualizado correctamente');
    }

    public function destroy(Horario $horario)
    {
        if ($horario->turnos()->count() > 0) {
            return redirect()->route('horarios.index')
                ->with('error', 'No se puede eliminar porque tiene turnos asociados');
        }

        $horario->delete();

        return redirect()->route('horarios.index')
            ->with('success', 'Horario eliminado correctamente');
    }
}