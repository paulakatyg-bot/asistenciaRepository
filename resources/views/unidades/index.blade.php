@extends('adminlte::page')

@section('title', 'Unidades')

@section('content_header')
    <h1>Unidades</h1>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
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

            <a href="{{ route('unidades.create') }}" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Nueva Unidad
            </a>
        </div>

        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th width="150">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($unidades as $unidad)
                <tr>
                    <td>{{ $unidad->id }}</td>
                    <td>{{ $unidad->nombre }}</td>
                    <td>{{ $unidad->descripcion ?? '-' }}</td>
                    <td>
                        <a href="{{ route('unidades.edit', $unidad) }}"
                           class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>

                        <form action="{{ route('unidades.destroy', $unidad) }}"
                              method="POST"
                              style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('¿Eliminar unidad?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $unidades->appends(request()->query())->links('pagination::bootstrap-4') }}

    </div>
</div>

@stop