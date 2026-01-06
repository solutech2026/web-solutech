import React from 'react';
import { Head } from '@inertiajs/react';
import Navbar from '../Layouts/Navbar/Navbar';
import Footer from '../Layouts/Footer/Footer';
import './Service.css';

const Services = () => {
  // Servicios principales
  const mainServices = [
    {
      id: 1,
      title: 'Administración de Sistemas',
      description: 'Gestión integral de servidores y sistemas empresariales con monitoreo 24/7.',
      icon: '🖥️',
      color: 'blue',
      features: [
        'Gestión de servidores Windows/Linux',
        'Monitoreo proactivo 24/7',
        'Backups automatizados',
        'Optimización de recursos',
        'Mantenimiento preventivo',
        'Actualización de sistemas'
      ],
      benefits: [
        '99.9% de disponibilidad',
        'Reducción de costos operativos',
        'Mayor seguridad y compliance',
        'Escalabilidad garantizada'
      ]
    },
    {
      id: 2,
      title: 'Administración de Redes',
      description: 'Infraestructura de red corporativa segura, escalable y de alto rendimiento.',
      icon: '🌐',
      color: 'purple',
      features: [
        'Diseño e implementación de redes',
        'Firewalls y seguridad avanzada',
        'VPN corporativas',
        'WiFi empresarial',
        'Monitoreo de tráfico',
        'SD-WAN'
      ],
      benefits: [
        'Conectividad confiable',
        'Seguridad multi-nivel',
        'Optimización del ancho de banda',
        'Reducción de vulnerabilidades'
      ]
    },
    {
      id: 3,
      title: 'Soporte Multi-Nivel',
      description: 'Asistencia técnica especializada con diferentes niveles de respuesta y resolución.',
      icon: '👨‍💼',
      color: 'green',
      features: [
        'Help Desk 24/7',
        'Soporte técnico remoto',
        'Asistencia presencial',
        'Capacitación de usuarios',
        'Gestión de incidencias',
        'Soporte de emergencias'
      ],
      benefits: [
        'Respuesta inmediata',
        'Minimización de downtime',
        'Personal especializado',
        'Protocolos optimizados'
      ]
    },
    {
      id: 4,
      title: 'Desarrollo de Software',
      description: 'Soluciones tecnológicas personalizadas para automatizar y optimizar procesos.',
      icon: '💻',
      color: 'orange',
      features: [
        'Software a medida',
        'Aplicaciones web y móviles',
        'APIs y microservicios',
        'Integración de sistemas',
        'Cloud Solutions',
        'DevOps'
      ],
      benefits: [
        'Automatización de procesos',
        'Aumento de productividad',
        'Soluciones escalables',
        'ROI optimizado'
      ]
    }
  ];

  // Servicios adicionales
  const additionalServices = [
    {
      icon: '🛡️',
      title: 'Ciberseguridad',
      description: 'Protección integral contra amenazas digitales'
    },
    {
      icon: '☁️',
      title: 'Cloud Solutions',
      description: 'Migración y gestión de infraestructura cloud'
    },
    {
      icon: '📊',
      title: 'Consultoría IT',
      description: 'Asesoramiento estratégico en tecnología'
    },
    {
      icon: '⚡',
      title: 'Automatización',
      description: 'Automatización de procesos empresariales'
    }
  ];

  // Proceso de trabajo
  const processSteps = [
    {
      step: '01',
      title: 'Análisis y Diagnóstico',
      description: 'Evaluamos tu infraestructura actual y necesidades específicas.'
    },
    {
      step: '02',
      title: 'Propuesta Personalizada',
      description: 'Diseñamos una solución adaptada a tus objetivos y presupuesto.'
    },
    {
      step: '03',
      title: 'Implementación',
      description: 'Desplegamos la solución con mínima interrupción operativa.'
    },
    {
      step: '04',
      title: 'Soporte Continuo',
      description: 'Monitoreo, mantenimiento y optimización permanente.'
    }
  ];

  // Testimonios
  const testimonials = [
    {
      name: 'Carlos Rodríguez',
      company: 'RetailCorp',
      role: 'Director IT',
      text: 'La administración de sistemas redujo nuestros tiempos de inactividad en un 95%. Excelente servicio.',
      rating: 5
    },
    {
      name: 'María González',
      company: 'SaludPlus',
      role: 'Gerente TI',
      text: 'El soporte multi nivel transformó nuestra operación. Respuesta rápida y soluciones efectivas.',
      rating: 5
    },
    {
      name: 'Roberto Sánchez',
      company: 'TechInnovate',
      role: 'CTO',
      text: 'Software a medida que automatizó procesos clave. Trabajo excepcional y atención al detalle.',
      rating: 5
    }
  ];

  // Estadísticas
  const stats = [
    { number: '500+', label: 'Proyectos Completados' },
    { number: '99.9%', label: 'Tiempo de Actividad' },
    { number: '24/7', label: 'Soporte Disponible' },
    { number: '98%', label: 'Clientes Satisfechos' }
  ];

  // Contacto
  const contactInfo = {
    whatsapp: 'https://wa.me/584124714588',
    email: 'solutech24@outlook.com',
    phone: '+58 412 471 45 88',
    address: 'Multi Centro Empresarial Del Este, Caracas/Venezuela'
  };

  // Abrir WhatsApp
  const openWhatsApp = (serviceName?: string) => {
    const message = serviceName 
      ? `Hola SoluTech, me interesa el servicio de ${serviceName}`
      : 'Hola SoluTech, me gustaría obtener información sobre sus servicios';
    
    const encodedMessage = encodeURIComponent(message);
    window.open(`${contactInfo.whatsapp}?text=${encodedMessage}`, '_blank');
  };

  // Abrir email
  const openEmail = (serviceName?: string) => {
    const subject = serviceName 
      ? `Consulta: ${serviceName}` 
      : 'Consulta de Servicios';
    
    const body = serviceName
      ? `Hola SoluTech,\n\nMe interesa el servicio de ${serviceName}. Por favor, envíenme más información.\n\nSaludos.`
      : 'Hola SoluTech,\n\nMe gustaría obtener información sobre sus servicios.\n\nSaludos.';
    
    window.open(`mailto:${contactInfo.email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`, '_blank');
  };

  return (
    <>
      <Head title="Servicios - SoluTech" />
      
      <Navbar />

      {/* Hero Section */}
      <section className="services-hero">
        <div className="services-container">
          <div className="hero-content">
            <div className="hero-badge">
              <span>🔥 Soluciones que Marcan la Diferencia</span>
            </div>
            <h1 className="hero-title">
              Tecnología que
              <span className="hero-title-gradient"> Impulsa tu Negocio</span>
            </h1>
            <p className="hero-description">
              Soluciones IT integrales diseñadas para optimizar operaciones, 
              mejorar productividad y garantizar la seguridad de tu infraestructura tecnológica.
            </p>
            <div className="hero-actions">
              <button 
                onClick={() => openWhatsApp()}
                className="btn-primary"
              >
                Solicitar Consultoría Gratuita
              </button>
              <a href="#servicios" className="btn-secondary">
                Explorar Servicios ↓
              </a>
            </div>
          </div>
        </div>
      </section>

      {/* Stats Section */}
      <section className="stats-section">
        <div className="services-container">
          <div className="stats-grid">
            {stats.map((stat, index) => (
              <div key={index} className="stat-item">
                <div className="stat-number">{stat.number}</div>
                <div className="stat-label">{stat.label}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Main Services */}
      <section id="servicios" className="services-main">
        <div className="services-container">
          <div className="section-header">
            <h2>Nuestros Servicios Especializados</h2>
            <p>Soluciones tecnológicas completas para empresas de todos los tamaños</p>
          </div>

          <div className="services-grid">
            {mainServices.map((service) => (
              <div key={service.id} className={`service-card ${service.color}`}>
                <div className="service-header">
                  <div className="service-icon">{service.icon}</div>
                  <h3>{service.title}</h3>
                </div>
                
                <p className="service-description">{service.description}</p>
                
                <div className="service-details">
                  <div className="service-features">
                    <h4>Incluye:</h4>
                    <ul>
                      {service.features.map((feature, idx) => (
                        <li key={idx}>{feature}</li>
                      ))}
                    </ul>
                  </div>
                  
                  <div className="service-benefits">
                    <h4>Beneficios:</h4>
                    <ul>
                      {service.benefits.map((benefit, idx) => (
                        <li key={idx}>{benefit}</li>
                      ))}
                    </ul>
                  </div>
                </div>

                <div className="service-actions">
                  <button 
                    onClick={() => openWhatsApp(service.title)}
                    className="service-btn whatsapp"
                  >
                    📱 Consultar por WhatsApp
                  </button>
                  <button 
                    onClick={() => openEmail(service.title)}
                    className="service-btn email"
                  >
                    ✉️ Solicitar Info por Email
                  </button>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Additional Services */}
      <section className="additional-services">
        <div className="services-container">
          <div className="section-header">
            <h2>Servicios Complementarios</h2>
            <p>Soluciones específicas para necesidades especializadas</p>
          </div>

          <div className="additional-grid">
            {additionalServices.map((service, index) => (
              <div key={index} className="additional-card">
                <div className="additional-icon">{service.icon}</div>
                <h4>{service.title}</h4>
                <p>{service.description}</p>
                <button 
                  onClick={() => openWhatsApp(service.title)}
                  className="additional-link"
                >
                  Más información →
                </button>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Process Section */}
      <section className="process-section">
        <div className="services-container">
          <div className="section-header">
            <h2>Nuestro Proceso de Trabajo</h2>
            <p>Metodología probada que garantiza resultados excepcionales</p>
          </div>

          <div className="process-timeline">
            {processSteps.map((step, index) => (
              <div key={index} className="process-step">
                <div className="step-number">{step.step}</div>
                <div className="step-content">
                  <h3>{step.title}</h3>
                  <p>{step.description}</p>
                </div>
                {index < processSteps.length - 1 && (
                  <div className="step-connector"></div>
                )}
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Testimonials */}
      <section className="testimonials-section">
        <div className="services-container">
          <div className="section-header">
            <h2>Lo que Dicen Nuestros Clientes</h2>
            <p>Historias de éxito y transformación digital</p>
          </div>

          <div className="testimonials-grid">
            {testimonials.map((testimonial, index) => (
              <div key={index} className="testimonial-card">
                <div className="testimonial-header">
                  <div className="testimonial-avatar">
                    {testimonial.name.charAt(0)}
                  </div>
                  <div className="testimonial-info">
                    <h4>{testimonial.name}</h4>
                    <p className="testimonial-company">{testimonial.company}</p>
                    <p className="testimonial-role">{testimonial.role}</p>
                  </div>
                </div>
                <div className="testimonial-rating">
                  {'★'.repeat(testimonial.rating)}
                </div>
                <p className="testimonial-text">"{testimonial.text}"</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="cta-section">
        <div className="services-container">
          <div className="cta-content">
            <h2>¿Listo para Transformar tu Infraestructura IT?</h2>
            <p>
              Contáctanos hoy mismo y descubre cómo nuestros servicios pueden optimizar 
              la tecnología de tu empresa y llevar tu negocio al siguiente nivel.
            </p>
            
            <div className="cta-buttons">
              <button 
                onClick={() => openWhatsApp()}
                className="cta-btn-primary"
              >
                💬 Contactar por WhatsApp
              </button>
              <button 
                onClick={() => openEmail()}
                className="cta-btn-secondary"
              >
                📧 Enviar Email
              </button>
            </div>

            <div className="contact-info">
              <div className="contact-item">
                <span>📞</span>
                <a href={`tel:${contactInfo.phone}`}>{contactInfo.phone}</a>
              </div>
              <div className="contact-item">
                <span>✉️</span>
                <a href={`mailto:${contactInfo.email}`}>{contactInfo.email}</a>
              </div>
              <div className="contact-item">
                <span>📍</span>
                <span>{contactInfo.address}</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <Footer />
    </>
  );
};

export default Services;