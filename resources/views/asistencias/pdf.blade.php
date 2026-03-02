<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Asistencia - {{ $empleado->nombres ?? 'General' }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #2c3e50; font-size: 18px; }
        .header p { margin: 5px 0; font-size: 12px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f8f9fa; color: #2c3e50; font-weight: bold; text-transform: uppercase; font-size: 9px; border: 1px solid #dee2e6; padding: 6px; }
        td { border: 1px solid #dee2e6; padding: 5px; text-align: center; }
        
        .bg-grey { background-color: #f2f2f2; }
        .text-danger { color: #e74c3c; font-weight: bold; }
        .text-primary { color: #3498db; font-weight: bold; }
        
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 8px; font-weight: bold; color: white; }
        .badge-tarde { background-color: #f39c12; }
        .badge-falta { background-color: #e74c3c; }
        .badge-normal { background-color: #27ae60; }
        .badge-manual { background-color: #17a2b8; }

        .resumen-container { margin-top: 20px; width: 100%; }
        .resumen-box { width: 45%; border: 1px solid #ccc; padding: 10px; background-color: #fcfcfc; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 8px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE ASISTENCIA DIARIA</h1>
        <p><strong>Periodo:</strong> {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}</p>
        @if($empleado)
            <p><strong>Empleado:</strong> {{ $empleado->nombres }} {{ $empleado->apellidos }} | <strong>ID:</strong> {{ $empleado->id }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th width="12%">Fecha</th>
                <th>E1</th>
                <th>S1</th>
                <th>E2</th>
                <th>S2</th>
                <th width="8%">Atraso</th>
                <th width="15%">Estado / Origen</th>
                <th width="20%">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($asistencias as $a)
            <tr class="{{ $a->fecha->isWeekend() ? 'bg-grey' : '' }}">
                <td style="text-align: left;">
                    <strong>{{ $a->fecha->translatedFormat('l') }}</strong><br>
                    {{ $a->fecha->format('d/m/Y') }}
                </td>
                
                {{-- Entrada 1 --}}
                <td>{{ $a->tipoEntrada1 ? $a->tipoEntrada1->nombre : ($a->entrada_1_real ?: '-') }}</td>
                {{-- Salida 1 --}}
                <td>{{ $a->tipoSalida1 ? $a->tipoSalida1->nombre : ($a->salida_1_real ?: '-') }}</td>
                {{-- Entrada 2 --}}
                <td>{{ $a->tipoEntrada2 ? $a->tipoEntrada2->nombre : ($a->entrada_2_real ?: '-') }}</td>
                {{-- Salida 2 --}}
                <td>{{ $a->tipoSalida2 ? $a->tipoSalida2->nombre : ($a->salida_2_real ?: '-') }}</td>

                <td class="{{ $a->minutos_tarde > 0 ? 'text-danger' : '' }}">
                    {{ $a->minutos_tarde > 0 ? $a->minutos_tarde . ' min' : '0' }}
                </td>

                <td>
                    <span class="badge {{ $a->estado_dia == 'TARDE' ? 'badge-tarde' : ($a->estado_dia == 'INASISTENCIA' ? 'badge-falta' : 'badge-normal') }}">
                        {{ $a->estado_dia }}
                    </span>
                    <br>
                    <small style="color: #777">{{ $a->tipo_registro }}</small>
                </td>

                <td style="text-align: left; font-size: 8px;">
                    {{ $a->observaciones }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="resumen-container">
        <div class="resumen-box">
            <strong>RESUMEN DEL PERIODO:</strong><br><br>
            <table style="border: none; margin: 0;">
                <tr style="border: none;">
                    <td style="border: none; text-align: left; padding: 2px;">Días Normales:</td>
                    <td style="border: none; text-align: right; padding: 2px;">{{ $asistencias->where('estado_dia', 'NORMAL')->count() }}</td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none; text-align: left; padding: 2px;">Días con Atraso:</td>
                    <td style="border: none; text-align: right; padding: 2px;">{{ $asistencias->where('estado_dia', 'TARDE')->count() }}</td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none; text-align: left; padding: 2px;">Inasistencias:</td>
                    <td style="border: none; text-align: right; padding: 2px;">{{ $asistencias->where('estado_dia', 'INASISTENCIA')->count() }}</td>
                </tr>
                <tr style="border: none; font-weight: bold; border-top: 1px solid #eee;">
                    <td style="border: none; text-align: left; padding: 2px;">Total Minutos Atraso:</td>
                    <td style="border: none; text-align: right; padding: 2px; color: red;">{{ $asistencias->sum('minutos_tarde') }} min</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer">
        Generado el: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>