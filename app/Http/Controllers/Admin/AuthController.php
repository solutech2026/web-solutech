<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login de administrador
     */
    public function showLoginForm()
    {
        // Si ya está autenticado y tiene permisos de admin, redirigir al dashboard
        if (Auth::check() && Auth::user()->hasPermissionTo('access admin panel')) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Procesar el login de administrador
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();
            
            // Verificar si tiene permiso para acceder al panel de admin
            if ($user->hasPermissionTo('access admin panel')) {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }
            
            // Si no tiene permisos, cerrar sesión y mostrar error
            Auth::logout();
            return back()->with('error', 'Acceso denegado. No tienes permisos de administrador.');
        }

        return back()->with('error', 'Credenciales incorrectas.')
            ->withInput($request->only('email'));
    }

    /**
     * Cerrar sesión del administrador
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login')->with('success', 'Sesión cerrada correctamente.');
    }
}