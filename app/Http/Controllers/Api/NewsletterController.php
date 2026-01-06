<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewsletterWelcomeMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    /**
     * Suscribir email al newsletter
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Request $request)
    {
        try {
            // Validar el email
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Por favor ingresa un email válido.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $email = $request->input('email');

            // Verificar si ya está suscrito
            $existing = NewsletterSubscriber::where('email', $email)->first();
            
            if ($existing) {
                if ($existing->unsubscribed_at) {
                    // Re-suscribir usuario que se había dado de baja
                    $existing->update([
                        'subscribed_at' => now(),
                        'unsubscribed_at' => null,
                        'status' => 'active'
                    ]);
                    
                    Mail::to($email)->send(new NewsletterWelcomeMail($email));
                    
                    return response()->json([
                        'success' => true,
                        'message' => '¡Bienvenido de nuevo! Te has re-suscrito exitosamente.',
                        'data' => ['email' => $email]
                    ]);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Este email ya está suscrito a nuestro newsletter.',
                ], 409);
            }

            // Crear nuevo suscriptor
            $subscriber = NewsletterSubscriber::create([
                'email' => $email,
                'subscribed_at' => now(),
                'status' => 'active',
                'source' => 'website_footer',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Enviar email de bienvenida
            Mail::to($email)->send(new NewsletterWelcomeMail($email));

            // Opcional: Notificar al administrador
            // Mail::to('admin@solutech.com')->send(new NewSubscriberNotification($email));

            return response()->json([
                'success' => true,
                'message' => '¡Gracias por suscribirte! Te hemos enviado un email de confirmación.',
                'data' => [
                    'email' => $email,
                    'subscribed_at' => $subscriber->subscribed_at->format('d/m/Y'),
                    'id' => $subscriber->id
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error en suscripción newsletter: ' . $e->getMessage(), [
                'email' => $request->input('email'),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar tu suscripción. Por favor, intenta nuevamente más tarde.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Cancelar suscripción (opcional)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unsubscribe(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email inválido.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $email = $request->input('email');
            
            $subscriber = NewsletterSubscriber::where('email', $email)->first();
            
            if (!$subscriber) {
                return response()->json([
                    'success' => false,
                    'message' => 'No encontramos este email en nuestra lista de suscriptores.',
                ], 404);
            }

            if ($subscriber->unsubscribed_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este email ya se había dado de baja anteriormente.',
                ], 409);
            }

            $subscriber->update([
                'unsubscribed_at' => now(),
                'status' => 'unsubscribed'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Te has dado de baja exitosamente. Lamentamos verte ir.',
            ]);

        } catch (\Exception $e) {
            Log::error('Error en baja newsletter: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la baja. Por favor, contacta con soporte.',
            ], 500);
        }
    }

    /**
     * Obtener lista de suscriptores (protegido)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Solo para administradores
        // $this->authorize('viewAny', NewsletterSubscriber::class);
        
        $subscribers = NewsletterSubscriber::orderBy('created_at', 'desc')->paginate(50);
        
        return response()->json([
            'success' => true,
            'data' => $subscribers
        ]);
    }
}