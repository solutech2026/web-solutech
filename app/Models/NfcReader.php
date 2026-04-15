<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfcReader extends Model
{
    protected $table = 'nfc_readers';
    
    protected $fillable = [
        'name', 'type', 'ip_address', 'port', 'protocol',
        'username', 'password', 'ssid', 'wifi_ip_address',
        'wifi_port', 'wifi_protocol', 'wifi_username', 'wifi_password',
        'ubicacion', 'status', 'last_connection'
    ];
    
    protected $casts = [
        'last_connection' => 'datetime',
        'port' => 'integer',
        'wifi_port' => 'integer'
    ];
    
    /**
     * Verificar si el lector está online
     */
    public function isOnline()
    {
        $ip = $this->type === 'network' ? $this->ip_address : $this->wifi_ip_address;
        $port = $this->type === 'network' ? $this->port : $this->wifi_port;
        
        if (empty($ip)) return false;
        
        $connection = @fsockopen($ip, $port, $errno, $errstr, 2);
        
        if ($connection) {
            fclose($connection);
            $this->update(['last_connection' => now(), 'status' => 'active']);
            return true;
        }
        
        $this->update(['status' => 'inactive']);
        return false;
    }
    
    /**
     * Obtener la IP actual según el tipo
     */
    public function getCurrentIpAttribute()
    {
        return $this->type === 'network' ? $this->ip_address : $this->wifi_ip_address;
    }
    
    /**
     * Obtener el puerto actual según el tipo
     */
    public function getCurrentPortAttribute()
    {
        return $this->type === 'network' ? $this->port : $this->wifi_port;
    }
    
    /**
     * Obtener el protocolo actual según el tipo
     */
    public function getCurrentProtocolAttribute()
    {
        return $this->type === 'network' ? $this->protocol : $this->wifi_protocol;
    }
}
