@extends('adminlte::page')

@section('title', 'Importar Marcaciones')

@section('content_header')
    <h1>Importar Marcaciones Crudas</h1>
@stop

@section('content')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

<div class="card">
<div class="card-body">

<form action="{{ route('marcaciones.store') }}"
      method="POST"
      enctype="multipart/form-data">

@csrf

<div class="form-group">
    <label>Archivo (.txt o .csv)</label>
    <input type="file"
           name="archivo"
           class="form-control"
           required>
</div>

<button class="btn btn-primary">
    <i class="fas fa-upload"></i> Importar
</button>

</form>

<hr>

<p><strong>Formato esperado:</strong></p>

<pre>
44  2026-01-01 15:41:49  1  255  1  0
</pre>

</div>
</div>

@stop