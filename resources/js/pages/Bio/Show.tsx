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
  Github,
  Linkedin,
  Twitter,
  Globe
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

// Mapeo de iconos para renderizado dinámico
const iconMap: Record<string, React.ReactNode> = {
  ShieldCheck: <ShieldCheck size={18} />,
  Cloud: <Cloud size={18} />,
  Network: <Network size={18} />,
  Database: <Database size={18} />,
  Code2: <Code2 size={18} />,
  Server: <Server size={18} />,
};

const Show = ({ profile }: Props) => {
  const [isLoaded, setIsLoaded] = useState(false);
  const cleanPhone = profile.phone.replace(/\s+/g, '').replace(/-/g, '');
  
  useEffect(() => {
    setIsLoaded(true);
  }, []);

  // Estadísticas dinámicas
  const stats = [
    { label: 'Experiencia', value: profile.experience || '5+ años', icon: <Briefcase size={16} /> },
    { label: 'Proyectos', value: profile.projects || '10+', icon: <Code2 size={16} /> },
    { label: 'Clientes', value: profile.clients || '6+', icon: <Users size={16} /> },
  ];

  return (
    <>
      <Head title={`${profile.name} - Perfil Profesional`} />
      
      <div className="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white font-['Inter',system-ui]">
        
        {/* Fondo con partículas */}
        <div className="particle-bg">
          <div className="blob-purple"></div>
          <div className="blob-yellow"></div>
          <div className="blob-pink"></div>
        </div>

        <main className="max-w-2xl mx-auto px-4 py-8 md:py-12">
          <div className={`fade-in ${isLoaded ? 'loaded' : ''}`}>
            
            {/* Tarjeta Principal */}
            <div className="card-glow p-6 md:p-8">
              
              {/* Header */}
              <div className="flex flex-col md:flex-row items-center md:items-start gap-6 mb-8">
                {/* Avatar */}
                <div className="avatar-wrapper">
                  <div className="avatar-glow"></div>
                  <div className="avatar-image">
                    <img 
                      src={profile.photo_path || "/img/solutech-logo.png"} 
                      alt={profile.name} 
                      className="w-full h-full object-cover"
                    />
                  </div>
                  <div className="avatar-status"></div>
                </div>

                {/* Información */}
                <div className="flex-1 text-center md:text-left">
                  <div className="flex items-center gap-2 justify-center md:justify-start mb-2">
                    <Sparkles className="w-4 h-4 text-yellow-500" />
                    <span className="text-xs font-mono text-blue-400 bg-blue-500/10 px-2 py-1 rounded-full">
                      Disponible para proyectos
                    </span>
                  </div>
                  
                  <h1 className="gradient-text text-3xl md:text-4xl font-bold mb-2">
                    {profile.name}
                  </h1>
                  
                  <p className="text-blue-400 font-medium text-sm mb-3 flex items-center gap-2 justify-center md:justify-start">
                    <span className="w-2 h-2 bg-blue-500 rounded-full animate-pulse-custom"></span>
                    {profile.role}
                  </p>
                  
                  {profile.location && (
                    <p className="text-gray-400 text-xs flex items-center gap-1 justify-center md:justify-start">
                      <MapPin size={12} />
                      {profile.location}
                    </p>
                  )}
                </div>
              </div>

              {/* Estadísticas */}
              <div className="stats-grid">
                {stats.map((stat, idx) => (
                  <div key={idx} className="stat-card">
                    <div className="stat-icon">
                      {stat.icon}
                      <span className="stat-value">{stat.value}</span>
                    </div>
                    <p className="stat-label">{stat.label}</p>
                  </div>
                ))}
              </div>

              {/* Bio */}
              <div className="bio-section">
                <div className="bio-divider">
                  <div className="bio-divider-line"></div>
                  <Star className="w-4 h-4 text-yellow-500" />
                  <div className="bio-divider-line"></div>
                </div>
                <p className="bio-text">{profile.summary}</p>
              </div>

              {/* Servicios */}
              <div>
                <div className="services-title">
                  <div className="services-title-line"></div>
                  Especialidades
                </div>
                <div className="services-container">
                  {profile.services.map((item, index) => (
                    <div key={index} className="service-tag">
                      <div className="service-tag-glow"></div>
                      <div className="service-tag-content">
                        {iconMap[item.icon] || <Server size={16} />} 
                        {item.label}
                      </div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Botones de Acción */}
              <div className="action-buttons">
                <a href={`https://wa.me/${cleanPhone}`} className="btn-whatsapp">
                  <div className="btn-whatsapp-glow"></div>
                  <div className="btn-whatsapp-content">
                    <div>
                      <MessageSquare className="whatsapp-icon" />
                      <span>WhatsApp Directo</span>
                    </div>
                    <ChevronRight size={18} className="chevron-icon" />
                  </div>
                </a>

                <div className="btn-grid">
                  <a href={`mailto:${profile.email}`} className="btn-secondary">
                    <Mail size={16} />
                    <span>Email</span>
                  </a>
                  <a href={`tel:${cleanPhone}`} className="btn-secondary">
                    <Phone size={16} />
                    <span>Llamar</span>
                  </a>
                </div>

                <a href="/bio/vcard" className="btn-outline">
                  <Download size={18} />
                  <span>Guardar Contacto (vCard)</span>
                </a>

                <a href="/servicio" className="btn-outline">
                  <Briefcase size={18} />
                  <span>Ver Portafolio</span>
                </a>
              </div>

              {/* Redes Sociales */}
              {profile.social && Object.values(profile.social).some(Boolean) && (
                <div className="social-section">
                  <div className="social-icons">
                    {profile.social.github && (
                      <a 
                        href={profile.social.github} 
                        target="_blank" 
                        rel="noopener noreferrer" 
                        className="social-icon github"
                        aria-label="GitHub"
                      >
                        <Github size={18} />
                      </a>
                    )}
                    {profile.social.linkedin && (
                      <a 
                        href={profile.social.linkedin} 
                        target="_blank" 
                        rel="noopener noreferrer"
                        className="social-icon linkedin"
                        aria-label="LinkedIn"
                      >
                        <Linkedin size={18} />
                      </a>
                    )}
                    {profile.social.twitter && (
                      <a 
                        href={profile.social.twitter} 
                        target="_blank" 
                        rel="noopener noreferrer"
                        className="social-icon twitter"
                        aria-label="Twitter"
                      >
                        <Twitter size={18} />
                      </a>
                    )}
                    {profile.social.website && (
                      <a 
                        href={profile.social.website} 
                        target="_blank" 
                        rel="noopener noreferrer"
                        className="social-icon website"
                        aria-label="Sitio Web"
                      >
                        <Globe size={18} />
                      </a>
                    )}
                  </div>
                </div>
              )}
            </div>
          </div>

          {/* Footer */}
          <footer className="footer">
            <div className="footer-text">
              <span>© 2026 Solutech</span>
              <span className="footer-dot"></span>
              <span>Soluciones Tecnológicas</span>
            </div>
            <div className="footer-line">
              <div className="footer-line-decoration"></div>
            </div>
          </footer>
        </main>
      </div>
    </>
  );
};

export default Show;