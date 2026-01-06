<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    /**
     * Mostrar página de servicios
     */
    public function servicios()
    {
        $contactInfo = [
            'email' => 'solutech24@outlook.com',
            'phone' => '+58 412 471 45 88',
            'whatsapp' => '584124714588',
            'address' => 'Multi Centro Empresarial Del Este, Caracas/Venezuela'
        ];

        $ctaData = [
            'title' => '¿Listo para Transformar tu IT?',
            'description' => 'Contáctanos hoy mismo y descubre cómo nuestros servicios pueden optimizar la tecnología de tu empresa.',
            'primaryButton' => [
                'text' => 'Contactar por WhatsApp',
                'link' => 'https://wa.me/584124714588',
                'type' => 'whatsapp'
            ],
            'secondaryButton' => [
                'text' => 'Enviar Email',
                'link' => 'mailto:solutech24@outlook.com',
                'type' => 'email'
            ]
        ];

        return Inertia::render('Service/Service', [
            'pageTitle' => 'Nuestros Servicios - SoluTech',
            'metaDescription' => 'Servicios especializados en administración de sistemas, redes, desarrollo de software y soporte multi nivel para empresas.',
            'metaKeywords' => 'servicios IT, administración de sistemas, redes empresariales, desarrollo de software, soporte técnico, SoluTech, WhatsApp',
            'services' => [
                [
                    'id' => 1,
                    'title' => "Administración de Sistemas",
                    'description' => "Gestión integral de servidores y sistemas empresariales para garantizar máxima disponibilidad y rendimiento.",
                    'icon' => 'server-stack',
                    'features' => [
                        "Gestión de servidores Windows/Linux",
                        "Monitoreo 24/7 de sistemas",
                        "Backups automáticos y restauración",
                        "Optimización de recursos y performance",
                        "Mantenimiento preventivo y correctivo",
                        "Actualización de sistemas y parches"
                    ],
                    'benefits' => [
                        "Reducción de tiempos de inactividad",
                        "Mayor seguridad de datos",
                        "Optimización de costos operativos",
                        "Escalabilidad garantizada"
                    ]
                ],
                [
                    'id' => 2,
                    'title' => "Administración de Redes",
                    'description' => "Diseño, implementación y mantenimiento de infraestructura de red corporativa segura y eficiente.",
                    'icon' => 'wifi',
                    'features' => [
                        "Diseño e implementación de redes",
                        "Configuración de routers y switches",
                        "Seguridad de red y firewalls",
                        "VPN empresarial segura",
                        "Optimización WiFi corporativo",
                        "Monitoreo de tráfico y ancho de banda"
                    ],
                    'benefits' => [
                        "Conectividad confiable",
                        "Seguridad de datos garantizada",
                        "Optimización del rendimiento",
                        "Escalabilidad de infraestructura"
                    ]
                ],
                [
                    'id' => 3,
                    'title' => "Soporte Multi Nivel",
                    'description' => "Asistencia técnica especializada con diferentes niveles de soporte adaptados a las necesidades de tu organización.",
                    'icon' => 'computer-desktop',
                    'features' => [
                        "Help Desk nivel 1, 2 y 3",
                        "Soporte técnico remoto 24/7",
                        "Asistencia presencial en sitio",
                        "Capacitación de usuarios",
                        "Gestión de incidencias",
                        "Soporte de emergencias IT"
                    ],
                    'benefits' => [
                        "Respuesta rápida a incidencias",
                        "Minimización de tiempos de resolución",
                        "Personal capacitado constantemente",
                        "Protocolos de emergencia establecidos"
                    ]
                ],
                [
                    'id' => 4,
                    'title' => "Desarrollo de Software",
                    'description' => "Soluciones personalizadas de software para automatizar y optimizar los procesos empresariales.",
                    'icon' => 'code-bracket',
                    'features' => [
                        "Software a medida",
                        "Aplicaciones web y móviles",
                        "Bases de datos y APIs",
                        "Integración de sistemas",
                        "Mantenimiento y actualizaciones"
                    ],
                    'benefits' => [
                        "Automatización de procesos",
                        "Mayor productividad",
                        "Soluciones escalables",
                        "Soporte técnico especializado"
                    ]
                ]
            ],
            'additionalServices' => [
                [
                    'title' => "Ciberseguridad",
                    'description' => "Protección integral contra amenazas digitales y vulnerabilidades.",
                    'icon' => 'shield-check'
                ],
                [
                    'title' => "Servicios en la Nube",
                    'description' => "Migración y gestión de infraestructura cloud para máxima flexibilidad.",
                    'icon' => 'cloud'
                ],
                [
                    'title' => "Consultoría IT",
                    'description' => "Asesoramiento especializado en tecnología para la toma de decisiones.",
                    'icon' => 'users'
                ],
                [
                    'title' => "Automatización",
                    'description' => "Automatización de procesos empresariales para mayor eficiencia.",
                    'icon' => 'cog'
                ]
            ],
            'processSteps' => [
                [
                    'step' => "01",
                    'title' => "Diagnóstico Inicial",
                    'description' => "Evaluamos tu infraestructura actual y necesidades específicas."
                ],
                [
                    'step' => "02",
                    'title' => "Propuesta Personalizada",
                    'description' => "Diseñamos una solución adaptada a tus requerimientos y presupuesto."
                ],
                [
                    'step' => "03",
                    'title' => "Implementación",
                    'description' => "Ejecutamos la solución con mínima interrupción de tus operaciones."
                ],
                [
                    'step' => "04",
                    'title' => "Soporte Continuo",
                    'description' => "Monitoreo, mantenimiento y optimización constante del sistema."
                ]
            ],
            'testimonials' => [
                [
                    'name' => "Carlos Rodríguez",
                    'position' => "Director IT - RetailCorp",
                    'text' => "La administración de sistemas que implementaron redujo nuestros tiempos de inactividad en un 95%. Profesionales excepcionales.",
                    'rating' => 5
                ],
                [
                    'name' => "María González",
                    'position' => "Gerente TI - SaludPlus",
                    'text' => "El soporte multi nivel transformó nuestra operación. Respuesta rápida y soluciones efectivas que mejoraron nuestra productividad.",
                    'rating' => 5
                ],
                [
                    'name' => "Roberto Sánchez",
                    'position' => "CTO - TechInnovate",
                    'text' => "Desarrollaron un software a medida que automatizó nuestros procesos clave. Increíble trabajo en equipo y atención al detalle.",
                    'rating' => 5
                ]
            ],
            'contactInfo' => $contactInfo,
            'ctaData' => $ctaData
        ]);
    }

    /**
     * Mostrar página de contacto con Inertia
     */
    public function contact()
    {
        // Si tienes un modelo Service
        // $services = \App\Models\Service::where('active', true)->get(['id', 'name']);

        // O si quieres datos estáticos
        $services = [
            ['id' => 'infrastructure', 'name' => 'Infraestructura IT'],
            ['id' => 'cloud', 'name' => 'Soluciones en la Nube'],
            ['id' => 'security', 'name' => 'Ciberseguridad y Firewall'],
            ['id' => 'consulting', 'name' => 'Consultoría IT'],
            ['id' => 'support', 'name' => 'Soporte Técnico 24/7'],
            ['id' => 'network', 'name' => 'Redes y Conectividad'],
            ['id' => 'development', 'name' => 'Desarrollo de Software'],
            ['id' => 'monitoring', 'name' => 'Monitoreo de Sistemas'],
            ['id' => 'backup', 'name' => 'Backup y Recuperación'],
            ['id' => 'maintenance', 'name' => 'Mantenimiento Preventivo'],
            ['id' => 'virtualization', 'name' => 'Virtualización'],
            ['id' => 'migration', 'name' => 'Migración de Datos'],
            ['id' => 'training', 'name' => 'Capacitación y Entrenamiento'],
            ['id' => 'custom', 'name' => 'Solución Personalizada']
        ];

        return Inertia::render('Contact/Contact', [
            'pageTitle' => 'Contacto - SoluTech',
            'metaDescription' => 'Contáctanos para consultas y soporte técnico',
            'contactInfo' => [
                'email' => 'solutech24@outlook.com',
                'phone' => '+58 412 471 45 88',
                'address' => 'Multi Centro Empresarial Del Este, Caracas/Venezuela',
                'businessHours' => 'Lunes a Viernes 9:00 AM - 6:00 PM'
            ],
            'services' => $services
        ]);
    }

    /**
     * Procesar formulario de contacto (API)
     */
    public function sendContact(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'company' => 'nullable|string|max:255',
                'service' => 'nullable|string|max:100',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|min:10'
            ]);

            // Enviar email con todos los datos
            Mail::to('solutech24@outlook.com')
                ->send(new ContactFormMail($validated));

            return response()->json([
                'success' => true,
                'message' => '¡Mensaje enviado con éxito! Te contactaremos en menos de 24 horas.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error contacto: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el mensaje. Intenta nuevamente.'
            ], 500);
        }
    }

    /**
     * Mostrar la página de nosotros.
     */
    public function about()
    {
        return Inertia::render('About/About', [
            'title' => 'About Us',
            'lastUpdated' => '2026-01-01',
            // Puedes pasar más datos aquí
        ]);
    }
}
