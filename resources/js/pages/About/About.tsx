import React from 'react';
import { Head } from '@inertiajs/react';
import Navbar from '../Layouts/Navbar/Navbar';
import Footer from '../Layouts/Footer/Footer';
import './About.css';

const About = () => {
  // Valores de la empresa
  const values = [
    {
      icon: '🎯',
      title: 'Excelencia Técnica',
      description: 'Mantenemos los más altos estándares en cada solución que implementamos.'
    },
    {
      icon: '🤝',
      title: 'Confianza y Transparencia',
      description: 'Construimos relaciones duraderas basadas en la honestidad y claridad.'
    },
    {
      icon: '🚀',
      title: 'Innovación Constante',
      description: 'Siempre buscamos nuevas tecnologías y metodologías para mejorar.'
    },
    {
      icon: '💼',
      title: 'Compromiso Total',
      description: 'Nos dedicamos completamente al éxito de cada proyecto y cliente.'
    }
  ];

  // Estadísticas
  const stats = [
    { number: '4+', label: 'Proyectos Completados' },
    { number: '15+', label: 'Años de Experiencia' },
    { number: '99.9%', label: 'Tiempo de Actividad' },
    { number: '100%', label: 'Clientes Satisfechos' }
  ];

  // Tecnologías que manejamos
  const technologies = [
    'AWS / Azure / GCP', 'Docker', 'React / Angular / Vue',
    'Node.js / Python / .NET / PHP', 'Ubiquiti / Microtik / TP-Link', 'Linux / Windows Server',
    'SQL Server / MySQL / PostgreSQL / MongoDB / Firebase', 'CI/CD Pipelines / Git / GitHub'
  ];

  // Proceso de trabajo
  const processSteps = [
    {
      step: '01',
      title: 'Análisis y Consultoría',
      description: 'Evaluamos tus necesidades y objetivos específicos.'
    },
    {
      step: '02',
      title: 'Diseño de Solución',
      description: 'Creamos una arquitectura tecnológica personalizada.'
    },
    {
      step: '03',
      title: 'Implementación',
      description: 'Desplegamos la solución con mínima interrupción.'
    },
    {
      step: '04',
      title: 'Soporte Continuo',
      description: 'Monitoreamos y optimizamos constantemente.'
    }
  ];

  return (
    <>
      <Head title="Nosotros - SoluTech" />
      
      <Navbar />

      {/* Hero Section */}
      <section className="about-hero">
        <div className="about-container">
          <div className="about-hero-content">
            <div className="hero-badge">
              <span>🌟 Desde 2020</span>
            </div>
            <h1 className="hero-title">
              Más que un proveedor,
              <span className="gradient-text"> tu aliado tecnológico</span>
            </h1>
            <p className="hero-description">
              En SoluTech, transformamos desafíos tecnológicos en oportunidades de crecimiento. 
              Combinamos experiencia, innovación y compromiso para ofrecer soluciones que realmente marcan la diferencia.
            </p>
          </div>
        </div>
      </section>

      {/* Stats Section */}
      <section className="stats-section">
        <div className="about-container">
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

      {/* Mission & Vision */}
      <section className="mission-section">
        <div className="about-container">
          <div className="mission-grid">
            <div className="mission-card">
              <div className="card-icon">🎯</div>
              <h3>Nuestra Misión</h3>
              <p>
                Proporcionar soluciones tecnológicas innovadoras y confiables que optimicen 
                las operaciones empresariales, mejoren la productividad y impulsen el crecimiento 
                sostenible de nuestros clientes.
              </p>
            </div>
            <div className="vision-card">
              <div className="card-icon">🚀</div>
              <h3>Nuestra Visión</h3>
              <p>
                Ser el partner tecnológico preferido en Latinoamérica, reconocidos por nuestra 
                excelencia, innovación y capacidad para transformar negocios a través de 
                tecnología de vanguardia.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Values Section */}
      <section className="values-section">
        <div className="about-container">
          <div className="section-header">
            <h2>Nuestros Valores</h2>
            <p>Los principios que guían cada decisión y acción en SoluTech</p>
          </div>
          
          <div className="values-grid">
            {values.map((value, index) => (
              <div key={index} className="value-card">
                <div className="value-icon">{value.icon}</div>
                <h4>{value.title}</h4>
                <p>{value.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Process Section */}
      <section className="process-section">
        <div className="about-container">
          <div className="section-header">
            <h2>Nuestro Proceso de Trabajo</h2>
            <p>Metodología probada que garantiza resultados excepcionales</p>
          </div>
          
          <div className="process-timeline">
            {processSteps.map((step, index) => (
              <div key={index} className="process-step">
                <div className="step-number">{step.step}</div>
                <div className="step-content">
                  <h4>{step.title}</h4>
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

      {/* Technologies Section */}
      <section className="technologies-section">
        <div className="about-container">
          <div className="section-header">
            <h2>Tecnologías que Dominamos</h2>
            <p>Estamos certificados en las mejores herramientas del mercado</p>
          </div>
          
          <div className="tech-grid">
            {technologies.map((tech, index) => (
              <div key={index} className="tech-item">
                {tech}
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="about-cta">
        <div className="about-container">
          <div className="cta-content">
            <h2>¿Listo para Transformar tu Empresa?</h2>
            <p>
              Únete a las empresas que ya confían en SoluTech 
              para su transformación digital.
            </p>
            <div className="cta-buttons">
              <a href="/contacto" className="btn-primary">
                Contáctanos
              </a>
              <a href="/servicios" className="btn-secondary">
                Ver Servicios
              </a>
            </div>
          </div>
        </div>
      </section>

      <Footer />
    </>
  );
};

export default About;