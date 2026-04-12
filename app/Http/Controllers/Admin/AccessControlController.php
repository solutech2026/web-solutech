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
     * Display a listing of the resource.
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::where('is_active', true)->get();
        $availableCards = NfcCard::whereNull('assigned_to')->where('status', 'active')->get();
        
        return view('admin.access-control.create', compact('companies', 'availableCards'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|in:employee,school',
            'subcategory' => 'nullable|in:student,teacher,administrative',
            'name' => 'required|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:persons,email',
            'document_id' => 'nullable|string|unique:persons,document_id',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
            'company_id' => 'required|exists:companies,id',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'grade_level' => 'nullable|string',
            'academic_year' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_phone' => 'nullable|string',
            'allergies' => 'nullable|string',
            'medical_conditions' => 'nullable|string',
            'teacher_type' => 'nullable|in:regular,substitute,special_education,part_time',
            'card_id' => 'nullable|exists:nfc_cards,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $person = Person::create([
                'category' => $request->category,
                'subcategory' => $request->subcategory,
                'name' => $request->name,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'document_id' => $request->document_id,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'company_id' => $request->company_id,
                'position' => $request->position,
                'department' => $request->department,
                'bio' => $request->bio,
                'grade_level' => $request->grade_level,
                'academic_year' => $request->academic_year,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_phone' => $request->emergency_phone,
                'allergies' => $request->allergies,
                'medical_conditions' => $request->medical_conditions,
                'teacher_type' => $request->teacher_type,
                'is_active' => true
            ]);

            if ($request->card_id) {
                $card = NfcCard::find($request->card_id);
                if ($card && !$card->assigned_to) {
                    $card->assigned_to = $person->id;
                    $card->assigned_at = now();
                    $card->save();
                    $person->nfc_card_id = $card->id;
                    $person->save();
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.access-control.index')
                ->with('success', 'Persona registrada correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al registrar: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $person = Person::with(['company', 'accessLogs'])->findOrFail($id);
        return view('admin.access-control.show', compact('person'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $person = Person::findOrFail($id);
        $companies = Company::where('is_active', true)->get();
        $availableCards = NfcCard::whereNull('assigned_to')->where('status', 'active')->get();
        
        return view('admin.access-control.edit', compact('person', 'companies', 'availableCards'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $person = Person::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'category' => 'required|in:employee,school',
            'subcategory' => 'nullable|in:student,teacher,administrative',
            'name' => 'required|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:persons,email,' . $id,
            'document_id' => 'nullable|string|unique:persons,document_id,' . $id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
            'company_id' => 'required|exists:companies,id',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'grade_level' => 'nullable|string',
            'academic_year' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_phone' => 'nullable|string',
            'allergies' => 'nullable|string',
            'medical_conditions' => 'nullable|string',
            'teacher_type' => 'nullable|in:regular,substitute,special_education,part_time',
            'card_id' => 'nullable|exists:nfc_cards,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $person->update([
                'category' => $request->category,
                'subcategory' => $request->subcategory,
                'name' => $request->name,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'document_id' => $request->document_id,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'company_id' => $request->company_id,
                'position' => $request->position,
                'department' => $request->department,
                'bio' => $request->bio,
                'grade_level' => $request->grade_level,
                'academic_year' => $request->academic_year,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_phone' => $request->emergency_phone,
                'allergies' => $request->allergies,
                'medical_conditions' => $request->medical_conditions,
                'teacher_type' => $request->teacher_type,
            ]);

            if ($request->card_id && !$person->nfc_card_id) {
                $card = NfcCard::find($request->card_id);
                if ($card && !$card->assigned_to) {
                    if ($person->nfc_card_id) {
                        $oldCard = NfcCard::find($person->nfc_card_id);
                        if ($oldCard) {
                            $oldCard->assigned_to = null;
                            $oldCard->assigned_at = null;
                            $oldCard->save();
                        }
                    }
                    $card->assigned_to = $person->id;
                    $card->assigned_at = now();
                    $card->save();
                    $person->nfc_card_id = $card->id;
                    $person->save();
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.access-control.index')
                ->with('success', 'Persona actualizada correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $person = Person::findOrFail($id);
            
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
            
            return redirect()
                ->route('admin.access-control.index')
                ->with('success', 'Persona eliminada correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    /**
     * Assign NFC card to a person.
     */
    public function assignNFC(Request $request, $id)
    {
        $request->validate([
            'card_id' => 'required|exists:nfc_cards,id'
        ]);

        try {
            DB::beginTransaction();

            $person = Person::findOrFail($id);
            $card = NfcCard::findOrFail($request->card_id);

            if ($card->assigned_to) {
                return redirect()->back()
                    ->with('error', 'Esta tarjeta ya está asignada a otra persona');
            }

            if ($person->nfc_card_id) {
                $oldCard = NfcCard::find($person->nfc_card_id);
                if ($oldCard) {
                    $oldCard->assigned_to = null;
                    $oldCard->assigned_at = null;
                    $oldCard->save();
                }
            }

            $card->assigned_to = $person->id;
            $card->assigned_at = now();
            $card->save();

            $person->nfc_card_id = $card->id;
            $person->save();

            DB::commit();

            return redirect()
                ->route('admin.access-control.index')
                ->with('success', "Tarjeta {$card->card_code} asignada a {$person->full_name}");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al asignar tarjeta: ' . $e->getMessage());
        }
    }

    /**
     * Unassign NFC card from a person.
     */
    public function unassignNFC($id)
    {
        try {
            DB::beginTransaction();

            $person = Person::findOrFail($id);
            
            if (!$person->nfc_card_id) {
                return redirect()->back()
                    ->with('error', 'Esta persona no tiene una tarjeta asignada');
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

            return redirect()
                ->route('admin.access-control.index')
                ->with('success', 'Tarjeta desvinculada correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al desvincular tarjeta: ' . $e->getMessage());
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

        fputcsv($handle, ['ID', 'Fecha/Hora', 'Persona', 'Ubicación', 'Método', 'Estado', 'Puerta', 'IP']);

        foreach ($logs as $log) {
            fputcsv($handle, [
                $log->id,
                $log->access_time->format('d/m/Y H:i:s'),
                $log->person ? $log->person->full_name : 'N/A',
                $log->company ? $log->company->name : 'N/A',
                strtoupper($log->verification_method),
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
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
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
                  ->orWhere('lastname', 'like', '%' . $request->search . '%');
            });
        }

        $logs = $query->orderBy('access_time', 'desc')->limit(100)->get();

        return response()->json($logs);
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

        return response()->json([
            'total_persons' => $totalPersons,
            'total_employees' => $totalEmployees,
            'total_school' => $totalSchool,
            'total_access_today' => $totalAccessToday,
            'total_access_granted' => $totalAccessGranted,
            'total_access_denied' => $totalAccessDenied,
            'active_cards' => $activeCards,
            'assigned_cards' => $assignedCards
        ]);
    }
}