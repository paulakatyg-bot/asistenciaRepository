@extends('adminlte::page')

@section('title', 'Gestión de Calendario')

@section('content_header')
    <h1><i class="fas fa-calendar-alt mr-2 text-primary"></i>Configuración de Calendario</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- Formulario de Registro --}}
        <div class="col-md-4">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">Nuevo Día Especial / Feriado</h3>
                </div>
                <form action="{{ route('calendario.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>Fecha</label>
                            <input type="date" name="fecha" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Tipo de Día</label>
                            <select name="tipo_dia" class="form-control" required>
                                <option value="FERIADO">FERIADO</option>
                                <option value="ESPECIAL">ESPECIAL (Festivo No Feriado)</option>
                                <option value="LABORAL">LABORAL (Excepción)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="2" maxlength="150" placeholder="Ej: Año Nuevo, Aniversario..."></textarea>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-save mr-1"></i> Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla de Listado --}}
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover table-sm m-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Día</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($eventos as $evento)
                            <tr>
                                <td class="font-weight-bold">{{ $evento->fecha->format('d/m/Y') }}</td>
                                <td class="text-capitalize text-muted small">{{ $evento->fecha->translatedFormat('l') }}</td>
                                <td>
                                    <span class="badge @if($evento->tipo_dia == 'FERIADO') badge-danger @elseif($evento->tipo_dia == 'ESPECIAL') badge-warning @else badge-success @endif">
                                        {{ $evento->tipo_dia }}
                                    </span>
                                </td>
                                <td>{{ $evento->descripcion }}</td>
                                <td class="text-right">
                                    <button class="btn btn-xs btn-info btn-edit" 
                                            data-fecha="{{ $evento->fecha->format('Y-m-d') }}"
                                            data-tipo="{{ $evento->tipo_dia }}"
                                            data-desc="{{ $evento->descripcion }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('calendario.destroy', $evento->fecha->format('Y-m-d')) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('¿Eliminar este registro?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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

{{-- MODAL PARA EDITAR --}}
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Editar Día: <span id="fecha_titulo"></span></h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tipo de Día</label>
                        <select name="tipo_dia" id="edit_tipo" class="form-control">
                            <option value="FERIADO">FERIADO</option>
                            <option value="ESPECIAL">ESPECIAL</option>
                            <option value="LABORAL">LABORAL</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <input type="text" name="descripcion" id="edit_desc" class="form-control" maxlength="150">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info btn-sm">Actualizar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).on('click', '.btn-edit', function() {
        const fecha = $(this).data('fecha');
        $('#fecha_titulo').text(fecha);
        $('#edit_tipo').val($(this).data('tipo'));
        $('#edit_desc').val($(this).data('desc'));
        
        // Ajustar la acción del formulario
        $('#editForm').attr('action', '/configuracion/calendario/' + fecha);
        $('#modalEdit').modal('show');
    });
</script>
@stop