<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\Company;
use App\Models\NFCCard;
use App\Models\AccessLog;
use Illuminate\Support\Str;

class AccessControlController extends Controller
{
    public function index()
    {
        $persons = Person::with('company')->orderBy('created_at', 'desc')->get();
        $companies = Company::where('is_active', true)->get();
        $availableCards = NFCCard::whereNull('person_id')->where('status', 'active')->get();
        $accessLogs = AccessLog::with(['person', 'company'])->orderBy('access_time', 'desc')->limit(100)->get();
        
        return view('admin.access-control.index', compact('persons', 'companies', 'availableCards', 'accessLogs'));
    }

    public function create()
    {
        $companies = Company::where('is_active', true)->get();
        $availableCards = NFCCard::whereNull('person_id')->where('status', 'active')->get();
        
        return view('admin.access-control.create', compact('companies', 'availableCards'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:employee,visitor',
            'company_id' => 'required|exists:companies,id',
            'email' => 'nullable|email|unique:persons',
            'document_id' => 'nullable|string|unique:persons',
            'phone' => 'nullable|string',
            'position' => 'nullable|string',
            'department' => 'nullable|string',
            'bio' => 'nullable|string',
            'companions' => 'nullable|integer|min:0',
            'visit_reason' => 'nullable|string',
            'card_id' => 'nullable|exists:nfc_cards,id'
        ]);

        $person = Person::create([
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

        if ($request->card_id) {
            $card = NFCCard::find($request->card_id);
            $card->person_id = $person->id;
            $card->assigned_at = now();
            $card->save();
            
            $person->nfc_card_id = $card->card_code;
            $person->save();
        }

        return redirect()->route('admin.access-control.index')->with('success', 'Persona registrada correctamente');
    }

    public function edit($id)
    {
        $person = Person::findOrFail($id);
        $companies = Company::where('is_active', true)->get();
        $availableCards = NFCCard::whereNull('person_id')->where('status', 'active')->get();
        
        return view('admin.access-control.edit', compact('person', 'companies', 'availableCards'));
    }

    public function update(Request $request, $id)
    {
        $person = Person::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:employee,visitor',
            'company_id' => 'required|exists:companies,id',
            'email' => 'nullable|email|unique:persons,email,' . $id,
            'document_id' => 'nullable|string|unique:persons,document_id,' . $id,
            'phone' => 'nullable|string',
            'position' => 'nullable|string',
            'department' => 'nullable|string',
            'bio' => 'nullable|string',
            'companions' => 'nullable|integer|min:0',
            'visit_reason' => 'nullable|string',
            'card_id' => 'nullable|exists:nfc_cards,id'
        ]);

        $person->update([
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
        ]);

        if ($request->card_id && !$person->nfc_card_id) {
            $card = NFCCard::find($request->card_id);
            $card->person_id = $person->id;
            $card->assigned_at = now();
            $card->save();
            
            $person->nfc_card_id = $card->card_code;
            $person->save();
        }

        return redirect()->route('admin.access-control.index')->with('success', 'Persona actualizada correctamente');
    }

    public function destroy($id)
    {
        $person = Person::findOrFail($id);
        
        if ($person->nfc_card_id) {
            NFCCard::where('card_code', $person->nfc_card_id)->update(['person_id' => null]);
        }
        
        $person->delete();
        
        return redirect()->route('admin.access-control.index')->with('success', 'Persona eliminada correctamente');
    }

    public function assignNFC(Request $request, $id)
    {
        $request->validate([
            'card_id' => 'required|exists:nfc_cards,id'
        ]);

        $person = Person::findOrFail($id);
        $card = NFCCard::findOrFail($request->card_id);

        if ($card->person_id) {
            return back()->with('error', 'Esta tarjeta ya está asignada a otra persona');
        }

        $card->person_id = $person->id;
        $card->assigned_at = now();
        $card->save();

        $person->nfc_card_id = $card->card_code;
        if (!$person->bio_url) {
            $person->bio_url = Person::generateUniqueBioUrl();
        }
        $person->save();

        return redirect()->route('admin.access-control.index')->with('success', "Tarjeta asignada a {$person->name}");
    }

    public function unassignNFC($id)
    {
        $person = Person::findOrFail($id);
        
        if ($person->nfc_card_id) {
            $card = NFCCard::where('card_code', $person->nfc_card_id)->first();
            if ($card) {
                $card->person_id = null;
                $card->assigned_at = null;
                $card->save();
            }
            $person->nfc_card_id = null;
            $person->save();
        }

        return redirect()->route('admin.access-control.index')->with('success', 'Tarjeta desvinculada correctamente');
    }
}