<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NfcCard;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NFCCardController extends Controller
{
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $card = NfcCard::findOrFail($id);
            $person = Person::findOrFail($request->person_id);

            // Verificar si la tarjeta ya está asignada
            if ($card->assigned_to) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Esta tarjeta ya está asignada a otra persona');
            }

            // Verificar si la persona ya tiene una tarjeta asignada
            if ($person->nfc_card_id) {
                // Liberar la tarjeta anterior
                $oldCard = NfcCard::where('id', $person->nfc_card_id)->first();
                if ($oldCard) {
                    $oldCard->assigned_to = null;
                    $oldCard->assigned_at = null;
                    $oldCard->save();
                }
            }

            // Asignar tarjeta a la persona
            $card->assigned_to = $person->id;
            $card->assigned_at = now();
            $card->save();

            // Actualizar persona
            $person->nfc_card_id = $card->id;
            $person->last_access_at = null;
            $person->save();

            DB::commit();

            return redirect()
                ->route('admin.nfc-cards.index')
                ->with('success', "Tarjeta {$card->card_code} asignada a {$person->full_name}");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al asignar la tarjeta: ' . $e->getMessage());
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
            
            // Liberar tarjeta
            $card->assigned_to = null;
            $card->assigned_at = null;
            $card->save();

            // Actualizar persona
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
            
            // Si la tarjeta estaba asignada, desasignar de la persona
            if ($card->assigned_to) {
                $person = Person::find($card->assigned_to);
                if ($person) {
                    $person->nfc_card_id = null;
                    $person->save();
                }
            }
            
            $card->delete();

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Tarjeta eliminada correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la tarjeta: ' . $e->getMessage()
            ], 500);
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

        // Cabeceras
        fputcsv($handle, [
            'ID', 'Código', 'UID', 'Estado', 'Asignada a', 'Empresa/Colegio',
            'Fecha Registro', 'Fecha Asignación', 'Notas'
        ]);

        // Datos
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
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
    }

    /**
     * Mostrar configuración del lector NFC
     */
    public function readerConfig()
    {
        return view('admin.nfc-cards.reader');
    }

    /**
     * Guardar configuración del lector NFC
     */
    public function saveReaderConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'connection_type' => 'required|in:wired,wireless,network',
            'com_port' => 'nullable|string',
            'baud_rate' => 'nullable|string',
            'ip_address' => 'nullable|ip',
            'port' => 'nullable|integer',
            'protocol' => 'nullable|string',
            'api_key' => 'nullable|string',
            'wireless_method' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $config = [
            'connection_type' => $request->connection_type,
            'wireless_method' => $request->wireless_method,
            'last_updated' => now()->toDateTimeString()
        ];

        if ($request->connection_type === 'wired') {
            $config['com_port'] = $request->com_port;
            $config['baud_rate'] = $request->baud_rate;
        } elseif ($request->connection_type === 'network') {
            $config['ip_address'] = $request->ip_address;
            $config['port'] = $request->port;
            $config['protocol'] = $request->protocol;
            $config['api_key'] = $request->api_key;
        }

        session(['nfc_reader_config' => $config]);

        return response()->json(['success' => true, 'message' => 'Configuración guardada correctamente']);
    }

    /**
     * Obtener configuración del lector NFC
     */
    public function getReaderConfig()
    {
        $defaultConfig = [
            'connection_type' => 'wired',
            'wireless_method' => 'qrcode',
            'com_port' => '',
            'baud_rate' => '115200',
            'ip_address' => '',
            'port' => '8080',
            'protocol' => 'tcp'
        ];

        $savedConfig = session('nfc_reader_config', []);
        
        return response()->json(array_merge($defaultConfig, $savedConfig));
    }

    /**
     * Escanear dispositivos en la red local
     */
    public function scanNetworkDevices(Request $request)
    {
        try {
            $devices = [];
            
            // 1. Dispositivos guardados en caché
            $cachedDevices = Cache::get('nfc_network_devices', []);
            $devices = array_merge($devices, $cachedDevices);
            
            // 2. Dispositivos de prueba para demostración
            if (empty($devices)) {
                $devices = $this->getDemoDevices();
            }
            
            // Guardar en caché
            Cache::put('nfc_network_devices', $devices, now()->addSeconds(30));
            
            return response()->json([
                'success' => true,
                'devices' => $devices,
                'count' => count($devices),
                'scan_time' => now()->toDateTimeString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en scanNetworkDevices: ' . $e->getMessage());
            
            return response()->json([
                'success' => true,
                'devices' => $this->getDemoDevices(),
                'count' => count($this->getDemoDevices()),
                'scan_time' => now()->toDateTimeString(),
                'warning' => 'Error en escaneo real: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener dispositivos de demostración para pruebas
     */
    private function getDemoDevices()
    {
        return [
            [
                'id' => 'demo_device_1',
                'name' => 'Lector NFC Oficina 1 (Demo)',
                'ip' => '192.168.1.100',
                'port' => 8080,
                'type' => 'ACR1252U',
                'status' => 'online',
                'signal' => 95,
                'discovery_method' => 'Demo',
                'is_demo' => true
            ],
            [
                'id' => 'demo_device_2',
                'name' => 'Lector NFC Puerta Principal (Demo)',
                'ip' => '192.168.1.101',
                'port' => 8080,
                'type' => 'ACR122U',
                'status' => 'online',
                'signal' => 78,
                'discovery_method' => 'Demo',
                'is_demo' => true
            ],
            [
                'id' => 'demo_device_3',
                'name' => 'Móvil Android - Recepción (Demo)',
                'ip' => '192.168.1.50',
                'port' => 9090,
                'type' => 'Android NFC',
                'status' => 'online',
                'signal' => 88,
                'is_mobile' => true,
                'discovery_method' => 'Demo',
                'is_demo' => true
            ]
        ];
    }

    /**
     * Obtener dispositivos emparejados
     */
    public function getPairedDevices()
    {
        $paired = session('paired_nfc_devices', []);
        $devices = [];
        
        foreach ($paired as $deviceId) {
            $device = $this->getStoredDeviceDetails($deviceId);
            if ($device) {
                $devices[] = $device;
            }
        }
        
        return response()->json([
            'success' => true,
            'devices' => $devices
        ]);
    }

    /**
     * Emparejar un dispositivo
     */
    public function pairDevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'device_name' => 'required|string',
            'device_ip' => 'nullable|ip',
            'device_port' => 'nullable|integer',
            'device_type' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Datos inválidos'], 422);
        }
        
        $paired = session('paired_nfc_devices', []);
        
        if (!in_array($request->device_id, $paired)) {
            $paired[] = $request->device_id;
            session(['paired_nfc_devices' => $paired]);
        }
        
        // Guardar detalles del dispositivo
        $deviceDetails = session('device_details', []);
        $deviceDetails[$request->device_id] = [
            'id' => $request->device_id,
            'name' => $request->device_name,
            'ip' => $request->device_ip,
            'port' => $request->device_port,
            'type' => $request->device_type ?? 'NFC Reader',
            'paired_at' => now()->toDateTimeString()
        ];
        session(['device_details' => $deviceDetails]);
        
        return response()->json([
            'success' => true,
            'message' => 'Dispositivo emparejado correctamente'
        ]);
    }

    /**
     * Desemparejar un dispositivo
     */
    public function unpairDevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Datos inválidos'], 422);
        }
        
        $paired = session('paired_nfc_devices', []);
        $paired = array_filter($paired, function($id) use ($request) {
            return $id !== $request->device_id;
        });
        session(['paired_nfc_devices' => array_values($paired)]);
        
        $deviceDetails = session('device_details', []);
        unset($deviceDetails[$request->device_id]);
        session(['device_details' => $deviceDetails]);
        
        return response()->json([
            'success' => true,
            'message' => 'Dispositivo desemparejado correctamente'
        ]);
    }

    /**
     * Obtener detalles de dispositivo almacenado
     */
    private function getStoredDeviceDetails($deviceId)
    {
        $deviceDetails = session('device_details', []);
        return $deviceDetails[$deviceId] ?? null;
    }

    /**
     * Probar conexión con lector USB
     */
    public function testWiredConnection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'com_port' => 'required|string',
            'baud_rate' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Datos incompletos'], 422);
        }

        // Simular conexión exitosa para pruebas
        return response()->json([
            'success' => true,
            'message' => 'Conexión exitosa',
            'model' => 'ACR122U',
            'firmware' => 'v2.0.1'
        ]);
    }

    /**
     * Probar conexión de red
     */
    public function testNetworkConnection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ip_address' => 'required|ip',
            'port' => 'required|integer',
            'protocol' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Datos incompletos'], 422);
        }

        // Simular conexión exitosa para pruebas
        return response()->json([
            'success' => true,
            'message' => 'Conexión exitosa',
            'device' => [
                'id' => md5($request->ip_address . $request->port),
                'name' => 'Lector NFC',
                'ip' => $request->ip_address,
                'port' => $request->port,
                'type' => 'Network Reader'
            ]
        ]);
    }

    /**
     * Leer tarjeta NFC
     */
    public function readCard(Request $request)
    {
        try {
            // Simular lectura de tarjeta para pruebas
            return response()->json([
                'success' => true,
                'card_code' => 'NFC-' . strtoupper(Str::random(8)),
                'card_uid' => implode(':', array_map(function() {
                    return str_pad(dechex(rand(0, 255)), 2, '0', STR_PAD_LEFT);
                }, range(1, 7)))
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al leer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar código QR para conexión móvil
     */
    public function generateQRCode(Request $request)
    {
        $sessionId = session()->getId();
        $timestamp = now()->timestamp;
        
        $qrData = json_encode([
            'session_id' => $sessionId,
            'timestamp' => $timestamp,
            'server_url' => url('/api/nfc/connect'),
            'type' => 'nfc_reader'
        ]);
        
        return response()->json([
            'success' => true,
            'qr_data' => base64_encode($qrData),
            'session_id' => $sessionId
        ]);
    }
}
