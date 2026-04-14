<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'name' => 'required|string|max:255|unique:companies',
            'type' => 'required|in:company,school',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        // Generar slug automáticamente desde el nombre
        $validated['slug'] = Str::slug($request->name);

        Company::create($validated);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Compañía creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:companies,name,' . $company->id,
            'type' => 'required|in:company,school',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        // Actualizar slug si el nombre cambió
        if ($company->name !== $request->name) {
            $validated['slug'] = Str::slug($request->name);
        }

        $company->update($validated);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Compañía actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        // Verificar si tiene personas asociadas
        if ($company->persons()->count() > 0) {
            return redirect()->route('admin.companies.index')
                ->with('error', 'No se puede eliminar la compañía porque tiene personas asociadas.');
        }

        $company->delete();

        return redirect()->route('admin.companies.index')
            ->with('success', 'Compañía eliminada exitosamente.');
    }
}
