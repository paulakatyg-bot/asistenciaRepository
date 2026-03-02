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

{{-- RESUMEN DE ATRASOS --}}
@if(request('empleado_id') && $asistenciasReales->count() > 0)
    @php
        $totalAtraso = $asistenciasReales->flatten()->sum('minutos_tarde');
    @endphp
    <div class="alert alert-info shadow-sm">
        <i class="fas fa-info-circle mr-2"></i> Reporte para el empleado seleccionado. 
        <strong>Total atrasos en el periodo: {{ $totalAtraso }} min.</strong>
    </div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
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
                
                <div class="ml-auto text-muted text-sm d-none d-md-block">
                    <span class="mr-3"><i class="fas fa-stop text-danger-soft border"></i> Feriado</span>
                    <span class="mr-3"><i class="fas fa-stop text-warning-soft border"></i> Especial</span>
                    <i class="fas fa-info-circle mr-1"></i> Registros manuales requieren observación.
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
                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde', $fecha_desde->format('Y-m-d')) }}" class="form-control form-control-sm border-info">
                </div>
                <div class="col-md-3 form-group">
                    <label class="text-xs text-uppercase font-weight-bold">Hasta</label>
                    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta', $fecha_hasta->format('Y-m-d')) }}" class="form-control form-control-sm border-info">
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
                <div class="col-md-2 d-flex align-items-end form-group" style="gap: 5px;">
                    <button type="submit" class="btn btn-info btn-sm shadow-sm font-weight-bold flex-grow-1">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="{{ route('asistencias.pdf', request()->all()) }}" class="btn btn-danger btn-sm shadow-sm font-weight-bold flex-grow-1">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TABLA DE RESULTADOS --}}
