@extends('adminlte::page')

@section('title', 'Nuevo Horario')

@section('content_header')
    <h1>Registrar Horario</h1>
@stop

@section('content')

<div class="card">
<div class="card-body">

<form action="{{ route('horarios.store') }}" method="POST">
@csrf

<div class="form-group">
    <label>Nombre</label>
    <input type="text" name="nombre"
           class="form-control"
           value="{{ old('nombre') }}"
           required>
</div>

<div class="form-group">
    <label>Tipo</label>
    <select name="tipo" class="form-control" required>
        <option value="">Seleccione</option>
        <option value="ADMIN_1_TURNO" {{ old('tipo') == 'ADMIN_1_TURNO' ? 'selected' : '' }}>
            Administrativo 1 Turno
        </option>
        <option value="ADMIN_2_TURNOS" {{ old('tipo') == 'ADMIN_2_TURNOS' ? 'selected' : '' }}>
            Administrativo 2 Turnos
        </option>
    </select>
</div>

<div class="form-group">
    <label>Horas Semanales</label>
    <input type="number"
           name="horas_semanales"
           class="form-control"
           value="{{ old('horas_semanales') }}">
</div>

<button class="btn btn-success">
    <i class="fas fa-save"></i> Guardar
</button>

<a href="{{ route('horarios.index') }}" class="btn btn-secondary">
    Cancelar
</a>

</form>

</div>
</div>

@stop