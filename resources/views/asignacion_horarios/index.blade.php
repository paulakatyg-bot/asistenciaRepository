@extends('adminlte::page')

@section('title', 'Asignación de Horarios')

@section('content_header')
    <h1>Asignaciones de Horario</h1>
@stop

@section('content')

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
<div class="card-body">

<a href="{{ route('asignacion_horarios.create') }}"
   class="btn btn-success mb-3">
   <i class="fas fa-plus"></i> Nueva Asignación
</a>

<table class="table table-bordered table-hover">
<thead class="thead-dark">
<tr>
    <th>Empleado</th>
    <th>Horario</th>
    <th>Inicio</th>
    <th>Fin</th>
    <th width="150">Acciones</th>
</tr>
</thead>
<tbody>
@foreach($asignaciones as $asignacion)
<tr>
   <td>
        {{ ($asignacion->empleado->nombres ?? '') . ' ' . ($asignacion->empleado->apellidos ?? '') ?: '-' }}
    </td>
    <td>{{ $asignacion->horario->nombre ?? '-' }}</td>
    <td>{{ $asignacion->fecha_inicio->format('d/m/Y') }}</td>
    <td>
        {{ $asignacion->fecha_fin
            ? $asignacion->fecha_fin->format('d/m/Y')
            : 'Activo' }}
    </td>
    <td>
        <a href="{{ route('asignacion_horarios.edit', $asignacion) }}"
           class="btn btn-warning btn-sm">
            <i class="fas fa-edit"></i>
        </a>

        <form action="{{ route('asignacion_horarios.destroy', $asignacion) }}"
              method="POST"
              style="display:inline;">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm"
                onclick="return confirm('¿Eliminar asignación?')">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </td>
</tr>
@endforeach
</tbody>
</table>

{{ $asignaciones->links('pagination::bootstrap-4') }}

</div>
</div>

@stop