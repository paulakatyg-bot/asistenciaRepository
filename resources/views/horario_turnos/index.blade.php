@extends('adminlte::page')

@section('title', 'Turnos de Horario')

@section('content_header')
    <h1>Turnos de Horario</h1>
@stop

@section('content')

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
<div class="card-body">

<a href="{{ route('horario_turnos.create') }}" class="btn btn-success mb-3">
    <i class="fas fa-plus"></i> Nuevo Turno
</a>

<table class="table table-bordered table-hover">
    <thead class="thead-dark">
        <tr>
            <th>Horario</th>
            <th>Día</th>
            <th>Turno</th>
            <th>Inicio</th>
            <th>Fin</th>
            <th>Tolerancia</th>
            <th width="150">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($turnos as $turno)
        <tr>
            <td>{{ $turno->horario->nombre ?? '-' }}</td>
            <td>{{ $turno->dia_semana }}</td>
            <td>{{ $turno->numero_turno }}</td>
            <td>{{ $turno->hora_inicio }}</td>
            <td>{{ $turno->hora_fin }}</td>
            <td>{{ $turno->minutos_tolerancia }} min</td>
            <td>
                <a href="{{ route('horario_turnos.edit', $turno) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i>
                </a>

                <form action="{{ route('horario_turnos.destroy', $turno) }}"
                      method="POST"
                      style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm"
                            onclick="return confirm('¿Eliminar turno?')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $turnos->links('pagination::bootstrap-4') }}

</div>
</div>

@stop