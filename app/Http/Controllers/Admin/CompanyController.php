<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::latest()->paginate(15);
        return view('admin.companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Datos básicos
            'name' => 'required|string|max:255|unique:companies',
            'type' => 'required|in:company,school,ngo_rescue,government',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            
            // Campos adicionales comunes
            'tax_id' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            
            // Logo
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Campos específicos para Empresa
            'industry' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:50',
            
            // Campos específicos para Colegio
            'levels' => 'nullable|array',
            'levels.*' => 'string',
            'shifts' => 'nullable|array',
            'shifts.*' => 'string',
            'principal' => 'nullable|string|max:255',
            
            // Campos específicos para ONG de Rescate
            'rescue_type' => 'nullable|string|max:100',
            'emergency_line' => 'nullable|string|max:50',
            'coverage_area' => 'nullable|string',
            
            // Campos específicos para Gobierno
            'government_level' => 'nullable|in:national,regional,municipal,parish',
            'government_branch' => 'nullable|in:executive,legislative,judicial,citizen,electoral',
            'government_entity_type' => 'nullable|string|max:100',
        ]);

        // Generar slug automáticamente desde el nombre
        $validated['slug'] = Str::slug($request->name);

        // Manejar el logo
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('companies/logos', 'public');
            $validated['logo'] = $logoPath;
        }

        // Convertir arrays a JSON
        if (isset($validated['levels'])) {
            $validated['levels'] = json_encode($validated['levels']);
        }
        if (isset($validated['shifts'])) {
            $validated['shifts'] = json_encode($validated['shifts']);
        }

        Company::create($validated);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Institución creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        // Decodificar JSON para los campos array
        if ($company->levels) {
            $company->levels = json_decode($company->levels, true);
        }
        if ($company->shifts) {
            $company->shifts = json_decode($company->shifts, true);
        }
        
        return view('admin.companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            // Datos básicos
            'name' => 'required|string|max:255|unique:companies,name,' . $company->id,
            'type' => 'required|in:company,school,ngo_rescue,government',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            
            // Campos adicionales comunes
            'tax_id' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            
            // Logo
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Campos específicos para Empresa
            'industry' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:50',
            
            // Campos específicos para Colegio
            'levels' => 'nullable|array',
            'levels.*' => 'string',
            'shifts' => 'nullable|array',
            'shifts.*' => 'string',
            'principal' => 'nullable|string|max:255',
            
            // Campos específicos para ONG de Rescate
            'rescue_type' => 'nullable|string|max:100',
            'emergency_line' => 'nullable|string|max:50',
            'coverage_area' => 'nullable|string',
            
            // Campos específicos para Gobierno
            'government_level' => 'nullable|in:national,regional,municipal,parish',
            'government_branch' => 'nullable|in:executive,legislative,judicial,citizen,electoral',
            'government_entity_type' => 'nullable|string|max:100',
        ]);

        // Actualizar slug si el nombre cambió
        if ($company->name !== $request->name) {
            $validated['slug'] = Str::slug($request->name);
        }

        // Manejar el logo
        if ($request->hasFile('logo')) {
            // Eliminar logo anterior
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            $logoPath = $request->file('logo')->store('companies/logos', 'public');
            $validated['logo'] = $logoPath;
        }

        // Manejar eliminación de logo
        if ($request->has('remove_logo') && $request->remove_logo == '1') {
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = null;
        }

        // Convertir arrays a JSON
        if (isset($validated['levels'])) {
            $validated['levels'] = json_encode($validated['levels']);
        } else {
            $validated['levels'] = null;
        }
        
        if (isset($validated['shifts'])) {
            $validated['shifts'] = json_encode($validated['shifts']);
        } else {
            $validated['shifts'] = null;
        }

        $company->update($validated);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Institución actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        // Verificar si tiene personas asociadas
        if ($company->persons()->count() > 0) {
            return redirect()->route('admin.companies.index')
                ->with('error', 'No se puede eliminar la institución porque tiene personas asociadas.');
        }

        // Eliminar logo si existe
        if ($company->logo && Storage::disk('public')->exists($company->logo)) {
            Storage::disk('public')->delete($company->logo);
        }

        $company->delete();

        return redirect()->route('admin.companies.index')
            ->with('success', 'Institución eliminada exitosamente.');
    }

    /**
     * Get type label for display
     */
    public function getTypeLabel($type)
    {
        $labels = [
            'company' => 'Empresa',
            'school' => 'Colegio',
            'ngo_rescue' => 'ONG de Rescate',
            'government' => 'Organización Gubernamental'
        ];
        return $labels[$type] ?? $type;
    }
}
