<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\Company;
use App\Models\NfcCard;
use App\Models\Schedule;
use App\Models\AccessLog;
use App\Models\ReportCard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $persons = Person::with('company', 'user')->latest()->paginate(15);
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
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::error('Error de validación:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validación manual de campos condicionales
        $conditionalErrors = $this->validateConditionalFields($request);
        if (!empty($conditionalErrors)) {
            return redirect()->back()
                ->withErrors($conditionalErrors)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Crear usuario si es necesario
            $userId = $this->createUserIfNeeded($request);

            // Crear persona
            $person = $this->createPerson($request, $userId);

            // Asignar tarjeta NFC si se seleccionó
            if ($request->card_id) {
                $this->assignNfcCardToPerson($person, $request->card_id);
            }

            // Guardar foto si se subió
            if ($request->hasFile('photo')) {
                $this->savePhoto($request->file('photo'), $person);
            }

            // Guardar logo de organización si se subió
            if ($request->hasFile('organization_logo')) {
                $this->saveOrganizationLogo($person, $request->file('organization_logo'));
            }

            // Guardar horarios (solo para estudiantes, docentes y administrativos)
            if ($this->shouldSaveSchedules($request)) {
                $this->saveSchedules($request, $person->id);
            }

            // Guardar boletines de notas (solo para estudiantes)
            if ($request->hasFile('grade_report_first') || $request->hasFile('grade_report_second') || $request->hasFile('grade_report_third')) {
                $this->saveGradeReports($request, $person);
            }

            DB::commit();

            return redirect()
                ->route('admin.persons.index')
                ->with('success', 'Persona registrada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ERROR AL REGISTRAR: ' . $e->getMessage());

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
            ->limit(100)
            ->get();

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
        $person = Person::with('schedules', 'reportCards')->findOrFail($id);
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

        // Validación manual de campos condicionales
        $conditionalErrors = $this->validateConditionalFields($request);
        if (!empty($conditionalErrors)) {
            return redirect()->back()
                ->withErrors($conditionalErrors)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Actualizar persona
            $this->updatePerson($request, $person);

            // Asignar nueva tarjeta si se seleccionó
            if ($request->card_id) {
                $this->assignNfcCardToPerson($person, $request->card_id);
            }

            // Desvincular tarjeta si se solicitó
            if ($request->has('unassign_card') && $request->unassign_card == '1') {
                $this->unassignNfcCardFromPerson($person);
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

            // Actualizar logo de organización
            if ($request->hasFile('organization_logo')) {
                $this->deleteOrganizationLogo($person);
                $this->saveOrganizationLogo($person, $request->file('organization_logo'));
            }

            // Eliminar logo si se solicitó
            if ($request->has('remove_organization_logo') && $request->remove_organization_logo == '1') {
                $this->deleteOrganizationLogo($person);
            }

            // Actualizar horarios (solo si aplica)
            if ($this->shouldSaveSchedules($request)) {
                Schedule::where('person_id', $person->id)->delete();
                $this->saveSchedules($request, $person->id);
            }

            // Actualizar boletines de notas
            if ($request->hasFile('grade_report_first') || $request->hasFile('grade_report_second') || $request->hasFile('grade_report_third')) {
                $this->saveGradeReports($request, $person, true);
            }

            DB::commit();
            
            $person->refresh();

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

            // Eliminar logo de organización
            $this->deleteOrganizationLogo($person);

            // Eliminar archivos de boletines
            if ($person->reportCards) {
                foreach ($person->reportCards as $reportCard) {
                    Storage::disk('public')->delete($reportCard->file_path);
                    $reportCard->delete();
                }
            }

            // Eliminar archivos de boletines antiguos
            if ($person->grade_report_first) {
                Storage::disk('public')->delete($person->grade_report_first);
            }
            if ($person->grade_report_second) {
                Storage::disk('public')->delete($person->grade_report_second);
            }
            if ($person->grade_report_third) {
                Storage::disk('public')->delete($person->grade_report_third);
            }

            // Eliminar horarios
            Schedule::where('person_id', $person->id)->delete();

            // Eliminar registros de acceso
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

    // ============================================
    // MÉTODOS PARA AJAX
    // ============================================

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

    /**
     * Get access logs for a person (AJAX).
     */
    public function getAccessLogs($id)
    {
        $logs = AccessLog::where('person_id', $id)
            ->latest('access_time')
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
     * Get report cards for a student (AJAX).
     */
    public function getReportCards($id)
    {
        try {
            $person = Person::findOrFail($id);

            if ($person->subcategory !== 'student') {
                return response()->json(['success' => false, 'error' => 'No es un estudiante'], 400);
            }

            $reportCards = $person->reportCards()->orderBy('academic_year', 'desc')->get()->map(function ($rc) {
                return [
                    'id' => $rc->id,
                    'period' => $rc->period,
                    'period_label' => $this->getPeriodLabel($rc->period),
                    'academic_year' => $rc->academic_year,
                    'grade_level' => $rc->grade_level,
                    'grade_level_label' => $this->getGradeLevelLabel($rc->grade_level),
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
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Upload report card (AJAX).
     */
    public function uploadReportCard(Request $request, $id)
    {
        $request->validate([
            'period' => 'required|in:first,second,third',
            'academic_year' => 'required|string',
            'grade_level' => 'required|string',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'average' => 'nullable|numeric|min:0|max:20',
        ]);

        try {
            $person = Person::findOrFail($id);

            if ($person->subcategory !== 'student') {
                return response()->json(['success' => false, 'error' => 'Solo estudiantes pueden tener boletines'], 400);
            }

            // Eliminar boletín existente del mismo periodo y año
            $existingReport = ReportCard::where('person_id', $person->id)
                ->where('period', $request->period)
                ->where('academic_year', $request->academic_year)
                ->first();

            if ($existingReport) {
                Storage::disk('public')->delete($existingReport->file_path);
                $existingReport->delete();
            }

            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName = $person->id . '_' . $request->period . '_' . $request->academic_year . '.' . $extension;
            $filePath = $file->storeAs('report_cards/' . $person->id, $fileName, 'public');

            $reportCard = ReportCard::create([
                'person_id' => $person->id,
                'period' => $request->period,
                'academic_year' => $request->academic_year,
                'grade_level' => $request->grade_level,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'average' => $request->average,
            ]);

            $this->updateStudentAverage($person->id);

            return response()->json([
                'success' => true,
                'message' => 'Boletín subido exitosamente',
                'data' => $reportCard
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete report card (AJAX).
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
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Upload photo (AJAX).
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
     * Assign NFC card (AJAX).
     */
    public function assignNfc(Request $request, $id)
    {
        $request->validate([
            'card_id' => 'required|exists:nfc_cards,id'
        ]);

        try {
            DB::beginTransaction();

            $person = Person::findOrFail($id);
            $this->assignNfcCardToPerson($person, $request->card_id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tarjeta NFC asignada exitosamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unassign NFC card (AJAX).
     */
    public function unassignNfc($id)
    {
        try {
            DB::beginTransaction();

            $person = Person::findOrFail($id);
            $this->unassignNfcCardFromPerson($person);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tarjeta NFC desvinculada exitosamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ============================================
    // MÉTODOS PRIVADOS DE VALIDACIÓN
    // ============================================

    /**
     * Validate required fields based on institution type
     */
    private function validateConditionalFields($request)
    {
        $errors = [];
        $institutionType = $request->institution_type;
        
        if ($institutionType == 'company') {
            // Campos de empresa son OPCIONALES - no se validan
            // No se agregan errores para position y department
            
        } elseif ($institutionType == 'school') {
            $subcategory = $request->subcategory;
            
            if ($subcategory == 'student') {
                if (empty($request->grade_level)) {
                    $errors['grade_level'] = 'El campo Grado es requerido para estudiantes.';
                }
                if (empty($request->academic_year)) {
                    $errors['academic_year'] = 'El campo Año Escolar es requerido para estudiantes.';
                }
                
            } elseif ($subcategory == 'teacher') {
                if (empty($request->position)) {
                    $errors['position'] = 'El campo Cargo es requerido para docentes.';
                }
                
            } elseif ($subcategory == 'administrative') {
                if (empty($request->position)) {
                    $errors['position'] = 'El campo Cargo es requerido para personal administrativo.';
                }
                if (empty($request->department)) {
                    $errors['department'] = 'El campo Departamento es requerido para personal administrativo.';
                }
            } elseif (empty($subcategory)) {
                $errors['subcategory'] = 'Debe seleccionar un rol en el colegio.';
            }
            
        } elseif ($institutionType == 'ngo_rescue') {
            if (empty($request->rescue_member_number)) {
                $errors['rescue_member_number'] = 'El campo Número de Miembro es requerido para ONG de Rescate.';
            }
            if (empty($request->rescue_member_category)) {
                $errors['rescue_member_category'] = 'El campo Categoría de Miembro es requerido para ONG de Rescate.';
            }
            
        } elseif ($institutionType == 'government') {
            if (empty($request->government_level)) {
                $errors['government_level'] = 'El campo Nivel del Gobierno es requerido.';
            }
            if (empty($request->government_entity)) {
                $errors['government_entity'] = 'El campo Ministerio / Ente es requerido.';
            }
            if (empty($request->government_position)) {
                $errors['government_position'] = 'El campo Cargo / Jerarquía es requerido.';
            }
        } else {
            $errors['institution_type'] = 'Debe seleccionar un tipo de organización.';
        }
        
        // Validar campos de emergencia para todos - AHORA SON OPCIONALES
        // Comentado para que sean opcionales
        // if (empty($request->emergency_contact_name)) {
        //     $errors['emergency_contact_name'] = 'El campo Nombre del contacto es requerido.';
        // }
        // if (empty($request->emergency_phone)) {
        //     $errors['emergency_phone'] = 'El campo Número de contacto es requerido.';
        // }
        // if (empty($request->emergency_relationship)) {
        //     $errors['emergency_relationship'] = 'El campo Parentesco es requerido.';
        // }
        
        return $errors;
    }

    /**
     * Get validation rules based on institution_type and subcategory
     */
    private function getValidationRules($request, $id = null)
    {
        $uniqueDocument = $id ? 'unique:persons,document_id,' . $id : 'unique:persons,document_id';
        $uniqueEmail = $id ? 'unique:persons,email,' . $id : 'unique:persons,email';

        $rules = [
            'institution_type' => 'required|in:company,school,ngo_rescue,government',
            'name' => 'required|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'document_id' => 'nullable|string|max:50|' . $uniqueDocument,
            'email' => 'nullable|email|max:255|' . $uniqueEmail,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
            'company_id' => 'required|exists:companies,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'organization_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Datos comunes para todos (ahora nullable - opcionales)
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'emergency_relationship' => 'nullable|string|max:100',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string',
            'medical_conditions' => 'nullable|string',
        ];

        if ($request->institution_type == 'company') {
            // Campos opcionales para empresa
            $rules['position'] = 'nullable|string|max:255';
            $rules['department'] = 'nullable|string|max:255';
            
        } elseif ($request->institution_type == 'school') {
            $rules['subcategory'] = 'required|in:student,teacher,administrative';
            
            if ($request->subcategory == 'student') {
                $rules['grade_level'] = 'nullable|string';
                $rules['academic_year'] = 'nullable|string';
                $rules['section'] = 'nullable|string|max:10';
                $rules['period'] = 'nullable|in:first,second,third';
                $rules['grade_report_first'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120';
                $rules['grade_report_second'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120';
                $rules['grade_report_third'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120';
                
            } elseif ($request->subcategory == 'teacher') {
                $rules['position'] = 'nullable|string|max:255';
                $rules['teacher_type'] = 'nullable|in:regular,substitute,special_education,part_time';
                $rules['bio'] = 'nullable|string';
                
            } elseif ($request->subcategory == 'administrative') {
                $rules['position'] = 'nullable|string|max:255';
                $rules['department'] = 'nullable|string|max:255';
                $rules['bio'] = 'nullable|string';
            }
            
        } elseif ($request->institution_type == 'ngo_rescue') {
            $rules['rescue_member_number'] = 'nullable|string|max:50';
            $rules['rescue_member_category'] = 'nullable|string|max:100';
            $rules['rescue_expiry_date'] = 'nullable|date';
            $rules['rescue_specialty_area'] = 'nullable|string|max:255';
            $rules['rescue_certifications'] = 'nullable|string';
            
        } elseif ($request->institution_type == 'government') {
            $rules['government_level'] = 'nullable|in:national,regional,municipal,parish';
            $rules['government_branch'] = 'nullable|in:executive,legislative,judicial,citizen,electoral';
            $rules['government_entity'] = 'nullable|string|max:255';
            $rules['government_position'] = 'nullable|string|max:255';
            $rules['government_card_number'] = 'nullable|string|max:100';
            $rules['government_joining_date'] = 'nullable|date';
        }

        return $rules;
    }

    // ============================================
    // MÉTODOS DE CREACIÓN Y ACTUALIZACIÓN
    // ============================================

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

        $role = $this->getRoleByCategory($request->institution_type, $request->subcategory ?? null);
        if ($role) {
            $user->assignRole($role);
        }

        return $user->id;
    }

    /**
     * Get role name based on institution type and subcategory
     */
    private function getRoleByCategory($institutionType, $subcategory = null)
    {
        if ($institutionType == 'company') {
            return 'employee';
        } elseif ($institutionType == 'school') {
            if ($subcategory == 'teacher') {
                return 'teacher';
            } elseif ($subcategory == 'administrative') {
                return 'administrative';
            } elseif ($subcategory == 'student') {
                return 'student';
            }
        } elseif ($institutionType == 'ngo_rescue') {
            return 'rescuer';
        } elseif ($institutionType == 'government') {
            return 'government';
        }
        return null;
    }

    /**
     * Create a new person
     */
    private function createPerson($request, $userId = null)
    {
        $data = [
            'user_id' => $userId,
            'institution_type' => $request->institution_type,
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
            
            // Datos de emergencia y salud
            'emergency_contact_name' => $request->emergency_contact_name ?? null,
            'emergency_phone' => $request->emergency_phone ?? null,
            'emergency_relationship' => $request->emergency_relationship ?? null,
            'blood_type' => $request->blood_type ?? null,
            'allergies' => $request->allergies ?? null,
            'medical_conditions' => $request->medical_conditions ?? null,
        ];

        if ($request->institution_type == 'company') {
            $data['position'] = $request->position ?? null;
            $data['department'] = $request->department ?? null;
            
        } elseif ($request->institution_type == 'school') {
            if ($request->subcategory == 'student') {
                $data['grade_level'] = $request->grade_level;
                $data['academic_year'] = $request->academic_year;
                $data['section'] = $request->section ?? null;
                $data['period'] = $request->period ?? null;
                
            } elseif ($request->subcategory == 'teacher') {
                $data['position'] = $request->position ?? null;
                $data['teacher_type'] = $request->teacher_type ?? null;
                $data['bio'] = $request->bio ?? null;
                
            } elseif ($request->subcategory == 'administrative') {
                $data['position'] = $request->position ?? null;
                $data['department'] = $request->department ?? null;
                $data['bio'] = $request->bio ?? null;
            }
            
        } elseif ($request->institution_type == 'ngo_rescue') {
            $data['rescue_member_number'] = $request->rescue_member_number ?? null;
            $data['rescue_member_category'] = $request->rescue_member_category ?? null;
            $data['rescue_expiry_date'] = $request->rescue_expiry_date ?? null;
            $data['rescue_specialty_area'] = $request->rescue_specialty_area ?? null;
            $data['rescue_certifications'] = $request->rescue_certifications ?? null;
            
        } elseif ($request->institution_type == 'government') {
            $data['government_level'] = $request->government_level ?? null;
            $data['government_branch'] = $request->government_branch ?? null;
            $data['government_entity'] = $request->government_entity ?? null;
            $data['government_position'] = $request->government_position ?? null;
            $data['government_card_number'] = $request->government_card_number ?? null;
            $data['government_joining_date'] = $request->government_joining_date ?? null;
        }

        return Person::create($data);
    }

    /**
     * Update an existing person
     */
    private function updatePerson($request, $person)
    {
        $data = [
            'institution_type' => $request->institution_type,
            'subcategory' => $request->subcategory ?? null,
            'name' => $request->name,
            'lastname' => $request->lastname,
            'document_id' => $request->document_id,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'company_id' => $request->company_id,
            
            // Datos de emergencia y salud
            'emergency_contact_name' => $request->emergency_contact_name ?? null,
            'emergency_phone' => $request->emergency_phone ?? null,
            'emergency_relationship' => $request->emergency_relationship ?? null,
            'blood_type' => $request->blood_type ?? null,
            'allergies' => $request->allergies ?? null,
            'medical_conditions' => $request->medical_conditions ?? null,
        ];

        if ($request->institution_type == 'company') {
            $data['position'] = $request->position ?? null;
            $data['department'] = $request->department ?? null;
            $this->clearSchoolFields($data);
            $this->clearRescueFields($data);
            $this->clearGovernmentFields($data);
            
        } elseif ($request->institution_type == 'school') {
            if ($request->subcategory == 'student') {
                $data['grade_level'] = $request->grade_level;
                $data['academic_year'] = $request->academic_year;
                $data['section'] = $request->section ?? null;
                $data['period'] = $request->period ?? null;
                $this->clearWorkFields($data);
                $this->clearRescueFields($data);
                $this->clearGovernmentFields($data);
                
            } elseif ($request->subcategory == 'teacher') {
                $data['position'] = $request->position ?? null;
                $data['teacher_type'] = $request->teacher_type ?? null;
                $data['bio'] = $request->bio ?? null;
                $this->clearStudentFields($data);
                $this->clearRescueFields($data);
                $this->clearGovernmentFields($data);
                
            } elseif ($request->subcategory == 'administrative') {
                $data['position'] = $request->position ?? null;
                $data['department'] = $request->department ?? null;
                $data['bio'] = $request->bio ?? null;
                $this->clearStudentFields($data);
                $this->clearRescueFields($data);
                $this->clearGovernmentFields($data);
            }
            
        } elseif ($request->institution_type == 'ngo_rescue') {
            $data['rescue_member_number'] = $request->rescue_member_number ?? null;
            $data['rescue_member_category'] = $request->rescue_member_category ?? null;
            $data['rescue_expiry_date'] = $request->rescue_expiry_date ?? null;
            $data['rescue_specialty_area'] = $request->rescue_specialty_area ?? null;
            $data['rescue_certifications'] = $request->rescue_certifications ?? null;
            $this->clearWorkFields($data);
            $this->clearStudentFields($data);
            $this->clearGovernmentFields($data);
            
        } elseif ($request->institution_type == 'government') {
            $data['government_level'] = $request->government_level ?? null;
            $data['government_branch'] = $request->government_branch ?? null;
            $data['government_entity'] = $request->government_entity ?? null;
            $data['government_position'] = $request->government_position ?? null;
            $data['government_card_number'] = $request->government_card_number ?? null;
            $data['government_joining_date'] = $request->government_joining_date ?? null;
            $this->clearWorkFields($data);
            $this->clearStudentFields($data);
            $this->clearRescueFields($data);
        }

        $person->update($data);
    }

    // ============================================
    // MÉTODOS DE LIMPIEZA DE CAMPOS
    // ============================================

    /**
     * Clear work-related fields
     */
    private function clearWorkFields(&$data)
    {
        $data['position'] = null;
        $data['department'] = null;
        $data['bio'] = null;
        $data['teacher_type'] = null;
    }

    /**
     * Clear student-related fields
     */
    private function clearStudentFields(&$data)
    {
        $data['grade_level'] = null;
        $data['academic_year'] = null;
        $data['section'] = null;
        $data['period'] = null;
    }

    /**
     * Clear school-related fields
     */
    private function clearSchoolFields(&$data)
    {
        $data['grade_level'] = null;
        $data['academic_year'] = null;
        $data['section'] = null;
        $data['period'] = null;
        $data['position'] = null;
        $data['department'] = null;
        $data['bio'] = null;
        $data['teacher_type'] = null;
    }

    /**
     * Clear rescue-related fields
     */
    private function clearRescueFields(&$data)
    {
        $data['rescue_member_number'] = null;
        $data['rescue_member_category'] = null;
        $data['rescue_expiry_date'] = null;
        $data['rescue_specialty_area'] = null;
        $data['rescue_certifications'] = null;
    }

    /**
     * Clear government-related fields
     */
    private function clearGovernmentFields(&$data)
    {
        $data['government_level'] = null;
        $data['government_branch'] = null;
        $data['government_entity'] = null;
        $data['government_position'] = null;
        $data['government_card_number'] = null;
        $data['government_joining_date'] = null;
    }

    // ============================================
    // MÉTODOS DE ARCHIVOS Y GUARDADO
    // ============================================

    /**
     * Save photo for a person
     */
    private function savePhoto($file, $person)
    {
        $fileName = 'person_' . $person->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('persons/photos', $fileName, 'public');

        $person->update(['photo' => $filePath]);
    }

    /**
     * Delete photo file from storage
     */
    private function deletePhotoFile($person)
    {
        if ($person->photo && Storage::disk('public')->exists($person->photo)) {
            Storage::disk('public')->delete($person->photo);
        }

        $person->update(['photo' => null]);
    }

    /**
     * Save organization logo
     */
    private function saveOrganizationLogo($person, $file)
    {
        $fileName = 'org_logo_' . $person->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('organizations/logos', $fileName, 'public');
        
        $person->update(['organization_logo' => $filePath]);
    }

    /**
     * Delete organization logo
     */
    private function deleteOrganizationLogo($person)
    {
        if ($person->organization_logo && Storage::disk('public')->exists($person->organization_logo)) {
            Storage::disk('public')->delete($person->organization_logo);
        }
        
        $person->update(['organization_logo' => null]);
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

    /**
     * Save grade reports (boletines) for a student
     */
    private function saveGradeReports($request, $person, $deleteExisting = false)
    {
        $periods = ['first', 'second', 'third'];
        
        foreach ($periods as $period) {
            $fieldName = 'grade_report_' . $period;
            
            if ($request->hasFile($fieldName)) {
                if ($deleteExisting && $person->$fieldName) {
                    Storage::disk('public')->delete($person->$fieldName);
                }
                
                $file = $request->file($fieldName);
                $extension = $file->getClientOriginalExtension();
                $fileName = $person->id . '_' . $period . '_' . time() . '.' . $extension;
                $filePath = $file->storeAs('report_cards/' . $person->id, $fileName, 'public');
                
                $person->update([$fieldName => $filePath]);
            }
        }
    }

    /**
     * Determine if schedules should be saved
     */
    private function shouldSaveSchedules($request)
    {
        if ($request->institution_type == 'company') {
            return false;
        }
        
        if ($request->institution_type == 'school') {
            return in_array($request->subcategory, ['student', 'teacher', 'administrative']);
        }
        
        return false;
    }

    /**
     * Assign NFC card to a person
     */
    private function assignNfcCardToPerson($person, $cardId)
    {
        $card = NfcCard::findOrFail($cardId);

        if ($card->assigned_to && $card->assigned_to != $person->id) {
            throw new \Exception('Esta tarjeta NFC ya está asignada a otra persona.');
        }

        if ($person->nfc_card_id && $person->nfc_card_id != $cardId) {
            $this->unassignNfcCardFromPerson($person);
        }

        $card->assigned_to = $person->id;
        $card->assigned_at = now();
        $card->save();

        $person->nfc_card_id = $card->id;
        $person->save();
    }

    /**
     * Unassign NFC card from a person
     */
    private function unassignNfcCardFromPerson($person)
    {
        if ($person->nfc_card_id) {
            $card = NfcCard::find($person->nfc_card_id);
            if ($card) {
                $card->assigned_to = null;
                $card->assigned_at = null;
                $card->save();
            }
            $person->nfc_card_id = null;
            $person->save();
        }
    }

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

    // ============================================
    // MÉTODOS DE ETIQUETAS
    // ============================================

    private function getPeriodLabel($period)
    {
        $labels = [
            'first' => 'Primer Lapso',
            'second' => 'Segundo Lapso',
            'third' => 'Tercer Lapso'
        ];
        return $labels[$period] ?? '';
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

    private function getGradeLevelLabel($gradeLevel)
    {
        $labels = [
            '1er_grado' => '1er Grado',
            '2do_grado' => '2do Grado',
            '3er_grado' => '3er Grado',
            '4to_grado' => '4to Grado',
            '5to_grado' => '5to Grado',
            '6to_grado' => '6to Grado',
            '7mo_grado' => '7mo Grado (1er Año)',
            '8vo_grado' => '8vo Grado (2do Año)',
            '9no_grado' => '9no Grado (3er Año)',
            '4to_ano' => '4to Año (10° grado)',
            '5to_ano' => '5to Año (11° grado)',
        ];
        return $labels[$gradeLevel] ?? $gradeLevel;
    }
}
