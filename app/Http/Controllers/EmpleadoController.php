<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\GrupoBeneficio;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->busqueda;

        $empleados = Empleado::with('grupoBeneficio')
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where('ci', 'like', "%$busqueda%")
                    ->orWhere('nombres', 'like', "%$busqueda%")
                    ->orWhere('apellidos', 'like', "%$busqueda%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('empleados.index', compact('empleados'));
    }

    public function create()
    {
        $grupos = GrupoBeneficio::all();
        return view('empleados.create', compact('grupos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ci' => 'required|unique:empleados',
            'nombres' => 'required',
            'apellidos' => 'required',
            'genero' => 'required|in:M,F,OTRO',
            'fecha_nacimiento' => 'required|date',
            'fecha_contratacion' => 'required|date',
            'email' => 'nullable|email'
        ]);

        Empleado::create($request->all());

        return redirect()->route('empleados.index')
            ->with('success', 'Empleado registrado correctamente');
    }

    public function edit(Empleado $empleado)
    {
        $grupos = GrupoBeneficio::all();
        return view('empleados.edit', compact('empleado', 'grupos'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $request->validate([
            'ci' => 'required|unique:empleados,ci,' . $empleado->id,
            'nombres' => 'required',
            'apellidos' => 'required',
            'genero' => 'required|in:M,F,OTRO',
            'fecha_nacimiento' => 'required|date',
            'fecha_contratacion' => 'required|date',
            'email' => 'nullable|email'
        ]);

        $empleado->update($request->all());

        return redirect()->route('empleados.index')
            ->with('success', 'Empleado actualizado correctamente');
    }

    public function destroy(Empleado $empleado)
    {
        $empleado->delete();

        return redirect()->route('empleados.index')
            ->with('success', 'Empleado eliminado correctamente');
    }
}