<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    protected $signature = 'email:test-contact';
    protected $description = 'Probar el envío de email de contacto';

    public function handle()
    {
        $this->info('🚀 Probando envío de email de contacto...');
        
        $testData = [
            'name' => 'Juan Pérez',
            'email' => 'juan.perez@ejemplo.com',
            'subject' => 'Prueba de formulario de contacto',
            'message' => 'Hola, esto es una prueba del formulario de contacto. 
            
Por favor confirmar recepción de este mensaje.

Gracias,
Juan Pérez'
        ];

        try {
            $this->info('📧 Enviando email a: solutech24@outlook.com');
            
            Mail::to('solutech24@outlook.com')->send(new ContactFormMail($testData));
            
            $this->info('✅ Email enviado exitosamente!');
            $this->info('');
            $this->info('📊 Datos de prueba:');
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['Nombre', $testData['name']],
                    ['Email', $testData['email']],
                    ['Asunto', $testData['subject']],
                    ['Mensaje', 'Vista previa del mensaje enviado...'],
                ]
            );
            
        } catch (\Exception $e) {
            $this->error('❌ Error al enviar el email: ' . $e->getMessage());
            $this->error('📍 Asegúrate de configurar correctamente el .env con las credenciales de email');
        }
    }
}
