<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'subscribed_at',
        'unsubscribed_at',
        'status',
        'source',
        'ip_address',
        'user_agent',
        'metadata'
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'metadata' => 'array'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSubscribed($query)
    {
        return $query->whereNotNull('subscribed_at')->whereNull('unsubscribed_at');
    }
}