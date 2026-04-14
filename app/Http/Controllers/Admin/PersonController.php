<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\Company;
use App\Models\NfcCard;
use App\Models\AccessLog;
use App\Models\Schedule;
use App\Models\ReportCard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $persons = Person::with('company', 'user')->latest()->get();
        $companies = Company::all();
        $availableCards = NfcCard::whereNull('assigned_to')->where('status', 'active')->get();

        return view('admin.persons.index', compact('persons', 'companies', 'availableCards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::all();
        return view('admin.persons.create_edit', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('=== INICIO DE REGISTRO DE PERSONA ===');
        Log::info('Datos recibidos:', $request->all());

        $rules = $this->getValidationRules($request);
        Log::info('Reglas de validación:', $rules);

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::error('Error de validación:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            Log::info('Iniciando transacción...');

            // Crear usuario si es necesario
            $userId = $this->createUserIfNeeded($request);
            Log::info('Usuario creado/obtenido: ' . ($userId ?? 'null'));

            // Crear persona
            $person = $this->createPerson($request, $userId);
            Log::info('Persona creada con ID: ' . ($person->id ?? 'null'));

            // Asignar tarjeta NFC si se seleccionó
            if ($request->card_id) {
                $card = NfcCard::find($request->card_id);
                if ($card && !$card->assigned_to) {
                    $card->assigned_to = $person->id;
                    $card->assigned_at = now();
                    $card->save();

                    $person->nfc_card_id = $card->id;
                    $person->save();
                    Log::info('Tarjeta NFC asignada: ' . $card->card_code);
                }
            }

            // Guardar foto si se subió
            if ($request->hasFile('photo')) {
                $this->savePhoto($request->file('photo'), $person);
                Log::info('Foto guardada');
            }

            // Guardar horarios (solo para estudiantes, docentes y administrativos)
            if ($this->shouldSaveSchedules($request)) {
                $this->saveSchedules($request, $person->id);
                Log::info('Horarios guardados');
            }

            DB::commit();
            Log::info('Transacción completada exitosamente');

            return redirect()
                ->route('admin.persons.index')
                ->with('success', 'Persona registrada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ERROR AL REGISTRAR: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Error al registrar la persona: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $person = Person::with([
            'company',
            'schedules',
            'reportCards'
        ])->findOrFail($id);

        $accessLogs = AccessLog::where('person_id', $id)
            ->with('nfcCard')
            ->orderBy('access_time', 'desc')
            ->paginate(50);

        $accessStats = [
            'total' => AccessLog::where('person_id', $id)->count(),
            'granted' => AccessLog::where('person_id', $id)->where('status', 'granted')->count(),
            'denied' => AccessLog::where('person_id', $id)->where('status', 'denied')->count(),
            'last_access' => AccessLog::where('person_id', $id)->where('status', 'granted')->latest('access_time')->first(),
            'today' => AccessLog::where('person_id', $id)->whereDate('access_time', today())->count(),
        ];

        $availableCards = NfcCard::whereNull('assigned_to')->where('status', 'active')->get();

        return view('admin.persons.show', compact('person', 'availableCards', 'accessLogs', 'accessStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $person = Person::with('schedules')->findOrFail($id);
        $companies = Company::all();

        return view('admin.persons.create_edit', compact('person', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        Log::info('=== INICIO DE ACTUALIZACIÓN ===');
        Log::info('Datos recibidos:', $request->all());
        
        $person = Person::findOrFail($id);

        $rules = $this->getValidationRules($request, $id);

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::error('Error de validación:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Actualizar persona
            $this->updatePerson($request, $person);

            // Asignar nueva tarjeta si se seleccionó y no tiene una
            if ($request->card_id && !$person->nfc_card_id) {
                $card = NfcCard::find($request->card_id);
                if ($card && !$card->assigned_to) {
                    $card->assigned_to = $person->id;
                    $card->assigned_at = now();
                    $card->save();

                    $person->nfc_card_id = $card->id;
                    $person->save();
                }
            }

            // Actualizar foto si se subió nueva
            if ($request->hasFile('photo')) {
                $this->deletePhotoFile($person);
                $this->savePhoto($request->file('photo'), $person);
            }

            // Eliminar foto si se solicitó
            if ($request->has('remove_photo') && $request->remove_photo == '1') {
                $this->deletePhotoFile($person);
            }

            // Actualizar horarios (solo si aplica)
            if ($this->shouldSaveSchedules($request)) {
                Schedule::where('person_id', $person->id)->delete();
                $this->saveSchedules($request, $person->id);
            }

            DB::commit();
            
            $person->refresh();
            Log::info('Persona actualizada correctamente');
            Log::info('Emergencia guardada - Nombre: ' . ($person->emergency_contact_name ?? 'NULL'));
            Log::info('Emergencia guardada - Teléfono: ' . ($person->emergency_phone ?? 'NULL'));

            return redirect()
                ->route('admin.persons.index')
                ->with('success', 'Persona actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ERROR AL ACTUALIZAR: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar la persona: ' . $e->getMessage())
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

            // Eliminar foto
            $this->deletePhotoFile($person);

            // Eliminar archivos de boletines
            if ($person->reportCards) {
                foreach ($person->reportCards as $reportCard) {
                    Storage::disk('public')->delete($reportCard->file_path);
                    $reportCard->delete();
                }
            }

            // Eliminar horarios
            Schedule::where('person_id', $person->id)->delete();

            // Eliminar registros de acceso de esta persona
            AccessLog::where('person_id', $person->id)->delete();

            // Liberar tarjeta NFC
            if ($person->nfc_card_id) {
                NfcCard::where('id', $person->nfc_card_id)->update([
                    'assigned_to' => null,
                    'assigned_at' => null
                ]);
            }

            // Eliminar usuario asociado
            if ($person->user_id) {
                $user = User::find($person->user_id);
                if ($user) {
                    $user->delete();
                }
            }

            $person->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Persona eliminada exitosamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la persona: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload photo for a person.
     */
    public function uploadPhoto(Request $request, $id)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $person = Person::findOrFail($id);

            $this->deletePhotoFile($person);
            $this->savePhoto($request->file('photo'), $person);

            return response()->json([
                'success' => true,
                'message' => 'Foto actualizada exitosamente',
                'photo_url' => $person->photo_url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir la foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete photo for a person.
     */
    public function deletePhoto($id)
    {
        try {
            $person = Person::findOrFail($id);
            $this->deletePhotoFile($person);

            return response()->json([
                'success' => true,
                'message' => 'Foto eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign NFC card to person.
     */
    public function assignNfc(Request $request, $id)
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
                    ->with('error', 'Esta tarjeta NFC ya está asignada a otra persona.');
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
                ->route('admin.persons.index')
                ->with('success', "Tarjeta {$card->card_code} asignada a {$person->full_name} exitosamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al asignar la tarjeta NFC: ' . $e->getMessage());
        }
    }

    /**
     * Unassign NFC card from person.
     */
    public function unassignNfc($id)
    {
        try {
            DB::beginTransaction();

            $person = Person::findOrFail($id);

            if (!$person->nfc_card_id) {
                return redirect()->back()
                    ->with('error', 'Esta persona no tiene una tarjeta NFC asignada.');
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
                ->route('admin.persons.index')
                ->with('success', 'Tarjeta NFC desvinculada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al desvincular la tarjeta NFC: ' . $e->getMessage());
        }
    }

    /**
     * Upload report card (boletín de notas) for a student.
     */
    public function uploadReportCard(Request $request, $id)
    {
        $request->validate([
            'period' => 'required|in:first,second,third',
            'academic_year' => 'required|string',
            'grade_level' => 'required|string',
            'file' => 'required|file|mimes:pdf|max:5120',
            'average' => 'nullable|numeric|min:0|max:100',
            'subjects_grades' => 'nullable|array'
        ]);

        try {
            $person = Person::findOrFail($id);

            if ($person->subcategory !== 'student') {
                return response()->json(['error' => 'Solo estudiantes pueden tener boletines'], 400);
            }

            $existingReport = ReportCard::where('person_id', $person->id)
                ->where('period', $request->period)
                ->where('academic_year', $request->academic_year)
                ->first();

            if ($existingReport) {
                Storage::disk('public')->delete($existingReport->file_path);
                $existingReport->delete();
            }

            $file = $request->file('file');
            $fileName = $person->id . '_' . $request->period . '_' . $request->academic_year . '.pdf';
            $filePath = $file->storeAs('report_cards/' . $person->id, $fileName, 'public');

            $reportCard = ReportCard::create([
                'person_id' => $person->id,
                'period' => $request->period,
                'academic_year' => $request->academic_year,
                'grade_level' => $request->grade_level,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'average' => $request->average,
                'subjects_grades' => $request->subjects_grades,
            ]);

            $this->updateStudentAverage($person->id);
            $person->update(['period' => $request->period]);

            return response()->json([
                'success' => true,
                'message' => 'Boletín subido exitosamente',
                'data' => $reportCard
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al subir el boletín: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get report cards for a student.
     */
    public function getReportCards($id)
    {
        try {
            $person = Person::findOrFail($id);

            if ($person->subcategory !== 'student') {
                return response()->json(['error' => 'No es un estudiante'], 400);
            }

            $reportCards = $person->reportCards()->orderBy('academic_year', 'desc')->get()->map(function ($rc) {
                return [
                    'id' => $rc->id,
                    'period' => $rc->period,
                    'period_label' => $this->getPeriodLabel($rc->period),
                    'academic_year' => $rc->academic_year,
                    'grade_level' => $rc->grade_level,
                    'file_url' => Storage::url($rc->file_path),
                    'file_name' => $rc->file_name,
                    'average' => $rc->average,
                    'created_at' => $rc->created_at->format('d/m/Y'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $reportCards,
                'overall_average' => $person->average_grade
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los boletines: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete report card.
     */
    public function deleteReportCard($personId, $reportCardId)
    {
        try {
            $reportCard = ReportCard::where('person_id', $personId)->findOrFail($reportCardId);

            Storage::disk('public')->delete($reportCard->file_path);
            $reportCard->delete();

            $this->updateStudentAverage($personId);

            return response()->json([
                'success' => true,
                'message' => 'Boletín eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el boletín: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export persons list to CSV.
     */
    public function export(Request $request)
    {
        $query = Person::with('company');

        if ($request->category && $request->category != 'all') {
            $query->where('category', $request->category);
        }

        if ($request->subcategory && $request->subcategory != 'all') {
            $query->where('subcategory', $request->subcategory);
        }

        if ($request->company && $request->company != 'all') {
            $query->where('company_id', $request->company);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('lastname', 'like', '%' . $request->search . '%')
                    ->orWhere('document_id', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $persons = $query->get();

        $filename = 'personas_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');

        fputcsv($handle, [
            'ID',
            'Nombre',
            'Apellido',
            'Categoría',
            'Subcategoría',
            'Empresa/Colegio',
            'Cédula',
            'Email',
            'Teléfono',
            'Género',
            'Fecha Nacimiento',
            'Cargo',
            'Departamento',
            'Biografía',
            'Grado',
            'Año Escolar',
            'Periodo',
            'Promedio',
            'Contacto Emergencia',
            'Teléfono Emergencia',
            'Alergias',
            'Condiciones Médicas',
            'Tipo Docente',
            'Fecha Registro',
            'Estado NFC',
            'URL Pública'
        ]);

        foreach ($persons as $person) {
            fputcsv($handle, [
                $person->id,
                $person->name,
                $person->lastname ?? '',
                $person->category == 'employee' ? 'Empleado' : 'Personal Escolar',
                $this->getSubcategoryLabel($person->subcategory),
                $person->company->name ?? 'N/A',
                $person->document_id ?? '',
                $person->email ?? '',
                $person->phone ?? '',
                $this->getGenderLabel($person->gender),
                $person->birth_date ?? '',
                $person->position ?? '',
                $person->department ?? '',
                $person->bio ?? '',
                $person->grade_level ?? '',
                $person->academic_year ?? '',
                $this->getPeriodLabel($person->period),
                $person->average_grade ?? '',
                $person->emergency_contact_name ?? '',
                $person->emergency_phone ?? '',
                $person->allergies ?? '',
                $person->medical_conditions ?? '',
                $this->getTeacherTypeLabel($person->teacher_type),
                $person->created_at->format('d/m/Y H:i'),
                $person->nfc_card_id ? 'Asignada' : 'Sin asignar',
                $person->bio_full_url ?? ''
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
     * Search persons (AJAX).
     */
    public function search(Request $request)
    {
        $query = Person::with('company');

        if ($request->q) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                    ->orWhere('lastname', 'like', '%' . $request->q . '%')
                    ->orWhere('document_id', 'like', '%' . $request->q . '%')
                    ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->category && $request->category != 'all') {
            $query->where('category', $request->category);
        }

        if ($request->subcategory && $request->subcategory != 'all') {
            $query->where('subcategory', $request->subcategory);
        }

        $persons = $query->limit(20)->get()->map(function ($person) {
            return [
                'id' => $person->id,
                'full_name' => $person->full_name,
                'photo_url' => $person->photo_url,
                'document_id' => $person->document_id,
                'email' => $person->email,
                'category_label' => $person->category_label,
                'subcategory_label' => $person->subcategory_label,
                'company_name' => $person->company->name ?? 'N/A',
            ];
        });

        return response()->json($persons);
    }

    /**
     * Get access logs for a person (AJAX).
     */
    public function getAccessLogs($id)
    {
        $logs = AccessLog::where('person_id', $id)
            ->latest()
            ->limit(100)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'access_time' => $log->access_time->format('d/m/Y H:i:s'),
                    'gate' => $log->gate ?? 'Puerta Principal',
                    'verification_method' => $log->verification_method,
                    'verification_method_label' => strtoupper($log->verification_method),
                    'status' => $log->status,
                    'status_label' => $log->status == 'granted' ? 'Permitido' : 'Denegado',
                ];
            });

        return response()->json($logs);
    }

    /**
     * Get schedules for a person (AJAX).
     */
    public function getSchedules($id)
    {
        $schedules = Schedule::where('person_id', $id)
            ->orderByRaw("FIELD(day, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday')")
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'day' => $schedule->day,
                    'day_label' => $this->getDayLabel($schedule->day),
                    'start_time' => date('H:i', strtotime($schedule->start_time)),
                    'end_time' => date('H:i', strtotime($schedule->end_time)),
                    'subject' => $schedule->subject,
                    'classroom' => $schedule->classroom,
                ];
            });

        return response()->json($schedules);
    }

    // ============================================
    // MÉTODOS PRIVADOS
    // ============================================

    /**
     * Update student average grade from report cards
     */
    private function updateStudentAverage($personId)
    {
        $person = Person::find($personId);
        if ($person && $person->subcategory === 'student') {
            $average = $person->reportCards()->avg('average');
            $person->update(['average_grade' => $average]);
        }
    }

    /**
     * Determine if schedules should be saved for this person type
     */
    private function shouldSaveSchedules($request)
    {
        if ($request->category === 'employee') {
            return false;
        }
        
        if ($request->category === 'school') {
            return in_array($request->subcategory, ['student', 'teacher', 'administrative']);
        }
        
        return false;
    }

    /**
     * Get validation rules based on category and subcategory
     */
    private function getValidationRules($request, $id = null)
    {
        $uniqueDocument = $id ? 'unique:persons,document_id,' . $id : 'unique:persons,document_id';
        $uniqueEmail = $id ? 'unique:persons,email,' . $id : 'unique:persons,email';

        $rules = [
            'category' => 'required|in:employee,school',
            'name' => 'required|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'document_id' => 'nullable|string|max:50|' . $uniqueDocument,
            'email' => 'nullable|email|max:255|' . $uniqueEmail,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
            'company_id' => 'required|exists:companies,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($request->category == 'employee') {
            $rules['position'] = 'nullable|string|max:255';
            $rules['department'] = 'nullable|string|max:255';
            $rules['bio'] = 'nullable|string';
            
        } elseif ($request->category == 'school') {
            $rules['subcategory'] = 'required|in:student,teacher,administrative';
            
            if ($request->subcategory == 'student') {
                $rules['grade_level'] = 'required|string';
                $rules['academic_year'] = 'required|string';
                $rules['period'] = 'nullable|in:first,second,third';
                $rules['emergency_contact_name'] = 'nullable|string';
                $rules['emergency_phone'] = 'nullable|string';
                $rules['allergies'] = 'nullable|string';
                $rules['medical_conditions'] = 'nullable|string';
                
            } elseif ($request->subcategory == 'teacher') {
                $rules['position'] = 'nullable|string|max:255';
                $rules['teacher_type'] = 'nullable|in:regular,substitute,special_education,part_time';
                $rules['bio'] = 'nullable|string';
                $rules['emergency_contact_name'] = 'nullable|string';
                $rules['emergency_phone'] = 'nullable|string';
                
            } elseif ($request->subcategory == 'administrative') {
                $rules['position'] = 'nullable|string|max:255';
                $rules['department'] = 'nullable|string|max:255';
                $rules['bio'] = 'nullable|string';
                $rules['emergency_contact_name'] = 'nullable|string';
                $rules['emergency_phone'] = 'nullable|string';
            }
        }

        return $rules;
    }

    /**
     * Create user account if requested
     */
    private function createUserIfNeeded($request)
    {
        if (!$request->create_user || !$request->email) {
            return null;
        }

        $fullName = $request->name . ($request->lastname ? ' ' . $request->lastname : '');
        $password = Str::random(10);

        $user = User::create([
            'name' => $fullName,
            'email' => $request->email,
            'password' => Hash::make($password),
        ]);

        $role = $this->getRoleByCategory($request->category, $request->subcategory);
        if ($role) {
            $user->assignRole($role);
        }

        return $user->id;
    }

    /**
     * Get role name based on category and subcategory
     */
    private function getRoleByCategory($category, $subcategory = null)
    {
        if ($category == 'employee') {
            return 'employee';
        } elseif ($category == 'school') {
            if ($subcategory == 'teacher') {
                return 'teacher';
            } elseif ($subcategory == 'administrative') {
                return 'administrative';
            } elseif ($subcategory == 'student') {
                return 'student';
            }
        }
        return null;
    }

    /**
     * Create a new person with filtered data based on category
     */
    private function createPerson($request, $userId = null)
    {
        $data = [
            'user_id' => $userId,
            'category' => $request->category,
            'subcategory' => $request->subcategory ?? null,
            'name' => $request->name,
            'lastname' => $request->lastname,
            'document_id' => $request->document_id,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'company_id' => $request->company_id,
            'bio_url' => Person::generateUniqueBioUrl(),
            'is_active' => true,
        ];

        if ($request->category == 'employee') {
            $data['position'] = $request->position ?? null;
            $data['department'] = $request->department ?? null;
            $data['bio'] = $request->bio ?? null;
            
        } elseif ($request->category == 'school') {
            if ($request->subcategory == 'student') {
                $data['grade_level'] = $request->grade_level;
                $data['academic_year'] = $request->academic_year;
                $data['period'] = $request->period;
                $data['emergency_contact_name'] = $request->emergency_contact_name ?? null;
                $data['emergency_phone'] = $request->emergency_phone ?? null;
                $data['allergies'] = $request->allergies ?? null;
                $data['medical_conditions'] = $request->medical_conditions ?? null;
                
            } elseif ($request->subcategory == 'teacher') {
                $data['position'] = $request->position ?? null;
                $data['teacher_type'] = $request->teacher_type;
                $data['bio'] = $request->bio ?? null;
                $data['emergency_contact_name'] = $request->emergency_contact_name ?? null;
                $data['emergency_phone'] = $request->emergency_phone ?? null;
                
            } elseif ($request->subcategory == 'administrative') {
                $data['position'] = $request->position ?? null;
                $data['department'] = $request->department ?? null;
                $data['bio'] = $request->bio ?? null;
                $data['emergency_contact_name'] = $request->emergency_contact_name ?? null;
                $data['emergency_phone'] = $request->emergency_phone ?? null;
            }
        }

        Log::info('Datos a guardar:', $data);
        
        return Person::create($data);
    }

    /**
     * Update an existing person with filtered data based on category
     */
    private function updatePerson($request, $person)
    {
        Log::info('=== updatePerson - INICIO ===');
        Log::info('Categoría: ' . $request->category);
        Log::info('Subcategoría: ' . $request->subcategory);
        Log::info('Emergency Contact Name recibido: "' . $request->emergency_contact_name . '"');
        Log::info('Emergency Phone recibido: "' . $request->emergency_phone . '"');

        $data = [
            'category' => $request->category,
            'subcategory' => $request->subcategory ?? null,
            'name' => $request->name,
            'lastname' => $request->lastname,
            'document_id' => $request->document_id,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'company_id' => $request->company_id,
        ];

        if ($request->category == 'employee') {
            $data['position'] = $request->position ?? null;
            $data['department'] = $request->department ?? null;
            $data['bio'] = $request->bio ?? null;
            $data['grade_level'] = null;
            $data['academic_year'] = null;
            $data['period'] = null;
            $data['emergency_contact_name'] = null;
            $data['emergency_phone'] = null;
            $data['allergies'] = null;
            $data['medical_conditions'] = null;
            $data['teacher_type'] = null;
            
        } elseif ($request->category == 'school') {
            if ($request->subcategory == 'student') {
                $data['grade_level'] = $request->grade_level;
                $data['academic_year'] = $request->academic_year;
                $data['period'] = $request->period;
                
                // Guardar los valores de emergencia - Usar el valor del request si existe
                // Si el campo viene vacío, guardar null, pero si tiene valor, guardarlo
                $data['emergency_contact_name'] = $request->filled('emergency_contact_name') ? $request->emergency_contact_name : null;
                $data['emergency_phone'] = $request->filled('emergency_phone') ? $request->emergency_phone : null;
                $data['allergies'] = $request->filled('allergies') ? $request->allergies : null;
                $data['medical_conditions'] = $request->filled('medical_conditions') ? $request->medical_conditions : null;
                
                // Limpiar campos laborales
                $data['position'] = null;
                $data['department'] = null;
                $data['bio'] = null;
                $data['teacher_type'] = null;
                
            } elseif ($request->subcategory == 'teacher') {
                $data['position'] = $request->position ?? null;
                $data['teacher_type'] = $request->teacher_type;
                $data['bio'] = $request->bio ?? null;
                $data['emergency_contact_name'] = $request->emergency_contact_name ?? null;
                $data['emergency_phone'] = $request->emergency_phone ?? null;
                $data['department'] = null;
                $data['grade_level'] = null;
                $data['academic_year'] = null;
                $data['allergies'] = null;
                $data['medical_conditions'] = null;
                
            } elseif ($request->subcategory == 'administrative') {
                $data['position'] = $request->position ?? null;
                $data['department'] = $request->department ?? null;
                $data['bio'] = $request->bio ?? null;
                $data['emergency_contact_name'] = $request->emergency_contact_name ?? null;
                $data['emergency_phone'] = $request->emergency_phone ?? null;
                $data['grade_level'] = null;
                $data['academic_year'] = null;
                $data['allergies'] = null;
                $data['medical_conditions'] = null;
                $data['teacher_type'] = null;
            }
        }

        Log::info('Datos a actualizar:', $data);
        
        $person->update($data);
        $person->refresh();
        
        Log::info('Después de actualizar - Emergency Contact Name: "' . ($person->emergency_contact_name ?? 'NULL') . '"');
        Log::info('Después de actualizar - Emergency Phone: "' . ($person->emergency_phone ?? 'NULL') . '"');
        Log::info('=== updatePerson - FIN ===');
    }

    /**
     * Save photo for a person
     */
    private function savePhoto($file, $person)
    {
        $fileName = 'person_' . $person->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('persons/photos', $fileName, 'public');

        $person->update([
            'photo' => $filePath,
            'photo_url' => null
        ]);
    }

    /**
     * Delete photo file from storage
     */
    private function deletePhotoFile($person)
    {
        if ($person->photo && Storage::disk('public')->exists($person->photo)) {
            Storage::disk('public')->delete($person->photo);
        }

        $person->update([
            'photo' => null,
            'photo_url' => null
        ]);
    }

    /**
     * Save schedules for a person
     */
    private function saveSchedules($request, $personId)
    {
        if (!$request->has('schedule')) {
            return;
        }

        foreach ($request->schedule as $scheduleData) {
            if (empty($scheduleData['day']) || empty($scheduleData['start_time']) || empty($scheduleData['end_time'])) {
                continue;
            }

            Schedule::create([
                'person_id' => $personId,
                'day' => $scheduleData['day'],
                'start_time' => $scheduleData['start_time'],
                'end_time' => $scheduleData['end_time'],
                'subject' => $scheduleData['subject'] ?? null,
                'classroom' => $scheduleData['classroom'] ?? null,
            ]);
        }
    }

    // ============================================
    // MÉTODOS DE ETIQUETAS (LABELS)
    // ============================================

    private function getSubcategoryLabel($subcategory)
    {
        $labels = [
            'student' => 'Estudiante',
            'teacher' => 'Docente',
            'administrative' => 'Administrativo'
        ];
        return $labels[$subcategory] ?? '';
    }

    private function getGenderLabel($gender)
    {
        $labels = [
            'male' => 'Masculino',
            'female' => 'Femenino',
            'other' => 'Otro'
        ];
        return $labels[$gender] ?? '';
    }

    private function getPeriodLabel($period)
    {
        $labels = [
            'first' => 'Primer Lapso',
            'second' => 'Segundo Lapso',
            'third' => 'Tercer Lapso'
        ];
        return $labels[$period] ?? '';
    }

    private function getTeacherTypeLabel($type)
    {
        $labels = [
            'regular' => 'Docente Regular',
            'substitute' => 'Docente Suplente',
            'special_education' => 'Educación Especial',
            'part_time' => 'Medio Tiempo'
        ];
        return $labels[$type] ?? '';
    }

    private function getDayLabel($day)
    {
        $labels = [
            'monday' => 'Lunes',
            'tuesday' => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sábado'
        ];
        return $labels[$day] ?? $day;
    }
}
