@extends('adminlte::page')

@section('title', 'Empleados')

@section('content_header')
    <h1>Empleados</h1>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between mb-3">

            <form method="GET" class="form-inline">
                <input type="text"
                       name="busqueda"
                       value="{{ request('busqueda') }}"
                       class="form-control mr-2"
                       placeholder="Buscar CI, nombre o código biométrico">

                <button class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <a href="{{ route('empleados.create') }}" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Nuevo Empleado
            </a>
        </div>

        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>CI</th>
                    <th>Nombre</th>
                    <th>Genero</th>
                    <th>Celular</th>
                    <th>Grupo</th>
                    <th>Código Biométrico</th> <!-- Nueva columna -->
                    <th>Estado</th>
                    <th width="150">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($empleados as $empleado)
                <tr>
                    <td>{{ $empleado->id }}</td>
                    <td>{{ $empleado->ci }}</td>
                    <td>{{ $empleado->nombres }} {{ $empleado->apellidos }}</td>
                    <td>{{ $empleado->genero }}</td>
                    <td>{{ $empleado->celular ?? '-' }}</td>
                    <td>{{ $empleado->grupoBeneficio->nombre ?? '-' }}</td>
                    <td>{{ $empleado->codigo_biometrico ?? '-' }}</td> <!-- Mostramos código biométrico -->
                    <td>
                        @if($empleado->estado)
                            <span class="badge badge-success">Activo</span>
                        @else
                            <span class="badge badge-danger">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('empleados.edit', $empleado) }}"
                           class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>

                        <form action="{{ route('empleados.destroy', $empleado) }}"
                              method="POST"
                              style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('¿Eliminar empleado?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $empleados->appends(request()->query())->links('pagination::bootstrap-4') }}

    </div>
</div>

@stop