@extends('adminlte::page')

@section('title', 'Cargos')

@section('content_header')
    <h1>Cargos</h1>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between mb-3">

            <form method="GET" class="form-inline">
                <input type="text" name="busqueda"
                       value="{{ request('busqueda') }}"
                       class="form-control mr-2"
                       placeholder="Buscar nombre">

                <button class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <a href="{{ route('cargos.create') }}" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Nuevo Cargo
            </a>
        </div>

        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Unidad</th>
                    <th width="150">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cargos as $cargo)
                <tr>
                    <td>{{ $cargo->id }}</td>
                    <td>{{ $cargo->nombre }}</td>
                    <td>{{ $cargo->unidad->nombre ?? '-' }}</td>
                    <td>
                        <a href="{{ route('cargos.edit', $cargo) }}"
                           class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>

                        <form action="{{ route('cargos.destroy', $cargo) }}"
                              method="POST"
                              style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('¿Eliminar cargo?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $cargos->appends(request()->query())->links('pagination::bootstrap-4') }}

    </div>
</div>

@stop