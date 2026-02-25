@extends('adminlte::page')

@section('title', 'Editar Empleado')

@section('content_header')
    <h1>Editar Empleado</h1>
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

        <form action="{{ route('empleados.update', $empleado) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>CI</label>
                        <input type="text" name="ci"
                               value="{{ old('ci', $empleado->ci) }}"
                               class="form-control" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Genero</label>
                        <select name="genero" class="form-control" required>
                            <option value="M" @selected($empleado->genero=='M')>Masculino</option>
                            <option value="F" @selected($empleado->genero=='F')>Femenino</option>
                            <option value="OTRO" @selected($empleado->genero=='OTRO')>Otro</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Nombres</label>
                <input type="text" name="nombres"
                       value="{{ old('nombres', $empleado->nombres) }}"
                       class="form-control" required>
            </div>

            <div class="form-group">
                <label>Apellidos</label>
                <input type="text" name="apellidos"
                       value="{{ old('apellidos', $empleado->apellidos) }}"
                       class="form-control" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Fecha Nacimiento</label>
                        <input type="date" name="fecha_nacimiento"
                               value="{{ old('fecha_nacimiento', $empleado->fecha_nacimiento->format('Y-m-d')) }}"
                               class="form-control" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Fecha Contratación</label>
                        <input type="date" name="fecha_contratacion"
                               value="{{ old('fecha_contratacion', $empleado->fecha_contratacion->format('Y-m-d')) }}"
                               class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Dirección</label>
                <input type="text" name="direccion"
                       value="{{ old('direccion', $empleado->direccion) }}"
                       class="form-control">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Celular</label>
                        <input type="text" name="celular"
                               value="{{ old('celular', $empleado->celular) }}"
                               class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email"
                               value="{{ old('email', $empleado->email) }}"
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
                            @selected($empleado->grupo_beneficio_id == $grupo->id)>
                            {{ $grupo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- CAMPO CODIGO BIOMETRICO -->
            <div class="form-group">
                <label>Código Biométrico</label>
                <input type="text" name="codigo_biometrico"
                       value="{{ old('codigo_biometrico', $empleado->codigo_biometrico) }}"
                       class="form-control"
                       placeholder="Ingrese código biométrico">
            </div>

            <div class="form-group">
                <label>Estado</label>
                <select name="estado" class="form-control">
                    <option value="1" @selected($empleado->estado==1)>Activo</option>
                    <option value="0" @selected($empleado->estado==0)>Inactivo</option>
                </select>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar
                </button>

                <a href="{{ route('empleados.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>

        </form>

    </div>
</div>

@stop