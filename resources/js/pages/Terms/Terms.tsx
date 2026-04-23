import React, { useState, useEffect, useRef } from 'react';
import Navbar from '../Layouts/Navbar/Navbar';
import Footer from '../Layouts/Footer/Footer';
import './Terms.css';

const Terms = () => {
  const [accepted, setAccepted] = useState(false);
  const [showFullContent, setShowFullContent] = useState(false);
  const [scrollProgress, setScrollProgress] = useState(0);
  const [hasRead, setHasRead] = useState(false);
  const [activeSection, setActiveSection] = useState('introduccion');
  const contentRef = useRef(null);

  const terms = [
    {
      id: 'introduccion',
      title: 'Introducción',
      content: 'Estos Términos y Condiciones regulan la relación entre SoluTech C.A y nuestros clientes en la prestación de servicios tecnológicos. Al utilizar nuestros servicios, usted acepta estos términos en su totalidad.'
    },
    {
      id: 'servicios',
      title: 'Alcance de los Servicios',
      content: 'Proveemos servicios de consultoría, implementación, soporte y mantenimiento de soluciones tecnológicas. Los servicios específicos se detallan en acuerdos por separado.'
    },
    {
      id: 'responsabilidades',
      title: 'Responsabilidades',
      content: 'Nos comprometemos a proporcionar servicios con profesionalidad y diligencia. El cliente se compromete a facilitar el acceso necesario y proporcionar información precisa para la ejecución de los servicios.'
    },
    {
      id: 'confidencialidad',
      title: 'Confidencialidad',
      content: 'Ambas partes mantendrán la confidencialidad de la información compartida durante la prestación de servicios. Esta obligación permanece vigente durante y después de la terminación del contrato.'
    },
    {
      id: 'proteccion-datos',
      title: 'Protección de Datos',
      content: 'Cumplimos con las normativas de protección de datos aplicables e implementamos medidas técnicas y organizativas apropiadas para garantizar la seguridad de los datos personales.'
    },
    {
      id: 'limitacion-responsabilidad',
      title: 'Limitación de Responsabilidad',
      content: 'Nuestra responsabilidad total por cualquier reclamación no excederá el monto total pagado por el cliente en los últimos 6 meses. No somos responsables por daños indirectos o pérdidas de beneficios.'
    },
    {
      id: 'pagos',
      title: 'Términos de Pago',
      content: 'Los pagos se realizarán según lo establecido en la factura correspondiente. Los pagos vencidos incurrirán en interés aplicable según la ley.'
    },
    {
      id: 'terminacion',
      title: 'Duración y Terminación',
      content: 'El contrato tendrá la duración especificada. Cualquiera de las partes puede terminarlo por causa justificada con 30 días de antelación por escrito.'
    },
    {
      id: 'disposiciones-generales',
      title: 'Disposiciones Generales',
      content: 'Este contrato constituye el acuerdo completo entre las partes. Cualquier modificación debe realizarse por escrito y ser firmada por ambas partes.'
    }
  ];

  const highlights = [
    { icon: '🔒', text: 'Confidencialidad Garantizada', color: '#10b981' },
    { icon: '🛡️', text: 'Protección de Datos Certificada', color: '#3b82f6' },
    { icon: '⚡', text: 'Respuesta Rápida a Incidentes', color: '#f59e0b' },
    { icon: '📋', text: 'SLA Personalizado', color: '#8b5cf6' }
  ];

  const handleScroll = () => {
    if (contentRef.current) {
      const element = contentRef.current;
      const totalHeight = element.scrollHeight - element.clientHeight;
      const scrollPosition = element.scrollTop;
      const progress = totalHeight > 0 ? (scrollPosition / totalHeight) * 100 : 0;
      
      setScrollProgress(Math.min(100, progress));
      setHasRead(progress > 90);
      
      // Detectar sección activa
      const sections = document.querySelectorAll('.term-section');
      let currentSection = 'introduccion';
      sections.forEach(section => {
        const rect = section.getBoundingClientRect();
        if (rect.top <= 200 && rect.bottom >= 200) {
          currentSection = section.id;
        }
      });
      setActiveSection(currentSection);
    }
  };

  useEffect(() => {
    const element = contentRef.current;
    if (element) {
      element.addEventListener('scroll', handleScroll);
      return () => {
        if (element) {
          element.removeEventListener('scroll', handleScroll);
        }
      };
    }
  }, []);

  const scrollToSection = (sectionId) => {
    const element = document.getElementById(sectionId);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  };

  const handleAccept = () => {
    setAccepted(true);
  };

  const handleDecline = () => {
    setAccepted(false);
  };

  return (
    <>
      <Navbar />
      
      <div className="terms-page">
        {/* Hero Section */}
        <section className="terms-hero">
          <div className="terms-hero-content">
            <div className="hero-badge">
              <span>📄 Documento Legal</span>
            </div>
            <h1 className="hero-title">
              Términos y <span className="gradient-text">Condiciones</span>
            </h1>
            <p className="hero-description">
              Estos términos regulan el uso de nuestros servicios tecnológicos. 
              Por favor, léalos detenidamente antes de aceptar.
            </p>
            <div className="hero-meta">
              <div className="meta-item">
                <svg viewBox="0 0 20 20" fill="currentColor">
                  <path fillRule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clipRule="evenodd" />
                </svg>
                <span>Última actualización: <strong>1 de enero de 2026</strong></span>
              </div>
              <div className="meta-item">
                <svg viewBox="0 0 20 20" fill="currentColor">
                  <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd" />
                </svg>
                <span>Versión 2.0 - Vigente desde 2026</span>
              </div>
            </div>
          </div>
        </section>

        {/* Main Content */}
        <div className="terms-main">
          <div className="terms-layout">
            
            {/* Sidebar */}
            <aside className="terms-sidebar">
              <div className="sidebar-sticky">
                <div className="sidebar-card">
                  <h3 className="sidebar-title">Contenido</h3>
                  <nav className="sidebar-nav">
                    {terms.map((term, index) => (
                      <button
                        key={term.id}
                        onClick={() => scrollToSection(term.id)}
                        className={`sidebar-link ${activeSection === term.id ? 'active' : ''}`}
                      >
                        <span className="link-number">{String(index + 1).padStart(2, '0')}</span>
                        <span className="link-text">{term.title}</span>
                      </button>
                    ))}
                  </nav>
                </div>

                <div className="sidebar-card">
                  <h3 className="sidebar-title">Beneficios Clave</h3>
                  <div className="highlights-list">
                    {highlights.map((highlight, index) => (
                      <div key={index} className="highlight-item">
                        <div className="highlight-icon" style={{ background: highlight.color + '20', color: highlight.color }}>
                          {highlight.icon}
                        </div>
                        <span>{highlight.text}</span>
                      </div>
                    ))}
                  </div>
                </div>

                <div className="sidebar-card progress-card">
                  <h3 className="sidebar-title">Progreso de Lectura</h3>
                  <div className="progress-circle">
                    <svg viewBox="0 0 100 100">
                      <circle cx="50" cy="50" r="45" fill="none" stroke="#e2e8f0" strokeWidth="8"/>
                      <circle 
                        cx="50" cy="50" r="45" fill="none" 
                        stroke="url(#gradient)" strokeWidth="8"
                        strokeDasharray={`${scrollProgress * 2.83} 283`}
                        strokeLinecap="round"
                        transform="rotate(-90 50 50)"
                      />
                      <defs>
                        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                          <stop offset="0%" stopColor="#4f46e5"/>
                          <stop offset="100%" stopColor="#7c3aed"/>
                        </linearGradient>
                      </defs>
                    </svg>
                    <div className="progress-text">
                      <span className="progress-value">{Math.round(scrollProgress)}%</span>
                      <span className="progress-label">Leído</span>
                    </div>
                  </div>
                  {hasRead && (
                    <div className="read-badge">
                      <span>✅</span> Lectura completa
                    </div>
                  )}
                </div>
              </div>
            </aside>

            {/* Content Area */}
            <div className="terms-content-area">
              {/* Important Alert */}
              <div className="alert-card">
                <div className="alert-icon">⚠️</div>
                <div className="alert-content">
                  <h3>Información Importante</h3>
                  <p>
                    Estos términos contienen información sobre sus derechos y obligaciones. 
                    Incluyen limitaciones de responsabilidad, políticas de confidencialidad y términos de servicio.
                  </p>
                </div>
              </div>

              {/* Terms Content */}
              <div className="terms-card">
                <div 
                  ref={contentRef}
                  className={`terms-scrollable ${showFullContent ? 'expanded' : ''}`}
                >
                  {terms.map((term, index) => (
                    <div key={term.id} id={term.id} className="term-section">
                      <div className="term-header">
                        <div className="term-number">{String(index + 1).padStart(2, '0')}</div>
                        <h3>{term.title}</h3>
                      </div>
                      <div className="term-content">
                        <p>{term.content}</p>
                      </div>
                      {index < terms.length - 1 && <div className="term-divider"></div>}
                    </div>
                  ))}
                </div>

                <button 
                  onClick={() => setShowFullContent(!showFullContent)}
                  className="expand-btn"
                >
                  <span>{showFullContent ? 'Mostrar menos' : 'Mostrar más'}</span>
                  <svg className={`expand-icon ${showFullContent ? 'rotated' : ''}`} viewBox="0 0 20 20" fill="currentColor">
                    <path fillRule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clipRule="evenodd" />
                  </svg>
                </button>
              </div>

              {/* Acceptance Section */}
              <div className="acceptance-card">
                <div className="acceptance-header">
                  <h3>Aceptación de Términos</h3>
                  <div className={`acceptance-status ${accepted ? 'accepted' : 'pending'}`}>
                    {accepted ? '✓ Aceptado' : '○ Pendiente'}
                  </div>
                </div>

                <div className="checklist-grid">
                  <div className="checklist-item">
                    <div className="check-icon">✓</div>
                    <span>He leído y comprendido estos términos</span>
                  </div>
                  <div className="checklist-item">
                    <div className="check-icon">✓</div>
                    <span>Acepto las políticas de privacidad</span>
                  </div>
                  <div className="checklist-item">
                    <div className="check-icon">✓</div>
                    <span>Reconozco los límites de responsabilidad</span>
                  </div>
                </div>

                <div className="acceptance-controls">
                  <label className="checkbox-label">
                    <input
                      type="checkbox"
                      checked={accepted}
                      onChange={(e) => setAccepted(e.target.checked)}
                      disabled={!hasRead}
                    />
                    <span className="checkmark"></span>
                    <span className="checkbox-text">
                      He leído y acepto los Términos y Condiciones
                    </span>
                  </label>

                  <div className="action-buttons">
                    <button
                      onClick={handleAccept}
                      disabled={!accepted || !hasRead}
                      className="btn-accept"
                    >
                      <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                      </svg>
                      Aceptar Términos
                    </button>
                    <button onClick={handleDecline} className="btn-decline">
                      <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fillRule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clipRule="evenodd" />
                      </svg>
                      Cancelar
                    </button>
                  </div>

                  {!hasRead && (
                    <div className="read-reminder">
                      <svg viewBox="0 0 20 20" fill="currentColor">
                        <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                      </svg>
                      <span>Por favor, lea todos los términos antes de aceptar</span>
                    </div>
                  )}
                </div>
              </div>

              {/* Contact Section */}
              <div className="contact-card">
                <div className="contact-icon">💬</div>
                <h3>¿Tiene preguntas?</h3>
                <p>Contacte a nuestro equipo legal para aclaraciones sobre estos términos</p>
                <div className="contact-links">
                  <a href="mailto:solutech24@outlook.com" className="contact-link">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                      <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                      <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                    </svg>
                    solutech24@outlook.com
                  </a>
                  <a href="tel:+584124714588" className="contact-link">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                      <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                    </svg>
                    +58 412 471 45 88
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <Footer />
    </>
  );
};

export default Terms;