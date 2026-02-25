@extends('adminlte::page')

@section('title', 'Editar Horario')

@section('content_header')
    <h1>Editar Horario</h1>
@stop

@section('content')

<div class="card">
<div class="card-body">

<form action="{{ route('horarios.update', $horario) }}" method="POST">
@csrf
@method('PUT')

<div class="form-group">
    <label>Nombre</label>
    <input type="text"
           name="nombre"
           value="{{ old('nombre', $horario->nombre) }}"
           class="form-control"
           required>
</div>

<div class="form-group">
    <label>Tipo</label>
    <select name="tipo" class="form-control" required>
        <option value="ADMIN_1_TURNO"
            {{ old('tipo', $horario->tipo) == 'ADMIN_1_TURNO' ? 'selected' : '' }}>
            Administrativo 1 Turno
        </option>

        <option value="ADMIN_2_TURNOS"
            {{ old('tipo', $horario->tipo) == 'ADMIN_2_TURNOS' ? 'selected' : '' }}>
            Administrativo 2 Turnos
        </option>
    </select>
</div>

<div class="form-group">
    <label>Horas Semanales</label>
    <input type="number"
           name="horas_semanales"
           value="{{ old('horas_semanales', $horario->horas_semanales) }}"
           class="form-control">
</div>

<button class="btn btn-primary">
    <i class="fas fa-save"></i> Actualizar
</button>

<a href="{{ route('horarios.index') }}" class="btn btn-secondary">
    Cancelar
</a>

</form>

</div>
</div>

@stop