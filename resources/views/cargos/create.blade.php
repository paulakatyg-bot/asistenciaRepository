@extends('adminlte::page')

@section('title', 'Nuevo Cargo')

@section('content_header')
    <h1>Registrar Cargo</h1>
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

        <form action="{{ route('cargos.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre"
                       value="{{ old('nombre') }}"
                       class="form-control" required>
            </div>

            <div class="form-group">
                <label>Unidad</label>
                <select name="unidad_id" class="form-control" required>
                    <option value="">Seleccione</option>
                    @foreach($unidades as $unidad)
                        <option value="{{ $unidad->id }}">
                            {{ $unidad->nombre }}
                        </option>
                    @endforeach
                </select>
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

            <a href="{{ route('cargos.index') }}" class="btn btn-secondary">
                Cancelar
            </a>

        </form>

    </div>
</div>

@stop