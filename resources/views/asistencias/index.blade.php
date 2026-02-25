@extends('adminlte::page')

@section('title', 'Asistencia Diaria')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0 text-dark"><i class="fas fa-calendar-check mr-2 text-primary"></i>Asistencia Diaria</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="#">RRHH</a></li>
                <li class="breadcrumb-item active">Asistencia</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')

{{-- RESUMEN DE ATRASOS (Aparece cuando se filtra por empleado) --}}
@if(request('empleado_id') && $asistencias->count() > 0)
<div class="row">
    <div class="col-md-3">
        <div class="small-box bg-danger shadow-sm">
            <div class="inner">
                <h3>{{ $asistencias->sum('minutos_tarde') }} <sup style="font-size: 20px">min</sup></h3>
                <p>Total Minutos de Atraso</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

{{-- SECCIÓN DE BOTONES DE ACCIÓN --}}
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card card-outline card-info shadow-sm mb-0">
            <div class="card-body p-2 d-flex align-items-center">
                <form action="{{ route('asistencias.procesar') }}" method="POST" class="mr-2" id="formProcesar">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm btn-loading">
                        <i class="fas fa-sync-alt mr-1"></i> Procesar Pendientes
                    </button>
                </form>

                <button type="button" class="btn btn-outline-warning btn-sm px-3 shadow-sm" data-toggle="modal" data-target="#modalReprocesar">
                    <i class="fas fa-history mr-1"></i> Reprocesar por Rango
                </button>
                
                <div class="ml-auto text-muted text-sm">
                    <i class="fas fa-info-circle mr-1"></i> Los registros manuales requieren observación.
                </div>
            </div>
        </div>
    </div>
</div>

