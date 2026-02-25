<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Unidad;
use Illuminate\Http\Request;

class CargoController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->busqueda;

        $cargos = Cargo::with('unidad')
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where('nombre', 'like', "%$busqueda%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('cargos.index', compact('cargos'));
    }

    public function create()
    {
        $unidades = Unidad::orderBy('nombre')->get();
        return view('cargos.create', compact('unidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:cargos',
            'unidad_id' => 'required|exists:unidades,id'
        ]);

        Cargo::create($request->all());

        return redirect()->route('cargos.index')
            ->with('success', 'Cargo creado correctamente');
    }

    public function edit(Cargo $cargo)
    {
        $unidades = Unidad::orderBy('nombre')->get();
        return view('cargos.edit', compact('cargo', 'unidades'));
    }

    public function update(Request $request, Cargo $cargo)
    {
        $request->validate([
            'nombre' => 'required|unique:cargos,nombre,' . $cargo->id,
            'unidad_id' => 'required|exists:unidades,id'
        ]);

        $cargo->update($request->all());

        return redirect()->route('cargos.index')
            ->with('success', 'Cargo actualizado correctamente');
    }

    public function destroy(Cargo $cargo)
    {
        $cargo->delete();

        return redirect()->route('cargos.index')
            ->with('success', 'Cargo eliminado correctamente');
    }
}