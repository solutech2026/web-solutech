<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NFCCard extends Model
{
    use HasFactory;

    // Especificar explícitamente el nombre de la tabla
    protected $table = 'nfc_cards';

    protected $fillable = [
        'card_code',
        'person_id',
        'status',
        'notes',
        'metadata',
        'assigned_at',
        'last_used_at'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'last_used_at' => 'datetime',
        'metadata' => 'array'
    ];

    // Relaciones
    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAssigned($query)
    {
        return $query->whereNotNull('person_id');
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('person_id');
    }

    // Accessors
    public function getIsAssignedAttribute()
    {
        return !is_null($this->person_id);
    }
}