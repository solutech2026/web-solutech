import React, { useState, useEffect } from 'react';
import { usePage, Link } from '@inertiajs/react';
import './Navbar.css';

const brandIcon = '/img/logo_solutech1.png';

const Navbar = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const { url, props } = usePage();
  const { auth } = props;

  const isAdmin = auth?.user?.role === 'admin' || auth?.user?.is_admin === true;

  useEffect(() => {
    const handleScroll = () => {
      setScrolled(window.scrollY > 20);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const menuItems = [
    { id: 'inicio', label: 'Inicio', url: '/', icon: '🏠' },
    { id: 'servicios', label: 'Servicios', url: '/servicio', icon: '⚡' },
    { id: 'nosotros', label: 'Nosotros', url: '/about-us', icon: '👥' },
    { id: 'contacto', label: 'Contacto', url: '/contacto', icon: '✉️' },
  ];

  const isActive = (path) => url === path;

  return (
    <>
      <nav className={`navbar-premium ${scrolled ? 'scrolled' : ''}`}>
        <div className="navbar-premium-container">

          {/* Logo */}
          <Link href="/" className="navbar-premium-logo">
            <div className="logo-wrapper">
              <img src={brandIcon} alt="SoluTech" className="logo-image" />
              <div className="logo-badge">IT</div>
            </div>
          </Link>

          {/* Desktop Menu */}
          <div className="nav-premium-menu">
            {menuItems.map((item) => (
              <Link
                key={item.id}
                href={item.url}
                className={`nav-premium-link ${isActive(item.url) ? 'active' : ''}`}
              >
                <span className="nav-link-icon">{item.icon}</span>
                <span className="nav-link-text">{item.label}</span>
              </Link>
            ))}

            {/* Admin Dropdown */}
            {auth?.user && isAdmin && (
              <div className="admin-premium-dropdown">
                <button className="admin-premium-btn">
                  <div className="admin-avatar">
                    <span>AD</span>
                  </div>
                  <span className="admin-name">Admin</span>
                  <svg className="dropdown-arrow" viewBox="0 0 20 20" fill="currentColor">
                    <path fillRule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clipRule="evenodd" />
                  </svg>
                </button>
                <div className="dropdown-premium-content">
                  <Link href="/admin/dashboard" className="dropdown-premium-link">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                      <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    Dashboard
                  </Link>
                  <Link href="/admin/access-control" className="dropdown-premium-link">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                      <path fillRule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clipRule="evenodd" />
                    </svg>
                    Control de Acceso
                  </Link>
                  <div className="dropdown-divider"></div>
                  <Link href="/logout" method="post" as="button" className="dropdown-premium-link logout">
                    <svg viewBox="0 0 20 20" fill="currentColor">
                      <path fillRule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 11H7a1 1 0 000 2h7.586l-1.293 1.293z" clipRule="evenodd" />
                    </svg>
                    Cerrar Sesión
                  </Link>
                </div>
              </div>
            )}
          </div>

          {/* Contact Button Desktop */}
          <a href="https://wa.me/584124714588" target="_blank" rel="noopener noreferrer" className="contact-premium-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
              <circle cx="12" cy="10" r="3" />
            </svg>
            <span>Contactar</span>
          </a>

          {/* Mobile Menu Button */}
          <button
            className={`mobile-premium-btn ${isOpen ? 'open' : ''}`}
            onClick={() => setIsOpen(!isOpen)}
            aria-label="Menu"
          >
            <span></span>
            <span></span>
            <span></span>
          </button>
        </div>
      </nav>

      {/* Mobile Menu */}
      <div className={`mobile-premium-nav ${isOpen ? 'open' : ''}`}>
        <div className="mobile-premium-header">
          <Link href="/" className="mobile-premium-logo" onClick={() => setIsOpen(false)}>
            <div className="mobile-logo-icon">
              <img src={brandIcon} alt="SoluTech" />
            </div>
            <span>SoluTech</span>
          </Link>
          <button className="mobile-premium-close" onClick={() => setIsOpen(false)}>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
              <path d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div className="mobile-premium-links">
          {menuItems.map((item) => (
            <Link
              key={item.id}
              href={item.url}
              className={`mobile-premium-link ${isActive(item.url) ? 'active' : ''}`}
              onClick={() => setIsOpen(false)}
            >
              <span className="mobile-link-icon">{item.icon}</span>
              <span>{item.label}</span>
            </Link>
          ))}

          {auth?.user && isAdmin && (
            <>
              <div className="mobile-premium-divider"></div>
              <Link href="/admin/dashboard" className="mobile-premium-link" onClick={() => setIsOpen(false)}>
                <span className="mobile-link-icon">📊</span>
                Dashboard Admin
              </Link>
              <Link href="/admin/access-control" className="mobile-premium-link" onClick={() => setIsOpen(false)}>
                <span className="mobile-link-icon">🔒</span>
                Control de Acceso
              </Link>
              <Link href="/logout" method="post" as="button" className="mobile-premium-link logout" onClick={() => setIsOpen(false)}>
                <span className="mobile-link-icon">🚪</span>
                Cerrar Sesión
              </Link>
            </>
          )}
        </div>

        <div className="mobile-premium-footer">
          <a href="https://wa.me/584124714588" target="_blank" rel="noopener noreferrer" className="mobile-premium-whatsapp">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
              <circle cx="12" cy="10" r="3" />
            </svg>
            Contactar por WhatsApp
          </a>
        </div>
      </div>

      {/* Overlay */}
      <div className={`mobile-premium-overlay ${isOpen ? 'open' : ''}`} onClick={() => setIsOpen(false)} />
    </>
  );
};

export default Navbar;