<?php

namespace App\Http\Controllers;

use App\Models\TipoTickeo;
use Illuminate\Http\Request;

class TipoTickeoController extends Controller
{
    public function index()
    {
        $tipos = TipoTickeo::all();
        return view('tipo_tickeos.index', compact('tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:tipo_tickeos,nombre',
            'color' => 'required|string|max:20',
            'requiere_observacion' => 'nullable|boolean'
        ]);

        TipoTickeo::create([
            'nombre' => strtoupper($request->nombre),
            'color' => $request->color,
            'requiere_observacion' => $request->has('requiere_observacion') ? 1 : 0
        ]);

        return redirect()->back()->with('success', 'Tipo de tickeo registrado.');
    }

    public function update(Request $request, $id)
    {
        $tipo = TipoTickeo::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:50|unique:tipo_tickeos,nombre,' . $id,
            'color' => 'required|string|max:20',
        ]);

        $tipo->update([
            'nombre' => strtoupper($request->nombre),
            'color' => $request->color,
            'requiere_observacion' => $request->has('requiere_observacion') ? 1 : 0
        ]);

        return redirect()->back()->with('success', 'Registro actualizado.');
    }

    public function destroy($id)
    {
        $tipo = TipoTickeo::findOrFail($id);
        // Evitar eliminar el tipo NORMAL si es el por defecto
        if($tipo->nombre == 'NORMAL'){
            return redirect()->back()->with('error', 'No se puede eliminar el tipo base.');
        }
        
        $tipo->delete();
        return redirect()->back()->with('success', 'Registro eliminado.');
    }
}