@extends('adminlte::page')

@section('title', 'Tipos de Tickeo')

@section('content_header')
    <h1><i class="fas fa-tags mr-2 text-primary"></i>Tipos de Tickeo / Justificaciones</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- Formulario --}}
        <div class="col-md-4">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">Nuevo Tipo</h3>
                </div>
                <form action="{{ route('tipo_tickeos.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: COMISIÓN" required>
                        </div>
                        <div class="form-group">
                            <label>Color del Badge</label>
                            <select name="color" class="form-control" required>
                                <option value="primary">Azul (Primary)</option>
                                <option value="success">Verde (Success)</option>
                                <option value="danger">Rojo (Danger)</option>
                                <option value="warning">Amarillo (Warning)</option>
                                <option value="info">Celeste (Info)</option>
                                <option value="secondary">Gris (Secondary)</option>
                                <option value="dark">Negro (Dark)</option>
                            </select>
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="obsSwitch" name="requiere_observacion">
                            <label class="custom-control-label" for="obsSwitch">¿Requiere nota obligatoria?</label>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover table-sm m-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-3">ID</th>
                                <th>Nombre / Vista Previa</th>
                                <th>¿Nota Oblig.?</th>
                                <th class="text-right pr-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tipos as $tipo)
                            <tr>
                                <td class="pl-3 align-middle">{{ $tipo->id }}</td>
                                <td class="align-middle">
                                    <span class="badge badge-{{ $tipo->color }} px-3">
                                        {{ $tipo->nombre }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    {!! $tipo->requiere_observacion ? '<span class="text-success">SÍ</span>' : '<span class="text-muted">NO</span>' !!}
                                </td>
                                <td class="text-right pr-3 align-middle">
                                    <button class="btn btn-xs btn-info btn-edit" 
                                            data-id="{{ $tipo->id }}"
                                            data-nombre="{{ $tipo->nombre }}"
                                            data-color="{{ $tipo->color }}"
                                            data-obs="{{ $tipo->requiere_observacion }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    @if($tipo->nombre !== 'NORMAL')
                                    <form action="{{ route('tipo_tickeos.destroy', $tipo->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('¿Eliminar?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDITAR --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Editar Tipo</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Color</label>
                        <select name="color" id="edit_color" class="form-control">
                            <option value="primary">Azul</option>
                            <option value="success">Verde</option>
                            <option value="danger">Rojo</option>
                            <option value="warning">Amarillo</option>
                            <option value="info">Celeste</option>
                            <option value="secondary">Gris</option>
                        </select>
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="edit_obs" name="requiere_observacion">
                        <label class="custom-control-label" for="edit_obs">¿Requiere nota obligatoria?</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info">Actualizar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        $('#edit_nombre').val($(this).data('nombre'));
        $('#edit_color').val($(this).data('color'));
        $('#edit_obs').prop('checked', $(this).data('obs') == 1);
        
        // AJUSTE AQUÍ: Añadimos /configuracion/ antes de /tipo-tickeos/
        $('#editForm').attr('action', '/configuracion/tipo-tickeos/' + id);
        
        $('#modalEdit').modal('show');
    });
</script>
@stop