<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Datos del formulario de contacto
     */
    public $formData;

    /**
     * Create a new message instance.
     */
    public function __construct($formData)
    {
        $this->formData = $formData;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this
            ->from(
                config('mail.from.address', 'sporteitsolutech@gmail.com'),
                config('mail.from.name', 'SoluTech Contacto')
            )
            ->to('solutech24@outlook.com')
            ->replyTo($this->formData['email'], $this->formData['name'])
            ->subject('📧 Nuevo mensaje de contacto: ' . $this->formData['subject'])
            ->view('emails.contact-form')
            ->with([
                'name' => $this->formData['name'],
                'email' => $this->formData['email'],
                'phone' => $this->formData['phone'] ?? 'No especificado',
                'company' => $this->formData['company'] ?? 'No especificada',
                'service' => $this->formData['service'] ?? 'No especificado',
                'subject' => $this->formData['subject'],
                'message' => $this->formData['message'],
                'receivedAt' => now()->format('d/m/Y H:i:s'),
            ]);
    }
}
