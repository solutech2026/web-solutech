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
     * Muestra la biografía pública de una persona usando bio_url
     */
    public function show($url)
    {
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
                ['label' => $person->position ?? 'Docente', 'icon' => 'ChalkboardUser'],
                ['label' => 'Tipo: ' . $person->teacher_type_label, 'icon' => 'UserGraduate'],
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
        $person = Person::where('bio_url', $url)
            ->with('company')
            ->firstOrFail();

        // Estructura del vCard
        $vcard = "BEGIN:VCARD\n"
            . "VERSION:3.0\n"
            . "FN:{$person->full_name}\n"
            . "ORG:" . ($person->company ? $person->company->name : 'Solubase') . "\n"
            . "TITLE:{$person->position}\n"
            . "TEL;TYPE=CELL:{$person->phone}\n"
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
     * Obtener datos de la biografía en formato JSON (para API)
     */
    public function getData($url)
    {
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
            ]
        ]);
    }

    /**
     * Redireccionar a la página de la persona
     */
    public function redirectToProfile($url)
    {
        $person = Person::where('bio_url', $url)->firstOrFail();
        return redirect()->route('admin.persons.show', $person->id);
    }
}
