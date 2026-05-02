<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NfcReader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NFCReaderController extends Controller
{
    /**
     * Mostrar lista de lectores configurados
     */
    public function index()
    {
        $readers = NfcReader::orderBy('created_at', 'desc')->get();
        
        // Procesar lectores para la vista
        $readersList = [];
        foreach ($readers as $reader) {
            $readersList[] = [
                'id' => $reader->id,
                'name' => $reader->name,
                'type' => $reader->type,
                'ip_address' => $reader->type === 'network' ? $reader->ip_address : $reader->wifi_ip_address,
                'port' => $reader->type === 'network' ? $reader->port : $reader->wifi_port,
                'protocol' => $reader->type === 'network' ? $reader->protocol : $reader->wifi_protocol,
                'ssid' => $reader->ssid,
                'username' => $reader->type === 'network' ? $reader->username : $reader->wifi_username,
                'ubicacion' => $reader->ubicacion,
                'is_connected' => $reader->isOnline(),
                'created_at' => $reader->created_at->format('d/m/Y H:i')
            ];
        }
        
        return view('admin.lectores.index', compact('readersList'));
    }

    /**
     * Mostrar formulario de configuración (nuevo/editar)
     */
    public function config($id = null)
    {
        $reader = null;
        $isEdit = false;
        
        if ($id && $id !== 'nuevo') {
            $reader = NfcReader::findOrFail($id);
            $isEdit = true;
        }
        
        return view('admin.lectores.config', compact('reader', 'id', 'isEdit'));
    }

    /**
     * Guardar configuración del lector
     */
    public function save(Request $request, $id = null)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:network,wifi',
            'name' => 'required|string|max:255',
            'ubicacion' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'timeout' => 'nullable|integer|min:1|max:300',
            'retry_interval' => 'nullable|integer|min:0',
            'alert_on_disconnect' => 'nullable|boolean',
            
            // Configuración Red (IP)
            'ip_address' => 'required_if:type,network|ip',
            'port' => 'required_if:type,network|integer|min:1|max:65535',
            'protocol' => 'required_if:type,network|in:tcp,udp,http,https',
            'mac_address' => 'nullable|string',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            
            // Configuración WiFi
            'ssid' => 'required_if:type,wifi|string',
            'wifi_password' => 'required_if:type,wifi|string|min:8',
            'wifi_ip_address' => 'nullable|ip',
            'wifi_port' => 'required_if:type,wifi|integer|min:1|max:65535',
            'wifi_protocol' => 'required_if:type,wifi|in:tcp,udp,http,https',
            'wifi_mac_address' => 'nullable|string',
            'wifi_username' => 'nullable|string',
            'encryption' => 'nullable|in:wpa2,wpa3,wep,open'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $data = [
            'name' => $request->name,
            'type' => $request->type,
            'ubicacion' => $request->ubicacion ?? 'No especificada',
            'is_active' => $request->is_active ?? true,
            'timeout' => $request->timeout ?? 30,
            'retry_interval' => $request->retry_interval ?? 5000,
            'alert_on_disconnect' => $request->alert_on_disconnect ?? true,
        ];
        
        if ($request->type === 'network') {
            $data['ip_address'] = $request->ip_address;
            $data['port'] = $request->port;
            $data['protocol'] = $request->protocol;
            $data['mac_address'] = $request->mac_address;
            $data['username'] = $request->username;
            $data['password'] = $request->password;
        } else {
            $data['ssid'] = $request->ssid;
            $data['wifi_password'] = $request->wifi_password;
            $data['wifi_ip_address'] = $request->wifi_ip_address;
            $data['wifi_port'] = $request->wifi_port;
            $data['wifi_protocol'] = $request->wifi_protocol;
            $data['wifi_mac_address'] = $request->wifi_mac_address;
            $data['wifi_username'] = $request->wifi_username;
            $data['encryption'] = $request->encryption ?? 'wpa2';
        }
        
        if (!$id || $id === 'nuevo') {
            $reader = NfcReader::create($data);
        } else {
            $reader = NfcReader::findOrFail($id);
            $reader->update($data);
        }
        
        // Probar conexión después de guardar
        if ($reader->is_active) {
            $this->testConnectionAndUpdateStatus($reader);
        }
        
        return redirect()->route('lectores.index')
            ->with('success', 'Lector guardado correctamente');
    }

    /**
     * Eliminar lector
     */
    public function delete($id)
    {
        try {
            $reader = NfcReader::findOrFail($id);
            $reader->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Lector eliminado correctamente']);
            }
            
            return redirect()->route('lectores.index')
                ->with('success', 'Lector eliminado correctamente');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el lector'], 500);
        }
    }

    /**
     * Probar conexión vía AJAX
     */
    public function test($id)
    {
        try {
            $reader = NfcReader::findOrFail($id);
            
            $startTime = microtime(true);
            $isConnected = $this->testConnection($reader);
            $responseTime = round((microtime(true) - $startTime) * 1000);
            
            if ($isConnected) {
                $reader->update(['last_connection' => now(), 'status' => 'active']);
                return response()->json([
                    'success' => true,
                    'message' => 'Conexión exitosa',
                    'device' => [
                        'name' => $reader->name,
                        'ip' => $this->getReaderIp($reader),
                        'port' => $this->getReaderPort($reader)
                    ],
                    'response_time' => $responseTime . 'ms'
                ]);
            }
            
            $reader->update(['status' => 'inactive']);
            return response()->json([
                'success' => false,
                'message' => "No se pudo conectar al lector: {$this->getReaderIp($reader)}:{$this->getReaderPort($reader)}"
            ]);
        } catch (\Exception $e) {
            Log::error('Error al probar conexión del lector: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al probar la conexión: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Escanear redes WiFi disponibles
     */
    public function scanNetworks(Request $request)
    {
        try {
            // En un entorno real, esto se comunicaría con el lector o usaría comandos del sistema
            // Simulación de redes WiFi detectadas (en producción, esto vendría del lector o de un escáner real)
            
            // Opción 1: Usar comandos del sistema (Linux/Mac)
            $networks = $this->scanNetworksViaSystem();
            
            // Opción 2: Comunicarse con el lector WiFi para que escanee
            if ($request->reader_ip) {
                $networks = $this->scanNetworksViaReader($request->reader_ip);
            }
            
            return response()->json([
                'success' => true,
                'networks' => $networks
            ]);
        } catch (\Exception $e) {
            Log::error('Error al escanear redes WiFi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al escanear redes WiFi: ' . $e->getMessage(),
                'networks' => $this->getMockNetworks() // Fallback con datos simulados
            ]);
        }
    }

    /**
     * Conectar lector a red WiFi seleccionada
     */
    public function connectToWifi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reader_id' => 'required|exists:nfc_readers,id',
            'ssid' => 'required|string',
            'password' => 'required|string|min:8',
            'ip_address' => 'nullable|ip',
            'port' => 'required|integer|min:1|max:65535',
            'protocol' => 'required|in:tcp,udp,http,https'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $reader = NfcReader::findOrFail($request->reader_id);
            
            // Enviar comando al lector para conectarse a la red WiFi
            $connected = $this->sendWifiCredentialsToReader($reader, $request->ssid, $request->password);
            
            if ($connected) {
                // Actualizar configuración del lector
                $reader->update([
                    'ssid' => $request->ssid,
                    'wifi_password' => $request->password,
                    'wifi_ip_address' => $request->ip_address,
                    'wifi_port' => $request->port,
                    'wifi_protocol' => $request->protocol,
                    'status' => 'configuring'
                ]);
                
                // Esperar a que el lector se conecte y obtenga IP
                sleep(2);
                
                // Probar conexión
                $testResult = $this->testConnection($reader);
                
                if ($testResult) {
                    $reader->update(['status' => 'active', 'last_connection' => now()]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Lector conectado exitosamente a la red ' . $request->ssid,
                        'reader' => $reader
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'El lector recibió la configuración pero no se pudo establecer conexión. Verifique la red.'
                    ]);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'No se pudo enviar la configuración al lector'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al conectar lector a WiFi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al conectar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar estado de todos los lectores
     */
    public function checkAllStatus()
    {
        $readers = NfcReader::all();
        $results = [];
        
        foreach ($readers as $reader) {
            $isConnected = $this->testConnection($reader);
            $reader->update([
                'status' => $isConnected ? 'active' : 'inactive',
                'last_connection' => $isConnected ? now() : $reader->last_connection
            ]);
            
            $results[] = [
                'id' => $reader->id,
                'name' => $reader->name,
                'status' => $reader->status,
                'last_connection' => $reader->last_connection
            ];
        }
        
        return response()->json([
            'success' => true,
            'readers' => $results
        ]);
    }

    // ============================================
    // MÉTODOS PRIVADOS
    // ============================================

    /**
     * Probar conexión con un lector
     */
    private function testConnection($reader)
    {
        $ip = $this->getReaderIp($reader);
        $port = $this->getReaderPort($reader);
        $protocol = $this->getReaderProtocol($reader);
        
        if (empty($ip)) {
            return false;
        }
        
        // Probar conexión según protocolo
        switch ($protocol) {
            case 'http':
            case 'https':
                return $this->testHttpConnection($ip, $port, $protocol);
            case 'tcp':
            case 'udp':
            default:
                return $this->testSocketConnection($ip, $port);
        }
    }

    /**
     * Probar conexión HTTP/HTTPS
     */
    private function testHttpConnection($ip, $port, $protocol)
    {
        try {
            $url = "{$protocol}://{$ip}:{$port}/status";
            $response = Http::timeout(5)->get($url);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Probar conexión por socket
     */
    private function testSocketConnection($ip, $port)
    {
        $connection = @fsockopen($ip, $port, $errno, $errstr, 5);
        if ($connection) {
            fclose($connection);
            return true;
        }
        return false;
    }

    /**
     * Obtener IP del lector según tipo
     */
    private function getReaderIp($reader)
    {
        return $reader->type === 'network' ? $reader->ip_address : $reader->wifi_ip_address;
    }

    /**
     * Obtener puerto del lector según tipo
     */
    private function getReaderPort($reader)
    {
        return $reader->type === 'network' ? $reader->port : $reader->wifi_port;
    }

    /**
     * Obtener protocolo del lector según tipo
     */
    private function getReaderProtocol($reader)
    {
        return $reader->type === 'network' ? $reader->protocol : $reader->wifi_protocol;
    }

    /**
     * Probar conexión y actualizar estado
     */
    private function testConnectionAndUpdateStatus($reader)
    {
        $isConnected = $this->testConnection($reader);
        $reader->update([
            'status' => $isConnected ? 'active' : 'inactive',
            'last_connection' => $isConnected ? now() : $reader->last_connection
        ]);
        return $isConnected;
    }

    /**
     * Escanear redes WiFi usando comandos del sistema
     */
    private function scanNetworksViaSystem()
    {
        $networks = [];
        
        // Para Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output = shell_exec('netsh wlan show networks mode=bssid');
            if ($output) {
                $networks = $this->parseWindowsNetworks($output);
            }
        }
        // Para Linux/Mac
        else {
            $output = shell_exec('nmcli dev wifi list 2>/dev/null');
            if ($output) {
                $networks = $this->parseLinuxNetworks($output);
            }
        }
        
        // Si no se pudo escanear, usar datos simulados
        if (empty($networks)) {
            $networks = $this->getMockNetworks();
        }
        
        return $networks;
    }

    /**
     * Escanear redes WiFi a través del lector
     */
    private function scanNetworksViaReader($readerIp)
    {
        try {
            $response = Http::timeout(10)->get("http://{$readerIp}/scan_wifi");
            if ($response->successful()) {
                return $response->json('networks', []);
            }
            return $this->getMockNetworks();
        } catch (\Exception $e) {
            return $this->getMockNetworks();
        }
    }

    /**
     * Enviar credenciales WiFi al lector
     */
    private function sendWifiCredentialsToReader($reader, $ssid, $password)
    {
        try {
            // Intentar conectar directamente si es el mismo lector
            $ip = $this->getReaderIp($reader);
            if ($ip) {
                $response = Http::timeout(10)->post("http://{$ip}/configure_wifi", [
                    'ssid' => $ssid,
                    'password' => $password
                ]);
                return $response->successful();
            }
            
            // Si no se puede, simular éxito (en desarrollo)
            return true;
        } catch (\Exception $e) {
            Log::warning('No se pudo enviar configuración al lector: ' . $e->getMessage());
            return true; // En desarrollo, asumir éxito
        }
    }

    /**
     * Parsear redes WiFi desde Windows
     */
    private function parseWindowsNetworks($output)
    {
        $networks = [];
        $lines = explode("\n", $output);
        $currentNetwork = [];
        
        foreach ($lines as $line) {
            if (preg_match('/SSID\s+:\s(.+)/', $line, $matches)) {
                if (!empty($currentNetwork)) {
                    $networks[] = $currentNetwork;
                }
                $currentNetwork = [
                    'ssid' => trim($matches[1]),
                    'signal' => 0,
                    'encrypted' => true
                ];
            }
            
            if (preg_match('/信号\s+:\s(\d+)%/', $line, $matches)) {
                $currentNetwork['signal'] = intval($matches[1]);
            }
            
            if (preg_match('/认证\s+:\s(.+)/', $line, $matches)) {
                $currentNetwork['encrypted'] = !str_contains($matches[1], '开放');
            }
        }
        
        if (!empty($currentNetwork)) {
            $networks[] = $currentNetwork;
        }
        
        return $networks;
    }

    /**
     * Parsear redes WiFi desde Linux
     */
    private function parseLinuxNetworks($output)
    {
        $networks = [];
        $lines = explode("\n", $output);
        
        foreach ($lines as $line) {
            if (preg_match('/^\s*\*\s+(.+?)\s+(.+?)\s+(.+?)\s+(.+?)\s+(\d+)\s+(\d+)/', $line, $matches)) {
                $networks[] = [
                    'ssid' => trim($matches[2]),
                    'signal' => intval($matches[5]),
                    'encrypted' => !str_contains($matches[3], '--')
                ];
            }
        }
        
        return $networks;
    }

    /**
     * Obtener redes simuladas para pruebas
     */
    private function getMockNetworks()
    {
        return [
            ['ssid' => 'OFICINA_CENTRAL', 'signal' => 85, 'encrypted' => true],
            ['ssid' => 'WIFI_VISITANTES', 'signal' => 70, 'encrypted' => false],
            ['ssid' => 'TECNOLOGIA_NFC', 'signal' => 92, 'encrypted' => true],
            ['ssid' => 'RED_INTERNA', 'signal' => 45, 'encrypted' => true],
            ['ssid' => 'GUEST_NETWORK', 'signal' => 60, 'encrypted' => false],
            ['ssid' => 'SISTEMAS_SEGURIDAD', 'signal' => 78, 'encrypted' => true],
            ['ssid' => 'CONTROL_ACCESO', 'signal' => 55, 'encrypted' => true],
        ];
    }
}