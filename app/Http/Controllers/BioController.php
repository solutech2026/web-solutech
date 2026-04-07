<?php

namespace App\Http\Controllers;

use App\Models\BioProfile;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BioController extends Controller
{
    /**
     * Muestra la vista de la Bio con datos estáticos.
     */
    public function show()
    {
        // Datos estáticos de tu perfil
        $profile = [
            'slug' => 'herbert',
            'name' => 'Ing. Herbert Diaz',
            'role' => 'Administración de Sistemas & Redes',
            'summary' => 'Especialista en infraestructura tecnológica, servidores (Cloud/Local) y soporte integral niveles 1, 2 y 3. Gestión de bases de datos, políticas de respaldo y desarrollo de soluciones a medida para garantizar la continuidad operativa.',
            'phone' => '584124714588',
            'email' => 'solutech24@outlook.com', // Corregido el 'q' por '@'
            'services' => [
                ['label' => 'Soporte Nivel 1, 2 y 3', 'icon' => 'ShieldCheck'],
                ['label' => 'Servidores Cloud y Locales', 'icon' => 'Cloud'],
                ['label' => 'Administración de Redes', 'icon' => 'Network'],
                ['label' => 'Bases de Datos y Respaldos', 'icon' => 'Database'],
                ['label' => 'Desarrollo de Software', 'icon' => 'Code2'],
                ['label' => 'Infraestructura TI', 'icon' => 'Server'],
            ],
            'photo_path' => '/img/img_herbert.png'
        ];

        return Inertia::render('Bio/Show', [
            'profile' => $profile
        ]);
    }

    /**
     * Genera y descarga el archivo vCard dinámicamente.
     */
    public function downloadVCard()
    {
        // Datos estáticos de tu perfil (mismos que en show)
        $name = 'Ing. Herbert Diaz';
        $role = 'Ingeniero de Sistemas';
        $phone = '584124714588';
        $email = 'solutech24@outlook.com';

        // Estructura del vCard
        $vcard = "BEGIN:VCARD\n"
            . "VERSION:3.0\n"
            . "FN:{$name}\n"
            . "ORG:Solutech\n"
            . "TITLE:{$role}\n"
            . "TEL;TYPE=CELL:{$phone}\n"
            . "EMAIL:{$email}\n"
            . "END:VCARD";

        $filename = str_replace(' ', '-', strtolower($name)) . ".vcf";

        return response($vcard)
            ->header('Content-Type', 'text/vcard')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
