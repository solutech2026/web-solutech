<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NFCCard;
use App\Models\Person;

class NFCCardController extends Controller
{
    /**
     * Mostrar lista de tarjetas NFC
     */
    public function index()
    {
        $cards = NFCCard::with('person.company')->orderBy('created_at', 'desc')->get();
        return view('admin.nfc-cards.index', compact('cards'));
    }

    /**
     * Mostrar formulario para crear nueva tarjeta
     */
    public function create()
    {
        return view('admin.nfc-cards.create');
    }

    /**
     * Registrar una nueva tarjeta NFC
     */
    public function store(Request $request)
    {
        $request->validate([
            'card_code' => 'required|string|unique:nfc_cards,card_code',
            'notes' => 'nullable|string'
        ]);

        NFCCard::create([
            'card_code' => strtoupper($request->card_code),
            'notes' => $request->notes,
            'status' => 'active'
        ]);

        return redirect()->route('admin.nfc-cards.index')->with('success', 'Tarjeta registrada correctamente');
    }

    /**
     * Mostrar detalles de una tarjeta (API)
     */
    public function show($id)
    {
        $card = NFCCard::with('person.company')->findOrFail($id);
        return response()->json($card);
    }

    /**
     * Mostrar formulario para asignar tarjeta a persona
     */
    public function assignForm($id)
    {
        $card = NFCCard::findOrFail($id);
        $persons = Person::with('company')->where('is_active', true)->orderBy('name')->get();
        return view('admin.nfc-cards.assign', compact('card', 'persons'));
    }

    /**
     * Asignar tarjeta a una persona
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'person_id' => 'required|exists:persons,id'
        ]);

        $card = NFCCard::findOrFail($id);
        $person = Person::findOrFail($request->person_id);

        // Verificar si la tarjeta ya está asignada
        if ($card->person_id) {
            return back()->with('error', 'Esta tarjeta ya está asignada a otra persona');
        }

        // Asignar tarjeta a la persona
        $card->person_id = $person->id;
        $card->assigned_at = now();
        $card->save();

        // Actualizar persona
        $person->nfc_card_id = $card->card_code;
        if (!$person->bio_url) {
            $person->bio_url = Person::generateUniqueBioUrl();
        }
        $person->save();

        return redirect()->route('admin.nfc-cards.index')->with('success', "Tarjeta {$card->card_code} asignada a {$person->name}");
    }

    /**
     * Eliminar una tarjeta NFC
     */
    public function destroy($id)
    {
        $card = NFCCard::findOrFail($id);
        
        // Si la tarjeta estaba asignada, desasignar de la persona
        if ($card->person_id) {
            $person = Person::find($card->person_id);
            if ($person) {
                $person->nfc_card_id = null;
                $person->save();
            }
        }
        
        $card->delete();
        
        return redirect()->route('admin.nfc-cards.index')->with('success', 'Tarjeta eliminada correctamente');
    }
}
