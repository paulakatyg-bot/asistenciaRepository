<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Asistencia - {{ $empleado->nombres }} {{ $empleado->apellidos }}</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 9px; color: #333; line-height: 1.4; }
        .header { border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 20px; }
        .header table { width: 100%; border: none; }
        .header h1 { margin: 0; color: #2c3e50; font-size: 18px; text-transform: uppercase; }
        .info-emp { font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #34495e; color: white; font-weight: bold; text-transform: uppercase; font-size: 8px; border: 1px solid #2c3e50; padding: 6px; }
        td { border: 1px solid #dee2e6; padding: 5px; text-align: center; }
        .bg-weekend { background-color: #f8f9fa; }
        .bg-feriado { background-color: #fff1f0; }
        .bg-falta { background-color: #fdf2f2; }
        .text-danger { color: #e74c3c; font-weight: bold; }
        .text-muted { color: #95a5a6; font-style: italic; }
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 7px; color: white; font-weight: bold; }
        .badge-tarde { background-color: #f39c12; }
        .badge-falta { background-color: #e74c3c; }
        .badge-normal { background-color: #27ae60; }
        .badge-feriado { background-color: #9b59b6; }
        .badge-libre { background-color: #95a5a6; }
        .resumen-container { width: 100%; margin-top: 20px; }
        .resumen-table { width: 250px; float: right; border: 1px solid #2c3e50; }
        .resumen-table td { text-align: left; padding: 6px; border: 1px solid #eee; }
        .resumen-header { background-color: #2c3e50; color: white; font-weight: bold; text-align: center !important; }
        .footer-firmas { margin-top: 60px; width: 100%; }
        .firma-box { width: 40%; border-top: 1px solid #000; text-align: center; padding-top: 5px; display: inline-block; }
        .spacer { width: 15%; display: inline-block; }
        .page-footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 7px; color: #95a5a6; }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td style="text-align: left; border: none; width: 60%;">
                    <h1>Reporte de Asistencia</h1>
                    <p class="info-emp">
                        <strong>Empleado:</strong> {{ $empleado->nombres }} {{ $empleado->apellidos }} <br>
                        <strong>ID:</strong> {{ $empleado->id }} | <strong>Cargo:</strong> {{ $empleado->cargo->nombre ?? 'N/A' }}
                    </p>
                </td>
                <td style="text-align: right; border: none; width: 40%;">
                    <p><strong>Periodo:</strong> {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}</p>
                    <!--<p><strong>Fecha Emisión:</strong> {{ now()->format('d/m/Y H:i') }}</p>-->
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="14%">Fecha / Día</th>
                <th>Entrada 1</th>
                <th>Salida 1</th>
                <th>Entrada 2</th>
                <th>Salida 2</th>
                <th width="8%">Atraso</th>
                <th width="12%">Estado</th>
                <th width="18%">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAtraso = 0;
                $diasTrabajados = 0;
                $totalFaltas = 0;
                $totalFeriados = 0;
                $diasLibres = 0;
                $hoy = now()->startOfDay();
            @endphp

            @foreach($periodo as $fecha)
                @php
                    $fStr = $fecha->format('Y-m-d');
                    $a = $asistenciasReales[$fStr] ?? null;
                    if(is_iterable($a)) $a = $a->first(); 

                    $evento = $eventos[$fStr] ?? null;
                    $esFeriado = ($evento && $evento->tipo_dia === 'FERIADO');
                    
                    $asignacion = $empleado->asignacionesHorarios
                        ->where('fecha_inicio', '<=', $fStr)
                        ->filter(function($q) use ($fStr) {
                            return is_null($q->fecha_fin) || $q->fecha_fin->format('Y-m-d') >= $fStr;
                        })->first();
                    
                    $tieneTurno = false;
                    if($asignacion && $asignacion->horario) {
                        $tieneTurno = $asignacion->horario->turnos->where('dia_semana', $fecha->dayOfWeekIso)->isNotEmpty();
                    }

                    $esFalta = (!$a && $tieneTurno && !$esFeriado && $fecha->lte($hoy));

                    // --- LÓGICA DE CONTEO ---
                    if ($a) { 
                        if ($a->estado_dia === 'INASISTENCIA') {
                            $totalFaltas++;
                        } else {
                            $diasTrabajados++; 
                            $totalAtraso += $a->minutos_tarde; 
                        }
                    } elseif ($esFeriado) { 
                        $totalFeriados++; 
                    } elseif ($esFalta) { 
                        $totalFaltas++; 
                    } else { 
                        $diasLibres++; 
                    }

                    // Clase de la fila
                    $rowClass = '';
                    if($esFeriado) $rowClass = 'bg-feriado';
                    elseif($esFalta || ($a && $a->estado_dia === 'INASISTENCIA')) $rowClass = 'bg-falta';
                    elseif($fecha->isWeekend()) $rowClass = 'bg-weekend';
                @endphp

                <tr class="{{ $rowClass }}">
                    <td style="text-align: left;">
                        <strong>{{ $fecha->translatedFormat('l') }}</strong><br>
                        {{ $fecha->format('d/m/Y') }}
                    </td>

                    @if($a)
                        {{-- Se muestran las marcaciones SIEMPRE que exista el registro, incluso si es INASISTENCIA --}}
                        <td>{{ $a->tipo_e1_id ? ($a->tipoEntrada1->nombre ?? 'MANUAL') : ($a->entrada_1_real ?: '-') }}</td>
                        <td>{{ $a->tipo_s1_id ? ($a->tipoSalida1->nombre ?? 'MANUAL') : ($a->salida_1_real ?: '-') }}</td>
                        <td>{{ $a->tipo_e2_id ? ($a->tipoEntrada2->nombre ?? 'MANUAL') : ($a->entrada_2_real ?: '-') }}</td>
                        <td>{{ $a->tipo_s2_id ? ($a->tipoSalida2->nombre ?? 'MANUAL') : ($a->salida_2_real ?: '-') }}</td>
                        
                        <td class="{{ $a->minutos_tarde > 0 ? 'text-danger' : '' }}">
                            {{ $a->minutos_tarde > 0 ? $a->minutos_tarde . ' min' : '0' }}
                        </td>
                        
                        <td>
                            @if($a->estado_dia === 'INASISTENCIA')
                                <span class="badge badge-falta">FALTA</span>
                            @else
                                <span class="badge {{ $a->estado_dia == 'TARDE' ? 'badge-tarde' : 'badge-normal' }}">
                                    {{ $a->estado_dia }}
                                </span>
                            @endif
                        </td>
                        <td style="font-size: 7px; text-align: left;">{{ $a->observaciones ?? '-' }}</td>

                    @elseif($esFeriado)
                        <td colspan="4" class="text-danger" style="font-weight: bold;">FERIADO: {{ $evento->descripcion }}</td>
                        <td>0</td>
                        <td><span class="badge badge-feriado">FERIADO</span></td>
                        <td>-</td>

                    @elseif($esFalta)
                        <td colspan="4" class="text-danger">SIN REGISTRO DE MARCACIÓN</td>
                        <td class="text-danger">--</td>
                        <td><span class="badge badge-falta">FALTA</span></td>
                        <td style="font-size: 7px;">Inasistencia en día laboral</td>

                    @else
                        <td colspan="4" class="text-muted">Día no laboral / Libre</td>
                        <td class="text-muted">--</td>
                        <td><span class="badge badge-libre">LIBRE</span></td>
                        <td>-</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="resumen-container">
        <table class="resumen-table">
            <tr><td colspan="2" class="resumen-header">RESUMEN DEL PERIODO</td></tr>
            <tr>
                <td>Días Trabajados:</td>
                <td style="text-align: right; font-weight: bold;">{{ $diasTrabajados }}</td>
            </tr>
            <tr>
                <td>Faltas / Inasistencias:</td>
                <td style="text-align: right; font-weight: bold; color: #e74c3c;">{{ $totalFaltas }}</td>
            </tr>
            <tr>
                <td>Feriados:</td>
                <td style="text-align: right; font-weight: bold;">{{ $totalFeriados }}</td>
            </tr>
            <tr>
                <td>Días Libres:</td>
                <td style="text-align: right; font-weight: bold;">{{ $diasLibres }}</td>
            </tr>
            <tr style="background-color: #f2f2f2;">
                <td><strong>Total Min. Atraso:</strong></td>
                <td style="text-align: right; font-weight: bold; color: #e74c3c;">{{ $totalAtraso }} min.</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>
    
    <!--<div class="footer-firmas">
        <div class="firma-box">Firma del Empleado<br>C.I.: __________________</div>-->
        <div class="spacer"></div>
        <div class="firma-box">Responsable de RRHH<br>Sello y Firma</div>
    </div>
</body>
</html>