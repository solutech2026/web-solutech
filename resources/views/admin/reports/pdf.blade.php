<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte PROXICARD</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 2rem; }
        .header { text-align: center; margin-bottom: 2rem; border-bottom: 2px solid #667eea; padding-bottom: 1rem; }
        .title { font-size: 1.5rem; font-weight: bold; color: #1e2a3a; }
        .subtitle { color: #6c7a8a; font-size: 0.8rem; margin-top: 0.5rem; }
        .stats { display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; }
        .stat-box { background: #f8fafc; padding: 1rem; border-radius: 8px; text-align: center; flex: 1; border: 1px solid #e2e8f0; }
        .stat-number { font-size: 1.5rem; font-weight: bold; color: #667eea; }
        .stat-label { font-size: 0.7rem; color: #6c7a8a; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e2e8f0; font-size: 0.75rem; }
        th { background: #f1f5f9; font-weight: 600; }
        .footer { margin-top: 2rem; text-align: center; font-size: 0.7rem; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 1rem; }
        .print-btn { text-align: center; margin-top: 1rem; }
        button { padding: 0.5rem 1rem; background: #667eea; color: white; border: none; border-radius: 8px; cursor: pointer; }
        @media print { .no-print { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">PROXICARD - Reporte de Accesos</div>
        <div class="subtitle">Generado el {{ now()->format('d/m/Y H:i:s') }}</div>
        <div class="subtitle">Período: {{ $periodLabel ?? 'Todos' }}</div>
    </div>
    
    <div class="stats">
        <div class="stat-box"><div class="stat-number">{{ $stats['total'] ?? 0 }}</div><div class="stat-label">Total Accesos</div></div>
        <div class="stat-box"><div class="stat-number">{{ $stats['granted'] ?? 0 }}</div><div class="stat-label">Permitidos</div></div>
        <div class="stat-box"><div class="stat-number">{{ $stats['denied'] ?? 0 }}</div><div class="stat-label">Denegados</div></div>
        <div class="stat-box"><div class="stat-number">{{ $stats['success_rate'] ?? 0 }}%</div><div class="stat-label">Tasa de Éxito</div></div>
    </div>
    
    <table>
        <thead><tr><th>Fecha/Hora</th><th>Persona</th><th>Empresa/Colegio</th><th>Estado</th></tr></thead>
        <tbody>
            @forelse($logs ?? [] as $log)
            <tr>
                <td>{{ $log->access_time->format('d/m/Y H:i:s') }}</td>
                <td>{{ $log->person->full_name ?? 'N/A' }}</td>
                <td>{{ $log->person->company->name ?? 'N/A' }}</td>
                <td>{{ $log->status === 'granted' ? 'Permitido' : 'Denegado' }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align: center">No hay datos</td></tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        <p>PROXICARD - Sistema de Control de Acceso</p>
        <p>Reporte generado automáticamente</p>
    </div>
    
    <div class="print-btn no-print">
        <button onclick="window.print()">🖨️ Imprimir / Guardar PDF</button>
    </div>
</body>
</html>