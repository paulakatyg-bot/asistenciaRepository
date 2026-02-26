@extends('adminlte::auth.login')

{{-- Cambia el título de la pestaña del navegador --}}
@section('title', 'asistencia_gam')

{{-- Cambia el título que aparece sobre el cuadro de login --}}
@section('auth_header', 'asistencia_gam')

@section('auth_body')
<form method="POST" action="{{ route('login') }}">
    @csrf

    {{-- Email --}}
    <div class="input-group mb-3">
        <input type="email"
               name="email"
               class="form-control @error('email') is-invalid @enderror"
               placeholder="Correo electrónico"
               value="{{ old('email') }}"
               required autofocus>

        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope"></span>
            </div>
        </div>
        
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    {{-- Password --}}
    <div class="input-group mb-3">
        <input type="password"
               name="password"
               class="form-control @error('password') is-invalid @enderror"
               placeholder="Contraseña"
               required>

        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
        </div>

        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    {{-- Botón Ingresar y Recordarme --}}
    <div class="row">
        <div class="col-8">
            <div class="icheck-primary">
                <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">
                    Recordarme
                </label>
            </div>
        </div>

        <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">
                Ingresar
            </button>
        </div>
    </div>
</form>
@stop

{{-- 
    Dejamos esta sección vacía para eliminar el enlace de 
    "¿Olvidaste tu contraseña?" que AdminLTE pone por defecto 
--}}
@section('auth_footer')
@stop