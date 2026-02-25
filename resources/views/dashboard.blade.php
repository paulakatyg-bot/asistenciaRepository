@extends('adminlte::page')

@section('title', 'Panel Institucional')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark">
                Gobierno Autónomo Municipal de Porco
            </h1>
            <small class="text-muted">
                Sistema de Control de Asistencia
            </small>
        </div>

        <div>
            <span class="badge badge-success">
                Usuario: {{ auth()->user()->name ?? 'Administrador' }}
            </span>
        </div>
    </div>
@stop

@section('content')



{{-- Card institucional --}}
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">
            Bienvenido al Sistema
        </h3>
    </div>
    <div class="card-body">
        <p>
            Este sistema permite gestionar el control de asistencia del personal
            del <strong>Gobierno Autónomo Municipal de Porco</strong>,
            incluyendo horarios administrativos, turnos, feriados,
            excepciones y reportes institucionales.
        </p>

        <p>
            Fecha actual:
            <strong>{{ now()->format('d/m/Y') }}</strong>
        </p>
    </div>
</div>

@stop