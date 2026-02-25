@extends('adminlte::page')

@section('title', 'Editar Asignación')

@section('content_header')
    <h1>Editar Asignación de Horario</h1>
@stop

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
<div class="card-body">

<form action="{{ route('asignacion_horarios.update', $asignacion_horario->id) }}" method="POST">
@csrf
@method('PUT')

<div class="form-group">
    <label>Empleado</label>
    <select name="empleado_id" class="form-control" required>
        @foreach($empleados as $empleado)
            <option value="{{ $empleado->id }}"
                {{ old('empleado_id', $asignacion_horario->empleado_id) == $empleado->id ? 'selected' : '' }}>
                {{ $empleado->nombres }} {{ $empleado->apellidos }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Horario</label>
    <select name="horario_id" class="form-control" required>
        @foreach($horarios as $horario)
            <option value="{{ $horario->id }}"
                {{ old('horario_id', $asignacion_horario->horario_id) == $horario->id ? 'selected' : '' }}>
                {{ $horario->nombre }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Fecha Inicio</label>
    <input type="date"
           name="fecha_inicio"
           value="{{ old('fecha_inicio', $asignacion_horario->fecha_inicio ? $asignacion_horario->fecha_inicio->format('Y-m-d') : '') }}"
           class="form-control"
           required>
</div>

<div class="form-group">
    <label>Fecha Fin (opcional)</label>
    <input type="date"
           name="fecha_fin"
           value="{{ old('fecha_fin', $asignacion_horario->fecha_fin ? $asignacion_horario->fecha_fin->format('Y-m-d') : '') }}"
           class="form-control">
</div>

<br>

<button type="submit" class="btn btn-primary">
    <i class="fas fa-save"></i> Actualizar
</button>

<a href="{{ route('asignacion_horarios.index') }}" class="btn btn-secondary">
    Cancelar
</a>

</form>

</div>
</div>

@stop