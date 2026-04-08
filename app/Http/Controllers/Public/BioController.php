<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\Request;

class BioController extends Controller
{
    /**
     * Mostrar la biografía pública de una persona
     */
    public function show($url)
    {
        // Buscar por bio_url (con o sin el prefijo 'bio/')
        $person = Person::where('bio_url', 'bio/' . $url)
            ->orWhere('bio_url', $url)
            ->where('is_active', true)
            ->firstOrFail();
        
        // Registrar vista (opcional)
        // $person->increment('views_count');
        
        // Obtener últimos accesos (últimos 5)
        $recentAccess = $person->accessLogs()
            ->with('company')
            ->orderBy('access_time', 'desc')
            ->limit(5)
            ->get();
        
        return view('public.bio', compact('person', 'recentAccess'));
    }
}
