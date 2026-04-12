<?php
// app/Http/Controllers/ContactController.php

namespace App\Http\Controllers;

use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        return Inertia::render('Contact/Contact', [
            'pageTitle' => 'Contacto | SoluTech',
            'metaDescription' => 'Contáctanos para soluciones en administración de sistemas, infraestructura IT y servicios tecnológicos',
            'contactInfo' => [
                'email' => 'solutech24@outlook.com',
                'phone' => '+58 412 471 45 88',
                'address' => 'Caracas, Venezuela',
                'businessHours' => 'Lunes a Viernes 9:00 AM - 6:00 PM'
            ],
            'services' => [
                ['id' => 'general', 'name' => 'Consulta General'],
                ['id' => 'infrastructure', 'name' => 'Infraestructura IT'],
                ['id' => 'cloud', 'name' => 'Soluciones en la Nube'],
                ['id' => 'security', 'name' => 'Ciberseguridad'],
                ['id' => 'support', 'name' => 'Soporte Técnico'],
                ['id' => 'network', 'name' => 'Redes y Conectividad'],
                ['id' => 'development', 'name' => 'Desarrollo de Software'],
                ['id' => 'consulting', 'name' => 'Consultoría IT']
            ]
        ]);
    }
}
