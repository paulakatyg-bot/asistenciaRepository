@extends('adminlte::page')

@section('title', 'Nueva Excepción')

@section('content_header')
    <h1>Registrar Excepción</h1>
@stop

@section('content')

<div class="card">
<div class="card-body">

<form action="{{ route('excepcion_empleados.store') }}" method="POST">
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
    <label>Fecha Inicio</label>
    <input type="date" name="fecha_inicio"
           class="form-control" required>
</div>

<div class="form-group">
    <label>Fecha Fin</label>
    <input type="date" name="fecha_fin"
           class="form-control" required>
</div>

<div class="form-group">
    <label>Minutos Extra Entrada</label>
    <input type="number"
           name="minutos_extra_entrada"
           class="form-control"
           value="0">
</div>

<div class="form-group">
    <label>Minutos Extra Salida</label>
    <input type="number"
           name="minutos_extra_salida"
           class="form-control"
           value="0">
</div>

<div class="form-group">
    <label>Motivo</label>
    <textarea name="motivo"
              class="form-control"
              rows="2"></textarea>
</div>

<button class="btn btn-success">
    <i class="fas fa-save"></i> Guardar
</button>

<a href="{{ route('excepcion_empleados.index') }}"
   class="btn btn-secondary">
   Cancelar
</a>

</form>

</div>
</div>

@stop