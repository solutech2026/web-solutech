<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    /**
     * Suscribir un email al newsletter
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'source' => 'nullable|string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Email inválido',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verificar si ya está suscrito activo
            $existing = NewsletterSubscriber::where('email', $request->email)
                ->where('status', 'active')
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este email ya está suscrito al newsletter'
                ], 400);
            }

            // Suscribir o reactivar
            $subscriber = NewsletterSubscriber::subscribe(
                $request->email,
                $request->source ?? 'api',
                $request->ip(),
                $request->userAgent()
            );

            return response()->json([
                'success' => true,
                'message' => 'Suscripción exitosa. Revisa tu correo para confirmar.',
                'data' => [
                    'email' => $subscriber->email,
                    'subscribed_at' => $subscriber->subscribed_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Newsletter subscription error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la suscripción'
            ], 500);
        }
    }

    /**
     * Desuscribir un email
     */
    public function unsubscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Email inválido'
            ], 422);
        }

        try {
            $subscriber = NewsletterSubscriber::unsubscribe($request->email);

            if (!$subscriber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email no encontrado en la lista'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Te has desuscrito del newsletter exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Newsletter unsubscribe error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la desuscripción'
            ], 500);
        }
    }

    /**
     * Listar suscriptores (solo admin)
     */
    public function index(Request $request)
    {
        // Aquí deberías agregar middleware de autenticación
        $subscribers = NewsletterSubscriber::orderBy('created_at', 'desc')
            ->paginate($request->get('limit', 20));

        return response()->json([
            'success' => true,
            'data' => $subscribers
        ]);
    }

    /**
     * Verificar si un email está suscrito
     */
    public function check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Email inválido'
            ], 422);
        }

        $subscriber = NewsletterSubscriber::where('email', $request->email)
            ->where('status', 'active')
            ->first();

        return response()->json([
            'success' => true,
            'is_subscribed' => !is_null($subscriber)
        ]);
    }
}