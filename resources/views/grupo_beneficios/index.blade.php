@extends('adminlte::page')

@section('title', 'Grupos de Beneficio')

@section('content_header')
    <h1>Grupos de Beneficio</h1>
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

            <a href="{{ route('grupo_beneficios.create') }}" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Nuevo
            </a>
        </div>

        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Tolerancia Entrada</th>
                    <th>Tolerancia Salida</th>
                    <th width="150">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grupos as $grupo)
                <tr>
                    <td>{{ $grupo->id }}</td>
                    <td>{{ $grupo->nombre }}</td>
                    <td>{{ $grupo->minutos_tolerancia_extra_entrada }} min</td>
                    <td>{{ $grupo->minutos_tolerancia_extra_salida }} min</td>
                    <td>
                        <a href="{{ route('grupo_beneficios.edit', $grupo) }}"
                           class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>

                        <form action="{{ route('grupo_beneficios.destroy', $grupo) }}"
                              method="POST"
                              style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('¿Eliminar grupo?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $grupos->appends(request()->query())->links('pagination::bootstrap-4') }}

    </div>
</div>

@stop