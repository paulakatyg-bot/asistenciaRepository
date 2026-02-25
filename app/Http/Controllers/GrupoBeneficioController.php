<?php

namespace App\Http\Controllers;

use App\Models\GrupoBeneficio;
use Illuminate\Http\Request;

class GrupoBeneficioController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->busqueda;

        $grupos = GrupoBeneficio::when($busqueda, function ($query) use ($busqueda) {
                $query->where('nombre', 'like', "%$busqueda%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('grupo_beneficios.index', compact('grupos'));
    }

    public function create()
    {
        return view('grupo_beneficios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:grupo_beneficios',
            'minutos_tolerancia_extra_entrada' => 'required|integer|min:0',
            'minutos_tolerancia_extra_salida' => 'required|integer|min:0',
        ]);

        GrupoBeneficio::create($request->all());

        return redirect()->route('grupo_beneficios.index')
            ->with('success', 'Grupo creado correctamente');
    }

    public function edit(GrupoBeneficio $grupo_beneficio)
    {
        return view('grupo_beneficios.edit', compact('grupo_beneficio'));
    }

    public function update(Request $request, GrupoBeneficio $grupo_beneficio)
    {
        $request->validate([
            'nombre' => 'required|unique:grupo_beneficios,nombre,' . $grupo_beneficio->id,
            'minutos_tolerancia_extra_entrada' => 'required|integer|min:0',
            'minutos_tolerancia_extra_salida' => 'required|integer|min:0',
        ]);

        $grupo_beneficio->update($request->all());

        return redirect()->route('grupo_beneficios.index')
            ->with('success', 'Grupo actualizado correctamente');
    }

    public function destroy(GrupoBeneficio $grupo_beneficio)
    {
        $grupo_beneficio->delete();

        return redirect()->route('grupo_beneficios.index')
            ->with('success', 'Grupo eliminado correctamente');
    }
}