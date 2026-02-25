@extends('adminlte::page')

@section('title', 'Editar Turno')

@section('content_header')
    <h1>Editar Turno</h1>
@stop

@section('content')

<div class="card">
<div class="card-body">

<form action="{{ route('horario_turnos.update', $horario_turno) }}" method="POST">
@csrf
@method('PUT')

<div class="form-group">
    <label>Horario</label>
    <select name="horario_id" class="form-control" required>
        @foreach($horarios as $horario)
            <option value="{{ $horario->id }}"
                @selected($horario_turno->horario_id == $horario->id)>
                {{ $horario->nombre }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Día de la Semana</label>
    <input type="number" name="dia_semana"
           value="{{ $horario_turno->dia_semana }}"
           class="form-control" min="1" max="7" required>
</div>

<div class="form-group">
    <label>Número de Turno</label>
    <input type="number" name="numero_turno"
           value="{{ $horario_turno->numero_turno }}"
           class="form-control" min="1" required>
</div>

<div class="form-group">
    <label>Hora Inicio</label>
    <input type="time" name="hora_inicio"
           value="{{ \Carbon\Carbon::parse($horario_turno->hora_inicio)->format('H:i') }}"
           class="form-control" required>
</div>

<div class="form-group">
    <label>Hora Fin</label>
    <input type="time" name="hora_fin"
           value="{{ \Carbon\Carbon::parse($horario_turno->hora_fin)->format('H:i') }}"
           class="form-control" required>
</div>

<div class="form-group">
    <label>Minutos Tolerancia</label>
    <input type="number" name="minutos_tolerancia"
           value="{{ $horario_turno->minutos_tolerancia }}"
           class="form-control" min="0" required>
</div>

<button class="btn btn-primary">
    <i class="fas fa-save"></i> Actualizar
</button>

<a href="{{ route('horario_turnos.index') }}" class="btn btn-secondary">
    Cancelar
</a>

</form>

</div>
</div>

@stop