{{-- FILTROS --}}
<div class="card card-default shadow-sm border-top-0">
    <div class="card-header bg-white border-bottom-0">
        <h3 class="card-title text-muted font-weight-bold"><i class="fas fa-search mr-1"></i> Panel de Búsqueda</h3>
    </div>
    <div class="card-body pt-0">
        <form method="GET" action="{{ route('asistencias.index') }}">
            <div class="row">
                <div class="col-md-3 form-group">
                    <label class="text-xs text-uppercase font-weight-bold">Desde</label>
                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="form-control form-control-sm border-info">
                </div>
                <div class="col-md-3 form-group">
                    <label class="text-xs text-uppercase font-weight-bold">Hasta</label>
                    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="form-control form-control-sm border-info">
                </div>
                <div class="col-md-4 form-group">
                    <label class="text-xs text-uppercase font-weight-bold">Empleado</label>
                    <select name="empleado_id" class="form-control select2">
                        <option value="">-- Todos los empleados --</option>
                        @foreach($empleados as $emp)
                            <option value="{{ $emp->id }}" {{ request('empleado_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->nombres }} {{ $emp->apellidos }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2 form-group">
                    <!-- Botón Filtrar -->
                    <button type="submit" class="btn btn-info btn-block btn-sm shadow-sm font-weight-bold">
                        <i class="fas fa-filter mr-1"></i> Filtrar
                    </button>

                    <!-- Botón Exportar PDF -->
                    <a href="{{ route('asistencias.pdf', request()->all()) }}" 
                    class="btn btn-danger btn-sm shadow-sm font-weight-bold d-flex align-items-center">
                        <i class="fas fa-file-pdf mr-1"></i> Exportar a PDF
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TABLA DE RESULTADOS --}}
<div class="card card-primary card-outline shadow-lg">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover m-0 text-center">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th rowspan="2" class="align-middle border-right" style="width: 120px;">Día</th>
                        <th rowspan="2" class="align-middle border-right">Fecha</th>
                        <th rowspan="2" class="align-middle border-right">Empleado</th>
                        <th colspan="2" class="bg-primary-light py-1 border-right">Turno 1 Real</th>
                        <th colspan="2" class="bg-primary-light py-1 border-right">Turno 2 Real</th>
                        <th rowspan="2" class="align-middle border-right">Atraso</th>
                        <th rowspan="2" class="align-middle border-right">Estado / Origen</th>
                        <th rowspan="2" class="align-middle">Acción</th>
                    </tr>
                    <tr class="text-xs text-muted">
                        <th class="py-1 border-right">Ent.</th><th class="py-1 border-right">Sal.</th>
                        <th class="py-1 border-right">Ent.</th><th class="py-1 border-right">Sal.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asistencias as $a)
                    @php
                        // Determinar si es fin de semana para el estilo
                        $esFinSemana = $a->fecha->isWeekend();
                        $nombreDia = $a->fecha->translatedFormat('l'); // Requiere Carbon locale 'es'
                    @endphp
                    <tr class="{{ $a->tipo_registro == 'MANUAL' ? 'row-manual' : '' }} {{ $esFinSemana ? 'bg-weekend' : '' }}">
                        {{-- Columna Día --}}
                        <td class="align-middle border-right text-uppercase font-weight-bold {{ $esFinSemana ? 'text-primary' : 'text-muted' }}" style="font-size: 0.7rem;">
                            {{ $nombreDia }}
                        </td>
                        
                        {{-- Columna Fecha --}}
                        <td class="align-middle border-right">
                            <span class="badge {{ $esFinSemana ? 'badge-primary' : 'badge-light border' }}">
                                {{ $a->fecha->format('d/m/Y') }}
                            </span>
                        </td>

                        {{-- Empleado --}}
                        <td class="text-left align-middle pl-3 border-right">
                            <span class="d-block font-weight-bold text-truncate" style="max-width: 150px;">
                                {{ $a->empleado->nombres }} {{ $a->empleado->apellidos }}
                            </span>
                        </td>

                        {{-- Horarios Reales (Se quedan en blanco si son null) --}}
                        <td class="align-middle font-weight-bold text-blue">{{ $a->entrada_1_real ?? '' }}</td>
                        <td class="align-middle font-weight-bold border-right text-blue">{{ $a->salida_1_real ?? '' }}</td>
                        <td class="align-middle font-weight-bold text-blue">{{ $a->entrada_2_real ?? '' }}</td>
                        <td class="align-middle font-weight-bold border-right text-blue">{{ $a->salida_2_real ?? '' }}</td>

                        {{-- Atraso --}}
                        <td class="align-middle border-right">
                            @if($a->minutos_tarde > 0)
                                <span class="badge badge-danger-soft">{{ $a->minutos_tarde }} min</span>
                            @elseif($a->entrada_1_real)
                                <span class="text-success"><i class="fas fa-check-circle"></i></span>
                            @endif
                        </td>

                        {{-- Estado --}}
                        <td class="align-middle border-right">
                            @if($a->estado_dia)
                                @php 
                                    $badgeColor = ['TARDE'=>'warning','NORMAL'=>'success','INASISTENCIA'=>'danger','FERIADO'=>'info'][$a->estado_dia] ?? 'secondary'; 
                                @endphp
                                <div class="d-flex flex-column align-items-center">
                                    <span class="badge badge-{{ $badgeColor }} shadow-sm mb-1 px-2" style="font-size: 0.65rem;">{{ $a->estado_dia }}</span>
                                    <small class="text-xs text-uppercase font-weight-bold {{ $a->tipo_registro == 'MANUAL' ? 'text-orange' : 'text-muted' }}">
                                        {{ $a->tipo_registro }}
                                    </small>
                                </div>
                            @endif
                        </td>

                        {{-- Acciones --}}
                        <td class="align-middle">
                            <button type="button" class="btn btn-outline-primary btn-xs rounded-circle btn-edit" 
                                data-id="{{ $a->id }}" data-empleado="{{ $a->empleado->nombres }} {{ $a->empleado->apellidos }}"
                                data-fecha="{{ $a->fecha->format('d/m/Y') }}" data-e1="{{ $a->entrada_1_real }}" 
                                data-s1="{{ $a->salida_1_real }}" data-e2="{{ $a->entrada_2_real }}" 
                                data-s2="{{ $a->salida_2_real }}" data-obs="{{ $a->observaciones }}">
                                <i class="fas fa-pen"></i>
                            </button>
                            
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="py-5 text-muted">No hay registros para este periodo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL: REGULARIZACIÓN MANUAL --}}
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="formManual" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Edición Manual de Asistencia</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="bg-light p-3 rounded mb-3 border">
                        <small class="text-muted d-block uppercase font-weight-bold">Empleado y Fecha:</small>
                        <span id="edit-info" class="font-weight-bold text-dark h6"></span>
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="text-sm font-weight-bold"><i class="fas fa-sign-in-alt text-success mr-1"></i> Entrada 1</label>
                            <input type="time" name="entrada_1_real" id="in_e1" class="form-control border-primary" step="1">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="text-sm font-weight-bold"><i class="fas fa-sign-out-alt text-danger mr-1"></i> Salida 1</label>
                            <input type="time" name="salida_1_real" id="in_s1" class="form-control border-primary" step="1">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="text-sm font-weight-bold"><i class="fas fa-sign-in-alt text-success mr-1"></i> Entrada 2</label>
                            <input type="time" name="entrada_2_real" id="in_e2" class="form-control border-primary" step="1">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="text-sm font-weight-bold"><i class="fas fa-sign-out-alt text-danger mr-1"></i> Salida 2</label>
                            <input type="time" name="salida_2_real" id="in_s2" class="form-control border-primary" step="1">
                        </div>
                        <div class="col-12 mt-2">
                            <label class="text-sm font-weight-bold text-orange">Observación / Justificación</label>
                            <textarea name="observaciones" id="in_obs" class="form-control border-warning" rows="3" required placeholder="Describa el motivo del cambio manual..."></textarea>
                            <small class="text-muted italic">* Este registro pasará a ser de origen MANUAL.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm font-weight-bold">Actualizar Registro</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: REPROCESAR RANGO --}}
