@extends('adminlte::page')

@section('title', 'Nuevo Empleado')

@section('content_header')
    <h1>Registrar Empleado</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('empleados.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>CI</label>
                        <input type="text" name="ci"
                               value="{{ old('ci') }}"
                               class="form-control" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Genero</label>
                        <select name="genero" class="form-control" required>
                            <option value="">Seleccione</option>
                            <option value="M" @selected(old('genero')=='M')>Masculino</option>
                            <option value="F" @selected(old('genero')=='F')>Femenino</option>
                            <option value="OTRO" @selected(old('genero')=='OTRO')>Otro</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Nombres</label>
                <input type="text" name="nombres"
                       value="{{ old('nombres') }}"
                       class="form-control" required>
            </div>

            <div class="form-group">
                <label>Apellidos</label>
                <input type="text" name="apellidos"
                       value="{{ old('apellidos') }}"
                       class="form-control" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Fecha Nacimiento</label>
                        <input type="date" name="fecha_nacimiento"
                               value="{{ old('fecha_nacimiento') }}"
                               class="form-control" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Fecha Contratación</label>
                        <input type="date" name="fecha_contratacion"
                               value="{{ old('fecha_contratacion') }}"
                               class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Dirección</label>
                <input type="text" name="direccion"
                       value="{{ old('direccion') }}"
                       class="form-control">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Celular</label>
                        <input type="text" name="celular"
                               value="{{ old('celular') }}"
                               class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email"
                               value="{{ old('email') }}"
                               class="form-control">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Grupo Beneficio</label>
                <select name="grupo_beneficio_id" class="form-control">
                    <option value="">Seleccione</option>
                    @foreach($grupos as $grupo)
                        <option value="{{ $grupo->id }}"
                            @selected(old('grupo_beneficio_id') == $grupo->id)>
                            {{ $grupo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- CAMPO CODIGO BIOMETRICO -->
            <div class="form-group">
                <label>Código Biométrico</label>
                <input type="text" name="codigo_biometrico"
                       value="{{ old('codigo_biometrico') }}"
                       class="form-control"
                       placeholder="Ingrese código biométrico">
            </div>

            <div class="form-group">
                <label>Estado</label>
                <select name="estado" class="form-control">
                    <option value="1" @selected(old('estado')==1)>Activo</option>
                    <option value="0" @selected(old('estado')==0)>Inactivo</option>
                </select>
            </div>

            <div class="mt-3">
                <button class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar
                </button>

                <a href="{{ route('empleados.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>

        </form>

    </div>
</div>

@stop