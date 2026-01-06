<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Mensaje de Contacto - SoluTech</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-card {
            background: #f8fafc;
            border-left: 4px solid #2563eb;
            padding: 15px;
            border-radius: 6px;
        }
        .info-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
            color: #1e293b;
            font-weight: 500;
        }
        .message-section {
            background: #f1f5f9;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .message-label {
            font-size: 14px;
            color: #475569;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .message-content {
            white-space: pre-wrap;
            line-height: 1.8;
            color: #334155;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #64748b;
            font-size: 12px;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .timestamp {
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
            margin-bottom: 10px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">SoluTech</div>
            <h1>📧 Nuevo Mensaje de Contacto</h1>
            <div style="font-size: 14px; opacity: 0.9; margin-top: 5px;">
                Soluciones Tecnológicas Integrales
            </div>
        </div>
        
        <div class="timestamp">
            Recibido: {{ $receivedAt }}
        </div>
        
        <div class="content">
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-label">👤 Nombre</div>
                    <div class="info-value">{{ $name }}</div>
                </div>
                
                <div class="info-card">
                    <div class="info-label">📧 Email</div>
                    <div class="info-value">
                        <a href="mailto:{{ $email }}" style="color: #2563eb; text-decoration: none;">
                            {{ $email }}
                        </a>
                    </div>
                </div>
                
                <div class="info-card">
                    <div class="info-label">📞 Teléfono</div>
                    <div class="info-value">
                        <a href="tel:{{ $phone }}" style="color: #2563eb; text-decoration: none;">
                            {{ $phone }}
                        </a>
                    </div>
                </div>
                
                <div class="info-card">
                    <div class="info-label">🏢 Empresa</div>
                    <div class="info-value">{{ $company }}</div>
                </div>
                
                <div class="info-card">
                    <div class="info-label">🎯 Servicio de Interés</div>
                    <div class="info-value">{{ $service }}</div>
                </div>
                
                <div class="info-card">
                    <div class="info-label">📝 Asunto</div>
                    <div class="info-value">{{ $subject }}</div>
                </div>
            </div>
            
            <div class="message-section">
                <div class="message-label">💬 Mensaje:</div>
                <div class="message-content">{{ $message }}</div>
            </div>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} SoluTech. Todos los derechos reservados.</p>
            <p>Este mensaje fue generado automáticamente desde el formulario de contacto del sitio web.</p>
            <p style="margin-top: 10px;">
                <small>Dirección: Multi Centro Empresarial Del Este, Caracas/Venezuela</small>
            </p>
        </div>
    </div>
</body>
</html>