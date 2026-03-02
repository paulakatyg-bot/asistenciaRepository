<?php

namespace App\Http\Controllers;

use App\Models\Calendario;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CalendarioController extends Controller
{
    public function index()
    {
        $eventos = Calendario::orderBy('fecha', 'desc')->get();
        return view('calendario.index', compact('eventos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date|unique:calendarios,fecha',
            'tipo_dia' => 'required|in:LABORAL,FERIADO,ESPECIAL',
            'descripcion' => 'nullable|string|max:150'
        ]);

        Calendario::create($request->all());

        return redirect()->back()->with('success', 'Día registrado correctamente.');
    }

    public function update(Request $request, $fecha)
    {
        $request->validate([
            'tipo_dia' => 'required|in:LABORAL,FERIADO,ESPECIAL',
            'descripcion' => 'nullable|string|max:150'
        ]);

        $dia = Calendario::where('fecha', $fecha)->firstOrFail();
        $dia->update($request->only(['tipo_dia', 'descripcion']));

        return redirect()->back()->with('success', 'Registro actualizado.');
    }

    public function destroy($fecha)
    {
        Calendario::where('fecha', $fecha)->delete();
        return redirect()->back()->with('success', 'Registro eliminado.');
    }
}