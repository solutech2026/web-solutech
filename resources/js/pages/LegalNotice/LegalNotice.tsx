import React, { useState, useEffect } from 'react';
import './Legal.css';

const Legal = () => {
  const [activeSection, setActiveSection] = useState('introduccion');
  const [isPrinting, setIsPrinting] = useState(false);

  // Secciones del aviso legal
  const sections = [
    {
      id: 'introduccion',
      title: 'Introducción',
      content: `El presente Aviso Legal regula el uso del sitio web de SoluTech C.A, con domicilio en Multi Centro Empresarial Del Este, Caracas/Venezuela, especializada en servicios tecnológicos y consultoría IT. Al acceder y utilizar este sitio web, usted acepta los términos y condiciones aquí establecidos.`
    },
    {
      id: 'propiedad-intelectual',
      title: 'Propiedad Intelectual',
      content: `Todos los contenidos de este sitio web, incluyendo textos, imágenes, logotipos, diseños, software y código fuente, son propiedad de SoluTech C.A o de sus licenciantes y están protegidos por las leyes de propiedad intelectual e industrial. Queda prohibida la reproducción, distribución o modificación sin autorización expresa.`
    },
    {
      id: 'responsabilidad',
      title: 'Limitación de Responsabilidad',
      content: `SoluTech C.A no se hace responsable de daños derivados del uso de la información contenida en el sitio web o de la falta de disponibilidad del mismo. La empresa se reserva el derecho de modificar, suspender o cancelar el contenido del sitio web sin previo aviso.`
    },
    {
      id: 'enlaces-externos',
      title: 'Enlaces a Terceros',
      content: `El sitio web puede contener enlaces a sitios web de terceros. SoluTech C.A no asume responsabilidad por el contenido, políticas de privacidad o prácticas de estos sitios enlazados. La inclusión de enlaces no implica una recomendación o aprobación de los contenidos.`
    },
    {
      id: 'proteccion-datos',
      title: 'Protección de Datos',
      content: `Los datos personales proporcionados a través del sitio web serán tratados conforme a lo establecido en nuestra Política de Privacidad, disponible en esta misma página web.`
    },
    {
      id: 'ley-aplicable',
      title: 'Ley Aplicable',
      content: `El presente Aviso Legal se rige por la legislación venezolana. Para cualquier controversia que pudiera derivarse del uso de este sitio web, las partes se someten a los tribunales competentes de Caracas, Venezuela.`
    },
    {
      id: 'modificaciones',
      title: 'Modificaciones',
      content: `SoluTech C.A se reserva el derecho de modificar este Aviso Legal en cualquier momento para adaptarlo a novedades legislativas o técnicas. Los cambios serán publicados en esta página con la fecha de última actualización correspondiente.`,
      lastUpdated: '1 de enero de 2026'
    }
  ];

  // Contact information
  const contactInfo = [
    {
      icon: '📍',
      title: 'Dirección',
      content: 'Multi Centro Empresarial Del Este, Caracas/Venezuela'
    },
    {
      icon: '✉️',
      title: 'Email',
      content: 'solutech24@outlook.com'
    },
    {
      icon: '📞',
      title: 'Teléfono',
      content: '+58 412 471 45 88'
    }
  ];

  // Handle print
  const handlePrint = () => {
    setIsPrinting(true);
    setTimeout(() => {
      window.print();
      setIsPrinting(false);
    }, 100);
  };

  // Scroll to section
  const scrollToSection = (sectionId: string) => {
    setActiveSection(sectionId);
    const element = document.getElementById(sectionId);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  };

  // Auto-update active section on scroll
  useEffect(() => {
    const handleScroll = () => {
      const sectionElements = sections.map(s => document.getElementById(s.id));
      const scrollPosition = window.scrollY + 100;

      for (let i = sections.length - 1; i >= 0; i--) {
        const element = sectionElements[i];
        if (element && element.offsetTop <= scrollPosition) {
          setActiveSection(sections[i].id);
          break;
        }
      }
    };

    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  return (
    <div className="legal-container">
      {/* Header */}
      <header className="legal-header">
        <div className="header-content">
          <div className="company-info">
            <div className="logo-container">
              <img
                src="/img/logo_solutech.PNG"
                alt="SoluTech Logo"
                className="logo-image"
              />
            </div>
            <div className="company-details">
              <h1>SoluTech C.A</h1>
              <p className="company-tagline">Soluciones Tecnológicas Integrales</p>
            </div>
          </div>

          <div className="header-actions">
            <button
              className="print-btn"
              onClick={handlePrint}
              aria-label="Imprimir documento"
            >
              <span className="print-icon">🖨️</span>
              <span>Imprimir</span>
            </button>
            {isPrinting && (
              <div className="print-indicator">
                Preparando para impresión...
              </div>
            )}
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="legal-main">
        {/* Hero */}
        <div className="legal-hero">
          <div className="hero-badge">📄 Documento Legal</div>
          <h2 className="legal-title">Aviso Legal</h2>
          <p className="legal-subtitle">
            Última actualización: <strong>1 de enero de 2026</strong>
          </p>
          <p className="legal-description">
            Este documento establece las condiciones de uso del sitio web de SoluTech C.A.
            Le recomendamos leerlo detenidamente antes de utilizar nuestros servicios.
          </p>
        </div>

        <div className="legal-content">
          {/* Table of Contents */}
          <aside className="toc-sidebar">
            <div className="toc-header">
              <h3>Contenido</h3>
              <div className="toc-count">
                <span>{sections.length}</span> secciones
              </div>
            </div>

            <nav className="toc-nav">
              <ul>
                {sections.map((section) => (
                  <li
                    key={section.id}
                    className={`toc-item ${activeSection === section.id ? 'active' : ''}`}
                  >
                    <button
                      onClick={() => scrollToSection(section.id)}
                      className="toc-link"
                    >
                      <span className="toc-dot"></span>
                      <span className="toc-text">{section.title}</span>
                    </button>
                  </li>
                ))}
              </ul>
            </nav>

            <div className="toc-footer">
              <div className="toc-info">
                <span className="info-icon">ℹ️</span>
                <p>Documento válido para todos los servicios de SoluTech C.A</p>
              </div>
            </div>
          </aside>

          {/* Legal Sections */}
          <div className="sections-container">
            {sections.map((section) => (
              <section
                key={section.id}
                id={section.id}
                className={`legal-section ${activeSection === section.id ? 'active' : ''}`}
              >
                <div className="section-header">
                  <div className="section-number">
                    {String(sections.findIndex(s => s.id === section.id) + 1).padStart(2, '0')}
                  </div>
                  <h3 className="section-title">{section.title}</h3>
                </div>

                <div className="section-content">
                  <p>{section.content}</p>

                  {section.lastUpdated && (
                    <div className="update-notice">
                      <span className="update-badge">🔄 Actualizado</span>
                      <span className="update-date">{section.lastUpdated}</span>
                    </div>
                  )}
                </div>
              </section>
            ))}

            {/* Contact Section */}
            <section className="contact-section">
              <div className="section-header">
                <div className="section-number">08</div>
                <h3 className="section-title">Información de Contacto</h3>
              </div>

              <div className="contact-grid">
                {contactInfo.map((item, index) => (
                  <div key={index} className="contact-card">
                    <div className="contact-icon">{item.icon}</div>
                    <div className="contact-details">
                      <h4>{item.title}</h4>
                      <p>{item.content}</p>
                    </div>
                  </div>
                ))}
              </div>
            </section>

            {/* Legal Footer */}
            <footer className="document-footer">
              <div className="footer-content">
                <div className="footer-header">
                  <div className="document-id">
                    <span>ID:</span>
                    <code>ST-LGL-2026-001</code>
                  </div>
                  <div className="document-status">
                    <span className="status-badge active">Vigente</span>
                  </div>
                </div>

                <div className="footer-copyright">
                  <p>© {new Date().getFullYear()} SoluTech C.A. Todos los derechos reservados.</p>
                  <p className="copyright-sub">
                    Este documento es propiedad intelectual de SoluTech C.A y está protegido por las leyes de propiedad intelectual.
                  </p>
                </div>

                <div className="footer-links">
                  <a href="/politica-de-privacidad" className="footer-link">
                    Política de Privacidad
                  </a>
                  <span className="link-separator">•</span>
                  <a href="/terminos-y-condiciones" className="footer-link">
                    Términos de Servicio
                  </a>
                  <span className="link-separator">•</span>
                  <a href="/cookies" className="footer-link">
                    Política de Cookies
                  </a>
                </div>
              </div>
            </footer>
          </div>
        </div>
      </main>
    </div>
  );
};

export default Legal;