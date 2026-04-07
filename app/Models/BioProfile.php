<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BioProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'role',
        'summary',
        'phone',
        'email',
        'photo_path',
        'services',
        'social_links',
    ];

    /**
     * Casteo de atributos.
     * Convierte los strings JSON de la DB a Arrays de PHP automáticamente.
     */
    protected $casts = [
        'services' => 'array',
        'social_links' => 'array',
    ];

    /**
     * Scope para buscar por slug directamente en el controlador
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
