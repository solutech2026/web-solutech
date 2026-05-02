<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\AccessLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BioController extends Controller
{
    /**
     * Muestra la biografía pública usando bio_url
     */
    public function show($url)
    {
        // Verificar si es PROXICARD (perfil estático)
        if ($url === 'proxicard') {
            return $this->showProxicard();
        }

        // Buscar persona por bio_url
        $person = Person::where('bio_url', $url)
            ->with('company', 'schedules', 'reportCards', 'nfcCard')
            ->firstOrFail();

        // Registrar la visita
        AccessLog::create([
            'person_id' => $person->id,
            'company_id' => $person->company_id,
            'access_type' => 'view',
            'verification_method' => 'bio_url',
            'access_time' => now(),
            'status' => 'granted',
            'gate' => 'Bio URL',
            'reason' => 'Visualización de perfil público'
        ]);

        // Si la petición espera JSON (API)
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $this->formatPersonData($person)
            ]);
        }

        // Determinar el tipo de diseño según el tipo de persona
        $designType = $this->getDesignType($person);

        // Retornar la vista con la persona y el tipo de diseño
        return view('bio.profile', compact('person', 'designType'));
    }

    /**
     * Muestra el perfil estático de PROXICARD
     */
    private function showProxicard()
    {
        if (request()->wantsJson()) {
            $profile = [
                'name' => 'Ing. Herbert Diaz',
                'email' => 'solutech24@outlook.com',
                'phone' => '+58 412 471 4588',
                'role' => 'Ingeniero de Software',
                'location' => 'Caracas, Venezuela',
            ];
            return response()->json([
                'success' => true,
                'data' => $profile
            ]);
        }

        $designType = 'default';
        return view('bio.profile', ['person' => null, 'designType' => $designType]);
    }

    /**
     * Determinar el tipo de diseño según la persona
     */
    private function getDesignType($person)
    {
        $institutionType = $person->institution_type;
        
        if (empty($institutionType)) {
            $institutionType = $person->category;
        }
        
        if (empty($institutionType)) {
            $institutionType = 'company';
        }
        
        if ($institutionType == 'company') {
            return 'company';
        }
        
        if ($institutionType == 'ngo_rescue') {
            return 'rescue';
        }
        
        if ($institutionType == 'government') {
            return 'government';
        }
        
        if ($institutionType == 'school') {
            $subcategory = $person->subcategory;
            
            if ($subcategory == 'student') {
                return 'student';
            }
            
            if ($subcategory == 'teacher') {
                return 'teacher';
            }
            
            if ($subcategory == 'administrative') {
                return 'administrative';
            }
            
            return 'school';
        }
        
        return 'default';
    }

    /**
     * Formatear datos de la persona para API
     */
    private function formatPersonData($person)
    {
        $institutionType = $person->institution_type;
        
        if (empty($institutionType)) {
            $institutionType = $person->category;
        }
        
        if (empty($institutionType)) {
            $institutionType = 'company';
        }
        
        $designType = $this->getDesignType($person);
        $companyName = null;
        
        if ($person->company) {
            $companyName = $person->company->name;
        }
        
        $hasNfc = false;
        if (!is_null($person->nfc_card_id)) {
            $hasNfc = true;
        }
        
        $createdAt = '';
        if ($person->created_at) {
            $createdAt = $person->created_at->format('d/m/Y');
        }
        
        return [
            'id' => $person->id,
            'name' => $person->full_name,
            'document_id' => $person->document_id,
            'email' => $person->email,
            'phone' => $person->phone,
            'photo_url' => $person->photo_url,
            'bio' => $person->bio,
            'institution_type' => $institutionType,
            'design_type' => $designType,
            'company' => $companyName,
            'position' => $person->position,
            'department' => $person->department,
            'subcategory' => $person->subcategory_label,
            'grade_level' => $person->grade_level_label,
            'academic_year' => $person->academic_year,
            'section' => $person->section,
            'average_grade' => $person->average_grade,
            'period_label' => $person->period_label,
            'teacher_type' => $person->teacher_type_label,
            'emergency_contact' => $person->emergency_contact_name,
            'emergency_phone' => $person->emergency_phone,
            'emergency_relationship' => $person->emergency_relationship,
            'blood_type' => $person->blood_type,
            'allergies' => $person->allergies,
            'medical_conditions' => $person->medical_conditions,
            'rescue_member_number' => $person->rescue_member_number,
            'rescue_member_category' => $person->rescue_member_category,
            'rescue_expiry_date' => $person->rescue_expiry_date,
            'rescue_specialty_area' => $person->rescue_specialty_area,
            'rescue_certifications' => $person->rescue_certifications,
            'government_level' => $person->government_level,
            'government_branch' => $person->government_branch,
            'government_entity' => $person->government_entity,
            'government_position' => $person->government_position,
            'government_card_number' => $person->government_card_number,
            'government_joining_date' => $person->government_joining_date,
            'has_nfc' => $hasNfc,
            'created_at' => $createdAt,
        ];
    }

    /**
     * Obtener el rol según el tipo de persona (para vCard)
     */
    private function getPersonRole($person)
    {
        $institutionType = $person->institution_type;
        
        if (empty($institutionType)) {
            $institutionType = $person->category;
        }
        
        if (empty($institutionType)) {
            $institutionType = 'company';
        }
        
        if ($institutionType == 'company') {
            return $person->position ?? 'Empleado';
        }
        
        if ($institutionType == 'school') {
            $subcategory = $person->subcategory;
            
            if ($subcategory == 'student') {
                $grade = $person->grade_level_label ?? '';
                return 'Estudiante - ' . $grade;
            }
            
            if ($subcategory == 'teacher') {
                return $person->position ?? 'Docente';
            }
            
            if ($subcategory == 'administrative') {
                return $person->position ?? 'Personal Administrativo';
            }
            
            return 'Personal Escolar';
        }
        
        if ($institutionType == 'ngo_rescue') {
            return $person->rescue_member_category ?? 'Miembro de Rescate';
        }
        
        if ($institutionType == 'government') {
            $govPosition = $person->government_position_label;
            if (empty($govPosition)) {
                $govPosition = $person->government_position;
            }
            if (empty($govPosition)) {
                $govPosition = 'Funcionario Público';
            }
            return $govPosition;
        }
        
        return 'Colaborador';
    }

    /**
     * Obtener servicios según la categoría de la persona (para API)
     */
    public function getServicesByCategory($person)
    {
        $services = [];
        $institutionType = $person->institution_type;
        
        if (empty($institutionType)) {
            $institutionType = $person->category;
        }
        
        if (empty($institutionType)) {
            $institutionType = 'company';
        }

        if ($institutionType === 'company') {
            $services = [
                ['label' => $person->position ?? 'Empleado', 'icon' => 'Briefcase'],
                ['label' => $person->department ?? 'Departamento', 'icon' => 'Building'],
                ['label' => 'Control de Acceso', 'icon' => 'ShieldCheck'],
            ];
        } elseif ($institutionType === 'school') {
            if ($person->subcategory === 'student') {
                $services = [
                    ['label' => 'Grado: ' . ($person->grade_level_label ?? 'N/A'), 'icon' => 'GraduationCap'],
                    ['label' => 'Año Escolar: ' . ($person->academic_year ?? 'N/A'), 'icon' => 'Calendar'],
                    ['label' => 'Promedio: ' . ($person->average_grade ?? 'N/A'), 'icon' => 'Star'],
                    ['label' => 'Sección: ' . ($person->section ?? 'N/A'), 'icon' => 'Users'],
                ];
            } elseif ($person->subcategory === 'teacher') {
                $services = [
                    ['label' => $person->position ?? 'Docente', 'icon' => 'User'],
                    ['label' => 'Tipo: ' . ($person->teacher_type_label ?? 'N/A'), 'icon' => 'GraduationCap'],
                    ['label' => 'Control de Acceso', 'icon' => 'ShieldCheck'],
                ];
            } elseif ($person->subcategory === 'administrative') {
                $services = [
                    ['label' => $person->position ?? 'Personal Administrativo', 'icon' => 'Building'],
                    ['label' => $person->department ?? 'Departamento', 'icon' => 'Folder'],
                    ['label' => 'Control de Acceso', 'icon' => 'ShieldCheck'],
                ];
            }
        } elseif ($institutionType === 'ngo_rescue') {
            $services = [
                ['label' => 'Miembro N°: ' . ($person->rescue_member_number ?? 'N/A'), 'icon' => 'IdCard'],
                ['label' => 'Categoría: ' . ($person->rescue_member_category ?? 'N/A'), 'icon' => 'UserShield'],
                ['label' => 'Especialidad: ' . ($person->rescue_specialty_area ?? 'N/A'), 'icon' => 'Heartbeat'],
            ];
        } elseif ($institutionType === 'government') {
            $level = $this->getGovernmentLevelLabel($person->government_level);
            $position = $person->government_position_label;
            if (empty($position)) {
                $position = $person->government_position;
            }
            if (empty($position)) {
                $position = 'N/A';
            }
            
            $services = [
                ['label' => 'Nivel: ' . $level, 'icon' => 'Landmark'],
                ['label' => 'Cargo: ' . $position, 'icon' => 'UserTie'],
                ['label' => 'Control de Acceso', 'icon' => 'ShieldCheck'],
            ];
        }

        return $services;
    }

    /**
     * Obtener etiqueta del nivel de gobierno
     */
    private function getGovernmentLevelLabel($level)
    {
        if ($level == 'national') {
            return 'Nacional';
        }
        if ($level == 'regional') {
            return 'Regional';
        }
        if ($level == 'municipal') {
            return 'Municipal';
        }
        if ($level == 'parish') {
            return 'Parroquial';
        }
        return 'N/A';
    }

    /**
     * Obtener etiqueta de la rama de gobierno
     */
    private function getGovernmentBranchLabel($branch)
    {
        if ($branch == 'executive') {
            return 'Ejecutivo';
        }
        if ($branch == 'legislative') {
            return 'Legislativo';
        }
        if ($branch == 'judicial') {
            return 'Judicial';
        }
        if ($branch == 'citizen') {
            return 'Ciudadano';
        }
        if ($branch == 'electoral') {
            return 'Electoral';
        }
        return 'N/A';
    }

    /**
     * Obtener etiqueta de ente gubernamental
     */
    private function getGovernmentEntityLabel($entity)
    {
        if ($entity == 'min_interior') {
            return 'Ministerio del Interior, Justicia y Paz';
        }
        if ($entity == 'min_defensa') {
            return 'Ministerio de la Defensa';
        }
        if ($entity == 'min_educacion') {
            return 'Ministerio de Educación';
        }
        if ($entity == 'min_salud') {
            return 'Ministerio de Salud';
        }
        if ($entity == 'asamblea_nacional') {
            return 'Asamblea Nacional';
        }
        if ($entity == 'fanb') {
            return 'Fuerza Armada Nacional Bolivariana';
        }
        if ($entity == 'gnb') {
            return 'Guardia Nacional Bolivariana';
        }
        if ($entity == 'cicpc') {
            return 'CICPC';
        }
        return $entity ?? 'N/A';
    }

    /**
     * Genera y descarga el archivo vCard dinámicamente
     */
    public function downloadVCard($url)
    {
        if ($url === 'proxicard') {
            return $this->downloadProxicardVCard();
        }

        $person = Person::where('bio_url', $url)
            ->with('company')
            ->firstOrFail();

        // Limpiar teléfono
        $phone = preg_replace('/[^0-9+]/', '', $person->phone ?? '');

        // Obtener título según tipo
        $title = $this->getPersonRole($person);
        $org = 'PROXICARD';
        
        if ($person->company) {
            $org = $person->company->name;
        }

        // Estructura del vCard
        $vcard = "BEGIN:VCARD\n"
            . "VERSION:3.0\n"
            . "FN:{$person->full_name}\n"
            . "ORG:{$org}\n"
            . "TITLE:{$title}\n"
            . "TEL;TYPE=CELL:{$phone}\n"
            . "EMAIL:{$person->email}\n"
            . "NOTE:{$person->bio}\n"
            . "URL:" . url('/bio/' . $person->bio_url) . "\n"
            . "END:VCARD";

        $filename = str_replace(' ', '-', strtolower($person->full_name)) . ".vcf";

        return response($vcard)
            ->header('Content-Type', 'text/vcard')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Descargar vCard de PROXICARD
     */
    private function downloadProxicardVCard()
    {
        $vcard = "BEGIN:VCARD\n"
            . "VERSION:3.0\n"
            . "FN:PROXICARD\n"
            . "ORG:PROXICARD\n"
            . "TITLE:Sistema de Control de Acceso\n"
            . "TEL;TYPE=WORK,VOICE:+582125551234\n"
            . "EMAIL:info@proxicard.com\n"
            . "URL:" . url('/bio/proxicard') . "\n"
            . "ADR;TYPE=WORK:;;Caracas;Caracas;;1010;Venezuela\n"
            . "END:VCARD";

        return response($vcard)
            ->header('Content-Type', 'text/vcard')
            ->header('Content-Disposition', 'attachment; filename="proxicard.vcf"');
    }

    /**
     * Obtener datos de la biografía en formato JSON (para API)
     */
    public function getData($url)
    {
        if ($url === 'proxicard') {
            $profile = [
                'name' => 'Ing. Herbert Diaz',
                'email' => 'solutech24@outlook.com',
                'phone' => '+58 412 471 4588',
                'role' => 'Ingeniero de Software',
                'location' => 'Caracas, Venezuela',
                'services' => [
                    ['label' => 'Desarrollo de Software a Medida', 'icon' => 'Code2'],
                    ['label' => 'Arquitectura de Sistemas', 'icon' => 'Server'],
                    ['label' => 'Integración de Hardware', 'icon' => 'Cpu'],
                    ['label' => 'Base de Datos', 'icon' => 'Database'],
                    ['label' => 'APIs RESTful', 'icon' => 'Network'],
                    ['label' => 'Seguridad Informática', 'icon' => 'ShieldCheck'],
                ],
                'experience' => 8,
                'projects' => 150,
                'clients' => 45,
                'social' => [
                    'github' => 'https://github.com/solutech2026',
                    'linkedin' => 'https://linkedin.com/herbert-diaz-a23530332',
                    'instagram' => 'https://instagram.com/therunningdev.ve',
                    'website' => 'https://solutechoficial.com',
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $profile
            ]);
        }

        $person = Person::where('bio_url', $url)
            ->with('company')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->formatPersonData($person)
        ]);
    }

    /**
     * Redireccionar a la página de administración de la persona
     */
    public function redirectToProfile($url)
    {
        if ($url === 'proxicard') {
            return redirect()->route('home');
        }

        $person = Person::where('bio_url', $url)->firstOrFail();
        return redirect()->route('admin.persons.show', $person->id);
    }
}
