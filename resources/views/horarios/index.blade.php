@extends('adminlte::page')

@section('title', 'Horarios')

@section('content_header')
    <h1>Horarios</h1>
@stop

@section('content')

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
<div class="card-body">

<a href="{{ route('horarios.create') }}" class="btn btn-success mb-3">
    <i class="fas fa-plus"></i> Nuevo Horario
</a>

<table class="table table-bordered table-hover">
    <thead class="thead-dark">
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Horas Semanales</th>
            <th width="180">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($horarios as $horario)
        <tr>
            <td>{{ $horario->nombre }}</td>
            <td>{{ $horario->tipo }}</td>
            <td>{{ $horario->horas_semanales }}</td>
            <td>
                <a href="{{ route('horarios.edit', $horario) }}"
                   class="btn btn-warning btn-sm">
                   <i class="fas fa-edit"></i>
                </a>

                <a href="{{ route('horario_turnos.index') }}?horario={{ $horario->id }}"
                   class="btn btn-info btn-sm">
                   Turnos
                </a>

                <form action="{{ route('horarios.destroy', $horario) }}"
                      method="POST"
                      style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm"
                            onclick="return confirm('¿Eliminar horario?')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $horarios->links('pagination::bootstrap-4') }}

</div>
</div>

@stop