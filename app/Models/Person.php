<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Person extends Model
{
    use HasFactory;

    protected $table = 'persons';

    protected $fillable = [
        'company_id',
        'type',
        'name',
        'document_id',
        'email',
        'phone',
        'position',
        'department',
        'bio',
        'avatar',
        'nfc_card_id',
        'bio_url',
        'companions',
        'visit_reason',
        'last_access_at',
        'is_active',
        'metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
        'last_access_at' => 'datetime',
        'companions' => 'integer'
    ];

    // Relaciones
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function nfcCard()
    {
        return $this->hasOne(NFCCard::class, 'person_id');
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class);
    }

    // Accessors
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

    public function getFullBioUrlAttribute()
    {
        if ($this->bio_url) {
            return url($this->bio_url);
        }
        return null;
    }

    // Mutators
    public function setBioUrlAttribute($value)
    {
        // Asegurar que la URL tenga el formato correcto
        if ($value && !Str::startsWith($value, 'bio/')) {
            $this->attributes['bio_url'] = 'bio/' . $value;
        } else {
            $this->attributes['bio_url'] = $value;
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeEmployees($query)
    {
        return $query->where('type', 'employee');
    }

    public function scopeVisitors($query)
    {
        return $query->where('type', 'visitor');
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Método para generar URL única
    public static function generateUniqueBioUrl()
    {
        do {
            $url = 'bio/' . Str::random(12);
        } while (self::where('bio_url', $url)->exists());
        
        return $url;
    }
}