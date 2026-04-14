<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Person;
use App\Models\Company;
use App\Models\NfcCard;
use App\Models\AccessLog;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Dashboard principal
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'admin_count' => User::role(['super-admin', 'admin'])->count(),
            'total_persons' => Person::count(),
            'total_employees' => Person::where('type', 'employee')->count(),
            'total_visitors' => Person::where('type', 'visitor')->count(),
            'active_cards' => NfcCard::where('status', 'active')->count(),
            'access_today' => AccessLog::whereDate('access_time', today())->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Validación de acceso por NFC (API)
     */
    public function validateAccess(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        
        $cardCode = strtoupper($request->code);
        $card = NfcCard::where('card_code', $cardCode)
            ->where('status', 'active')
            ->with('person.company')
            ->first();
        
        // Tarjeta no válida
        if (!$card) {
            $this->logAccess(null, null, 'denied', 'Tarjeta no válida', $request);
            return $this->jsonResponse(false, 'Tarjeta no válida o inactiva');
        }
        
        // Persona no activa
        if (!$card->person || !$card->person->is_active) {
            $this->logAccess($card->person->id ?? null, $card->id, 'denied', 'Persona no activa', $request);
            return $this->jsonResponse(false, 'Persona no activa en el sistema');
        }
        
        // Acceso permitido
        $this->logAccess($card->person->id, $card->id, 'granted', null, $request);
        
        // Actualizar timestamps
        $card->update(['last_used_at' => now()]);
        $card->person->update(['last_access_at' => now()]);
        
        return $this->jsonResponse(true, 'Acceso permitido', [
            'name' => $card->person->name,
            'type' => $card->person->type,
            'company' => $card->person->company?->name,
            'bio_url' => $card->person->bio_url ? url($card->person->bio_url) : null
        ]);
    }

    /**
     * Gestión de Usuarios del Sistema
     */
    public function users()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|exists:roles,name'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado exitosamente');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|exists:roles,name'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado exitosamente');
    }

    public function deleteUser($id)
    {
        if ($id == Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', 'No puedes eliminar tu propio usuario');
        }
        
        User::findOrFail($id)->delete();
        
        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado exitosamente');
    }

    /**
     * Gestión de Personas
     */
    public function persons()
    {
        $persons = Person::with('company')->latest()->get();
        $companies = Company::where('is_active', true)->get();
        $availableCards = NfcCard::whereNull('person_id')->where('status', 'active')->get();
        
        return view('admin.persons.index', compact('persons', 'companies', 'availableCards'));
    }

    public function getPerson($id)
    {
        return response()->json(Person::findOrFail($id));
    }

    public function personDetail($id)
    {
        $person = Person::with(['company', 'accessLogs' => fn($q) => $q->latest()->limit(20)])
            ->findOrFail($id);
        
        $availableCards = NfcCard::whereNull('person_id')->where('status', 'active')->get();
        $companies = Company::where('is_active', true)->get();
        
        return view('admin.persons.detail', compact('person', 'availableCards', 'companies'));
    }

    public function storePerson(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:employee,visitor',
            'company_id' => 'required|exists:companies,id',
            'email' => 'nullable|email|unique:persons',
            'document_id' => 'nullable|string|unique:persons',
        ]);

        Person::create([
            'name' => $request->name,
            'type' => $request->type,
            'company_id' => $request->company_id,
            'email' => $request->email,
            'document_id' => $request->document_id,
            'phone' => $request->phone,
            'position' => $request->position,
            'department' => $request->department,
            'bio' => $request->bio,
            'companions' => $request->companions ?? 0,
            'visit_reason' => $request->visit_reason,
            'bio_url' => Person::generateUniqueBioUrl(),
            'is_active' => true
        ]);

        return redirect()->route('admin.persons.index')->with('success', 'Persona creada correctamente');
    }

    public function updatePerson(Request $request, $id)
    {
        $person = Person::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:employee,visitor',
            'company_id' => 'required|exists:companies,id',
            'email' => 'nullable|email|unique:persons,email,' . $id,
        ]);

        $person->update($request->only([
            'name', 'type', 'company_id', 'email', 'document_id', 'phone',
            'position', 'department', 'bio', 'companions', 'visit_reason'
        ]));

        return redirect()->route('admin.persons.index')->with('success', 'Persona actualizada correctamente');
    }

    public function deletePerson($id)
    {
        $person = Person::findOrFail($id);
        
        // Liberar tarjeta asignada
        if ($person->nfc_card_id) {
            NfcCard::where('card_code', $person->nfc_card_id)->update(['person_id' => null]);
        }
        
        $person->delete();
        
        return redirect()->route('admin.persons.index')->with('success', 'Persona eliminada correctamente');
    }

    public function assignNFCToPerson(Request $request, $id)
    {
        $request->validate(['card_id' => 'required|exists:nfc_cards,id']);

        $person = Person::findOrFail($id);
        $card = NfcCard::findOrFail($request->card_id);

        if ($card->person_id) {
            return back()->with('error', 'Esta tarjeta ya está asignada a otra persona');
        }

        $card->update([
            'person_id' => $person->id,
            'assigned_at' => now()
        ]);

        $person->update([
            'nfc_card_id' => $card->card_code,
            'bio_url' => $person->bio_url ?? Person::generateUniqueBioUrl()
        ]);

        return back()->with('success', "Tarjeta asignada a {$person->name}");
    }

    /**
     * Gestión de Tarjetas NFC
     */
    public function nfcCards()
    {
        $cards = NfcCard::with('person')->latest()->get();
        return view('admin.nfc-cards.index', compact('cards'));
    }

    public function storeNFCCard(Request $request)
    {
        $request->validate([
            'card_code' => 'required|string|unique:nfc_cards,card_code',
            'notes' => 'nullable|string'
        ]);

        NfcCard::create([
            'card_code' => strtoupper($request->card_code),
            'notes' => $request->notes,
            'status' => 'active'
        ]);

        return redirect()->route('admin.nfc-cards.index')->with('success', 'Tarjeta registrada correctamente');
    }

    public function deleteNFCCard($id)
    {
        $card = NfcCard::findOrFail($id);
        
        if ($card->person_id) {
            Person::where('id', $card->person_id)->update(['nfc_card_id' => null]);
        }
        
        $card->delete();
        
        return redirect()->route('admin.nfc-cards.index')->with('success', 'Tarjeta eliminada correctamente');
    }

    /**
     * Perfil de Usuario
     */
    public function profile()
    {
        return view('admin.profile.index');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user->update($request->only(['name', 'email', 'phone']));

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('uploads/avatars'), $avatarName);
            $user->avatar = '/uploads/avatars/' . $avatarName;
            $user->save();
        }

        return redirect()->route('admin.profile.index')->with('success', 'Perfil actualizado correctamente');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('admin.profile.index')->with('success', 'Contraseña cambiada correctamente');
    }

    /**
     * Configuración (placeholder)
     */
    public function settings()
    {
        return view('admin.settings.index');
    }

    public function updateSettings(Request $request)
    {
        // Implementar lógica de configuración
        return redirect()->route('admin.settings.index')->with('success', 'Configuración actualizada exitosamente');
    }

    // ============ MÉTODOS PRIVADOS ============

    /**
     * Registrar acceso en el log
     */
    private function logAccess($personId, $cardId, $status, $reason = null, $request)
    {
        AccessLog::create([
            'person_id' => $personId,
            'nfc_card_id' => $cardId,
            'access_type' => 'entry',
            'verification_method' => 'manual',
            'access_time' => now(),
            'status' => $status,
            'reason' => $reason,
            'gate' => $request->gate ?? 'Puerta Principal',
            'ip_address' => $request->ip()
        ]);
    }

    /**
     * Respuesta JSON estandarizada
     */
    private function jsonResponse($success, $message, $data = null)
    {
        $response = ['success' => $success, 'message' => $message];
        if ($data) {
            $response['person'] = $data;
        }
        return response()->json($response);
    }
}