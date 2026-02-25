@extends('adminlte::page')

@section('title', 'Nueva Asignación')

@section('content_header')
    <h1>Asignar Horario</h1>
@stop

@section('content')

<div class="card">
<div class="card-body">

<form action="{{ route('asignacion_horarios.store') }}" method="POST">
@csrf

<div class="form-group">
    <label>Empleado</label>
    <select name="empleado_id" class="form-control" required>
        <option value="">Seleccione</option>
        @foreach($empleados as $empleado)
            <option value="{{ $empleado->id }}">
                {{ $empleado->nombres }} {{ $empleado->apellidos }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Horario</label>
    <select name="horario_id" class="form-control" required>
        <option value="">Seleccione</option>
        @foreach($horarios as $horario)
            <option value="{{ $horario->id }}">
                {{ $horario->nombre }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Fecha Inicio</label>
    <input type="date" name="fecha_inicio"
           class="form-control" required>
</div>

<div class="form-group">
    <label>Fecha Fin (opcional)</label>
    <input type="date" name="fecha_fin"
           class="form-control">
</div>

<button class="btn btn-success">
    <i class="fas fa-save"></i> Guardar
</button>

<a href="{{ route('asignacion_horarios.index') }}"
   class="btn btn-secondary">
   Cancelar
</a>

</form>

</div>
</div>

@stop