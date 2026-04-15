<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\Company;
use App\Models\NfcCard;
use App\Models\AccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AccessControlController extends Controller
{
    /**
     * Display a listing of persons and access logs.
     */
    public function index()
    {
        $persons = Person::with('company')->orderBy('created_at', 'desc')->get();
        $companies = Company::where('is_active', true)->get();
        $availableCards = NfcCard::whereNull('assigned_to')->where('status', 'active')->get();
        $accessLogs = AccessLog::with(['person', 'company'])->orderBy('access_time', 'desc')->limit(100)->get();
        
        return view('admin.access-control.index', compact('persons', 'companies', 'availableCards', 'accessLogs'));
    }

    /**
     * Assign NFC card to a person (AJAX + Redirect support).
     */
    public function assignNFC(Request $request, $id)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'card_id' => 'required|exists:nfc_cards,id'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe seleccionar una tarjeta válida'
                ], 422);
            }
            return redirect()->back()
                ->with('error', 'Debe seleccionar una tarjeta válida');
        }

        try {
            DB::beginTransaction();

            $person = Person::findOrFail($id);
            $card = NfcCard::findOrFail($request->card_id);

            // Verificar que la tarjeta no esté asignada
            if ($card->assigned_to) {
                DB::rollBack();
                $message = 'Esta tarjeta ya está asignada a otra persona';
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 400);
                }
                return redirect()->back()->with('error', $message);
            }

            // Si la persona ya tiene una tarjeta, desasignarla
            if ($person->nfc_card_id) {
                $oldCard = NfcCard::find($person->nfc_card_id);
                if ($oldCard) {
                    $oldCard->assigned_to = null;
                    $oldCard->assigned_at = null;
                    $oldCard->save();
                }
            }

            // Asignar nueva tarjeta
            $card->assigned_to = $person->id;
            $card->assigned_at = now();
            $card->save();

            $person->nfc_card_id = $card->id;
            $person->save();

            DB::commit();

            $message = "Tarjeta {$card->card_code} asignada a {$person->full_name}";

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'person_id' => $person->id,
                        'card_id' => $card->id,
                        'card_code' => $card->card_code,
                        'person_name' => $person->full_name
                    ]
                ]);
            }

            return redirect()
                ->route('admin.access-control.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            $message = 'Error al asignar tarjeta: ' . $e->getMessage();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', $message);
        }
    }

    /**
     * Unassign NFC card from a person.
     */
    public function unassignNFC(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $person = Person::findOrFail($id);
            
            if (!$person->nfc_card_id) {
                $message = 'Esta persona no tiene una tarjeta asignada';
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 400);
                }
                return redirect()->back()->with('error', $message);
            }

            $card = NfcCard::find($person->nfc_card_id);
            if ($card) {
                $card->assigned_to = null;
                $card->assigned_at = null;
                $card->save();
            }
            
            $person->nfc_card_id = null;
            $person->save();

            DB::commit();

            $message = 'Tarjeta desvinculada correctamente';

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()
                ->route('admin.access-control.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            $message = 'Error al desvincular tarjeta: ' . $e->getMessage();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', $message);
        }
    }

    /**
     * Remove the specified person from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $person = Person::findOrFail($id);
            
            // Desasignar tarjeta NFC si tiene
            if ($person->nfc_card_id) {
                $card = NfcCard::find($person->nfc_card_id);
                if ($card) {
                    $card->assigned_to = null;
                    $card->assigned_at = null;
                    $card->save();
                }
            }
            
            $person->delete();

            DB::commit();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Persona eliminada correctamente'
                ]);
            }
            
            return redirect()
                ->route('admin.access-control.index')
                ->with('success', 'Persona eliminada correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    /**
     * Export access logs to CSV.
     */
    public function exportLogs(Request $request)
    {
        $query = AccessLog::with(['person', 'company']);

        if ($request->company && $request->company != 'all') {
            $query->where('company_id', $request->company);
        }

        if ($request->search) {
            $query->whereHas('person', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('lastname', 'like', '%' . $request->search . '%')
                  ->orWhere('document_id', 'like', '%' . $request->search . '%');
            });
        }

        $logs = $query->orderBy('access_time', 'desc')->get();

        $filename = 'accesos_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');

        // Agregar BOM para UTF-8 en Excel
        fputs($handle, "\xEF\xBB\xBF");
        
        fputcsv($handle, ['ID', 'Fecha/Hora', 'Persona', 'Documento', 'Ubicación', 'Método', 'Estado', 'Puerta', 'IP']);

        foreach ($logs as $log) {
            fputcsv($handle, [
                $log->id,
                $log->access_time->format('d/m/Y H:i:s'),
                $log->person ? $log->person->full_name : 'N/A',
                $log->person ? $log->person->document_id : 'N/A',
                $log->company ? $log->company->name : 'N/A',
                strtoupper($log->verification_method ?? 'NFC'),
                $log->status == 'granted' ? 'Permitido' : 'Denegado',
                $log->gate ?? 'N/A',
                $log->ip_address ?? 'N/A'
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->withHeaders([
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate'
            ]);
    }

    /**
     * Get access logs (AJAX).
     */
    public function getAccessLogs(Request $request)
    {
        $query = AccessLog::with(['person', 'company']);

        if ($request->company && $request->company != 'all') {
            $query->where('company_id', $request->company);
        }

        if ($request->search) {
            $query->whereHas('person', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('lastname', 'like', '%' . $request->search . '%')
                  ->orWhere('document_id', 'like', '%' . $request->search . '%');
            });
        }

        $logs = $query->orderBy('access_time', 'desc')->limit(100)->get();

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Get statistics for dashboard.
     */
    public function getStats()
    {
        $totalPersons = Person::count();
        $totalEmployees = Person::where('category', 'employee')->count();
        $totalSchool = Person::where('category', 'school')->count();
        $totalAccessToday = AccessLog::whereDate('access_time', today())->count();
        $totalAccessGranted = AccessLog::where('status', 'granted')->count();
        $totalAccessDenied = AccessLog::where('status', 'denied')->count();
        $activeCards = NfcCard::where('status', 'active')->count();
        $assignedCards = NfcCard::whereNotNull('assigned_to')->count();
        $availableCards = NfcCard::whereNull('assigned_to')->where('status', 'active')->count();

        return response()->json([
            'success' => true,
            'total_persons' => $totalPersons,
            'total_employees' => $totalEmployees,
            'total_school' => $totalSchool,
            'total_access_today' => $totalAccessToday,
            'total_access_granted' => $totalAccessGranted,
            'total_access_denied' => $totalAccessDenied,
            'active_cards' => $activeCards,
            'assigned_cards' => $assignedCards,
            'available_cards' => $availableCards
        ]);
    }

    /**
     * Show person details (modal/view).
     */
    public function show($id)
    {
        $person = Person::with(['company', 'accessLogs' => function($query) {
            $query->orderBy('access_time', 'desc')->limit(20);
        }])->findOrFail($id);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $person
            ]);
        }
        
        return view('admin.access-control.show', compact('person'));
    }
}