import React from 'react';
import { Head } from '@inertiajs/react';
import { 
  Server, 
  ShieldCheck, 
  Database, 
  Code2, 
  Network, 
  MessageSquare, 
  Download,
  Cloud
} from 'lucide-react';

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
  }
}

// Mapeo de iconos para renderizado dinámico
const iconMap: Record<string, React.ReactNode> = {
  ShieldCheck: <ShieldCheck size={20} />,
  Cloud: <Cloud size={20} />,
  Network: <Network size={20} />,
  Database: <Database size={20} />,
  Code2: <Code2 size={20} />,
  Server: <Server size={20} />,
};

const Show = ({ profile }: Props) => {
  // Limpiar número de teléfono (eliminar espacios, guiones, etc.)
  const cleanPhone = profile.phone.replace(/\s+/g, '').replace(/-/g, '');
  
  return (
    <div className="min-h-screen bg-slate-900 text-white font-sans selection:bg-blue-500">
      <Head title={`${profile.name} - Digital Bio`} />
      
      {/* Fondo con gradiente decorativo */}
      <div className="fixed inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-blue-900/20 via-slate-900 to-black -z-10" />

      <main className="max-w-md mx-auto px-6 py-12 flex flex-col items-center">
        
        {/* Header / Logo - Usando imagen dinámica si existe, sino fallback al logo */}
        <div className="w-32 h-32 rounded-full border-2 border-blue-500/30 p-1 mb-6 shadow-[0_0_20px_rgba(59,130,246,0.5)] overflow-hidden">
          <img 
            src={profile.photo_path || "/img/solutech-logo.png"} 
            alt={profile.name} 
            className="w-full h-full object-cover rounded-full bg-slate-800"
          />
        </div>

        {/* Información Principal */}
        <h1 className="text-2xl font-bold tracking-tight text-center bg-gradient-to-r from-white to-blue-400 bg-clip-text text-transparent">
          {profile.name}
        </h1>
        <p className="text-blue-400 font-medium text-sm mb-4 uppercase tracking-widest text-center">
          {profile.role}
        </p>

        {/* Bio / Pitch dinámico */}
        <div className="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-5 mb-8 text-center shadow-xl">
          <p className="text-slate-300 text-sm leading-relaxed">
            {profile.summary}
          </p>
        </div>

        {/* Servicios Dinámicos (Mini Tags) */}
        <div className="flex flex-wrap justify-center gap-2 mb-10">
          {profile.services.map((item, index) => (
            <span key={index} className="flex items-center gap-1.5 bg-blue-500/10 border border-blue-500/20 px-3 py-1.5 rounded-full text-[12px] text-blue-300">
              {iconMap[item.icon] || <Server size={20} />} {item.label}
            </span>
          ))}
        </div>

        {/* Botones de Acción (Links) */}
        <div className="w-full space-y-4">
          
          <a href={`https://wa.me/${cleanPhone}`} 
             className="flex items-center justify-between w-full p-4 bg-blue-600 hover:bg-blue-500 transition-all rounded-xl font-semibold shadow-lg shadow-blue-900/20 group">
            <span className="flex items-center gap-3">
              <MessageSquare className="group-hover:scale-110 transition-transform" /> 
              WhatsApp de Soporte
            </span>
            <span className="bg-white/20 px-2 py-1 rounded text-[10px]">DISPONIBLE</span>
          </a>

          {/* CORREGIDO: Ruta fija para descargar vCard (sin slug dinámico) */}
          <a href="/bio/vcard"
             className="flex items-center gap-3 w-full p-4 bg-white/5 border border-white/10 hover:bg-white/10 transition-all rounded-xl font-medium text-slate-200">
            <Download size={22} className="text-blue-400" />
            Guardar Contacto (vCard)
          </a>

          <a href="/servicio" 
             className="flex items-center gap-3 w-full p-4 bg-white/5 border border-white/10 hover:bg-white/10 transition-all rounded-xl font-medium text-slate-200">
            <Server size={22} className="text-blue-400" />
            Portafolio de Servicios
          </a>

        </div>

        {/* Footer */}
        <footer className="mt-16 text-slate-500 text-[10px] flex flex-col items-center gap-2 uppercase tracking-tighter">
          <p>© 2026 Solutech • Soluciones Tecnologicas</p>
          <div className="h-1 w-12 bg-blue-500/30 rounded-full" />
        </footer>

      </main>
    </div>
  );
};

export default Show;