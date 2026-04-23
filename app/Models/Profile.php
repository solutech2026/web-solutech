<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'profiles'; // O tu tabla correspondiente
    
    protected $fillable = [
        'slug',
        'name',
        'role',
        'summary',
        'phone',
        'email',
        'services',
        'photo_path',
        'experience',
        'projects',
        'clients',
        'location',
        'social',
    ];
    
    protected $casts = [
        'services' => 'array',
        'social' => 'array',
    ];
}