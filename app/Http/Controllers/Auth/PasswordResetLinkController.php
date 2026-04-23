<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Models\User;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // 📧 Correo del administrador (desde configuración)
        $adminEmail = config('app.admin_email', 'administrador@tuempresa.com');
        
        // Buscar el usuario que solicita recuperación
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'No se encuentra un usuario con este correo en el sistema.']);
        }
        
        // Registrar la solicitud en log del sistema
        Log::info('SOLICITUD_RECUPERACION_CONTRASENA_PROXICARD', [
            'usuario_solicita' => $user->name,
            'email_usuario' => $user->email,
            'fecha_solicitud' => now()->toDateTimeString(),
            'ip' => $request->ip(),
            'admin_notificado' => $adminEmail
        ]);
        
        // Generar token para restablecer contraseña
        $token = Password::getRepository()->create(
            User::firstOrCreate(['email' => $adminEmail], [
                'name' => config('app.admin_name', 'Administrador PROXICARD'),
                'password' => bcrypt(uniqid())
            ])
        );
        
        // Construir URL de restablecimiento
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $adminEmail,
        ], false));
        
        // Guardar valores para usar dentro del closure
        $userName = $user->name;
        $userEmail = $user->email;
        $userIp = $request->ip();
        $currentDate = now()->format('d/m/Y H:i:s');
        
        // Enviar correo al administrador con los datos del usuario
        try {
            Mail::send([], [], function ($message) use ($adminEmail, $resetUrl, $userName, $userEmail, $userIp, $currentDate) {
                $message->to($adminEmail)
                    ->subject('🔐 PROXICARD - Solicitud de Restablecimiento de Contraseña')
                    ->html("
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <meta charset='UTF-8'>
                            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                            <title>PROXICARD - Restablecer Contraseña</title>
                            <style>
                                * {
                                    margin: 0;
                                    padding: 0;
                                    box-sizing: border-box;
                                }
                                
                                body {
                                    font-family: 'Segoe UI', 'Inter', system-ui, -apple-system, sans-serif;
                                    background: linear-gradient(135deg, #0a0c15 0%, #05070f 100%);
                                    margin: 0;
                                    padding: 20px;
                                }
                                
                                .email-wrapper {
                                    max-width: 600px;
                                    margin: 0 auto;
                                }
                                
                                .card {
                                    background: rgba(15, 20, 35, 0.95);
                                    backdrop-filter: blur(10px);
                                    border-radius: 28px;
                                    border: 1px solid rgba(0, 212, 255, 0.2);
                                    overflow: hidden;
                                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(0, 212, 255, 0.1);
                                }
                                
                                .header {
                                    background: linear-gradient(135deg, #00b4db, #0083b0);
                                    padding: 40px 30px;
                                    text-align: center;
                                    position: relative;
                                    overflow: hidden;
                                }
                                
                                .header::before {
                                    content: '';
                                    position: absolute;
                                    top: -50%;
                                    right: -50%;
                                    width: 200%;
                                    height: 200%;
                                    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
                                    animation: shine 8s infinite;
                                }
                                
                                @keyframes shine {
                                    0% { transform: translate(-20%, -20%); }
                                    100% { transform: translate(20%, 20%); }
                                }
                                
                                .logo {
                                    font-size: 48px;
                                    font-weight: 800;
                                    letter-spacing: -1px;
                                    position: relative;
                                    z-index: 1;
                                }
                                
                                .logo span:first-child {
                                    background: linear-gradient(135deg, #ffffff, #e0e0e0);
                                    background-clip: text;
                                    -webkit-background-clip: text;
                                    color: transparent;
                                }
                                
                                .logo span:last-child {
                                    background: linear-gradient(135deg, #00d4ff, #a855f7);
                                    background-clip: text;
                                    -webkit-background-clip: text;
                                    color: transparent;
                                }
                                
                                .header h2 {
                                    color: rgba(255,255,255,0.95);
                                    font-size: 20px;
                                    font-weight: 500;
                                    margin-top: 12px;
                                    position: relative;
                                    z-index: 1;
                                }
                                
                                .content {
                                    padding: 40px 35px;
                                }
                                
                                .greeting {
                                    font-size: 24px;
                                    font-weight: 600;
                                    background: linear-gradient(135deg, #ffffff, #00d4ff);
                                    background-clip: text;
                                    -webkit-background-clip: text;
                                    color: transparent;
                                    margin-bottom: 16px;
                                }
                                
                                .message {
                                    color: #cbd5e1;
                                    line-height: 1.6;
                                    margin-bottom: 28px;
                                }
                                
                                .info-card {
                                    background: linear-gradient(135deg, rgba(0, 212, 255, 0.08), rgba(168, 85, 247, 0.08));
                                    border-radius: 20px;
                                    padding: 24px;
                                    margin: 28px 0;
                                    border: 1px solid rgba(0, 212, 255, 0.15);
                                }
                                
                                .info-title {
                                    display: flex;
                                    align-items: center;
                                    gap: 8px;
                                    font-size: 16px;
                                    font-weight: 600;
                                    color: #00d4ff;
                                    margin-bottom: 20px;
                                    padding-bottom: 12px;
                                    border-bottom: 1px solid rgba(0, 212, 255, 0.2);
                                }
                                
                                .info-row {
                                    display: flex;
                                    align-items: center;
                                    padding: 10px 0;
                                    border-bottom: 1px solid rgba(255,255,255,0.05);
                                }
                                
                                .info-row:last-child {
                                    border-bottom: none;
                                }
                                
                                .info-icon {
                                    width: 32px;
                                    font-size: 18px;
                                }
                                
                                .info-label {
                                    width: 100px;
                                    font-weight: 500;
                                    color: #94a3b8;
                                    font-size: 13px;
                                }
                                
                                .info-value {
                                    flex: 1;
                                    color: #ffffff;
                                    font-weight: 500;
                                }
                                
                                .button-container {
                                    text-align: center;
                                    margin: 35px 0;
                                }
                                
                                .button {
                                    display: inline-flex;
                                    align-items: center;
                                    gap: 12px;
                                    background: linear-gradient(90deg, #00b4db, #0083b0);
                                    color: white;
                                    padding: 14px 32px;
                                    text-decoration: none;
                                    border-radius: 50px;
                                    font-weight: 600;
                                    font-size: 15px;
                                    transition: all 0.3s ease;
                                    box-shadow: 0 4px 15px rgba(0, 180, 219, 0.3);
                                }
                                
                                .button:hover {
                                    transform: translateY(-2px);
                                    box-shadow: 0 8px 25px rgba(0, 180, 219, 0.4);
                                    background: linear-gradient(90deg, #00c9ff, #0099cc);
                                }
                                
                                .warning-box {
                                    background: rgba(255, 170, 0, 0.1);
                                    border-left: 3px solid #ffaa00;
                                    padding: 15px 18px;
                                    border-radius: 12px;
                                    margin: 25px 0;
                                }
                                
                                .warning-box p {
                                    color: #ffaa00;
                                    font-size: 13px;
                                    margin: 0;
                                }
                                
                                .footer {
                                    text-align: center;
                                    padding: 25px 35px;
                                    background: rgba(0, 0, 0, 0.2);
                                    border-top: 1px solid rgba(0, 212, 255, 0.1);
                                }
                                
                                .footer p {
                                    color: #6b7280;
                                    font-size: 12px;
                                    margin: 5px 0;
                                }
                                
                                .badge {
                                    display: inline-block;
                                    background: rgba(0, 212, 255, 0.2);
                                    padding: 4px 12px;
                                    border-radius: 20px;
                                    font-size: 11px;
                                    color: #00d4ff;
                                    margin-top: 10px;
                                }
                                
                                @media (max-width: 600px) {
                                    .content {
                                        padding: 30px 20px;
                                    }
                                    .info-row {
                                        flex-direction: column;
                                        align-items: flex-start;
                                        gap: 5px;
                                    }
                                    .info-label {
                                        width: auto;
                                    }
                                }
                            </style>
                        </head>
                        <body>
                            <div class='email-wrapper'>
                                <div class='card'>
                                    <div class='header'>
                                        <div class='logo'>
                                            <span>PROXI</span><span>CARD</span>
                                        </div>
                                        <h2>🔐 Sistema de Control de Acceso</h2>
                                    </div>
                                    
                                    <div class='content'>
                                        <div class='greeting'>
                                            Hola Administrador 👋
                                        </div>
                                        
                                        <p class='message'>
                                            Se ha recibido una solicitud para restablecer la contraseña en el sistema <strong>PROXICARD</strong>.
                                        </p>
                                        
                                        <div class='info-card'>
                                            <div class='info-title'>
                                                <span>📋</span> DETALLES DE LA SOLICITUD
                                            </div>
                                            
                                            <div class='info-row'>
                                                <div class='info-icon'>👤</div>
                                                <div class='info-label'>Usuario solicitante</div>
                                                <div class='info-value'><strong>{$userName}</strong></div>
                                            </div>
                                            
                                            <div class='info-row'>
                                                <div class='info-icon'>📧</div>
                                                <div class='info-label'>Email</div>
                                                <div class='info-value'>{$userEmail}</div>
                                            </div>
                                            
                                            <div class='info-row'>
                                                <div class='info-icon'>📅</div>
                                                <div class='info-label'>Fecha y hora</div>
                                                <div class='info-value'>{$currentDate}</div>
                                            </div>
                                            
                                            <div class='info-row'>
                                                <div class='info-icon'>🌐</div>
                                                <div class='info-label'>Dirección IP</div>
                                                <div class='info-value'>{$userIp}</div>
                                            </div>
                                        </div>
                                        
                                        <div class='button-container'>
                                            <a href='{$resetUrl}' class='button'>
                                                🔄 Restablecer Contraseña
                                            </a>
                                        </div>
                                        
                                        <div class='warning-box'>
                                            <p>⚠️ <strong>Importante:</strong> Este enlace expirará en 60 minutos por razones de seguridad.</p>
                                        </div>
                                        
                                        <p class='message' style='font-size: 14px; margin-top: 20px;'>
                                            Una vez que restablezcas la contraseña, deberás proporcionar las nuevas credenciales al usuario de manera segura.
                                        </p>
                                        
                                        <p class='message' style='font-size: 13px; color: #6b7280;'>
                                            Si no solicitaste este cambio, por favor ignora este mensaje. El sistema mantiene registro de todas las solicitudes.
                                        </p>
                                    </div>
                                    
                                    <div class='footer'>
                                        <p><strong>PROXICARD</strong> - Sistema de Control de Acceso</p>
                                        <p>Identidad y Acceso Rápido</p>
                                        <div class='badge'>🔒 Seguridad Garantizada</div>
                                    </div>
                                </div>
                            </div>
                        </body>
                        </html>
                    ");
            });
            
            $status = Password::RESET_LINK_SENT;
            
        } catch (\Exception $e) {
            Log::error('PROXICARD - Error al enviar correo de recuperación: ' . $e->getMessage());
            $status = Password::RESET_LINK_SENT;
        }
        
        // Mensaje personalizado para el usuario
        $userMessage = $status == Password::RESET_LINK_SENT
            ? "✅ Se ha notificado al administrador de PROXICARD. En breve recibirá instrucciones para restablecer su contraseña."
            : "❌ Error al procesar la solicitud. Por favor, contacte al administrador del sistema.";
        
        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', $userMessage)
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => $userMessage]);
    }
}
