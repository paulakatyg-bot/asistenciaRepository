@extends('adminlte::page')

@section('title', 'Nuevo Turno')

@section('content_header')
    <h1>Registrar Turno</h1>
@stop

@section('content')

<div class="card">
<div class="card-body">

<form action="{{ route('horario_turnos.store') }}" method="POST">
@csrf

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
    <label>Día de la Semana (1=Lunes ... 7=Domingo)</label>
    <input type="number" name="dia_semana"
           class="form-control" min="1" max="7" required>
</div>

<div class="form-group">
    <label>Número de Turno</label>
    <input type="number" name="numero_turno"
           class="form-control" min="1" required>
</div>

<div class="form-group">
    <label>Hora Inicio</label>
    <input type="time" name="hora_inicio"
           class="form-control" required>
</div>

<div class="form-group">
    <label>Hora Fin</label>
    <input type="time" name="hora_fin"
           class="form-control" required>
</div>

<div class="form-group">
    <label>Minutos Tolerancia</label>
    <input type="number" name="minutos_tolerancia"
           class="form-control" min="0" required>
</div>

<button class="btn btn-success">
    <i class="fas fa-save"></i> Guardar
</button>

<a href="{{ route('horario_turnos.index') }}" class="btn btn-secondary">
    Cancelar
</a>

</form>

</div>
</div>

@stop