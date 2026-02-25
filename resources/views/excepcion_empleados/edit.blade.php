@extends('adminlte::page')

@section('title', 'Editar Excepción')

@section('content_header')
    <h1>Editar Excepción</h1>
@stop

@section('content')

<div class="card">
<div class="card-body">

<form action="{{ route('excepcion_empleados.update', $excepcion_empleado) }}"
      method="POST">
@csrf
@method('PUT')

<div class="form-group">
    <label>Empleado</label>
    <select name="empleado_id" class="form-control" required>
        @foreach($empleados as $empleado)
            <option value="{{ $empleado->id }}"
                {{ $excepcion_empleado->empleado_id == $empleado->id ? 'selected' : '' }}>
                {{ $empleado->nombres }} {{ $empleado->apellidos }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Fecha Inicio</label>
    <input type="date"
           name="fecha_inicio"
           value="{{ $excepcion_empleado->fecha_inicio->format('Y-m-d') }}"
           class="form-control"
           required>
</div>

<div class="form-group">
    <label>Fecha Fin</label>
    <input type="date"
           name="fecha_fin"
           value="{{ $excepcion_empleado->fecha_fin->format('Y-m-d') }}"
           class="form-control"
           required>
</div>

<div class="form-group">
    <label>Minutos Extra Entrada</label>
    <input type="number"
           name="minutos_extra_entrada"
           value="{{ $excepcion_empleado->minutos_extra_entrada }}"
           class="form-control">
</div>

<div class="form-group">
    <label>Minutos Extra Salida</label>
    <input type="number"
           name="minutos_extra_salida"
           value="{{ $excepcion_empleado->minutos_extra_salida }}"
           class="form-control">
</div>

<div class="form-group">
    <label>Motivo</label>
    <textarea name="motivo"
              class="form-control"
              rows="2">{{ $excepcion_empleado->motivo }}</textarea>
</div>

<button class="btn btn-primary">
    <i class="fas fa-save"></i> Actualizar
</button>

<a href="{{ route('excepcion_empleados.index') }}"
   class="btn btn-secondary">
   Cancelar
</a>

</form>

</div>
</div>

@stop