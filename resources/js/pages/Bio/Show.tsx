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
  ExternalLink
} from 'lucide-react';
import './ProfileShow.css';

// Interfaz para TypeScript
interface Props {
  profile: {
    slug: string;
    name: string;
    role: string;
    summary: string;
    phone: string;
    email: string;
    services: Array<{ label: string, icon: string }>;
    photo_path?: string;
    experience?: number;
    projects?: number;
    clients?: number;
    location?: string;
    social?: {
      github?: string;
      linkedin?: string;
      twitter?: string;
      website?: string;
    };
  };
}

// Mapeo de iconos
const iconMap: Record<string, React.ReactNode> = {
  ShieldCheck: <ShieldCheck size={16} />,
  Cloud: <Cloud size={16} />,
  Network: <Network size={16} />,
  Database: <Database size={16} />,
  Code2: <Code2 size={16} />,
  Server: <Server size={16} />,
};

const Show = ({ profile }: Props) => {
  const [isVisible, setIsVisible] = useState(false);
  const cleanPhone = profile.phone.replace(/\s+/g, '').replace(/-/g, '');
  
  useEffect(() => {
    setIsVisible(true);
  }, []);

  const stats = [
    { label: 'Experiencia', value: profile.experience || '5+', icon: <Award size={18} />, suffix: ' años' },
    { label: 'Proyectos', value: profile.projects || '10+', icon: <Code2 size={18} />, suffix: '' },
    { label: 'Clientes', value: profile.clients || '6+', icon: <Users size={18} />, suffix: '' },
  ];

  return (
    <>
      <Head title={`${profile.name} - Perfil Profesional`} />
      
      <div className="profile-page">
        {/* Background Gradient */}
        <div className="bg-gradient"></div>
        
        {/* Floating Elements */}
        <div className="floating-elements">
          <div className="float-1"></div>
          <div className="float-2"></div>
          <div className="float-3"></div>
          <div className="float-4"></div>
        </div>

        <div className="container">
          <div className={`profile-wrapper ${isVisible ? 'visible' : ''}`}>
            
            {/* Main Card */}
            <div className="profile-card">
              
              {/* Left Column - Info */}
              <div className="profile-left">
                <div className="avatar-frame">
                  <div className="avatar-border"></div>
                  <div className="avatar">
                    <img 
                      src={profile.photo_path || "/img/solutech-logo.png"} 
                      alt={profile.name}
                    />
                  </div>
                  <div className="status-dot"></div>
                </div>
                
                <h1 className="profile-name">{profile.name}</h1>
                <p className="profile-role">{profile.role}</p>
                
                {profile.location && (
                  <div className="profile-location">
                    <MapPin size={14} />
                    <span>{profile.location}</span>
                  </div>
                )}
                
                <div className="availability-badge">
                  <Sparkles size={12} />
                  <span>Disponible para proyectos</span>
                </div>

                {/* Stats */}
                <div className="stats-container">
                  {stats.map((stat, idx) => (
                    <div key={idx} className="stat-block">
                      <div className="stat-icon">{stat.icon}</div>
                      <div className="stat-info">
                        <span className="stat-value">{stat.value}</span>
                        <span className="stat-suffix">{stat.suffix}</span>
                        <span className="stat-label">{stat.label}</span>
                      </div>
                    </div>
                  ))}
                </div>

                {/* Contact Buttons */}
                <div className="contact-buttons">
                  <a href={`https://wa.me/${cleanPhone}`} className="btn-wa">
                    <MessageSquare size={18} />
                    <span>WhatsApp</span>
                  </a>
                  <a href={`mailto:${profile.email}`} className="btn-email">
                    <Mail size={18} />
                    <span>Email</span>
                  </a>
                  <a href={`tel:${cleanPhone}`} className="btn-call">
                    <Phone size={18} />
                    <span>Llamar</span>
                  </a>
                </div>

                {/* Social Links */}
                {profile.social && Object.values(profile.social).some(Boolean) && (
                  <div className="social-container">
                    <div className="social-divider"></div>
                    <div className="social-links">
                      {profile.social.github && (
                        <a href={profile.social.github} target="_blank" rel="noopener noreferrer" className="social-link">
                          <GithubIcon size={18} />
                        </a>
                      )}
                      {profile.social.linkedin && (
                        <a href={profile.social.linkedin} target="_blank" rel="noopener noreferrer" className="social-link">
                          <Linkedin size={18} />
                        </a>
                      )}
                      {profile.social.twitter && (
                        <a href={profile.social.twitter} target="_blank" rel="noopener noreferrer" className="social-link">
                          <Twitter size={18} />
                        </a>
                      )}
                      {profile.social.website && (
                        <a href={profile.social.website} target="_blank" rel="noopener noreferrer" className="social-link">
                          <Globe size={18} />
                        </a>
                      )}
                    </div>
                  </div>
                )}
              </div>

              {/* Right Column - Details */}
              <div className="profile-right">
                {/* Bio */}
                <div className="bio-container">
                  <h3>Sobre mí</h3>
                  <p>{profile.summary}</p>
                </div>

                {/* Services */}
                <div className="services-container">
                  <h3>Especialidades</h3>
                  <div className="services-grid">
                    {profile.services.map((item, index) => (
                      <div key={index} className="service-item">
                        <div className="service-icon">
                          {iconMap[item.icon] || <Server size={16} />}
                        </div>
                        <span>{item.label}</span>
                      </div>
                    ))}
                  </div>
                </div>

                {/* Actions */}
                <div className="actions-container">
                  <a href="/bio/vcard" className="action-btn secondary">
                    <Download size={16} />
                    <span>vCard</span>
                  </a>
                  <a href="/servicio" className="action-btn primary">
                    <Briefcase size={16} />
                    <span>Portafolio</span>
                    <ChevronRight size={14} />
                  </a>
                </div>
              </div>
            </div>

            {/* Footer */}
            <div className="profile-footer">
              <span>© 2026 Solutech</span>
              <span className="separator">•</span>
              <span>Soluciones Tecnológicas</span>
              <span className="separator">•</span>
              <span>Control de Acceso</span>
            </div>
          </div>
        </div>
      </div>
    </>
  );
};

// Importar GithubIcon correctamente
import { Github as GithubIcon } from 'lucide-react';

export default Show;