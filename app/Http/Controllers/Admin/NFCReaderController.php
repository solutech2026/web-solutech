<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NfcReader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            'ip_address' => 'required_if:type,network|string',
            'port' => 'required_if:type,network|integer',
            'protocol' => 'required_if:type,network|string',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'ssid' => 'required_if:type,wifi|string',
            'wifi_ip_address' => 'required_if:type,wifi|string',
            'wifi_port' => 'required_if:type,wifi|integer',
            'wifi_protocol' => 'required_if:type,wifi|string',
            'wifi_username' => 'nullable|string',
            'wifi_password' => 'nullable|string',
            'ubicacion' => 'nullable|string'
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
            'status' => 'active'
        ];
        
        if ($request->type === 'network') {
            $data['ip_address'] = $request->ip_address;
            $data['port'] = $request->port;
            $data['protocol'] = $request->protocol;
            $data['username'] = $request->username;
            $data['password'] = $request->password;
        } else {
            $data['ssid'] = $request->ssid;
            $data['wifi_ip_address'] = $request->wifi_ip_address;
            $data['wifi_port'] = $request->wifi_port;
            $data['wifi_protocol'] = $request->wifi_protocol;
            $data['wifi_username'] = $request->wifi_username;
            $data['wifi_password'] = $request->wifi_password;
        }
        
        if (!$id || $id === 'nuevo') {
            $reader = NfcReader::create($data);
        } else {
            $reader = NfcReader::findOrFail($id);
            $reader->update($data);
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
            
            $ip = $reader->type === 'network' ? $reader->ip_address : $reader->wifi_ip_address;
            $port = $reader->type === 'network' ? $reader->port : $reader->wifi_port;
            
            if (empty($ip)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dirección IP no configurada'
                ]);
            }
            
            $connection = @fsockopen($ip, $port, $errno, $errstr, 2);
            
            if ($connection) {
                fclose($connection);
                $reader->update(['last_connection' => now(), 'status' => 'active']);
                return response()->json([
                    'success' => true,
                    'message' => 'Conexión exitosa',
                    'device' => ['name' => $reader->name],
                    'response_time' => '< 100ms'
                ]);
            }
            
            $reader->update(['status' => 'inactive']);
            return response()->json([
                'success' => false,
                'message' => "No se pudo conectar a {$ip}:{$port}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al probar la conexión: ' . $e->getMessage()
            ], 500);
        }
    }
}