<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'address',
        'phone',
        'email',
        'logo',
        'settings',
        'is_active'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean'
    ];

    public function persons()
    {
        return $this->hasMany(Person::class);
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class);
    }
}