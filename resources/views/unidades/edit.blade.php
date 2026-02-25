@extends('adminlte::page')

@section('title', 'Editar Unidad')

@section('content_header')
    <h1>Editar Unidad</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('unidades.update', $unidad) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Nombre</label>
                <input type="text"
                       name="nombre"
                       value="{{ old('nombre', $unidad->nombre) }}"
                       class="form-control" required>
            </div>

            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion"
                          class="form-control"
                          rows="3">{{ old('descripcion', $unidad->descripcion) }}</textarea>
            </div>

            <button class="btn btn-primary">
                <i class="fas fa-save"></i> Actualizar
            </button>

            <a href="{{ route('unidades.index') }}" class="btn btn-secondary">
                Cancelar
            </a>

        </form>

    </div>
</div>

@stop