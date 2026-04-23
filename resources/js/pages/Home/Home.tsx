import React from 'react';
import Navbar from '../Layouts/Navbar/Navbar';
import Footer from '../Layouts/Footer/Footer';
import './Home.css';

const Home = () => {
  // Servicios principales
  const services = [
    {
      id: 1,
      icon: '🖥️',
      title: 'Administración de Sistemas',
      description: 'Gestión integral de servidores con monitoreo 24/7 y máxima disponibilidad.',
      features: ['Backup & Recovery', 'Monitoreo en tiempo real', 'Escalabilidad', 'Seguridad avanzada'],
      color: 'blue'
    },
    {
      id: 2,
      icon: '🌐',
      title: 'Administración de Redes',
      description: 'Infraestructura de red corporativa segura y de alto rendimiento.',
      features: ['Firewalls', 'VPN corporativas', 'WiFi empresarial', 'SD-WAN'],
      color: 'purple'
    },
    {
      id: 3,
      icon: '💻',
      title: 'Desarrollo de Software',
      description: 'Soluciones tecnológicas personalizadas para automatizar procesos.',
      features: ['Web & Mobile Apps', 'APIs', 'Cloud Solutions', 'DevOps'],
      color: 'orange'
    },
    {
      id: 4,
      icon: '👨‍💼',
      title: 'Soporte Multi-Nivel',
      description: 'Asistencia técnica especializada con respuesta inmediata.',
      features: ['Soporte 24/7', 'Help Desk', 'On-site support', 'Capacitación'],
      color: 'green'
    }
  ];

  // Estadísticas
  const stats = [
    { number: '99.9%', label: 'Tiempo de Actividad' },
    { number: '24/7', label: 'Soporte Disponible' },
    { number: '+500', label: 'Clientes Satisfechos' },
    { number: '+15', label: 'Años de Experiencia' }
  ];

  // Tecnologías
  const technologies = [
    'AWS / Azure / GCP', 'Docker / Kubernetes', 'React / Angular / Vue',
    'Node.js / Python / .NET / PHP', 'Ubiquiti / MikroTik / Cisco', 'Linux / Windows Server',
    'SQL Server / MySQL / PostgreSQL', 'CI/CD / Git / GitHub Actions', 'Terraform / Ansible', 'Prometheus / Grafana'
  ];

  // Beneficios
  const benefits = [
    {
      icon: '⚡',
      title: 'Implementación Rápida',
      description: 'Despliegue ágil con mínima interrupción operativa.'
    },
    {
      icon: '🛡️',
      title: 'Seguridad Garantizada',
      description: 'Protección multi-nivel para tus datos críticos.'
    },
    {
      icon: '📈',
      title: 'Escalabilidad',
      description: 'Soluciones que crecen con tu negocio.'
    },
    {
      icon: '🤝',
      title: 'Soporte Continuo',
      description: 'Acompañamiento 24/7 en tu transformación digital.'
    }
  ];

  const openWhatsApp = () => {
    const message = 'Hola SoluTech, me gustaría solicitar una consultoría gratuita';
    window.open(`https://wa.me/584124714588?text=${encodeURIComponent(message)}`, '_blank');
  };

  return (
    <>
      <Navbar />
      
      {/* Hero Section */}
      <section className="home-hero">
        <div className="hero-container">
          <div className="hero-content">
            <div className="hero-badge">
              <span>🚀 Innovación Tecnológica</span>
            </div>
            <h1 className="hero-title">
              Tecnología que
              <span className="hero-title-accent"> Impulsa tu Éxito</span>
            </h1>
            <p className="hero-description">
              Soluciones IT integrales en administración de sistemas, redes, desarrollo de software 
              y soporte técnico multinivel para empresas que buscan crecer.
            </p>
            <div className="hero-actions">
              <button onClick={openWhatsApp} className="btn-primary">
                Solicitar Consultoría Gratuita
                <span className="btn-arrow">→</span>
              </button>
              <a href="#servicios" className="btn-secondary">
                Ver Servicios ↓
              </a>
            </div>
          </div>
        </div>
      </section>

      {/* Stats Section */}
      <section className="stats-section">
        <div className="container">
          <div className="stats-grid">
            {stats.map((stat, index) => (
              <div key={index} className="stat-card">
                <div className="stat-number">{stat.number}</div>
                <div className="stat-label">{stat.label}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Services Section */}
      <section id="servicios" className="services-section">
        <div className="container">
          <div className="section-header">
            <h2>Servicios Especializados</h2>
            <p>Soluciones completas para optimizar y proteger tu infraestructura tecnológica</p>
          </div>

          <div className="services-grid">
            {services.map((service) => (
              <div key={service.id} className={`service-card service-${service.color}`}>
                <div className="service-header">
                  <div className="service-icon">{service.icon}</div>
                  <h3>{service.title}</h3>
                </div>
                <p className="service-description">{service.description}</p>
                <div className="service-features">
                  {service.features.map((feature, idx) => (
                    <div key={idx} className="feature-item">
                      <span className="feature-check">✓</span>
                      <span>{feature}</span>
                    </div>
                  ))}
                </div>
                <button onClick={openWhatsApp} className="service-btn">
                  Solicitar Información
                  <span>→</span>
                </button>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Benefits Section */}
      <section className="benefits-section">
        <div className="container">
          <div className="section-header">
            <h2>¿Por qué Elegirnos?</h2>
            <p>Ventajas que nos diferencian y benefician a tu empresa</p>
          </div>

          <div className="benefits-grid">
            {benefits.map((benefit, index) => (
              <div key={index} className="benefit-card">
                <div className="benefit-icon">{benefit.icon}</div>
                <h4>{benefit.title}</h4>
                <p>{benefit.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Technologies Section */}
      <section className="technologies-section">
        <div className="container">
          <div className="section-header">
            <h2>Tecnologías de Vanguardia</h2>
            <p>Las mejores herramientas y plataformas del mercado</p>
          </div>

          <div className="technologies-grid">
            {technologies.map((tech, index) => (
              <div key={index} className="technology-item">
                {tech}
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="cta-section">
        <div className="container">
          <div className="cta-content">
            <div className="cta-badge">
              <span>🌟 Oferta Especial</span>
            </div>
            <h2>¿Listo para Transformar tu Empresa?</h2>
            <p>
              Agenda una auditoría gratuita de tus sistemas y descubre oportunidades 
              de optimización y mejora para tu negocio.
            </p>
            
            <div className="cta-actions">
              <button onClick={openWhatsApp} className="cta-btn-primary">
                💬 Solicitar Auditoría Gratuita
              </button>
              <button onClick={() => window.location.href = '/contacto'} className="cta-btn-secondary">
                📧 Contactar por Email
              </button>
            </div>

            <div className="cta-footer">
              <div className="cta-feature">
                <span>✅</span>
                <span>Sin compromiso</span>
              </div>
              <div className="cta-feature">
                <span>✅</span>
                <span>Respuesta 24h</span>
              </div>
              <div className="cta-feature">
                <span>✅</span>
                <span>Análisis personalizado</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <Footer />
    </>
  );
};

export default Home;