import React, { useState, useEffect } from 'react';
import './Footer.css';

const Footer = () => {
  const [showBackToTop, setShowBackToTop] = useState(false);
  const [email, setEmail] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [subscriptionMessage, setSubscriptionMessage] = useState('');
  const [subscriptionStatus, setSubscriptionStatus] = useState(''); // 'success', 'error', ''

  const services = [
    { name: 'Admin. de Sistemas', short: 'Sistemas' },
    { name: 'Admin. de Redes', short: 'Redes' },
    { name: 'Soporte Multi-Nivel', short: 'Soporte' },
    { name: 'Desarrollo de Software', short: 'Desarrollo' },
  ];

  const quickLinks = [
    { name: 'Inicio', url: '/' },
    { name: 'Servicios', url: '/servicio' },
    { name: 'Nosotros', url: '/about-us' },
    { name: 'Contacto', url: '/contacto' },
  ];

  const contactInfo = {
    address: 'Multi Centro Empresarial Del Este, Caracas/Venezuela',
    phone: '+58 412 471 45 88',
    email: 'solutech24@outlook.com',
    hours: 'Lun-Vie: 8:00 AM - 6:00 PM'
  };

  const socialMedia = [
    { name: 'LinkedIn', icon: '↗', url: 'https://linkedin.com' },
    { name: 'Instagram', icon: '↗', url: 'https://instagram.com' },
  ];

  const newsletterSubscribe = async (e) => {
    e.preventDefault();

    if (!email) {
      setSubscriptionStatus('error');
      setSubscriptionMessage('Por favor ingresa tu email');
      return;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      setSubscriptionStatus('error');
      setSubscriptionMessage('Por favor ingresa un email válido');
      return;
    }

    setIsSubmitting(true);
    setSubscriptionMessage('');
    setSubscriptionStatus('');

    try {
      const response = await fetch('/newsletter/subscribe', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ email })
      });

      const data = await response.json();

      if (response.ok && data.success) {
        setSubscriptionStatus('success');
        setSubscriptionMessage(data.message || '¡Te has suscrito exitosamente!');
        setEmail('');

        // Limpiar mensaje después de 5 segundos
        setTimeout(() => {
          setSubscriptionMessage('');
          setSubscriptionStatus('');
        }, 5000);
      } else {
        setSubscriptionStatus('error');
        setSubscriptionMessage(data.message || 'Error al procesar la suscripción');
      }
    } catch (error) {
      console.error('Error en suscripción:', error);
      setSubscriptionStatus('error');
      setSubscriptionMessage('Error de conexión. Por favor intenta nuevamente.');
    } finally {
      setIsSubmitting(false);
    }
  };

  useEffect(() => {
    const handleScroll = () => {
      setShowBackToTop(window.pageYOffset > 300);
    };

    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  return (
    <footer className="footer-modern-minimal">
      <div className="footer-container">

        {/* Main Content Grid */}
        <div className="footer-main-grid">

          {/* Brand Column */}
          <div className="footer-brand-column">
            <div className="brand-logo">
              {/* Logo como imagen */}
              <img
                src="/img/logo_solutech.png"
                alt="SoluTech Logo"
                className="logo-image"
                onError={(e) => {
                  // Fallback si la imagen no carga
                  e.target.style.display = 'none';
                  const fallback = document.querySelector('.logo-fallback');
                  if (fallback) {
                    fallback.style.display = 'flex';
                  }
                }}
              />

              {/* Fallback de texto que se muestra solo si la imagen falla */}
              <div className="logo-fallback">
                <div className="logo-mark">ST</div>
                <div className="brand-name">
                  <span className="brand-primary">Solu</span>
                  <span className="brand-accent">Tech</span>
                </div>
              </div>
            </div>
            <p className="brand-tagline">
              Soluciones Tecnológicas Integrales
            </p>

            {/* Newsletter */}
            <div className="newsletter-section">
              <p className="newsletter-label">Mantente informado</p>
              <form onSubmit={newsletterSubscribe} className="newsletter-form" noValidate>
                <div className="input-group">
                  <input
                    type="email"
                    placeholder="tu@email.com"
                    className="newsletter-input"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    required
                    disabled={isSubmitting}
                  />
                  <button
                    type="submit"
                    className="newsletter-submit"
                    aria-label="Suscribirse"
                    disabled={isSubmitting}
                  >
                    {isSubmitting ? (
                      <span className="submit-spinner"></span>
                    ) : (
                      <svg className="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M5 12h14M12 5l7 7-7 7" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                      </svg>
                    )}
                  </button>
                </div>

                {/* Mensaje de estado */}
                {subscriptionMessage && (
                  <div className={`subscription-message ${subscriptionStatus}`}>
                    {subscriptionMessage}
                  </div>
                )}
              </form>
            </div>
          </div>

          {/* Services Column */}
          <div className="footer-column">
            <h4 className="column-title">Servicios</h4>
            <div className="services-list">
              {services.map((service, index) => (
                <span key={index} className="service-item">
                  {service.short}
                </span>
              ))}
            </div>
          </div>

          {/* Navigation Column */}
          <div className="footer-column">
            <h4 className="column-title">Enlaces</h4>
            <nav className="nav-links">
              {quickLinks.map((link, index) => (
                <a key={index} href={link.url} className="nav-link">
                  {link.name}
                </a>
              ))}
            </nav>
          </div>

          {/* Contact Column */}
          <div className="footer-column">
            <h4 className="column-title">Contacto</h4>
            <div className="contact-info">
              <div className="contact-item">
                <div className="contact-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                    <circle cx="12" cy="10" r="3" />
                  </svg>
                </div>
                <span className="contact-text">{contactInfo.address}</span>
              </div>
              <div className="contact-item">
                <div className="contact-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                  </svg>
                </div>
                <span className="contact-text">{contactInfo.phone}</span>
              </div>
              <div className="contact-item">
                <div className="contact-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                    <polyline points="22,6 12,13 2,6" />
                  </svg>
                </div>
                <span className="contact-text">{contactInfo.email}</span>
              </div>
            </div>
          </div>

          {/* Social Media */}
          <div className="footer-social">
            <div className="social-links">
              {socialMedia.map((social, index) => (
                <a
                  key={index}
                  href={social.url}
                  className="social-link"
                  target="_blank"
                  rel="noopener noreferrer"
                  aria-label={social.name}
                >
                  <span className="social-text">{social.name}</span>
                  <svg className="external-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" strokeWidth="2" strokeLinecap="round" />
                    <polyline points="15 3 21 3 21 9" strokeWidth="2" strokeLinecap="round" />
                    <line x1="10" y1="14" x2="21" y2="3" strokeWidth="2" strokeLinecap="round" />
                  </svg>
                </a>
              ))}
            </div>
          </div>

        </div>

        {/* Footer Bottom */}
        <div className="footer-bottom">
          <div className="footer-bottom-content">
            <div className="copyright">
              <p className="copyright-text">
                © {new Date().getFullYear()} SoluTech. Todos los derechos reservados.
              </p>
              <p className="copyright-subtext">
                Innovación tecnológica para el éxito empresarial.
              </p>
            </div>

            <div className="legal-links">
              <div className="legal-links-container">
                <a href="/politica-de-privacidad" className="legal-link">Politicas de Privacidad</a>
                <span className="legal-separator">•</span>
                <a href="/terminos-y-condiciones" className="legal-link">Términos y Condiciones</a>
                <span className="legal-separator">•</span>
                <a href="/aviso-legal" className="legal-link">Aviso Legal</a>
              </div>
            </div>
          </div>
        </div>

      </div>

      {/* Back to Top Button */}
      <button
        className={`back-to-top ${showBackToTop ? 'visible' : ''}`}
        onClick={() => window.scrollTo({ top: 0, behavior: 'smooth' })}
        aria-label="Volver arriba"
      >
        <svg className="arrow-up-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path d="M12 19V5M5 12l7-7 7 7" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
        </svg>
      </button>
    </footer>
  );
};

export default Footer;