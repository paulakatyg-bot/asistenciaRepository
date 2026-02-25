<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Asistencia</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { bg-color: #f2f2f2; font-weight: bold; text-transform: uppercase; }
        .header { text-align: center; margin-bottom: 30px; }
        .atraso { color: red; font-weight: bold; }
        .resumen { margin-top: 20px; padding: 10px; border: 1px solid #000; width: 300px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE ASISTENCIA DIARIA</h1>
        <p>Periodo: {{ $desde }} al {{ $hasta }}</p>
        @if($empleado)
            <h3>Empleado: {{ $empleado->nombres }} {{ $empleado->apellidos }}</h3>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>E1</th><th>S1</th>
                <th>E2</th><th>S2</th>
                <th>Atraso</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($asistencias as $a)
            <tr>
                <td>{{ $a->fecha->format('d/m/Y') }}</td>
                <td>{{ $a->entrada_1_real ?? '-' }}</td>
                <td>{{ $a->salida_1_real ?? '-' }}</td>
                <td>{{ $a->entrada_2_real ?? '-' }}</td>
                <td>{{ $a->salida_2_real ?? '-' }}</td>
                <td class="{{ $a->minutos_tarde > 0 ? 'atraso' : '' }}">
                    {{ $a->minutos_tarde }} min
                </td>
                <td>{{ $a->estado_dia }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="resumen">
        <strong>RESUMEN TOTAL:</strong><br>
        Días Normales: {{ $asistencias->where('estado_dia', 'NORMAL')->count() }}<br>
        Días con Tarde: {{ $asistencias->where('estado_dia', 'TARDE')->count() }}<br>
        Inasistencias: {{ $asistencias->where('estado_dia', 'INASISTENCIA')->count() }}<br>
        <strong>Total Atrasos: {{ $asistencias->sum('minutos_tarde') }} minutos</strong>
    </div>
</body>
</html>