<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use Illuminate\Http\Request;

class UnidadController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->busqueda;

        $unidades = Unidad::when($busqueda, function ($query) use ($busqueda) {
                $query->where('nombre', 'like', "%$busqueda%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('unidades.index', compact('unidades'));
    }

    public function create()
    {
        return view('unidades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:unidades',
        ]);

        Unidad::create($request->all());

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad creada correctamente');
    }

    public function edit(Unidad $unidad)
    {
        return view('unidades.edit', compact('unidad'));
    }

    public function update(Request $request, Unidad $unidad)
    {
        $request->validate([
            'nombre' => 'required|unique:unidades,nombre,' . $unidad->id,
        ]);

        $unidad->update($request->all());

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad actualizada correctamente');
    }

    public function destroy(Unidad $unidad)
    {
        // Opcional: evitar eliminar si tiene cargos
        if ($unidad->cargos()->count() > 0) {
            return redirect()->route('unidades.index')
                ->with('error', 'No se puede eliminar. Tiene cargos asociados.');
        }

        $unidad->delete();

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad eliminada correctamente');
    }
}