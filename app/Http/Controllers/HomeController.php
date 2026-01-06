<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Mostrar la página de inicio.
     */
    public function Index()
    {
        return Inertia::render('Home/Home', [
            'title' => 'Home Page',
            'lastUpdated' => '2026-01-01',
            // Puedes pasar más datos aquí
        ]);
    }

    /**
     *  Mostrar la pagina de Terms
     */
    public function terms()
    {
        return Inertia::render('Terms/Terms', [
            'title' => 'Términos y Condiciones',
            'lastUpdated' => '2026-01-01',
            // Puedes pasar más datos aquí
        ]);
    }

    public function privacy()
    {
        return Inertia::render('Privacy/Privacy', [
            'title' => 'Política de Privacidad',
        ]);
    }

    public function legal()
    {
        return Inertia::render('LegalNotice/LegalNotice', [
            'title' => 'Aviso Legal',
        ]);
    }
}