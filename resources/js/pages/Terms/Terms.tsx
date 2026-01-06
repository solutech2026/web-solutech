import React, { useState, useEffect, useRef } from 'react';
import './Terms.css';

const Terms = () => {
  const [accepted, setAccepted] = useState(false);
  const [showFullContent, setShowFullContent] = useState(false);
  const [scrollProgress, setScrollProgress] = useState(0);
  const [hasRead, setHasRead] = useState(false);
  const contentRef = useRef<HTMLDivElement>(null);

  // Términos y condiciones
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

  // Beneficios destacados
  const highlights = [
    { icon: '🔒', text: 'Confidencialidad garantizada' },
    { icon: '🛡️', text: 'Protección de datos certificada' },
    { icon: '⚡', text: 'Respuesta rápida a incidentes' },
    { icon: '📋', text: 'SLA personalizado' }
  ];

  // Manejar scroll
  const handleScroll = () => {
    if (contentRef.current) {
      const element = contentRef.current;
      const totalHeight = element.scrollHeight - element.clientHeight;
      const scrollPosition = element.scrollTop;
      const progress = totalHeight > 0 ? (scrollPosition / totalHeight) * 100 : 0;
      
      setScrollProgress(Math.min(100, progress));
      setHasRead(progress > 90);
    }
  };

  useEffect(() => {
    const element = contentRef.current;
    if (element) {
      element.addEventListener('scroll', handleScroll);
      return () => element.removeEventListener('scroll', handleScroll);
    }
  }, []);

  // Manejar aceptación
  const handleAccept = () => {
    setAccepted(true);
    console.log('Términos aceptados');
  };

  // Manejar rechazo
  const handleDecline = () => {
    setAccepted(false);
    console.log('Términos rechazados');
  };

  // Scroll to section
  const scrollToSection = (sectionId: string) => {
    const element = document.getElementById(sectionId);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  };

  return (
    <div className="terms-container">
      {/* Header */}
      <header className="terms-header">
        <div className="header-content">
          <div className="header-badge">📄 Documento Legal</div>
          <h1 className="terms-title">Términos y Condiciones</h1>
          <p className="terms-subtitle">
            Última actualización: <strong>1 de enero de 2026</strong>
          </p>
          <p className="terms-description">
            Estos términos regulan el uso de nuestros servicios tecnológicos. 
            Por favor, léalos detenidamente antes de aceptar.
          </p>
        </div>
      </header>

      {/* Main Content */}
      <main className="terms-main">
        <div className="terms-content-layout">
          {/* Sidebar */}
          <aside className="terms-sidebar">
            <div className="sidebar-section">
              <h3>Contenido</h3>
              <nav className="sidebar-nav">
                <ul>
                  {terms.map((term, index) => (
                    <li key={term.id}>
                      <button 
                        onClick={() => scrollToSection(term.id)}
                        className="sidebar-link"
                      >
                        <span className="link-number">{String(index + 1).padStart(2, '0')}</span>
                        <span className="link-text">{term.title}</span>
                      </button>
                    </li>
                  ))}
                </ul>
              </nav>
            </div>

            <div className="sidebar-section">
              <h3>Beneficios</h3>
              <div className="highlights">
                {highlights.map((highlight, index) => (
                  <div key={index} className="highlight-item">
                    <span className="highlight-icon">{highlight.icon}</span>
                    <span className="highlight-text">{highlight.text}</span>
                  </div>
                ))}
              </div>
            </div>

            <div className="sidebar-info">
              <div className="info-badge">
                <span>ℹ️</span>
                <p>Aceptación requerida para el uso de servicios</p>
              </div>
            </div>
          </aside>

          {/* Main Content Area */}
          <div className="terms-content-area">
            {/* Important Notice */}
            <div className="important-notice">
              <div className="notice-icon">⚠️</div>
              <div>
                <h3>Importante</h3>
                <p>
                  Estos términos contienen información importante sobre sus derechos y obligaciones. 
                  Incluyen limitaciones de responsabilidad, políticas de confidencialidad y términos de servicio.
                </p>
              </div>
            </div>

            {/* Terms Content */}
            <div className="terms-content-wrapper">
              <div 
                ref={contentRef}
                className={`terms-content ${showFullContent ? 'expanded' : ''}`}
              >
                {terms.map((term, index) => (
                  <section key={term.id} id={term.id} className="term-section">
                    <div className="section-header">
                      <div className="section-number">{String(index + 1).padStart(2, '0')}</div>
                      <h3>{term.title}</h3>
                    </div>
                    <div className="section-content">
                      <p>{term.content}</p>
                    </div>
                    {index < terms.length - 1 && (
                      <div className="section-divider"></div>
                    )}
                  </section>
                ))}
              </div>

              {/* Expand/Collapse Button */}
              <button 
                onClick={() => setShowFullContent(!showFullContent)}
                className="expand-btn"
              >
                {showFullContent ? 'Mostrar menos' : 'Mostrar más'}
                <span className="expand-icon">{showFullContent ? '↑' : '↓'}</span>
              </button>

              {/* Scroll Progress */}
              <div className="scroll-progress">
                <div className="progress-bar">
                  <div 
                    className="progress-fill"
                    style={{ width: `${scrollProgress}%` }}
                  ></div>
                </div>
                <div className="progress-info">
                  <span>{Math.round(scrollProgress)}% leído</span>
                  {hasRead && <span className="read-complete">✅ Completo</span>}
                </div>
              </div>
            </div>

            {/* Acceptance Section */}
            <div className="acceptance-section">
              <div className="acceptance-header">
                <h3>Aceptación de Términos</h3>
                <div className="acceptance-status">
                  {accepted ? '✅ Aceptado' : '⏳ Pendiente'}
                </div>
              </div>

              <div className="acceptance-checklist">
                <div className="checklist-item">
                  <span className="check-icon">✓</span>
                  <span>He leído y comprendido estos términos</span>
                </div>
                <div className="checklist-item">
                  <span className="check-icon">✓</span>
                  <span>Acepto las políticas de privacidad</span>
                </div>
                <div className="checklist-item">
                  <span className="check-icon">✓</span>
                  <span>Reconozco los límites de responsabilidad</span>
                </div>
              </div>

              <div className="acceptance-controls">
                <div className="acceptance-checkbox">
                  <input
                    type="checkbox"
                    id="acceptTerms"
                    checked={accepted}
                    onChange={(e) => setAccepted(e.target.checked)}
                    disabled={!hasRead}
                  />
                  <label htmlFor="acceptTerms">
                    He leído y acepto los Términos y Condiciones
                  </label>
                </div>

                <div className="acceptance-buttons">
                  <button
                    onClick={handleAccept}
                    disabled={!accepted || !hasRead}
                    className={`accept-btn ${accepted && hasRead ? 'active' : 'disabled'}`}
                  >
                    ✅ Aceptar Términos
                  </button>
                  <button
                    onClick={handleDecline}
                    className="decline-btn"
                  >
                    ❌ Cancelar
                  </button>
                </div>

                {!hasRead && (
                  <p className="read-reminder">
                    ⚠️ Por favor, lea todos los términos antes de aceptar
                  </p>
                )}
              </div>
            </div>

            {/* Contact Info */}
            <div className="contact-info">
              <h3>¿Tiene preguntas?</h3>
              <p>Contacte a nuestro equipo legal para aclaraciones sobre estos términos:</p>
              <div className="contact-details">
                <div className="contact-item">
                  <span>📧</span>
                  <span>solutech24@outlook.com</span>
                </div>
                <div className="contact-item">
                  <span>📞</span>
                  <span>+58 412 471 45 88</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>

      {/* Footer */}
      <footer className="terms-footer">
        <div className="footer-content">
          <div className="footer-info">
            <p>© {new Date().getFullYear()} SoluTech C.A. Todos los derechos reservados.</p>
            <p className="footer-sub">Versión 1.0 de los Términos y Condiciones</p>
          </div>
          <div className="footer-links">
            <a href="/aviso-legal">Aviso Legal</a>
            <span>•</span>
            <a href="/politica-privacidad">Política de Privacidad</a>
            <span>•</span>
            <a href="/cookies">Cookies</a>
          </div>
        </div>
      </footer>
    </div>
  );
};

export default Terms;