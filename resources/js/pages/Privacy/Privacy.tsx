import React, { useState, useEffect, useRef } from 'react';
import './Privacy.css';

interface PrivacySection {
  id: string;
  title: string;
  content: React.ReactNode;
  important?: boolean;
}

interface PrivacyPolicyProps {
  companyName?: string;
  effectiveDate?: string;
  showConsentButtons?: boolean;
  onConsentChange?: (consents: Record<string, boolean>) => void;
}

const Privacy: React.FC<PrivacyPolicyProps> = ({
  companyName = "SoluTech C.A.",
  effectiveDate = "1 de enero de 2026",
  showConsentButtons = true,
  onConsentChange
}) => {
  const [activeSection, setActiveSection] = useState<string>('introduccion');
  const [consents, setConsents] = useState<Record<string, boolean>>({
    essential: true,
    analytics: false,
    marketing: false,
    thirdParty: false
  });
  const [isExpanded, setIsExpanded] = useState<Record<string, boolean>>({});
  const [language, setLanguage] = useState<'es' | 'en'>('es');
  const [showConsentBanner, setShowConsentBanner] = useState(showConsentButtons);
  const [isNavVisible, setIsNavVisible] = useState(false);
  const containerRef = useRef<HTMLDivElement>(null);

  const privacySections: PrivacySection[] = [
    {
      id: 'introduccion',
      title: 'Introducción',
      content: (
        <div className="compact-section">
          <p><strong>{companyName}</strong> se compromete a proteger su privacidad conforme al RGPD (UE) 2016/679.</p>
          <div className="compact-contact">
            <div className="contact-row">
              <span className="label">DPO:</span>
              <span>soporteitsolutech@gmail.com</span>
            </div>
            <div className="contact-row">
              <span className="label">Teléfono:</span>
              <span>+58 412 471 45 88</span>
            </div>
          </div>
        </div>
      ),
      important: true
    },
    {
      id: 'datos-recopilados',
      title: 'Datos Recopilados',
      content: (
        <div className="compact-data">
          <div className="data-category">
            <h4>Directos</h4>
            <ul>
              <li>Identificación y contacto</li>
              <li>Información profesional</li>
              <li>Datos de facturación</li>
            </ul>
          </div>
          <div className="data-category">
            <h4>Automáticos</h4>
            <ul>
              <li>Técnicos y de uso</li>
              <li>Cookies</li>
              <li>Metadatos</li>
            </ul>
          </div>
        </div>
      )
    },
    {
      id: 'finalidades',
      title: 'Finalidades',
      content: (
        <div className="compact-purposes">
          <div className="purpose-tag essential">Contratos</div>
          <div className="purpose-tag improvement">Mejora</div>
          <div className="purpose-tag marketing">Marketing</div>
          <div className="purpose-tag security">Seguridad</div>
          <p className="legal-basis">Bases: Contrato, Consentimiento, Interés legítimo</p>
        </div>
      )
    },
    {
      id: 'comparticion',
      title: 'Compartición',
      content: (
        <div className="compact-sharing">
          <div className="sharing-item">
            <span className="icon">☁️</span>
            <span>Proveedores Cloud</span>
          </div>
          <div className="sharing-item">
            <span className="icon">🤝</span>
            <span>Partners</span>
          </div>
          <div className="sharing-item">
            <span className="icon">⚖️</span>
            <span>Autoridades</span>
          </div>
        </div>
      ),
      important: true
    },
    {
      id: 'conservacion',
      title: 'Conservación',
      content: (
        <div className="compact-retention">
          <div className="retention-item">
            <div className="duration">5 años +</div>
            <div className="description">Clientes activos</div>
          </div>
          <div className="retention-item">
            <div className="duration">10 años</div>
            <div className="description">Facturación</div>
          </div>
          <div className="retention-item">
            <div className="duration">Hasta revocación</div>
            <div className="description">Marketing</div>
          </div>
        </div>
      )
    },
    {
      id: 'derechos',
      title: 'Sus Derechos',
      content: (
        <div className="compact-rights">
          <div className="rights-grid">
            <span className="right-tag">Acceso</span>
            <span className="right-tag">Rectificación</span>
            <span className="right-tag">Supresión</span>
            <span className="right-tag">Portabilidad</span>
            <span className="right-tag">Limitación</span>
            <span className="right-tag">Oposición</span>
          </div>
          <button
            className="rights-action-btn"
            onClick={() => window.location.href = 'mailto:dpo@techsolutions.com'}
          >
            Ejercer derechos
          </button>
        </div>
      ),
      important: true
    },
    {
      id: 'cookies',
      title: 'Cookies',
      content: (
        <div className="compact-cookies">
          <div className="cookie-category">
            <span className="cookie-status essential">Esenciales</span>
            <span className="cookie-desc">Siempre activas</span>
          </div>

          <div className="cookie-category">
            <span className="cookie-status">Analíticas</span>
            <label className="toggle-switch compact">
              <input
                type="checkbox"
                checked={consents.analytics}
                onChange={(e) => handleConsentChange('analytics', e.target.checked)}
                aria-label="Cookies analíticas"
              />
              <span className="toggle-slider"></span>
              <span className="sr-only">Cookies analíticas</span>
            </label>
          </div>

          <div className="cookie-category">
            <span className="cookie-status">Marketing</span>
            <label className="toggle-switch compact">
              <input
                type="checkbox"
                checked={consents.marketing}
                onChange={(e) => handleConsentChange('marketing', e.target.checked)}
                aria-label="Cookies de marketing"
              />
              <span className="toggle-slider"></span>
              <span className="sr-only">Cookies de marketing</span>
            </label>
          </div>
        </div>
      )
    },
    {
      id: 'seguridad',
      title: 'Seguridad',
      content: (
        <div className="compact-security">
          <div className="security-badges">
            <span className="badge">ISO 27001</span>
            <span className="badge">GDPR</span>
            <span className="badge">ENS</span>
          </div>
          <ul>
            <li>Encriptación SSL/TLS</li>
            <li>Cifrado AES-256</li>
            <li>MFA para accesos</li>
            <li>Backups automáticos</li>
          </ul>
        </div>
      )
    },
    {
      id: 'actualizaciones',
      title: 'Actualizaciones',
      content: (
        <div className="compact-updates">
          <div className="version-info">
            <span className="version">v3.2</span>
            <span className="date">{effectiveDate}</span>
          </div>
          <p>Notificaremos cambios por email y web.</p>
        </div>
      )
    }
  ];

  const handleConsentChange = (key: string, value: boolean) => {
    const newConsents = { ...consents, [key]: value };
    setConsents(newConsents);
    if (onConsentChange) {
      onConsentChange(newConsents);
    }
  };

  const handleAcceptAll = () => {
    const allAccepted = {
      essential: true,
      analytics: true,
      marketing: true,
      thirdParty: true
    };
    setConsents(allAccepted);
    if (onConsentChange) {
      onConsentChange(allAccepted);
    }
    setShowConsentBanner(false);
  };

  const handleRejectAll = () => {
    const allRejected = {
      essential: true,
      analytics: false,
      marketing: false,
      thirdParty: false
    };
    setConsents(allRejected);
    if (onConsentChange) {
      onConsentChange(allRejected);
    }
    setShowConsentBanner(false);
  };

  const handleSavePreferences = () => {
    if (onConsentChange) {
      onConsentChange(consents);
    }
    setShowConsentBanner(false);
  };

  const toggleSection = (sectionId: string) => {
    setIsExpanded(prev => ({
      ...prev,
      [sectionId]: !prev[sectionId]
    }));
  };

  const scrollToSection = (sectionId: string) => {
    const element = document.getElementById(sectionId);
    if (element) {
      const offset = 80;
      const elementPosition = element.offsetTop - offset;
      window.scrollTo({
        top: elementPosition,
        behavior: 'smooth'
      });
      setActiveSection(sectionId);
      setIsNavVisible(false);
    }
  };

  useEffect(() => {
    const handleScroll = () => {
      const sections = privacySections.map(s => s.id);
      const current = sections.find(sectionId => {
        const element = document.getElementById(sectionId);
        if (element) {
          const rect = element.getBoundingClientRect();
          return rect.top <= 100 && rect.bottom >= 100;
        }
        return false;
      });
      if (current && current !== activeSection) {
        setActiveSection(current);
      }
    };

    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, [privacySections]);

  return (
    <div className="privacy-modern" ref={containerRef}>
      {/* Header compacto */}
      <header className="privacy-header">
        <div className="header-main">
          <div className="header-logo">
            <div className="shield-icon">
              <svg viewBox="0 0 24 24">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z" />
              </svg>
            </div>
            <div className="header-titles">
              <h1>Política de Privacidad</h1>
              <p className="company">{companyName}</p>
            </div>
          </div>

          <div className="header-actions">
            <div className="language-selector">
              <button
                className={`lang-btn ${language === 'es' ? 'active' : ''}`}
                onClick={() => setLanguage('es')}
              >
                ES
              </button>
              <button
                className={`lang-btn ${language === 'en' ? 'active' : ''}`}
                onClick={() => setLanguage('en')}
              >
                EN
              </button>
            </div>

            <div className="header-meta">
              <span className="effective-date">
                <span className="date-icon">📅</span>
                {effectiveDate}
              </span>
              <span className="compliance-badge">GDPR</span>
            </div>

            <button
              className="nav-toggle"
              onClick={() => setIsNavVisible(!isNavVisible)}
              aria-label="Toggle navigation"
            >
              <span className={`nav-icon ${isNavVisible ? 'open' : ''}`}>
                <span></span>
                <span></span>
                <span></span>
              </span>
            </button>
          </div>
        </div>
      </header>

      {/* Consent Banner flotante */}
      {showConsentBanner && (
        <div className="consent-floating-banner">
          <div className="consent-content">
            <div className="consent-header">
              <h3>🎯 Preferencias de Privacidad</h3>
              <button
                className="close-consent"
                onClick={() => setShowConsentBanner(false)}
                aria-label="Cerrar"
              >
                ×
              </button>
            </div>
            <p>Gestiona cómo procesamos tus datos.</p>

            <div className="quick-consent">
              <button className="btn-consent accept-all" onClick={handleAcceptAll}>
                Aceptar todo
              </button>
              <button className="btn-consent reject-all" onClick={handleRejectAll}>
                Solo esenciales
              </button>
              <button className="btn-consent customize" onClick={() => setIsNavVisible(true)}>
                Personalizar
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Navegación lateral móvil */}
      <aside className={`privacy-nav ${isNavVisible ? 'visible' : ''}`}>
        <div className="nav-header">
          <h3>Navegación</h3>
          <button
            className="nav-close"
            onClick={() => setIsNavVisible(false)}
            aria-label="Cerrar navegación"
          >
            ×
          </button>
        </div>

        <div className="nav-sections">
          {privacySections.map(section => (
            <button
              key={section.id}
              className={`nav-item ${activeSection === section.id ? 'active' : ''} ${section.important ? 'important' : ''}`}
              onClick={() => scrollToSection(section.id)}
            >
              <span className="nav-number">{privacySections.indexOf(section) + 1}</span>
              <span className="nav-title">{section.title}</span>
              {section.important && <span className="nav-important">!</span>}
            </button>
          ))}
        </div>

        <div className="nav-actions">
          <button className="nav-action-btn" onClick={handleSavePreferences}>
            💾 Guardar preferencias
          </button>
          <button className="nav-action-btn secondary" onClick={() => window.print()}>
            🖨️ Imprimir
          </button>
        </div>
      </aside>

      {/* Contenido principal */}
      <main className="privacy-content">
        {/* Resumen ejecutivo compacto */}
        <section className="executive-summary-compact">
          <h2>Resumen Ejecutivo</h2>
          <div className="summary-cards">
            <div className="summary-card">
              <div className="card-icon">🔒</div>
              <h4>Seguridad</h4>
              <p>Protección de datos con estándares ISO 27001 y cifrado avanzado.</p>
            </div>
            <div className="summary-card">
              <div className="card-icon">👁️</div>
              <h4>Transparencia</h4>
              <p>Comunicación clara sobre el uso de sus datos personales.</p>
            </div>
            <div className="summary-card">
              <div className="card-icon">⚖️</div>
              <h4>Derechos</h4>
              <p>Control total sobre sus datos en cualquier momento.</p>
            </div>
          </div>
        </section>

        {/* Secciones acordeón */}
        <div className="accordion-sections">
          {privacySections.map(section => (
            <div
              key={section.id}
              id={section.id}
              className={`accordion-section ${activeSection === section.id ? 'active' : ''} ${isExpanded[section.id] ? 'expanded' : ''}`}
            >
              <div
                className="accordion-header"
                onClick={() => toggleSection(section.id)}
              >
                <div className="header-content">
                  <span className="section-number">
                    {String(privacySections.indexOf(section) + 1).padStart(2, '0')}
                  </span>
                  <h3>{section.title}</h3>
                  {section.important && (
                    <span className="important-badge">Importante</span>
                  )}
                </div>
                <span className="accordion-icon">
                  {isExpanded[section.id] ? '−' : '+'}
                </span>
              </div>

              <div className="accordion-content">
                <div className="content-wrapper">
                  {section.content}
                </div>

                {section.id === 'derechos' && (
                  <div className="rights-actions">
                    <h4>Ejercer derechos:</h4>
                    <div className="action-buttons">
                      <button className="action-btn email">
                        📧 Enviar email
                      </button>
                      <button className="action-btn download">
                        ⬇️ Formulario PDF
                      </button>
                    </div>
                  </div>
                )}
              </div>
            </div>
          ))}
        </div>

        {/* Footer compacto */}
        <footer className="privacy-footer-compact">
          <div className="footer-content">
            <div className="contact-info">
              <h4>Contacto CEO</h4>
              <div className="contact-details">
                <a href="mailto:dpo@techsolutions.com" className="contact-link">
                  solutech24@outlook.com
                </a>
                <span className="contact-phone">+58 412 4714588</span>
              </div>
            </div>

            <div className="footer-links">
              <a href="/aviso-legal">Aviso Legal</a>
              <a href="/cookies">Cookies</a>
              <a href="/terminos">Términos</a>
            </div>

            <div className="copyright">
              <p>© {new Date().getFullYear()} {companyName}</p>
              <p className="version">v1.0 | {effectiveDate}</p>
            </div>
          </div>
        </footer>
      </main>

      {/* Botón de scroll to top */}
      <button
        className="scroll-top-btn"
        onClick={() => window.scrollTo({ top: 0, behavior: 'smooth' })}
        aria-label="Volver arriba"
      >
        ↑
      </button>
    </div>
  );
};

export default Privacy;