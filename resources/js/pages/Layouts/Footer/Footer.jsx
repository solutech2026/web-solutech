import React, { useState, useEffect } from 'react';
import './Footer.css';

const Footer = () => {
  const [showBackToTop, setShowBackToTop] = useState(false);
  const [email, setEmail] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [subscriptionMessage, setSubscriptionMessage] = useState('');
  const [subscriptionStatus, setSubscriptionStatus] = useState('');

  const services = [
    { name: 'Administración de Sistemas', icon: '🖥️', url: '/servicio#sistemas' },
    { name: 'Administración de Redes', icon: '🌐', url: '/servicio#redes' },
    { name: 'Desarrollo de Software', icon: '💻', url: '/servicio#software' },
    { name: 'Soporte Multi-Nivel', icon: '👨‍💼', url: '/servicio#soporte' },
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
    whatsapp: 'https://wa.me/584124714588'
  };

  const socialMedia = [
    { name: 'Facebook', icon: '📘', url: 'https://www.facebook.com/Solutech.ve' },
    { name: 'Instagram', icon: '📸', url: 'https://www.instagram.com/solutechoficial.ve/' },
    { name: 'LinkedIn', icon: '🔗', url: 'https://linkedin.com/company/solutech' },
    { name: 'Twitter', icon: '🐦', url: 'https://twitter.com/solutech' },
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
      const response = await fetch('/api/newsletter/subscribe', {
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
        setTimeout(() => {
          setSubscriptionMessage('');
          setSubscriptionStatus('');
        }, 5000);
      } else {
        setSubscriptionStatus('error');
        setSubscriptionMessage(data.message || 'Error al procesar la suscripción');
      }
    } catch (error) {
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
    <footer className="footer-premium">
      {/* Waves Effect */}
      <div className="footer-waves">
        <svg className="waves-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
          <path fill="#4f46e5" fillOpacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
      </div>

      <div className="footer-premium-container">
        {/* Main Grid */}
        <div className="footer-premium-grid">

          {/* Brand Column */}
          <div className="footer-brand">
            <div className="brand-logo">
              <div className="logo-wrapper">
                <img
                  src="/img/logo_solutech1.png"
                  alt="SoluTech"
                  className="footer-logo-image"
                />
                <div className="logo-badge">IT</div>
              </div>
              <div className="logo-text">
                <span className="logo-solu">Solu</span>
                <span className="logo-tech">Tech</span>
              </div>
            </div>
            <p className="brand-description">
              Transformamos negocios a través de soluciones tecnológicas innovadoras,
              ofreciendo servicios de alta calidad y soporte especializado.
            </p>
          </div>

          {/* Services Column */}
          <div className="footer-column">
            <h4 className="column-title">
              <span className="title-icon">⚡</span>
              Servicios
            </h4>
            <ul className="footer-links">
              {services.map((service, index) => (
                <li key={index}>
                  <a href={service.url} className="footer-link">
                    <span className="link-icon">{service.icon}</span>
                    {service.name}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          {/* Quick Links Column */}
          <div className="footer-column">
            <h4 className="column-title">
              <span className="title-icon">🔗</span>
              Enlaces Rápidos
            </h4>
            <ul className="footer-links">
              {quickLinks.map((link, index) => (
                <li key={index}>
                  <a href={link.url} className="footer-link">
                    {link.name}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          {/* Contact Column */}
          <div className="footer-column">
            <h4 className="column-title">
              <span className="title-icon">📞</span>
              Contacto
            </h4>
            <ul className="footer-contact">
              <li>
                <div className="contact-icon">📍</div>
                <span>{contactInfo.address}</span>
              </li>
              <li>
                <div className="contact-icon">📱</div>
                <a href={`tel:${contactInfo.phone}`}>{contactInfo.phone}</a>
              </li>
              <li>
                <div className="contact-icon">✉️</div>
                <a href={`mailto:${contactInfo.email}`}>{contactInfo.email}</a>
              </li>
              <li>
                <div className="contact-icon">💬</div>
                <a href={contactInfo.whatsapp} target="_blank" rel="noopener noreferrer">WhatsApp</a>
              </li>
            </ul>
          </div>
        </div>

        {/* Newsletter Section */}
        <div className="newsletter-section">
          <div className="newsletter-content">
            <div className="newsletter-text">
              <h3 className="newsletter-title">Suscríbete a nuestro newsletter</h3>
              <p className="newsletter-description">Recibe las últimas novedades y promociones exclusivas</p>
            </div>
            <form onSubmit={newsletterSubscribe} className="newsletter-form" noValidate>
              <div className="input-wrapper">
                <input
                  type="email"
                  placeholder="tu@email.com"
                  className="newsletter-input"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  disabled={isSubmitting}
                />
                <button type="submit" className="newsletter-button" disabled={isSubmitting}>
                  {isSubmitting ? (
                    <span className="spinner"></span>
                  ) : (
                    <>
                      Suscribirse
                      <svg className="button-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fillRule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clipRule="evenodd" />
                      </svg>
                    </>
                  )}
                </button>
              </div>
              {subscriptionMessage && (
                <div className={`subscription-message ${subscriptionStatus}`}>
                  {subscriptionMessage}
                </div>
              )}
            </form>
          </div>
        </div>

        {/* Social Links */}
        <div className="social-section">
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
                <span className="social-icon">{social.icon}</span>
                <span className="social-name">{social.name}</span>
              </a>
            ))}
          </div>
        </div>

        {/* Footer Bottom */}
        <div className="footer-bottom">
          <div className="footer-bottom-content">
            <p className="copyright">
              © {new Date().getFullYear()} SoluTech. Todos los derechos reservados.
            </p>
            <div className="legal-links">
              <a href="/privacy" className="legal-link">Política de Privacidad</a>
              <span className="separator">•</span>
              <a href="/terms" className="legal-link">Términos y Condiciones</a>
              <span className="separator">•</span>
              <a href="/legal" className="legal-link">Aviso Legal</a>
              <span className="separator">•</span>
              <a href="/bio/proxicard" className="legal-link" target="_blank" rel="noopener noreferrer">
                🔐 PROXICARD
              </a>
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
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
          <path d="M12 19V5M5 12l7-7 7 7" strokeLinecap="round" strokeLinejoin="round" />
        </svg>
      </button>
    </footer>
  );
};

export default Footer;