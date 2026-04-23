import React, { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import {
  Server,
  ShieldCheck,
  Database,
  Code2,
  Network,
  MessageSquare,
  Download,
  Cloud,
  Mail,
  Phone,
  MapPin,
  Star,
  ChevronRight,
  Sparkles,
  Briefcase,
  Users,
  Linkedin,
  Twitter,
  Globe,
  CheckCircle,
  Award,
  Calendar,
  ExternalLink,
  Github,
  Zap,
  Fingerprint,
  Lock,
  Key,
  Cpu,
  HardDrive,
  Building,
  Folder,
  GraduationCap,
  User,
  Shield,
  Clock,
  Instagram
} from 'lucide-react';
import './ProfileShow.css';

// Tipos para los iconos
type IconName = 'ShieldCheck' | 'Cloud' | 'Network' | 'Database' | 'Code2' | 'Server' |
  'Fingerprint' | 'Lock' | 'Key' | 'Cpu' | 'HardDrive' | 'Briefcase' |
  'Building' | 'Folder' | 'GraduationCap' | 'Calendar' | 'Star' | 'User' |
  'Shield' | 'Clock' | 'Instagram';

// Mapeo de iconos
const iconMap: Record<IconName, React.ReactNode> = {
  ShieldCheck: <ShieldCheck size={16} />,
  Cloud: <Cloud size={16} />,
  Network: <Network size={16} />,
  Database: <Database size={16} />,
  Code2: <Code2 size={16} />,
  Server: <Server size={16} />,
  Fingerprint: <Fingerprint size={16} />,
  Lock: <Lock size={16} />,
  Key: <Key size={16} />,
  Cpu: <Cpu size={16} />,
  HardDrive: <HardDrive size={16} />,
  Briefcase: <Briefcase size={16} />,
  Building: <Building size={16} />,
  Folder: <Folder size={16} />,
  GraduationCap: <GraduationCap size={16} />,
  Calendar: <Calendar size={16} />,
  Star: <Star size={16} />,
  User: <User size={16} />,
  Shield: <Shield size={16} />,
  Clock: <Clock size={16} />,
  Instagram: <Instagram size={16} />,
};

// Función para obtener icono
const getIcon = (iconName: string): React.ReactNode => {
  return iconMap[iconName as IconName] || <ShieldCheck size={16} />;
};

// Interfaz para las props
interface Props {
  profile: {
    slug: string;
    name: string;
    role: string;
    summary: string;
    phone: string;
    email: string;
    services: Array<{ label: string; icon: string }>;
    photo_path: string;
    experience?: number | null;
    projects?: number | null;
    clients?: number | null;
    location?: string | null;
    social?: {
      github?: string;
      linkedin?: string;
      instagram?: string;
      website?: string;
    } | null;
    company?: string | null;
    category?: string | null;
    subcategory?: string | null;
    document_id?: string | null;
    emergency_contact?: string | null;
    emergency_phone?: string | null;
  };
}

const Show = ({ profile: initialProfile }: Props) => {
  const [isVisible, setIsVisible] = useState(false);
  const profile = initialProfile;

  // Limpiar teléfono para WhatsApp y llamadas
  const cleanPhone = profile.phone ? profile.phone.replace(/\s+/g, '').replace(/-/g, '') : '';

  useEffect(() => {
    setIsVisible(true);
  }, []);

  // Determinar si es PROXICARD o una persona normal
  const isProxicard = profile.slug === 'proxicard';

  // Stats solo para PROXICARD
  const stats = isProxicard && profile.experience && profile.projects && profile.clients ? [
    { label: 'Experiencia', value: profile.experience, icon: <Award size={18} />, suffix: ' años' },
    { label: 'Proyectos', value: profile.projects, icon: <Code2 size={18} />, suffix: '+' },
    { label: 'Clientes', value: profile.clients, icon: <Users size={18} />, suffix: '+' },
  ] : [];

  // Características destacadas (solo para PROXICARD)
  const features = isProxicard ? [
    { text: 'Seguridad garantizada', icon: <CheckCircle size={16} className="feature-check" /> },
    { text: 'Acceso rápido y confiable', icon: <CheckCircle size={16} className="feature-check" /> },
    { text: 'Soporte 24/7', icon: <CheckCircle size={16} className="feature-check" /> },
  ] : [];

  // Manejador de error de imagen
  const handleImageError = (e: React.SyntheticEvent<HTMLImageElement, Event>) => {
    const target = e.currentTarget;
    target.src = '/img/img_herbert.png'; // Asegúrate que este archivo existe en public/img/
  };

  // Obtener el nombre para mostrar
  const displayName = isProxicard ? (
    <>
      <span className="name-cyan">PROXI</span>
      <span className="name-purple">CARD</span>
    </>
  ) : (
    profile.name
  );

  return (
    <>
      <Head title={`${profile.name} - Perfil Profesional`} />

      <div className="profile-page-proxicard">
        {/* Background Gradient */}
        <div className="bg-gradient-proxicard"></div>

        {/* Grid Background */}
        <div className="grid-bg-proxicard"></div>

        {/* Floating Orbs */}
        <div className="floating-orbs-proxicard">
          <div className="orb-cyan-proxicard"></div>
          <div className="orb-magenta-proxicard"></div>
          <div className="orb-blue-proxicard"></div>
          <div className="orb-purple-proxicard"></div>
        </div>

        <div className="container-proxicard">
          <div className={`profile-wrapper-proxicard ${isVisible ? 'visible' : ''}`}>

            {/* Main Card */}
            <div className="profile-card-proxicard">

              {/* Left Column - Info */}
              <div className="profile-left-proxicard">
                <div className="avatar-frame-proxicard">
                  <div className="avatar-border-proxicard"></div>
                  <div className="avatar-proxicard">
                    <img
                      src={profile.photo_path || '/img/default-avatar.png'}
                      alt={profile.name}
                      onError={handleImageError}
                    />
                  </div>
                  <div className="status-dot-proxicard"></div>
                </div>

                <h1 className="profile-name-proxicard">
                  {displayName}
                </h1>

                <p className="profile-role-proxicard">
                  <Zap size={14} className="role-icon" />
                  {profile.role}
                </p>

                {/* Location - solo mostrar si existe */}
                {profile.location && (
                  <div className="profile-location-proxicard">
                    <MapPin size={14} />
                    <span>{profile.location}</span>
                  </div>
                )}

                {/* Company - para personas normales */}
                {profile.company && !isProxicard && (
                  <div className="profile-location-proxicard">
                    <Building size={14} />
                    <span>{profile.company}</span>
                  </div>
                )}

                {/* Document ID - para personas normales */}
                {profile.document_id && !isProxicard && (
                  <div className="profile-location-proxicard">
                    <Key size={14} />
                    <span>ID: {profile.document_id}</span>
                  </div>
                )}

                {/* Availability Badge - solo para PROXICARD */}
                {isProxicard && (
                  <div className="availability-badge-proxicard">
                    <Sparkles size={12} />
                    <span>Disponible para proyectos</span>
                  </div>
                )}

                {/* Stats - solo para PROXICARD */}
                {stats.length > 0 && (
                  <div className="stats-container-proxicard">
                    {stats.map((stat, idx) => (
                      <div key={idx} className="stat-block-proxicard">
                        <div className="stat-icon-proxicard">{stat.icon}</div>
                        <div className="stat-info-proxicard">
                          <span className="stat-value-proxicard">{stat.value}</span>
                          <span className="stat-suffix-proxicard">{stat.suffix}</span>
                          <span className="stat-label-proxicard">{stat.label}</span>
                        </div>
                      </div>
                    ))}
                  </div>
                )}

                {/* Emergency Contact - para personas normales */}
                {profile.emergency_contact && !isProxicard && (
                  <div className="emergency-contact-proxicard">
                    <div className="emergency-badge">
                      <ShieldCheck size={14} />
                      <span>Contacto de Emergencia</span>
                    </div>
                    <p><strong>{profile.emergency_contact}</strong> - {profile.emergency_phone}</p>
                  </div>
                )}

                {/* Contact Buttons - solo si hay teléfono o email */}
                {(profile.phone || profile.email) && (
                  <div className="contact-buttons-proxicard">
                    {profile.phone && (
                      <>
                        <a href={`https://wa.me/${cleanPhone}`} className="btn-wa-proxicard" target="_blank" rel="noopener noreferrer">
                          <MessageSquare size={18} />
                          <span>WhatsApp</span>
                        </a>
                        <a href={`tel:${cleanPhone}`} className="btn-call-proxicard">
                          <Phone size={18} />
                          <span>Llamar</span>
                        </a>
                      </>
                    )}
                    {profile.email && (
                      <a href={`mailto:${profile.email}`} className="btn-email-proxicard">
                        <Mail size={18} />
                        <span>Email</span>
                      </a>
                    )}
                  </div>
                )}

                {/* Social Links */}
                {profile.social && Object.values(profile.social).some(Boolean) && (
                  <div className="social-container-proxicard">
                    <div className="social-divider-proxicard"></div>
                    <div className="social-links-proxicard">
                      {profile.social.github && (
                        <a href={profile.social.github} target="_blank" rel="noopener noreferrer" className="social-link-proxicard">
                          <Github size={18} />
                        </a>
                      )}
                      {profile.social.linkedin && (
                        <a href={profile.social.linkedin} target="_blank" rel="noopener noreferrer" className="social-link-proxicard">
                          <Linkedin size={18} />
                        </a>
                      )}
                      {profile.social.instagram && (
                        <a href={profile.social.instagram} target="_blank" rel="noopener noreferrer" className="social-link-proxicard">
                          <Instagram size={18} />
                        </a>
                      )}
                      {profile.social.website && (
                        <a href={profile.social.website} target="_blank" rel="noopener noreferrer" className="social-link-proxicard">
                          <Globe size={18} />
                        </a>
                      )}
                    </div>
                  </div>
                )}
              </div>

              {/* Right Column - Details */}
              <div className="profile-right-proxicard">
                {/* Bio / Summary */}
                {profile.summary && (
                  <div className="bio-container-proxicard">
                    <h3>{isProxicard ? 'Sobre nosotros' : 'Biografía'}</h3>
                    <p>{profile.summary}</p>
                  </div>
                )}

                {/* Services / Especialidades */}
                {profile.services && profile.services.length > 0 && (
                  <div className="services-container-proxicard">
                    <h3>{isProxicard ? 'Especialidades' : 'Información'}</h3>
                    <div className="services-grid-proxicard">
                      {profile.services.map((item, index) => (
                        <div key={index} className="service-item-proxicard">
                          <div className="service-icon-proxicard">
                            {getIcon(item.icon)}
                          </div>
                          <span>{item.label}</span>
                        </div>
                      ))}
                    </div>
                  </div>
                )}

                {/* Features Highlight - solo para PROXICARD */}
                {features.length > 0 && (
                  <div className="features-container-proxicard">
                    {features.map((feature, idx) => (
                      <div key={idx} className="feature-item-proxicard">
                        {feature.icon}
                        <span>{feature.text}</span>
                      </div>
                    ))}
                  </div>
                )}

                {/* Category/Subcategory info - para personas normales */}
                {!isProxicard && (profile.category || profile.subcategory) && (
                  <div className="category-info-proxicard">
                    <div className="category-badge">
                      {profile.category && <span className="badge">{profile.category}</span>}
                      {profile.subcategory && <span className="badge sub">{profile.subcategory}</span>}
                    </div>
                  </div>
                )}

                {/* Actions */}
                <div className="actions-container-proxicard">
                  {profile.slug && (
                    <a href={`/bio/${profile.slug}/vcard`} className="action-btn-proxicard secondary">
                      <Download size={16} />
                      <span>vCard</span>
                    </a>
                  )}
                  <a href={isProxicard ? "/servicio" : "/contacto"} className="action-btn-proxicard primary">
                    <Briefcase size={16} />
                    <span>{isProxicard ? 'Portafolio' : 'Contactar'}</span>
                    <ChevronRight size={14} />
                  </a>
                </div>
              </div>
            </div>

            {/* Footer */}
            <div className="profile-footer-proxicard">
              <span>© 2026 {isProxicard ? 'PROXICARD' : profile.name}</span>
              <span className="separator-proxicard">•</span>
              <span>{isProxicard ? 'Control de Acceso' : (profile.role || 'Perfil Profesional')}</span>
              {isProxicard && (
                <>
                  <span className="separator-proxicard">•</span>
                  <span>Identidad y Seguridad</span>
                </>
              )}
            </div>
          </div>
        </div>
      </div>
    </>
  );
};

export default Show;