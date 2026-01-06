<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido al Newsletter de SoluTech</title>
    <style>
        /* Estilos similares a los de contacto, puedes reutilizar */
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 ¡Bienvenido a SoluTech!</h1>
        </div>
        
        <div class="content">
            <p>Hola,</p>
            
            <p>Gracias por suscribirte a nuestro newsletter. Ahora recibirás:</p>
            
            <ul>
                <li>📰 Últimas noticias tecnológicas</li>
                <li>💡 Consejos y mejores prácticas IT</li>
                <li>🔧 Novedades de nuestros servicios</li>
                <li>🎁 Ofertas especiales exclusivas</li>
            </ul>
            
            <p>Fecha de suscripción: <strong>{{ $date }}</strong></p>
            
            <div class="footer">
                <p>Si no solicitaste esta suscripción, puedes <a href="{{ $unsubscribe_url }}">darte de baja aquí</a>.</p>
            </div>
        </div>
    </div>
</body>
</html>