@extends('adminlte::auth.login')

@section('auth_header', 'Iniciar Sesión')

@section('auth_body')

<form method="POST" action="{{ route('login') }}">
    @csrf

    {{-- Email --}}
    <div class="input-group mb-3">
        <input type="email"
               name="email"
               class="form-control"
               placeholder="Correo electrónico"
               value="{{ old('email') }}"
               required autofocus>

        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope"></span>
            </div>
        </div>
    </div>

    @error('email')
        <span class="text-danger text-sm">{{ $message }}</span>
    @enderror


    {{-- Password --}}
    <div class="input-group mb-3">
        <input type="password"
               name="password"
               class="form-control"
               placeholder="Contraseña"
               required>

        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
        </div>
    </div>

    @error('password')
        <span class="text-danger text-sm">{{ $message }}</span>
    @enderror


    {{-- Remember --}}
    <div class="row">
        <div class="col-8">
            <div class="icheck-primary">
                <input type="checkbox" id="remember" name="remember">
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

@section('auth_footer')
    @if (Route::has('password.request'))
        <p class="mb-1">
            <a href="{{ route('password.request') }}">
                ¿Olvidaste tu contraseña?
            </a>
        </p>
    @endif
@stop