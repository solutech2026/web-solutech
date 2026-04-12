<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected $table = 'newsletter_subscribers';

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

    /**
     * Scope para suscriptores activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Suscribir un email
     */
    public static function subscribe($email, $source = null, $ip = null, $userAgent = null)
    {
        return self::updateOrCreate(
            ['email' => $email],
            [
                'status' => 'active',
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
                'source' => $source,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'metadata' => json_encode(['subscribed_via' => 'api'])
            ]
        );
    }

    /**
     * Desuscribir un email
     */
    public static function unsubscribe($email)
    {
        $subscriber = self::where('email', $email)->first();
        if ($subscriber) {
            $subscriber->update([
                'status' => 'unsubscribed',
                'unsubscribed_at' => now()
            ]);
        }
        return $subscriber;
    }
}