@extends('adminlte::page')

@section('title', 'Editar Cargo')

@section('content_header')
    <h1>Editar Cargo</h1>
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

        <form action="{{ route('cargos.update', $cargo) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Nombre</label>
                <input type="text"
                       name="nombre"
                       value="{{ old('nombre', $cargo->nombre) }}"
                       class="form-control" required>
            </div>

            <div class="form-group">
                <label>Unidad</label>
                <select name="unidad_id" class="form-control" required>
                    @foreach($unidades as $unidad)
                        <option value="{{ $unidad->id }}"
                            @selected($cargo->unidad_id == $unidad->id)>
                            {{ $unidad->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion"
                          class="form-control"
                          rows="3">{{ old('descripcion', $cargo->descripcion) }}</textarea>
            </div>

            <button class="btn btn-primary">
                <i class="fas fa-save"></i> Actualizar
            </button>

            <a href="{{ route('cargos.index') }}" class="btn btn-secondary">
                Cancelar
            </a>

        </form>

    </div>
</div>

@stop