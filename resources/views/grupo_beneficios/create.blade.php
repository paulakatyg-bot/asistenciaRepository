@extends('adminlte::page')

@section('title', 'Nuevo Grupo')

@section('content_header')
    <h1>Registrar Grupo de Beneficio</h1>
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

        <form action="{{ route('grupo_beneficios.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre"
                       value="{{ old('nombre') }}"
                       class="form-control" required>
            </div>

            <div class="form-group">
                <label>Minutos Tolerancia Extra Entrada</label>
                <input type="number"
                       name="minutos_tolerancia_extra_entrada"
                       value="{{ old('minutos_tolerancia_extra_entrada', 0) }}"
                       class="form-control" min="0" required>
            </div>

            <div class="form-group">
                <label>Minutos Tolerancia Extra Salida</label>
                <input type="number"
                       name="minutos_tolerancia_extra_salida"
                       value="{{ old('minutos_tolerancia_extra_salida', 0) }}"
                       class="form-control" min="0" required>
            </div>

            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion"
                          class="form-control"
                          rows="3">{{ old('descripcion') }}</textarea>
            </div>

            <button class="btn btn-success">
                <i class="fas fa-save"></i> Guardar
            </button>

            <a href="{{ route('grupo_beneficios.index') }}" class="btn btn-secondary">
                Cancelar
            </a>

        </form>

    </div>
</div>

@stop