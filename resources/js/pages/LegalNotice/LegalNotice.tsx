import React, { useState, useEffect, useRef } from 'react';
import Navbar from '../Layouts/Navbar/Navbar';
import Footer from '../Layouts/Footer/Footer';
import './Legal.css';

const Legal = () => {
  const [activeSection, setActiveSection] = useState('introduccion');
  const [scrollProgress, setScrollProgress] = useState(0);
  const contentRef = useRef(null);

  const sections = [
    {
      id: 'introduccion',
      title: 'Introducción',
      icon: '📋',
      content: 'El presente Aviso Legal regula el uso del sitio web de SoluTech C.A, con domicilio en Multi Centro Empresarial Del Este, Caracas/Venezuela, especializada en servicios tecnológicos y consultoría IT. Al acceder y utilizar este sitio web, usted acepta los términos y condiciones aquí establecidos.'
    },
    {
      id: 'propiedad-intelectual',
      title: 'Propiedad Intelectual',
      icon: '©️',
      content: 'Todos los contenidos de este sitio web, incluyendo textos, imágenes, logotipos, diseños, software y código fuente, son propiedad de SoluTech C.A o de sus licenciantes y están protegidos por las leyes de propiedad intelectual e industrial. Queda prohibida la reproducción, distribución o modificación sin autorización expresa.'
    },
    {
      id: 'responsabilidad',
      title: 'Limitación de Responsabilidad',
      icon: '🛡️',
      content: 'SoluTech C.A no se hace responsable de daños derivados del uso de la información contenida en el sitio web o de la falta de disponibilidad del mismo. La empresa se reserva el derecho de modificar, suspender o cancelar el contenido del sitio web sin previo aviso.'
    },
    {
      id: 'enlaces-externos',
      title: 'Enlaces a Terceros',
      icon: '🔗',
      content: 'El sitio web puede contener enlaces a sitios web de terceros. SoluTech C.A no asume responsabilidad por el contenido, políticas de privacidad o prácticas de estos sitios enlazados. La inclusión de enlaces no implica una recomendación o aprobación de los contenidos.'
    },
    {
      id: 'proteccion-datos',
      title: 'Protección de Datos',
      icon: '🔒',
      content: 'Los datos personales proporcionados a través del sitio web serán tratados conforme a lo establecido en nuestra Política de Privacidad, disponible en esta misma página web.'
    },
    {
      id: 'ley-aplicable',
      title: 'Ley Aplicable',
      icon: '⚖️',
      content: 'El presente Aviso Legal se rige por la legislación venezolana. Para cualquier controversia que pudiera derivarse del uso de este sitio web, las partes se someten a los tribunales competentes de Caracas, Venezuela.'
    },
    {
      id: 'modificaciones',
      title: 'Modificaciones',
      icon: '📢',
      content: 'SoluTech C.A se reserva el derecho de modificar este Aviso Legal en cualquier momento para adaptarlo a novedades legislativas o técnicas. Los cambios serán publicados en esta página con la fecha de última actualización correspondiente.',
      lastUpdated: '1 de enero de 2026'
    }
  ];

  const contactInfo = [
    {
      icon: '📍',
      title: 'Dirección',
      content: 'Multi Centro Empresarial Del Este, Caracas/Venezuela'
    },
    {
      icon: '✉️',
      title: 'Email',
      content: 'solutech24@outlook.com',
      link: 'mailto:solutech24@outlook.com'
    },
    {
      icon: '📞',
      title: 'Teléfono',
      content: '+58 412 471 45 88',
      link: 'tel:+584124714588'
    }
  ];

  const handleScroll = () => {
    if (contentRef.current) {
      const element = contentRef.current;
      const totalHeight = element.scrollHeight - element.clientHeight;
      const scrollPosition = element.scrollTop;
      const progress = totalHeight > 0 ? (scrollPosition / totalHeight) * 100 : 0;
      setScrollProgress(Math.min(100, progress));
    }

    // Update active section based on scroll position
    const sectionsElements = sections.map(s => document.getElementById(s.id));
    const scrollPositionWindow = window.scrollY + 200;

    for (let i = sectionsElements.length - 1; i >= 0; i--) {
      const element = sectionsElements[i];
      if (element && element.offsetTop <= scrollPositionWindow) {
        setActiveSection(sections[i].id);
        break;
      }
    }
  };

  useEffect(() => {
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const scrollToSection = (sectionId) => {
    const element = document.getElementById(sectionId);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'start' });
      setActiveSection(sectionId);
    }
  };

  const handlePrint = () => {
    window.print();
  };

  return (
    <>
      <Navbar />
      
      <div className="legal-page" ref={contentRef}>
        {/* Hero Section */}
        <section className="legal-hero">
          <div className="legal-hero-content">
            <div className="hero-badge">
              <span>📄 Documento Legal</span>
            </div>
            <h1 className="hero-title">
              Aviso <span className="gradient-text">Legal</span>
            </h1>
            <p className="hero-description">
              Este documento establece las condiciones de uso del sitio web de SoluTech C.A.
              Le recomendamos leerlo detenidamente antes de utilizar nuestros servicios.
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
              <button onClick={handlePrint} className="print-btn">
                <svg viewBox="0 0 20 20" fill="currentColor">
                  <path fillRule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clipRule="evenodd" />
                </svg>
                Imprimir
              </button>
            </div>
          </div>
        </section>

        {/* Main Content */}
        <div className="legal-main">
          <div className="legal-layout">
            
            {/* Sidebar */}
            <aside className="legal-sidebar">
              <div className="sidebar-sticky">
                <div className="sidebar-card">
                  <h3 className="sidebar-title">Contenido</h3>
                  <nav className="sidebar-nav">
                    {sections.map((section, index) => (
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
                  <h3 className="sidebar-title">Progreso de Lectura</h3>
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
                  <h3 className="sidebar-title">Documento</h3>
                  <div className="doc-info">
                    <div className="doc-id">
                      <span>ID:</span>
                      <code>ST-LGL-2026-001</code>
                    </div>
                    <div className="doc-status">
                      <span className="status-badge">✓ Vigente</span>
                    </div>
                  </div>
                </div>
              </div>
            </aside>

            {/* Content Area */}
            <div className="legal-content-area">
              {/* Legal Sections */}
              <div className="sections-container">
                {sections.map((section, index) => (
                  <div
                    key={section.id}
                    id={section.id}
                    className={`legal-card ${activeSection === section.id ? 'active' : ''}`}
                  >
                    <div className="card-header">
                      <div className="card-number">{String(index + 1).padStart(2, '0')}</div>
                      <div className="card-icon">{section.icon}</div>
                      <h3>{section.title}</h3>
                    </div>
                    <div className="card-content">
                      <p>{section.content}</p>
                      {section.lastUpdated && (
                        <div className="update-badge">
                          <span>🔄 Actualizado</span>
                          <span>{section.lastUpdated}</span>
                        </div>
                      )}
                    </div>
                  </div>
                ))}
              </div>

              {/* Contact Section */}
              <div className="contact-card">
                <div className="contact-header">
                  <div className="contact-number">08</div>
                  <h3>Información de Contacto</h3>
                </div>
                <div className="contact-grid">
                  {contactInfo.map((item, index) => (
                    <div key={index} className="contact-item">
                      <div className="contact-icon">{item.icon}</div>
                      <div className="contact-details">
                        <h4>{item.title}</h4>
                        {item.link ? (
                          <a href={item.link}>{item.content}</a>
                        ) : (
                          <p>{item.content}</p>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Footer Note */}
              <div className="legal-note">
                <div className="note-header">
                  <span className="note-icon">⚠️</span>
                  <span>Nota Informativa</span>
                </div>
                <p>
                  Este documento es propiedad intelectual de SoluTech C.A y está protegido por las leyes de propiedad intelectual.
                  Queda prohibida su reproducción total o parcial sin autorización expresa.
                </p>
              </div>
            </div>
          </div>
        </div>

        {/* Footer */}
        <footer className="legal-footer">
          <div className="footer-container">
            <p>© {new Date().getFullYear()} SoluTech C.A. Todos los derechos reservados.</p>
            <div className="footer-links">
              <a href="/politica-de-privacidad">Política de Privacidad</a>
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

export default Legal;