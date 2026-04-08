<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AccessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'person_id',
        'nfc_card_id',
        'access_type', // Ejemplo: 'entry', 'exit'
        'verification_method', // Ejemplo: 'nfc', 'qr', 'manual'
        'access_time',
        'status', // Ejemplo: 'authorized', 'denied', 'pending_exit'
        'gate', // Ejemplo: 'Sabas Nieves', 'Puesto 1'
        'ip_address',
        'reason',
        'metadata' // Aquí guardaremos datos extra como temperatura, clima o batería del sensor
    ];

    protected $casts = [
        'access_time' => 'datetime',
        'metadata' => 'array'
    ];

    // --- Relaciones ---

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function nfcCard()
    {
        return $this->belongsTo(NFCCard::class);
    }

    // --- Scopes para facilitar tus reportes (Bitácoras) ---

    /**
     * Scope para filtrar accesos por una puerta específica (ej. Entrada del Ávila)
     */
    public function scopeAtGate($query, $gate)
    {
        return $query->where('gate', $gate);
    }

    /**
     * Scope para encontrar personas que entraron y aún no han salido
     */
    public function scopeStillInside($query)
    {
        return $query->where('access_type', 'entry')
                     ->where('status', 'pending_exit');
    }

    /**
     * Helper para verificar si el acceso fue por NFC
     */
    public function isNfc(): bool
    {
        return $this->verification_method === 'nfc';
    }
}