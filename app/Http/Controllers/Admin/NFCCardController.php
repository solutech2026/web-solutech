<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NfcCard;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NFCCardController extends Controller
{
    // ============================================
    // GESTIÓN DE TARJETAS NFC
    // ============================================

    /**
     * Mostrar lista de tarjetas NFC
     */
    public function index()
    {
        $cards = NfcCard::with('assignedPerson.company')->orderBy('created_at', 'desc')->get();
        return view('admin.nfc-cards.index', compact('cards'));
    }

    /**
     * Mostrar formulario para crear nueva tarjeta
     */
    public function create(Request $request)
    {
        $cardCode = $request->get('card_code');
        $cardUid = $request->get('card_uid');
        return view('admin.nfc-cards.create', compact('cardCode', 'cardUid'));
    }

    /**
     * Registrar una nueva tarjeta NFC
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_code' => 'required|string|max:255|unique:nfc_cards,card_code',
            'card_uid' => 'nullable|string|max:255|unique:nfc_cards,card_uid',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $card = NfcCard::create([
                'card_code' => strtoupper($request->card_code),
                'card_uid' => $request->card_uid ? strtoupper($request->card_uid) : null,
                'notes' => $request->notes,
                'status' => 'active'
            ]);

            return redirect()
                ->route('admin.nfc-cards.index')
                ->with('success', "Tarjeta {$card->card_code} registrada correctamente");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar la tarjeta: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalles de una tarjeta (API)
     */
    public function show($id)
    {
        $card = NfcCard::with('assignedPerson.company')->findOrFail($id);
        
        return response()->json([
            'id' => $card->id,
            'card_code' => $card->card_code,
            'card_uid' => $card->card_uid,
            'notes' => $card->notes,
            'status' => $card->status,
            'assigned_to' => $card->assigned_to,
            'assigned_at' => $card->assigned_at,
            'created_at' => $card->created_at,
            'assigned_person' => $card->assignedPerson ? [
                'id' => $card->assignedPerson->id,
                'name' => $card->assignedPerson->name,
                'lastname' => $card->assignedPerson->lastname,
                'full_name' => $card->assignedPerson->full_name,
                'document_id' => $card->assignedPerson->document_id,
                'email' => $card->assignedPerson->email,
                'company' => $card->assignedPerson->company ? [
                    'id' => $card->assignedPerson->company->id,
                    'name' => $card->assignedPerson->company->name
                ] : null
            ] : null
        ]);
    }

    /**
     * Mostrar formulario para asignar tarjeta a persona
     */
    public function assignForm($id)
    {
        $card = NfcCard::findOrFail($id);
        $persons = Person::with('company')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('admin.nfc-cards.assign', compact('card', 'persons'));
    }

    /**
     * Asignar tarjeta a una persona
     */
    public function assign(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'person_id' => 'required|exists:persons,id'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Persona inválida'], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $card = NfcCard::findOrFail($id);
            $person = Person::findOrFail($request->person_id);

            if ($card->assigned_to) {
                DB::rollBack();
                $message = 'Esta tarjeta ya está asignada a otra persona';
                return $request->ajax() 
                    ? response()->json(['success' => false, 'message' => $message], 400)
                    : redirect()->back()->with('error', $message);
            }

            if ($person->nfc_card_id) {
                $oldCard = NfcCard::find($person->nfc_card_id);
                if ($oldCard) {
                    $oldCard->assigned_to = null;
                    $oldCard->assigned_at = null;
                    $oldCard->save();
                }
            }

            $card->assigned_to = $person->id;
            $card->assigned_at = now();
            $card->save();

            $person->nfc_card_id = $card->id;
            $person->last_access_at = null;
            $person->save();

            DB::commit();

            $message = "Tarjeta {$card->card_code} asignada a {$person->full_name}";

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            return redirect()
                ->route('admin.nfc-cards.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            $message = 'Error al asignar la tarjeta: ' . $e->getMessage();
            
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $message], 500)
                : redirect()->back()->with('error', $message);
        }
    }

    /**
     * Desasignar tarjeta de una persona
     */
    public function unassign($id)
    {
        try {
            DB::beginTransaction();

            $card = NfcCard::findOrFail($id);

            if (!$card->assigned_to) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Esta tarjeta no está asignada a ninguna persona');
            }

            $person = Person::find($card->assigned_to);
            
            $card->assigned_to = null;
            $card->assigned_at = null;
            $card->save();

            if ($person) {
                $person->nfc_card_id = null;
                $person->save();
            }

            DB::commit();

            return redirect()
                ->route('admin.nfc-cards.index')
                ->with('success', "Tarjeta {$card->card_code} desasignada correctamente");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al desasignar la tarjeta: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar una tarjeta NFC
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $card = NfcCard::findOrFail($id);
            
            if ($card->assigned_to) {
                $person = Person::find($card->assigned_to);
                if ($person) {
                    $person->nfc_card_id = null;
                    $person->save();
                }
            }
            
            $card->delete();

            DB::commit();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tarjeta eliminada correctamente'
                ]);
            }
            
            return redirect()
                ->route('admin.nfc-cards.index')
                ->with('success', 'Tarjeta eliminada correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la tarjeta: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error al eliminar la tarjeta: ' . $e->getMessage());
        }
    }

    /**
     * Exportar tarjetas a CSV
     */
    public function export(Request $request)
    {
        $query = NfcCard::with('assignedPerson.company');

        if ($request->status == 'assigned') {
            $query->whereNotNull('assigned_to');
        } elseif ($request->status == 'unassigned') {
            $query->whereNull('assigned_to');
        }

        $cards = $query->get();

        $filename = 'tarjetas_nfc_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');

        fputcsv($handle, [
            'ID', 'Código', 'UID', 'Estado', 'Asignada a', 'Empresa/Colegio',
            'Fecha Registro', 'Fecha Asignación', 'Notas'
        ]);

        foreach ($cards as $card) {
            fputcsv($handle, [
                $card->id,
                $card->card_code,
                $card->card_uid ?? '',
                $card->assigned_to ? 'Asignada' : 'Sin asignar',
                $card->assignedPerson ? $card->assignedPerson->full_name : '',
                $card->assignedPerson && $card->assignedPerson->company ? $card->assignedPerson->company->name : '',
                $card->created_at->format('d/m/Y H:i'),
                $card->assigned_at ? $card->assigned_at->format('d/m/Y H:i') : '',
                $card->notes ?? ''
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->withHeaders([
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
    }
}
