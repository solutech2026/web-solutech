<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NfcCard extends Model
{
    use HasFactory;

    protected $table = 'nfc_cards';

    protected $fillable = [
        'card_code',
        'card_uid',
        'assigned_to',
        'assigned_at',
        'status',
        'notes',
        'metadata',
        'last_used_at'
        // ELIMINA 'person_id' del fillable
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'last_used_at' => 'datetime',
        'metadata' => 'array'
    ];

    /**
     * Relación con la persona asignada (usando assigned_to)
     */
    public function assignedPerson()
    {
        return $this->belongsTo(Person::class, 'assigned_to');
    }

    /**
     * Relación con la persona (usando assigned_to)
     */
    public function person()
    {
        return $this->belongsTo(Person::class, 'assigned_to');
    }

    /**
     * Verificar si está asignada
     */
    public function isAssigned()
    {
        return !is_null($this->assigned_to);
    }

    /**
     * Scope para tarjetas disponibles
     */
    public function scopeAvailable($query)
    {
        return $query->whereNull('assigned_to')->where('status', 'active');
    }

    /**
     * Scope para tarjetas activas
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para tarjetas asignadas
     */
    public function scopeAssigned($query)
    {
        return $query->whereNotNull('assigned_to');
    }

    /**
     * Get assigned person name
     */
    public function getAssignedPersonNameAttribute()
    {
        return $this->assignedPerson ? $this->assignedPerson->full_name : null;
    }
}