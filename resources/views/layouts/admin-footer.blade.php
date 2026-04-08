<style>
    .footer {
        background: white;
        padding: 20px 30px;
        text-align: center;
        border-top: 1px solid #e5e7eb;
        color: #6c757d;
        font-size: 14px;
        margin-top: auto;
    }
    
    .footer a {
        color: #667eea;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .footer a:hover {
        color: #764ba2;
    }
    
    @media (max-width: 768px) {
        .footer {
            padding: 15px 20px;
            font-size: 12px;
        }
    }
</style>

<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <p class="mb-0">
                    &copy; {{ date('Y') }} SoluTech - Sistema de Control de Acceso. 
                    Todos los derechos reservados.
                </p>
                <small class="text-muted">
                    Versión 1.0 | Desarrollado con <i class="fas fa-heart text-danger"></i> para tu seguridad
                </small>
            </div>
        </div>
    </div>
</footer>