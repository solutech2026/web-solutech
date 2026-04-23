<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\AccessLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
            ->with('company')
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

        // Preparar datos para la vista
        $profile = [
            'slug' => $person->bio_url,
            'name' => $person->full_name,
            'role' => $person->position ?? $person->category_label,
            'summary' => $person->bio ?? 'Sin información adicional',
            'phone' => $person->phone ?? '',
            'email' => $person->email ?? '',
            'services' => $this->getServicesByCategory($person),
            'photo_path' => $person->photo_url,
            'experience' => null,
            'projects' => null,
            'clients' => null,
            'location' => $person->company ? $person->company->location : null,
            'social' => null,
            'company' => $person->company ? $person->company->name : null,
            'category' => $person->category_label,
            'subcategory' => $person->subcategory_label,
            'document_id' => $person->document_id,
            'emergency_contact' => $person->emergency_contact_name,
            'emergency_phone' => $person->emergency_phone,
        ];

        // Si la petición espera JSON (API)
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $profile
            ]);
        }

        return Inertia::render('Bio/Show', [
            'profile' => $profile
        ]);
    }

    /**
     * Muestra el perfil estático de PROXICARD
     */
    private function showProxicard()
    {
        $profile = [
            'slug' => 'Ing. Herbert Diaz',
            'name' => 'Ing. Herbert Diaz',
            'role' => 'Ingeniero de Software',
            'summary' => 'Senior Software Developer especializado en Laravel & React. Apasionado por la arquitectura de sistemas, la integración de hardware y la creación de soluciones escalables que impulsan negocios. De los bits al despliegue, construyendo el futuro de la tecnología.',
            'phone' => '+58 412 471 4588',
            'email' => 'solutech24@outlook.com',
            'services' => [
                ['label' => 'Desarrollo de Software a Medida', 'icon' => 'Code2'],
                ['label' => 'Arquitectura de Sistemas', 'icon' => 'Server'],
                ['label' => 'Integración de Hardware', 'icon' => 'Cpu'],
                ['label' => 'Base de Datos', 'icon' => 'Database'],
                ['label' => 'APIs RESTful', 'icon' => 'Network'],
                ['label' => 'Seguridad Informática', 'icon' => 'ShieldCheck'],
            ],
            'photo_path' => '/img/logo_app.png',
            'experience' => 8,
            'projects' => 150,
            'clients' => 45,
            'location' => 'Caracas, Venezuela',
            'social' => [
                'github' => 'https://github.com/solutech2026',
                'linkedin' => 'https://linkedin.com/herbert-diaz-a23530332',
                'instagram' => 'https://instagram.com/therunningdev.ve',
                'website' => 'https://solutechoficial.com',
            ],
            'company' => null,
            'category' => null,
            'subcategory' => null,
            'document_id' => null,
            'emergency_contact' => null,
            'emergency_phone' => null,
        ];

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $profile
            ]);
        }

        return Inertia::render('Bio/Show', [
            'profile' => $profile
        ]);
    }

    /**
     * Obtener servicios según la categoría de la persona
     */
    private function getServicesByCategory($person)
    {
        $services = [];

        if ($person->category === 'employee') {
            $services = [
                ['label' => $person->position ?? 'Empleado', 'icon' => 'Briefcase'],
                ['label' => $person->department ?? 'Departamento', 'icon' => 'Building'],
                ['label' => 'Control de Acceso', 'icon' => 'ShieldCheck'],
            ];
        } elseif ($person->subcategory === 'student') {
            $services = [
                ['label' => 'Grado: ' . $person->grade_level_label, 'icon' => 'GraduationCap'],
                ['label' => 'Año Escolar: ' . $person->academic_year, 'icon' => 'Calendar'],
                ['label' => 'Promedio: ' . ($person->average_grade ?? 'N/A'), 'icon' => 'Star'],
            ];
        } elseif ($person->subcategory === 'teacher') {
            $services = [
                ['label' => $person->position ?? 'Docente', 'icon' => 'User'],
                ['label' => 'Tipo: ' . $person->teacher_type_label, 'icon' => 'GraduationCap'],
                ['label' => 'Control de Acceso', 'icon' => 'ShieldCheck'],
            ];
        } elseif ($person->subcategory === 'administrative') {
            $services = [
                ['label' => $person->position ?? 'Personal Administrativo', 'icon' => 'Building'],
                ['label' => $person->department ?? 'Departamento', 'icon' => 'Folder'],
                ['label' => 'Control de Acceso', 'icon' => 'ShieldCheck'],
            ];
        }

        return $services;
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

        // Estructura del vCard
        $vcard = "BEGIN:VCARD\n"
            . "VERSION:3.0\n"
            . "FN:{$person->full_name}\n"
            . "ORG:" . ($person->company ? $person->company->name : 'Solubase') . "\n"
            . "TITLE:{$person->position}\n"
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
        if ($url === 'Ing. Herbert Diaz') {
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
            'data' => [
                'name' => $person->full_name,
                'document_id' => $person->document_id,
                'email' => $person->email,
                'phone' => $person->phone,
                'company' => $person->company ? $person->company->name : null,
                'position' => $person->position,
                'bio' => $person->bio,
                'photo_url' => $person->photo_url,
                'category' => $person->category_label,
                'subcategory' => $person->subcategory_label,
                'grade_level' => $person->grade_level_label,
                'academic_year' => $person->academic_year,
                'average_grade' => $person->average_grade,
                'emergency_contact' => $person->emergency_contact_name,
                'emergency_phone' => $person->emergency_phone,
                'location' => $person->company ? $person->company->location : null,
            ]
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