<div class="modal fade" id="modalReprocesar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('asistencias.reprocesar') }}" method="POST">
            @csrf
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-sync-alt mr-2"></i>Recalcular Asistencia</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body text-center">
                    <div class="alert alert-warning-soft mb-3 text-left">
                        <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Atención:</strong> Esta acción borrará los registros calculados actualmente en el rango seleccionado y los volverá a generar.
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="text-xs font-weight-bold">Desde:</label>
                            <input type="date" name="fecha_desde" class="form-control form-control-sm border-warning" required>
                        </div>
                        <div class="col-6">
                            <label class="text-xs font-weight-bold">Hasta:</label>
                            <input type="date" name="fecha_hasta" class="form-control form-control-sm border-warning" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm border" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning btn-sm font-weight-bold btn-loading">Confirmar Recálculo</button>
                </div>
            </div>
        </form>
    </div>
</div>

@stop

@section('css')
<style>
    /* Colores personalizados */
    .bg-gray-light { background-color: #f8f9fa; color: #6c757d; font-size: 0.75rem; }
    .bg-primary-light { background-color: #eef2ff; color: #4e73df; font-size: 0.75rem; }
    .badge-danger-soft { background-color: #fff1f0; color: #e74c3c; border: 1px solid #ffccc7; }
    .alert-warning-soft { background-color: #fffbe6; border: 1px solid #ffe58f; color: #856404; }
    
    /* Resaltar manual */
    .row-manual { background-color: #fffaf0; }
    .row-manual td:first-child { border-left: 3px solid #fd7e14; }
    .text-orange { color: #fd7e14 !important; }
    
    /* Estética de tabla */
    .table thead th { vertical-align: middle; border-bottom-width: 0 !important; font-size: 0.75rem; text-transform: uppercase; }
    .table td { font-size: 0.9rem; }
    .text-blue { color: #007bff; }
    
    /* Botón Loading */
    .btn-loading.loading { pointer-events: none; opacity: 0.8; }
    .btn-loading.loading i { animation: spin 1s infinite linear; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    /* Select2 */
    .select2-container--bootstrap4 .select2-selection { border-color: #17a2b8 !important; height: calc(1.5em + 0.5rem + 2px) !important; font-size: 0.875rem !important; }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Inicializar Tooltips y Select2
        $('[data-toggle="tooltip"]').tooltip();
        if ($.fn.select2) {
            $('.select2').select2({ theme: 'bootstrap4' });
        }

        // Efecto loading en botones
        $('.btn-loading').on('click', function() {
            $(this).addClass('loading').html('<i class="fas fa-spinner mr-1"></i> Cargando...');
        });

        // Delegación de eventos para el botón Editar
        $(document).on('click', '.btn-edit', function(e) {
            e.preventDefault();
            const btn = $(this);
            
            // Llenar info y campos
            $('#edit-info').text(btn.data('empleado') + ' | ' + btn.data('fecha'));
            $('#in_e1').val(btn.data('e1') || '');
            $('#in_s1').val(btn.data('s1') || '');
            $('#in_e2').val(btn.data('e2') || '');
            $('#in_s2').val(btn.data('s2') || '');
            $('#in_obs').val(btn.data('obs') || '');

            let actionUrl = "{{ url('asistencias') }}/" + btn.data('id') + "/manual";
            $('#formManual').attr('action', actionUrl);

            $('#modalEditar').modal('show');
        });
    });
</script>
@stop