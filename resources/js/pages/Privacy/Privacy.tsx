import React, { useState, useEffect, useRef } from 'react';
import Navbar from '../Layouts/Navbar/Navbar';
import Footer from '../Layouts/Footer/Footer';
import './Privacy.css';

const Privacy = () => {
  const [activeSection, setActiveSection] = useState('introduccion');
  const [consents, setConsents] = useState({
    essential: true,
    analytics: false,
    marketing: false,
    thirdParty: false
  });
  const [isExpanded, setIsExpanded] = useState({});
  const [showConsentBanner, setShowConsentBanner] = useState(true);
  const [isNavVisible, setIsNavVisible] = useState(false);
  const [scrollProgress, setScrollProgress] = useState(0);
  const contentRef = useRef(null);

  const privacySections = [
    {
      id: 'introduccion',
      title: 'Introducción',
      icon: '🔒',
      important: true,
      content: 'En SoluTech C.A., nos comprometemos a proteger su privacidad y datos personales conforme al Reglamento General de Protección de Datos (RGPD) y la legislación aplicable. Esta política explica cómo recopilamos, usamos y protegemos su información.'
    },
    {
      id: 'datos-recopilados',
      title: 'Datos que Recopilamos',
      icon: '📋',
      content: 'Recopilamos información que usted nos proporciona directamente, como nombre, correo electrónico, teléfono, así como datos automáticos mediante el uso de cookies y tecnologías similares cuando interactúa con nuestros servicios.'
    },
    {
      id: 'finalidades',
      title: 'Finalidades del Tratamiento',
      icon: '🎯',
      content: 'Utilizamos sus datos para prestar nuestros servicios, mejorar su experiencia, enviarle comunicaciones relevantes y cumplir con obligaciones legales. Siempre basados en una base legal legítima.'
    },
    {
      id: 'comparticion',
      title: 'Compartición de Datos',
      icon: '🤝',
      important: true,
      content: 'No vendemos sus datos. Podemos compartirlos con proveedores de servicios bajo estrictas medidas de seguridad, o cuando sea requerido por ley. Todos nuestros socios cumplen con estándares de protección de datos.'
    },
    {
      id: 'conservacion',
      title: 'Conservación de Datos',
      icon: '⏱️',
      content: 'Conservamos sus datos mientras sean necesarios para los fines descritos, o durante los plazos legales establecidos. Posteriormente, los eliminamos de forma segura o los anonimizamos.'
    },
    {
      id: 'derechos',
      title: 'Sus Derechos',
      icon: '⚖️',
      important: true,
      content: 'Usted tiene derecho a acceder, rectificar, suprimir, limitar el tratamiento, portar sus datos y oponerse a su procesamiento. Puede ejercer estos derechos contactando a nuestro Delegado de Protección de Datos.'
    },
    {
      id: 'cookies',
      title: 'Uso de Cookies',
      icon: '🍪',
      content: 'Utilizamos cookies esenciales, analíticas y de marketing para mejorar su experiencia. Puede gestionar sus preferencias en cualquier momento desde nuestro panel de configuración.'
    },
    {
      id: 'seguridad',
      title: 'Seguridad de la Información',
      icon: '🛡️',
      content: 'Implementamos medidas técnicas y organizativas avanzadas, incluyendo cifrado SSL/TLS, autenticación multifactor y auditorías regulares para proteger sus datos contra accesos no autorizados.'
    },
    {
      id: 'actualizaciones',
      title: 'Actualizaciones de la Política',
      icon: '📢',
      content: 'Revisamos y actualizamos esta política periódicamente. Notificaremos cualquier cambio significativo a través de nuestros canales habituales y publicaremos la versión actualizada en nuestro sitio web.'
    }
  ];

  const handleConsentChange = (key, value) => {
    setConsents(prev => ({ ...prev, [key]: value }));
  };

  const handleAcceptAll = () => {
    setConsents({
      essential: true,
      analytics: true,
      marketing: true,
      thirdParty: true
    });
    setShowConsentBanner(false);
  };

  const handleRejectAll = () => {
    setConsents({
      essential: true,
      analytics: false,
      marketing: false,
      thirdParty: false
    });
    setShowConsentBanner(false);
  };

  const toggleSection = (sectionId) => {
    setIsExpanded(prev => ({
      ...prev,
      [sectionId]: !prev[sectionId]
    }));
  };

  const scrollToSection = (sectionId) => {
    const element = document.getElementById(sectionId);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'start' });
      setActiveSection(sectionId);
    }
  };

  const handleScroll = () => {
    if (contentRef.current) {
      const element = contentRef.current;
      const totalHeight = element.scrollHeight - element.clientHeight;
      const scrollPosition = element.scrollTop;
      const progress = totalHeight > 0 ? (scrollPosition / totalHeight) * 100 : 0;
      setScrollProgress(Math.min(100, progress));
    }
  };

  useEffect(() => {
    const handleScrollSpy = () => {
      const sections = privacySections.map(s => s.id);
      const current = sections.find(sectionId => {
        const element = document.getElementById(sectionId);
        if (element) {
          const rect = element.getBoundingClientRect();
          return rect.top <= 150 && rect.bottom >= 150;
        }
        return false;
      });
      if (current && current !== activeSection) {
        setActiveSection(current);
      }
    };

    window.addEventListener('scroll', handleScrollSpy);
    return () => window.removeEventListener('scroll', handleScrollSpy);
  }, [activeSection, privacySections]);

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

  return (
    <>
      <Navbar />
      
      <div className="privacy-page" ref={contentRef}>
        {/* Hero Section */}
        <section className="privacy-hero">
          <div className="privacy-hero-content">
            <div className="hero-badge">
              <span>🔒 Protección de Datos</span>
            </div>
            <h1 className="hero-title">
              Política de <span className="gradient-text">Privacidad</span>
            </h1>
            <p className="hero-description">
              En SoluTech, la protección de tus datos es nuestra prioridad. 
              Conoce cómo gestionamos tu información personal.
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
        <div className="privacy-main">
          <div className="privacy-layout">
            
            {/* Sidebar */}
            <aside className="privacy-sidebar">
              <div className="sidebar-sticky">
                <div className="sidebar-card">
                  <h3 className="sidebar-title">Contenido</h3>
                  <nav className="sidebar-nav">
                    {privacySections.map((section, index) => (
                      <button
                        key={section.id}
                        onClick={() => scrollToSection(section.id)}
                        className={`sidebar-link ${activeSection === section.id ? 'active' : ''}`}
                      >
                        <span className="link-number">{String(index + 1).padStart(2, '0')}</span>
                        <span className="link-icon">{section.icon}</span>
                        <span className="link-text">{section.title}</span>
                      </button>
                    ))}
                  </nav>
                </div>

                <div className="sidebar-card progress-card">
                  <h3 className="sidebar-title">Progreso</h3>
                  <div className="progress-circle">
                    <svg viewBox="0 0 100 100">
                      <circle cx="50" cy="50" r="45" fill="none" stroke="#e2e8f0" strokeWidth="6"/>
                      <circle 
                        cx="50" cy="50" r="45" fill="none" 
                        stroke="url(#progressGradient)" strokeWidth="6"
                        strokeDasharray={`${scrollProgress * 2.83} 283`}
                        strokeLinecap="round"
                        transform="rotate(-90 50 50)"
                      />
                      <defs>
                        <linearGradient id="progressGradient" x1="0%" y1="0%" x2="100%" y2="100%">
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
                </div>

                <div className="sidebar-card">
                  <h3 className="sidebar-title">Certificaciones</h3>
                  <div className="certifications">
                    <div className="cert-badge">ISO 27001</div>
                    <div className="cert-badge">GDPR</div>
                    <div className="cert-badge">ENS</div>
                  </div>
                </div>
              </div>
            </aside>

            {/* Content Area */}
            <div className="privacy-content-area">
              {/* Summary Cards */}
              <div className="summary-cards">
                <div className="summary-card">
                  <div className="summary-icon">🔒</div>
                  <h4>Seguridad Garantizada</h4>
                  <p>Protección de datos con estándares ISO 27001</p>
                </div>
                <div className="summary-card">
                  <div className="summary-icon">👁️</div>
                  <h4>Transparencia Total</h4>
                  <p>Comunicación clara sobre el uso de tus datos</p>
                </div>
                <div className="summary-card">
                  <div className="summary-icon">⚖️</div>
                  <h4>Control Total</h4>
                  <p>Gestiona tus datos en cualquier momento</p>
                </div>
              </div>

              {/* Accordion Sections */}
              <div className="accordion-container">
                {privacySections.map((section, index) => (
                  <div
                    key={section.id}
                    id={section.id}
                    className={`accordion-item ${activeSection === section.id ? 'active' : ''}`}
                  >
                    <div
                      className="accordion-header"
                      onClick={() => toggleSection(section.id)}
                    >
                      <div className="header-left">
                        <div className="section-number">{String(index + 1).padStart(2, '0')}</div>
                        <div className="section-icon">{section.icon}</div>
                        <h3>{section.title}</h3>
                        {section.important && (
                          <span className="important-badge">Importante</span>
                        )}
                      </div>
                      <div className="accordion-icon">
                        {isExpanded[section.id] ? '−' : '+'}
                      </div>
                    </div>
                    
                    <div className={`accordion-content ${isExpanded[section.id] ? 'expanded' : ''}`}>
                      <div className="content-inner">
                        <p>{section.content}</p>
                        
                        {section.id === 'cookies' && (
                          <div className="cookie-preferences">
                            <div className="cookie-option">
                              <span>Cookies Esenciales</span>
                              <span className="cookie-status essential">Siempre activas</span>
                            </div>
                            <div className="cookie-option">
                              <span>Cookies Analíticas</span>
                              <label className="toggle-switch">
                                <input
                                  type="checkbox"
                                  checked={consents.analytics}
                                  onChange={(e) => handleConsentChange('analytics', e.target.checked)}
                                />
                                <span className="toggle-slider"></span>
                              </label>
                            </div>
                            <div className="cookie-option">
                              <span>Cookies de Marketing</span>
                              <label className="toggle-switch">
                                <input
                                  type="checkbox"
                                  checked={consents.marketing}
                                  onChange={(e) => handleConsentChange('marketing', e.target.checked)}
                                />
                                <span className="toggle-slider"></span>
                              </label>
                            </div>
                          </div>
                        )}

                        {section.id === 'derechos' && (
                          <div className="rights-buttons">
                            <button className="rights-btn email">
                              📧 Enviar solicitud
                            </button>
                            <button className="rights-btn download">
                              ⬇️ Descargar formulario
                            </button>
                          </div>
                        )}
                      </div>
                    </div>
                  </div>
                ))}
              </div>

              {/* Contact Section */}
              <div className="contact-card">
                <div className="contact-icon">💬</div>
                <h3>¿Tiene preguntas sobre sus datos?</h3>
                <p>Contacte a nuestro Delegado de Protección de Datos</p>
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

        {/* Consent Banner */}
        {showConsentBanner && (
          <div className="consent-banner">
            <div className="consent-content">
              <div className="consent-text">
                <h3>🍪 Gestionar Cookies</h3>
                <p>Utilizamos cookies para mejorar tu experiencia. Puedes configurarlas según tus preferencias.</p>
              </div>
              <div className="consent-buttons">
                <button onClick={handleRejectAll} className="consent-btn reject">
                  Rechazar todo
                </button>
                <button onClick={handleAcceptAll} className="consent-btn accept">
                  Aceptar todo
                </button>
                <button onClick={() => setIsNavVisible(true)} className="consent-btn customize">
                  Personalizar
                </button>
              </div>
            </div>
          </div>
        )}

        {/* Footer */}
        <footer className="privacy-footer">
          <div className="footer-container">
            <p>© {new Date().getFullYear()} SoluTech C.A. Todos los derechos reservados.</p>
            <div className="footer-links">
              <a href="/aviso-legal">Aviso Legal</a>
              <span>•</span>
              <a href="/terminos-y-condiciones">Términos y Condiciones</a>
              <span>•</span>
              <a href="/cookies">Política de Cookies</a>
            </div>
          </div>
        </footer>
      </div>

      {/* Back to Top Button */}
      <button
        className="back-to-top"
        onClick={() => window.scrollTo({ top: 0, behavior: 'smooth' })}
        aria-label="Volver arriba"
      >
        <svg viewBox="0 0 20 20" fill="currentColor">
          <path fillRule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clipRule="evenodd" />
        </svg>
      </button>

      <Footer />
    </>
  );
};

export default Privacy;