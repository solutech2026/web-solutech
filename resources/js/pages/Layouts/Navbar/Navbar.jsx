import React, { useState, useEffect } from 'react';
import { usePage, Link } from '@inertiajs/react';
import './Navbar.css';
const brandIcon = '/img/logo_solutech1.png';

const Navbar = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const { url, props } = usePage();
  const { auth } = props;

  // Verificar si el usuario es administrador
  const isAdmin = auth?.user?.role === 'admin' || auth?.user?.is_admin === true;

  // Maneja el scroll para cambiar el estilo
  useEffect(() => {
    const handleScroll = () => {
      setScrolled(window.scrollY > 20);
    };

    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  // Menú principal
  const menuItems = [
    { id: 'inicio', label: 'Inicio', url: '/' },
    { id: 'servicios', label: 'Servicios', url: '/servicio' },
    { id: 'nosotros', label: 'Nosotros', url: '/about-us' },
    { id: 'contacto', label: 'Contacto', url: '/contacto' },
  ];

  const isActive = (path) => {
    return url === path;
  };

  // Cerrar menú móvil al hacer clic fuera
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (isOpen && !event.target.closest('.navbar-container')) {
        setIsOpen(false);
      }
    };

    document.addEventListener('click', handleClickOutside);
    return () => document.removeEventListener('click', handleClickOutside);
  }, [isOpen]);

  return (
    <>
      <nav className={`navbar ${scrolled ? 'scrolled' : ''}`}>
        <div className="navbar-container">

          {/* Logo Brand */}
          <Link href="/" className="navbar-brand">
            <div className="brand-icon">
              <img
                src={brandIcon}
                alt="Icono de marca SoluTech"
                className="brand-icon-img"
              />
            </div>
            <div className="brand-text">
              <span className="brand-primary">Solu</span>
              <span className="brand-accent">Tech</span>
            </div>
          </Link>

          {/* Desktop Navigation */}
          <div className="desktop-nav">
            {menuItems.map((item) => (
              <Link
                key={item.id}
                href={item.url}
                className={`nav-link ${isActive(item.url) ? 'active' : ''}`}
              >
                <span className="link-text">{item.label}</span>
                <span className="link-dot"></span>
              </Link>
            ))}
          </div>

          {/* Menú de Administrador - Solo visible cuando está autenticado */}
          {auth?.user && isAdmin && (
            <div className="admin-menu">
              <button className="admin-menu-button">
                <div className="admin-avatar">
                  <span>AD</span>
                </div>
                <span className="admin-name">Admin</span>
                <svg className="dropdown-icon" viewBox="0 0 20 20" fill="currentColor">
                  <path fillRule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clipRule="evenodd" />
                </svg>
              </button>
              <div className="admin-dropdown">
                <Link href="/admin/dashboard" className="dropdown-item">
                  <svg viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                  </svg>
                  <span>Dashboard Admin</span>
                </Link>
                <Link href="/admin/access-control" className="dropdown-item">
                  <svg viewBox="0 0 20 20" fill="currentColor">
                    <path fillRule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clipRule="evenodd" />
                  </svg>
                  <span>Control de Acceso</span>
                </Link>
                <div className="dropdown-divider"></div>
                <Link href="/logout" method="post" as="button" className="dropdown-item logout">
                  <svg viewBox="0 0 20 20" fill="currentColor">
                    <path fillRule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 11H7a1 1 0 000 2h7.586l-1.293 1.293z" clipRule="evenodd" />
                  </svg>
                  <span>Cerrar Sesión</span>
                </Link>
              </div>
            </div>
          )}

          {/* Mobile Menu Toggle */}
          <button
            className={`menu-toggle ${isOpen ? 'open' : ''}`}
            onClick={() => setIsOpen(!isOpen)}
            aria-label="Toggle menu"
            aria-expanded={isOpen}
          >
            <span className="toggle-line"></span>
            <span className="toggle-line"></span>
            <span className="toggle-line"></span>
          </button>
        </div>
      </nav>

      {/* Mobile Menu Overlay */}
      <div className={`mobile-menu-overlay ${isOpen ? 'open' : ''}`}>
        <div className="mobile-menu-container">
          <div className="mobile-menu-header">
            <Link href="/" className="mobile-brand" onClick={() => setIsOpen(false)}>
              <div className="mobile-brand-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </div>
              <div className="mobile-brand-text">
                <span className="mobile-brand-primary">Solu</span>
                <span className="mobile-brand-accent">Tech</span>
              </div>
            </Link>
            <button
              className="mobile-close-btn"
              onClick={() => setIsOpen(false)}
              aria-label="Close menu"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div className="mobile-menu-content">
            {menuItems.map((item) => (
              <Link
                key={item.id}
                href={item.url}
                className={`mobile-nav-link ${isActive(item.url) ? 'active' : ''}`}
                onClick={() => setIsOpen(false)}
              >
                <span className="mobile-link-text">{item.label}</span>
                {isActive(item.url) && (
                  <span className="mobile-link-indicator"></span>
                )}
              </Link>
            ))}
            
            {/* Menú admin para móvil - Solo visible cuando está autenticado */}
            {auth?.user && isAdmin && (
              <>
                <div className="mobile-admin-header">
                  <div className="mobile-admin-avatar">AD</div>
                  <div className="mobile-admin-info">
                    <span className="mobile-admin-name">{auth.user.name || 'Administrador'}</span>
                    <span className="mobile-admin-role">Administrador</span>
                  </div>
                </div>
                <Link href="/admin/dashboard" className="mobile-nav-link" onClick={() => setIsOpen(false)}>
                  <span>Dashboard Admin</span>
                </Link>
                <Link href="/admin/access-control" className="mobile-nav-link" onClick={() => setIsOpen(false)}>
                  <span>Control de Acceso</span>
                </Link>
                <Link href="/logout" method="post" as="button" className="mobile-logout-btn" onClick={() => setIsOpen(false)}>
                  <span>Cerrar Sesión</span>
                </Link>
              </>
            )}
          </div>

          <div className="mobile-menu-footer">
            <Link
              href="/contacto"
              className="mobile-nav-cta"
              onClick={() => setIsOpen(false)}
            >
              <span>Contactar Ahora</span>
              <svg viewBox="0 0 20 20" fill="currentColor">
                <path fillRule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clipRule="evenodd" />
              </svg>
            </Link>

            <div className="mobile-contact-info">
              <div className="contact-item">
                <svg viewBox="0 0 20 20" fill="currentColor">
                  <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                </svg>
                <span>+58 412 471 45 88</span>
              </div>
              <div className="contact-item">
                <svg viewBox="0 0 20 20" fill="currentColor">
                  <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                  <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                </svg>
                <span>solutech24@outlook.com</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Blur overlay para menú móvil */}
      <div className={`menu-backdrop ${isOpen ? 'open' : ''}`} onClick={() => setIsOpen(false)} />
    </>
  );
};

export default Navbar;