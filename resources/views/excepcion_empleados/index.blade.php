@extends('adminlte::page')

@section('title', 'Excepciones de Empleados')

@section('content_header')
    <h1>Excepciones de Empleados</h1>
@stop

@section('content')

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
<div class="card-body">

<a href="{{ route('excepcion_empleados.create') }}"
   class="btn btn-success mb-3">
   <i class="fas fa-plus"></i> Nueva Excepción
</a>

<table class="table table-bordered table-hover">
<thead class="thead-dark">
<tr>
    <th>Empleado</th>
    <th>Desde</th>
    <th>Hasta</th>
    <th>Extra Entrada</th>
    <th>Extra Salida</th>
    <th>Motivo</th>
    <th width="150">Acciones</th>
</tr>
</thead>
<tbody>
@foreach($excepciones as $excepcion)
<tr>
    <td>
        {{ $excepcion->empleado->nombres ?? '' }}
        {{ $excepcion->empleado->apellidos ?? '' }}
    </td>

    <td>{{ $excepcion->fecha_inicio->format('d/m/Y') }}</td>
    <td>{{ $excepcion->fecha_fin->format('d/m/Y') }}</td>

    <td>{{ $excepcion->minutos_extra_entrada }} min</td>
    <td>{{ $excepcion->minutos_extra_salida }} min</td>

    <td>{{ $excepcion->motivo }}</td>

    <td>
        <a href="{{ route('excepcion_empleados.edit', $excepcion) }}"
           class="btn btn-warning btn-sm">
            <i class="fas fa-edit"></i>
        </a>

        <form action="{{ route('excepcion_empleados.destroy', $excepcion) }}"
              method="POST"
              style="display:inline;">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm"
                onclick="return confirm('¿Eliminar excepción?')">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </td>
</tr>
@endforeach
</tbody>
</table>

{{ $excepciones->links('pagination::bootstrap-4') }}

</div>
</div>

@stop