<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Person;
use App\Models\NfcCard;
use App\Models\AccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        $companies = Company::where('is_active', true)->get();

        $stats = [
            'total_persons' => Person::count(),
            'total_employees' => Person::where('category', 'employee')->count(),
            'total_school_personnel' => Person::where('category', 'school')->count(),
            'total_cards' => NfcCard::count(),
            'assigned_cards' => NfcCard::whereNotNull('assigned_to')->count(),
            'available_cards' => NfcCard::whereNull('assigned_to')->where('status', 'active')->count(),
            'today_access' => AccessLog::whereDate('access_time', today())->count(),
            'week_access' => AccessLog::whereBetween('access_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'month_access' => AccessLog::whereMonth('access_time', Carbon::now()->month)->count(),
        ];

        return view('admin.reports.index', compact('companies', 'stats'));
    }

    /**
     * Generate report based on filters
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:daily,weekly,monthly,yearly,custom',
            'start_date' => 'required_if:report_type,custom|date',
            'end_date' => 'required_if:report_type,custom|date|after_or_equal:start_date',
            'month' => 'required_if:report_type,monthly|integer|min:1|max:12',
            'year' => 'required_if:report_type,monthly,yearly|integer|min:2020',
            'category' => 'nullable|in:all,employee,school',
            'subcategory' => 'nullable|in:all,student,teacher,administrative',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        try {
            $data = $this->getReportData($request);

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Reporte generado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get report data based on filters
     */
    private function getReportData($request)
    {
        $query = AccessLog::with(['person.company', 'nfcCard']);

        // Aplicar filtro de fecha
        switch ($request->report_type) {
            case 'daily':
                $date = $request->start_date ?? today();
                $query->whereDate('access_time', $date);
                $periodLabel = Carbon::parse($date)->format('d/m/Y');
                break;

            case 'weekly':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $query->whereBetween('access_time', [$startDate, $endDate]);
                $periodLabel = "Semana del {$startDate->format('d/m')} al {$endDate->format('d/m/Y')}";
                break;

            case 'monthly':
                $month = $request->month ?? Carbon::now()->month;
                $year = $request->year ?? Carbon::now()->year;
                $query->whereMonth('access_time', $month)->whereYear('access_time', $year);
                $periodLabel = Carbon::create()->month($month)->locale('es')->monthName . " {$year}";
                break;

            case 'yearly':
                $year = $request->year ?? Carbon::now()->year;
                $query->whereYear('access_time', $year);
                $periodLabel = "Año {$year}";
                break;

            default:
                if ($request->start_date && $request->end_date) {
                    $query->whereBetween('access_time', [
                        Carbon::parse($request->start_date)->startOfDay(),
                        Carbon::parse($request->end_date)->endOfDay()
                    ]);
                    $periodLabel = Carbon::parse($request->start_date)->format('d/m/Y') . ' - ' . Carbon::parse($request->end_date)->format('d/m/Y');
                }
                break;
        }

        // Filtrar por categoría
        if ($request->category && $request->category !== 'all') {
            $query->whereHas('person', fn($q) => $q->where('category', $request->category));
        }

        // Filtrar por subcategoría
        if ($request->subcategory && $request->subcategory !== 'all') {
            $query->whereHas('person', fn($q) => $q->where('subcategory', $request->subcategory));
        }

        // Filtrar por empresa/colegio
        if ($request->company_id && $request->company_id !== 'all') {
            $query->whereHas('person', fn($q) => $q->where('company_id', $request->company_id));
        }

        $logs = $query->orderBy('access_time', 'desc')->get();

        // Estadísticas
        $stats = [
            'total' => $logs->count(),
            'granted' => $logs->where('status', 'granted')->count(),
            'denied' => $logs->where('status', 'denied')->count(),
            'unique_persons' => $logs->groupBy('person_id')->count(),
            'success_rate' => $logs->count() > 0 ? round(($logs->where('status', 'granted')->count() / $logs->count()) * 100, 2) : 0,
        ];

        // Horas pico
        $peakHours = $logs->groupBy(fn($log) => Carbon::parse($log->access_time)->format('H:00'))
            ->map->count()
            ->sortDesc()
            ->take(5);

        // Top usuarios
        $topUsers = $logs->groupBy('person_id')
            ->map(fn($group) => [
                'name' => $group->first()->person->full_name ?? 'N/A',
                'count' => $group->count()
            ])
            ->sortByDesc('count')
            ->take(10)
            ->values();

        // Accesos por día
        $accessByDay = $logs->groupBy(fn($log) => Carbon::parse($log->access_time)->locale('es')->isoFormat('dddd'))
            ->map->count();

        return [
            'logs' => $logs,
            'stats' => $stats,
            'peakHours' => $peakHours,
            'topUsers' => $topUsers,
            'accessByDay' => $accessByDay,
            'periodLabel' => $periodLabel ?? 'Personalizado',
            'totalRecords' => $logs->count()
        ];
    }

    /**
     * Export access logs to CSV
     */
    public function exportAccessCsv(Request $request)
    {
        $data = $this->getReportData($request);

        $filename = 'reporte_accesos_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');

        // UTF-8 BOM
        fputs($handle, "\xEF\xBB\xBF");

        // Headers
        fputcsv($handle, [
            'ID',
            'Fecha/Hora',
            'Persona',
            'Documento',
            'Empresa/Colegio',
            'Categoría',
            'Subcategoría',
            'Puerta',
            'Método',
            'Estado'
        ]);

        // Datos
        foreach ($data['logs'] as $log) {
            fputcsv($handle, [
                $log->id,
                $log->access_time->format('d/m/Y H:i:s'),
                $log->person->full_name ?? 'N/A',
                $log->person->document_id ?? 'N/A',
                $log->person->company->name ?? 'N/A',
                $log->person->category_label ?? 'N/A',
                $log->person->subcategory_label ?? 'N/A',
                $log->gate ?? 'Puerta Principal',
                strtoupper($log->verification_method ?? 'NFC'),
                $log->status === 'granted' ? 'Permitido' : 'Denegado'
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->withHeaders([
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
    }

    /**
     * Export persons to CSV
     */
    public function exportPersonsCsv(Request $request)
    {
        $query = Person::with('company');

        if ($request->category && $request->category !== 'all') {
            $query->where('category', $request->category);
        }
        if ($request->subcategory && $request->subcategory !== 'all') {
            $query->where('subcategory', $request->subcategory);
        }
        if ($request->company_id && $request->company_id !== 'all') {
            $query->where('company_id', $request->company_id);
        }

        $persons = $query->latest()->get();

        $filename = 'reporte_personas_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');

        fputs($handle, "\xEF\xBB\xBF");

        fputcsv($handle, [
            'ID',
            'Nombre',
            'Documento',
            'Email',
            'Teléfono',
            'Categoría',
            'Subcategoría',
            'Empresa/Colegio',
            'Cargo',
            'Departamento',
            'Estado',
            'Tarjeta NFC',
            'Registro'
        ]);

        foreach ($persons as $person) {
            fputcsv($handle, [
                $person->id,
                $person->full_name,
                $person->document_id ?? 'N/A',
                $person->email ?? 'N/A',
                $person->phone ?? 'N/A',
                $person->category_label,
                $person->subcategory_label ?? 'N/A',
                $person->company->name ?? 'N/A',
                $person->position ?? 'N/A',
                $person->department ?? 'N/A',
                $person->is_active ? 'Activo' : 'Inactivo',
                $person->nfc_card_id ? 'Asignada' : 'Sin asignar',
                $person->created_at->format('d/m/Y')
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->withHeaders([
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
    }

    /**
     * Export cards to CSV
     */
    public function exportCardsCsv(Request $request)
    {
        $query = NfcCard::with('person.company');

        if ($request->status && $request->status !== 'all') {
            if ($request->status === 'assigned') {
                $query->whereNotNull('assigned_to');
            } elseif ($request->status === 'available') {
                $query->whereNull('assigned_to')->where('status', 'active');
            } else {
                $query->where('status', $request->status);
            }
        }

        $cards = $query->latest()->get();

        $filename = 'reporte_tarjetas_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');

        fputs($handle, "\xEF\xBB\xBF");

        fputcsv($handle, [
            'ID',
            'Código',
            'Estado',
            'Asignada A',
            'Empresa/Colegio',
            'Fecha Asignación',
            'Creación'
        ]);

        foreach ($cards as $card) {
            fputcsv($handle, [
                $card->id,
                $card->card_code,
                $card->status === 'active' ? 'Activa' : 'Inactiva',
                $card->person->full_name ?? 'Sin asignar',
                $card->person->company->name ?? 'N/A',
                $card->assigned_at ? Carbon::parse($card->assigned_at)->format('d/m/Y') : 'N/A',
                $card->created_at->format('d/m/Y')
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->withHeaders([
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
    }

    /**
     * Get statistics for charts (AJAX)
     */
    public function getStatistics(Request $request)
    {
        $period = $request->period ?? 'month';

        switch ($period) {
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
        }

        $accessLogs = AccessLog::whereBetween('access_time', [$startDate, $endDate])
            ->select(DB::raw('DATE(access_time) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->get();

        $totalAccess = AccessLog::whereBetween('access_time', [$startDate, $endDate])->count();
        $grantedAccess = AccessLog::whereBetween('access_time', [$startDate, $endDate])
            ->where('status', 'granted')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'daily_access' => $accessLogs,
                'total_access' => $totalAccess,
                'granted_rate' => $totalAccess > 0 ? round(($grantedAccess / $totalAccess) * 100, 2) : 0,
                'period' => $period
            ]
        ]);
    }

    /**
     * Export report to PDF - CORREGIDO
     */
    public function exportPdf($type, Request $request)
    {
        try {
            // Obtener los datos del reporte según el tipo
            if ($type === 'access') {
                $data = $this->getReportData($request);
                $view = 'admin.reports.pdf-access';
            } elseif ($type === 'persons') {
                $query = Person::with('company');
                
                if ($request->category && $request->category !== 'all') {
                    $query->where('category', $request->category);
                }
                if ($request->subcategory && $request->subcategory !== 'all') {
                    $query->where('subcategory', $request->subcategory);
                }
                if ($request->company_id && $request->company_id !== 'all') {
                    $query->where('company_id', $request->company_id);
                }
                
                $data = [
                    'persons' => $query->latest()->get(),
                    'stats' => [
                        'total' => $query->count()
                    ],
                    'periodLabel' => 'Reporte de personas',
                    'totalRecords' => $query->count()
                ];
                $view = 'admin.reports.pdf-persons';
            } elseif ($type === 'cards') {
                $query = NfcCard::with('person.company');
                
                if ($request->status && $request->status !== 'all') {
                    if ($request->status === 'assigned') {
                        $query->whereNotNull('assigned_to');
                    } elseif ($request->status === 'available') {
                        $query->whereNull('assigned_to')->where('status', 'active');
                    }
                }
                
                $data = [
                    'cards' => $query->latest()->get(),
                    'stats' => [
                        'total' => $query->count(),
                        'assigned' => NfcCard::whereNotNull('assigned_to')->count(),
                        'available' => NfcCard::whereNull('assigned_to')->where('status', 'active')->count()
                    ],
                    'periodLabel' => 'Reporte de tarjetas',
                    'totalRecords' => $query->count()
                ];
                $view = 'admin.reports.pdf-cards';
            } else {
                throw new \Exception('Tipo de reporte no válido');
            }

            // Agregar información adicional
            $data['report_type'] = $type;
            $data['filters'] = $request->all();
            $data['generated_at'] = Carbon::now()->format('d/m/Y H:i:s');
            $data['company_name'] = $request->company_id ? Company::find($request->company_id)?->name : 'Todas';

            // Generar PDF
            $pdf = Pdf::loadView($view, $data);
            $pdf->setPaper('a4', 'landscape');
            
            return $pdf->download("reporte_{$type}_" . date('Y-m-d_His') . '.pdf');
            
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al generar PDF: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }
}