<div class="card card-primary card-outline shadow-lg">
    <div class="card-header border-0">
        <h3 class="card-title">
            <i class="fas fa-calendar-alt mr-2"></i>
            Asistencia: <strong>{{ $empleado->nombres ?? 'Seleccione Empleado' }} {{ $empleado->apellidos ?? '' }}</strong>
        </h3>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover m-0 text-center">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th rowspan="2" class="align-middle border-right" style="width: 120px;">Día</th>
                        <th rowspan="2" class="align-middle border-right">Fecha</th>
                        <th rowspan="2" class="align-middle border-right">Empleado</th>
                        <th colspan="2" class="bg-primary-light py-1 border-right text-primary">Turno 1 Real</th>
                        <th colspan="2" class="bg-primary-light py-1 border-right text-primary">Turno 2 Real</th>
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
                    @if($empleado)
                        @foreach($periodo as $fecha)
                            @php
                                $fechaStr = $fecha->format('Y-m-d');
                                $asistencia = $asistenciasReales[$fechaStr][0] ?? null;
                                $evento = $eventosCalendario[$fechaStr] ?? null;
                                
                                $esFinSemana = $fecha->isWeekend();
                                $esFeriado = ($evento && $evento->tipo_dia === 'FERIADO');
                                $esEspecial = ($evento && $evento->tipo_dia === 'ESPECIAL');

                                $asignacion = $empleado->asignacionesHorarios
                                    ->where('fecha_inicio', '<=', $fechaStr)
                                    ->filter(function($q) use ($fechaStr) {
                                        return is_null($q->fecha_fin) || $q->fecha_fin->format('Y-m-d') >= $fechaStr;
                                    })->first();

                                $turnosProgramados = collect();
                                if ($asignacion && $asignacion->horario) {
                                    $turnosProgramados = $asignacion->horario->turnos->where('dia_semana', $fecha->dayOfWeekIso);
                                }
                                
                                $tieneTurnoEseDia = $turnosProgramados->count() > 0;
                                $esFalta = (!$asistencia && $tieneTurnoEseDia && $fecha->isPast() && !$esFeriado);
                                
                                $rowClass = '';
                                if ($esFeriado) $rowClass = 'bg-feriado';
                                elseif ($esEspecial) $rowClass = 'bg-especial';
                                elseif ($esFalta) $rowClass = 'bg-light-danger';
                                elseif ($esFinSemana) $rowClass = 'bg-weekend';
                            @endphp

                            <tr class="{{ $rowClass }}">
                                <td class="align-middle border-right text-uppercase font-weight-bold {{ $esFinSemana ? 'text-primary' : 'text-muted' }}" style="font-size: 0.7rem;">
                                    {{ $fecha->translatedFormat('l') }}
                                </td>

                                <td class="align-middle border-right">
                                    <span class="badge {{ $esFinSemana ? 'badge-primary' : 'badge-light border' }}">
                                        {{ $fecha->format('d/m/Y') }}
                                    </span>
                                </td>

                                <td class="text-left align-middle pl-3 border-right">
                                    <span class="d-block font-weight-bold text-truncate" style="max-width: 150px;">
                                        {{ $empleado->nombres }} {{ $empleado->apellidos }}
                                    </span>
                                </td>

                                @if($asistencia && $asistencia->estado_dia !== 'FERIADO')
                                    {{-- ENTRADA 1 --}}
                                    <td class="align-middle font-weight-bold">
                                        @if($asistencia->tipo_e1_id)
                                            <span class="badge badge-info" title="{{ $asistencia->tipoEntrada1->nombre ?? 'Manual' }}">
                                                <i class="fas fa-user-check mr-1"></i>{{ $asistencia->tipoEntrada1->nombre ?? 'MAN' }}
                                            </span>
                                        @else
                                            {!! $asistencia->entrada_1_real ?: ($asistencia->entrada_1_prog ? '<span class="text-danger">S.T.</span>' : '-') !!}
                                        @endif
                                    </td>
                                    {{-- SALIDA 1 --}}
                                    <td class="align-middle font-weight-bold border-right">
                                        @if($asistencia->tipo_s1_id)
                                            <span class="badge badge-info">
                                                <i class="fas fa-user-check mr-1"></i>{{ $asistencia->tipoSalida1->nombre ?? 'MAN' }}
                                            </span>
                                        @else
                                            {!! $asistencia->salida_1_real ?: ($asistencia->salida_1_prog ? '<span class="text-danger">S.T.</span>' : '-') !!}
                                        @endif
                                    </td>
                                    {{-- ENTRADA 2 --}}
                                    <td class="align-middle font-weight-bold">
                                        @if($asistencia->tipo_e2_id)
                                            <span class="badge badge-info">
                                                <i class="fas fa-user-check mr-1"></i>{{ $asistencia->tipoEntrada2->nombre ?? 'MAN' }}
                                            </span>
                                        @else
                                            {!! $asistencia->entrada_2_real ?: ($asistencia->entrada_2_prog ? '<span class="text-danger">S.T.</span>' : '-') !!}
                                        @endif
                                    </td>
                                    {{-- SALIDA 2 --}}
                                    <td class="align-middle font-weight-bold border-right">
                                        @if($asistencia->tipo_s2_id)
                                            <span class="badge badge-info">
                                                <i class="fas fa-user-check mr-1"></i>{{ $asistencia->tipoSalida2->nombre ?? 'MAN' }}
                                            </span>
                                        @else
                                            {!! $asistencia->salida_2_real ?: ($asistencia->salida_2_prog ? '<span class="text-danger">S.T.</span>' : '-') !!}
                                        @endif
                                    </td>
                                    <td class="align-middle border-right">
                                        @if($asistencia->minutos_tarde > 0)
                                            <span class="badge badge-danger-soft text-danger">{{ $asistencia->minutos_tarde }} min</span>
                                        @else
                                            <span class="text-success"><i class="fas fa-check-circle"></i></span>
                                        @endif
                                    </td>
                                    <td class="align-middle border-right">
                                        <span class="badge {{ $asistencia->estado_dia == 'TARDE' ? 'badge-warning' : ($asistencia->estado_dia == 'INASISTENCIA' ? 'badge-danger' : 'badge-success') }} shadow-sm px-2" style="font-size: 0.65rem;">
                                            {{ $asistencia->estado_dia }} / {{ $asistencia->tipo_registro }}
                                        </span>
                                    </td>
                                @elseif($esFeriado)
                                    <td colspan="4" class="align-middle text-danger font-weight-bold">
                                        <i class="fas fa-flag mr-1"></i> {{ $evento->descripcion }}
                                    </td>
                                    <td class="align-middle border-right">0</td>
                                    <td class="align-middle border-right">
                                        <span class="badge badge-danger px-2">FERIADO</span>
                                    </td>
                                @else
                                    @if($tieneTurnoEseDia)
                                        <td class="align-middle font-weight-bold"><span class="text-danger">SIN TICKEO</span></td>
                                        <td class="align-middle font-weight-bold border-right"><span class="text-danger">SIN TICKEO</span></td>
                                        <td class="align-middle font-weight-bold"><span class="text-danger">SIN TICKEO</span></td>
                                        <td class="align-middle font-weight-bold border-right"><span class="text-danger">SIN TICKEO</span></td>
                                        <td class="align-middle border-right">--</td>
                                        <td class="align-middle border-right"><span class="badge badge-danger">FALTA</span></td>
                                    @else
                                        <td colspan="4" class="align-middle text-muted small italic">
                                            {{ $esEspecial ? 'Especial: '.$evento->descripcion : 'Día no laboral / Libre' }}
                                        </td>
                                        <td class="align-middle border-right">--</td>
                                        <td class="align-middle border-right">
                                            <span class="badge {{ $esEspecial ? 'badge-warning' : 'badge-secondary' }}">
                                                {{ $esEspecial ? 'ESPECIAL' : 'LIBRE' }}
                                            </span>
                                        </td>
                                    @endif
                                @endif

                                <td class="align-middle">
                                    @if($tieneTurnoEseDia && !$esFeriado)
                                        @if($asistencia)
                                            <button type="button" class="btn btn-outline-primary btn-xs rounded-circle btn-edit" 
                                                data-id="{{ $asistencia->id }}"
                                                data-empleado="{{ $empleado->nombres }} {{ $empleado->apellidos }}"
                                                data-fecha="{{ $fecha->format('d/m/Y') }}"
                                                data-e1="{{ $asistencia->entrada_1_real }}"
                                                data-s1="{{ $asistencia->salida_1_real }}"
                                                data-e2="{{ $asistencia->entrada_2_real }}"
                                                data-s2="{{ $asistencia->salida_2_real }}"
                                                data-te1="{{ $asistencia->tipo_e1_id }}"
                                                data-ts1="{{ $asistencia->tipo_s1_id }}"
                                                data-te2="{{ $asistencia->tipo_e2_id }}"
                                                data-ts2="{{ $asistencia->tipo_s2_id }}"
                                                data-obs="{{ $asistencia->observaciones }}">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-outline-secondary btn-xs rounded-circle btn-edit"
                                                data-id="new"
                                                data-empleado="{{ $empleado->nombres }} {{ $empleado->apellidos }}"
                                                data-fecha="{{ $fecha->format('d/m/Y') }}"
                                                data-e1="" data-s1="" data-e2="" data-s2="" data-obs="">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        @endif
                                    @else
                                        <i class="fas fa-lock text-muted small" title="Día no editable"></i>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10" class="py-5 text-muted">
                                <i class="fas fa-user-clock fa-3x mb-3 d-block"></i>
                                Seleccione un empleado y un rango de fechas para ver el reporte
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL: REGULARIZACIÓN MANUAL CON TIPOS --}}
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form id="formManual" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Regularización de Asistencia</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="bg-light p-3 rounded mb-3 border">
                        <small class="text-muted d-block uppercase font-weight-bold">Empleado y Fecha:</small>
                        <span id="edit-info" class="font-weight-bold text-dark h6"></span>
                    </div>
                    
                    <div class="row">
                        {{-- TURNO 1 --}}
                        <div class="col-md-6 border-right">
                            <h6 class="font-weight-bold text-primary border-bottom pb-1 mb-3">Primer Turno</h6>
                            <div class="form-group mb-3">
                                <label class="text-xs font-weight-bold">Entrada 1</label>
                                <div class="input-group input-group-sm">
                                    <input type="time" name="entrada_1_real" id="in_e1" class="form-control" step="1">
                                    <select name="tipo_e1_id" id="sel_te1" class="form-control">
                                        <option value="">-- Tipo --</option>
                                        @foreach($tiposTickeo as $tipo)
                                            <option value="{{ $tipo->id }}"> {{ $tipo->nombre }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="text-xs font-weight-bold">Salida 1</label>
                                <div class="input-group input-group-sm">
                                    <input type="time" name="salida_1_real" id="in_s1" class="form-control" step="1">
                                    <select name="tipo_s1_id" id="sel_ts1" class="form-control">
                                        <option value="">-- Tipo --</option>
                                        @foreach($tiposTickeo as $tipo)
                                            <option value="{{ $tipo->id }}"> {{ $tipo->nombre }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- TURNO 2 --}}
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-primary border-bottom pb-1 mb-3">Segundo Turno</h6>
                            <div class="form-group mb-3">
                                <label class="text-xs font-weight-bold">Entrada 2</label>
                                <div class="input-group input-group-sm">
                                    <input type="time" name="entrada_2_real" id="in_e2" class="form-control" step="1">
                                    <select name="tipo_e2_id" id="sel_te2" class="form-control">
                                        <option value="">-- Tipo --</option>
                                        @foreach($tiposTickeo as $tipo)
                                            <option value="{{ $tipo->id }}"> {{ $tipo->nombre }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="text-xs font-weight-bold">Salida 2</label>
                                <div class="input-group input-group-sm">
                                    <input type="time" name="salida_2_real" id="in_s2" class="form-control" step="1">
                                    <select name="tipo_s2_id" id="sel_ts2" class="form-control">
                                        <option value="">-- Tipo --</option>
                                        @foreach($tiposTickeo as $tipo)
                                            <option value="{{ $tipo->id }}"> {{ $tipo->nombre }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <label class="text-sm font-weight-bold text-orange">Observación / Justificación Obligatoria</label>
                            <textarea name="observaciones" id="in_obs" class="form-control border-warning" rows="2" required placeholder="Ej: Olvido de tickeo, Comisión de servicio..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm font-weight-bold">Guardar Cambios</button>
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
                        <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Atención:</strong> Se borrarán los cálculos actuales y se generarán de nuevo.
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="text-xs font-weight-bold">Desde:</label>
                            <input type="date" name="fecha_desde" value="{{ $fecha_desde->format('Y-m-d') }}" class="form-control form-control-sm border-warning" required>
                        </div>
                        <div class="col-6">
                            <label class="text-xs font-weight-bold">Hasta:</label>
                            <input type="date" name="fecha_hasta" value="{{ $fecha_hasta->format('Y-m-d') }}" class="form-control form-control-sm border-warning" required>
                        </div>
                        <input type="hidden" name="empleado_id" value="{{ request('empleado_id') }}">
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
    .bg-feriado { background-color: #fff1f0 !important; }
    .bg-especial { background-color: #fffbe6 !important; }
    .bg-light-danger { background-color: #fcf1f1 !important; }
    .bg-weekend { background-color: #f0f7ff !important; }
    .badge-danger-soft { background-color: #fff1f0; color: #e74c3c; border: 1px solid #ffccc7; }
    .alert-warning-soft { background-color: #fffbe6; border: 1px solid #ffe58f; color: #856404; }
    .table thead th { font-size: 0.7rem; text-transform: uppercase; background: #f8f9fa; }
    .table td { font-size: 0.8rem; }
    .text-orange { color: #fd7e14 !important; }
    .btn-xs { padding: .125rem .25rem; font-size: .75rem; }
    .select2-container--bootstrap4 .select2-selection { border-color: #17a2b8 !important; }
    /* Estilo para campos solo lectura cuando hay tipo seleccionado */
    .bg-read-only { background-color: #e9ecef !important; cursor: not-allowed; }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
        }

        // Lógica: Si selecciona un "Tipo" (Comisión, etc), borra y bloquea la hora
        $('select[id^="sel_t"]').on('change', function() {
            let inputTime = $(this).siblings('input[type="time"]');
            if ($(this).val() !== "") {
                inputTime.val("").prop('readonly', true).addClass('bg-read-only');
            } else {
                inputTime.prop('readonly', false).removeClass('bg-read-only');
            }
        });

        $(document).on('click', '.btn-edit', function(e) {
            e.preventDefault();
            const btn = $(this);
            const id = btn.data('id');
            
            $('#edit-info').text(btn.data('empleado') + ' | ' + btn.data('fecha'));
            
            // Cargar Horas
            $('#in_e1').val(btn.data('e1') || '');
            $('#in_s1').val(btn.data('s1') || '');
            $('#in_e2').val(btn.data('e2') || '');
            $('#in_s2').val(btn.data('s2') || '');
            
            // Cargar Tipos (Usando los IDs de los select)
            $('#sel_te1').val(btn.data('te1') || "").trigger('change');
            $('#sel_ts1').val(btn.data('ts1') || "").trigger('change');
            $('#sel_te2').val(btn.data('te2') || "").trigger('change');
            $('#sel_ts2').val(btn.data('ts2') || "").trigger('change');
            
            $('#in_obs').val(btn.data('obs') || '');

            let actionUrl = (id === 'new') ? "{{ route('asistencias.procesar') }}" : "{{ url('asistencias') }}/" + id + "/manual";
            $('#formManual').attr('action', actionUrl);
            $('#formManual').find('input[name="_method"]').val(id === 'new' ? 'POST' : 'PUT');
            
            $('#modalEditar').modal('show');
        });
    });
</script>
@stop