@extends('adminlte::page')

@section('title', 'Editar Grupo')

@section('content_header')
    <h1>Editar Grupo de Beneficio</h1>
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

        <form action="{{ route('grupo_beneficios.update', $grupo_beneficio) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Nombre</label>
                <input type="text"
                       name="nombre"
                       value="{{ old('nombre', $grupo_beneficio->nombre) }}"
                       class="form-control" required>
            </div>

            <div class="form-group">
                <label>Minutos Tolerancia Extra Entrada</label>
                <input type="number"
                       name="minutos_tolerancia_extra_entrada"
                       value="{{ old('minutos_tolerancia_extra_entrada', $grupo_beneficio->minutos_tolerancia_extra_entrada) }}"
                       class="form-control" min="0" required>
            </div>

            <div class="form-group">
                <label>Minutos Tolerancia Extra Salida</label>
                <input type="number"
                       name="minutos_tolerancia_extra_salida"
                       value="{{ old('minutos_tolerancia_extra_salida', $grupo_beneficio->minutos_tolerancia_extra_salida) }}"
                       class="form-control" min="0" required>
            </div>

            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion"
                          class="form-control"
                          rows="3">{{ old('descripcion', $grupo_beneficio->descripcion) }}</textarea>
            </div>

            <button class="btn btn-primary">
                <i class="fas fa-save"></i> Actualizar
            </button>

            <a href="{{ route('grupo_beneficios.index') }}" class="btn btn-secondary">
                Cancelar
            </a>

        </form>

    </div>
</div>

@stop