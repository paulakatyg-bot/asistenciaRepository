@extends('adminlte::page')

@section('title', 'Nueva Unidad')

@section('content_header')
    <h1>Registrar Unidad</h1>
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

        <form action="{{ route('unidades.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre"
                       value="{{ old('nombre') }}"
                       class="form-control" required>
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

            <a href="{{ route('unidades.index') }}" class="btn btn-secondary">
                Cancelar
            </a>

        </form>

    </div>
</div>

@stop