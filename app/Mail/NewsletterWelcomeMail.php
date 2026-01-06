<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function build()
    {
        return $this->subject('🎉 ¡Bienvenido al Newsletter de SoluTech!')
                    ->from('newsletter@solutech.com', 'SoluTech Newsletter')
                    ->to($this->email)
                    ->view('emails.newsletter.welcome')
                    ->with([
                        'email' => $this->email,
                        'date' => now()->format('d/m/Y'),
                        'unsubscribe_url' => url('/newsletter/unsubscribe?email=' . urlencode($this->email))
                    ]);
    }
}
