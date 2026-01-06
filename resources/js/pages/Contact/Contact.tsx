import React, { useState } from 'react';
import { Head, usePage } from '@inertiajs/react';
import { PageProps as InertiaPageProps } from '@inertiajs/core';
import Navbar from '../Layouts/Navbar/Navbar';
import Footer from '../Layouts/Footer/Footer';
import './Contact.css';

// Definir tipos locales
interface FormData {
    name: string;
    email: string;
    phone: string;
    company: string;
    service: string;
    subject: string;
    message: string;
}

interface FormErrors {
    name?: string;
    email?: string;
    phone?: string;
    company?: string;
    service?: string;
    subject?: string;
    message?: string;
}

interface ContactInfo {
    email: string;
    phone: string;
    address: string;
    businessHours: string;
}

interface Service {
    id: string;
    name: string;
}

// Tipo para las props de la página
interface PageProps extends InertiaPageProps {
    pageTitle: string;
    metaDescription: string;
    contactInfo: ContactInfo;
    services: Service[];
    [key: string]: any;
}

const Contact: React.FC = () => {
    // Obtener props de Inertia con el tipo correcto
    const { props } = usePage<PageProps>();
    const { pageTitle, metaDescription, contactInfo, services } = props;
    
    const [formData, setFormData] = useState<FormData>({
        name: '',
        email: '',
        phone: '',
        company: '',
        service: '',
        subject: '',
        message: ''
    });

    const [errors, setErrors] = useState<FormErrors>({});
    const [isSubmitting, setIsSubmitting] = useState<boolean>(false);
    const [isSubmitted, setIsSubmitted] = useState<boolean>(false);
    const [serverErrors, setServerErrors] = useState<FormErrors>({});
    const [activeTab, setActiveTab] = useState<'form' | 'schedule'>('form');

    // Agregar servicios por defecto si no vienen del backend
    const availableServices = services && services.length > 0 
        ? services 
        : [
            { id: 'general', name: 'Consulta General' },
            { id: 'infrastructure', name: 'Infraestructura IT' },
            { id: 'cloud', name: 'Soluciones en la Nube' },
            { id: 'security', name: 'Ciberseguridad' },
            { id: 'support', name: 'Soporte Técnico' },
            { id: 'network', name: 'Redes y Conectividad' },
            { id: 'development', name: 'Desarrollo de Software' },
            { id: 'consulting', name: 'Consultoría IT' }
        ];

    const handleChange = (
        e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>
    ): void => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));

        // Limpiar errores al escribir
        if (errors[name as keyof FormErrors] || serverErrors[name as keyof FormErrors]) {
            setErrors(prev => ({ ...prev, [name]: undefined }));
            setServerErrors(prev => ({ ...prev, [name]: undefined }));
        }
    };

    const validateForm = (): FormErrors => {
        const newErrors: FormErrors = {};

        if (!formData.name.trim()) {
            newErrors.name = 'El nombre es obligatorio';
        } else if (formData.name.trim().length < 2) {
            newErrors.name = 'El nombre debe tener al menos 2 caracteres';
        }

        if (!formData.email.trim()) {
            newErrors.email = 'El email es obligatorio';
        } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
            newErrors.email = 'El email no es válido';
        }

        if (!formData.phone.trim()) {
            newErrors.phone = 'El teléfono es obligatorio';
        } else if (!/^[0-9\s\+\-\(\)]{7,}$/.test(formData.phone)) {
            newErrors.phone = 'Ingresa un teléfono válido';
        }

        if (!formData.subject.trim()) {
            newErrors.subject = 'El asunto es obligatorio';
        } else if (formData.subject.length < 5) {
            newErrors.subject = 'El asunto debe tener al menos 5 caracteres';
        }

        if (!formData.message.trim()) {
            newErrors.message = 'El mensaje es obligatorio';
        } else if (formData.message.length < 10) {
            newErrors.message = 'El mensaje debe tener al menos 10 caracteres';
        }

        return newErrors;
    };

    const handleSubmit = async (e: React.FormEvent<HTMLFormElement>): Promise<void> => {
        e.preventDefault();

        const formErrors = validateForm();

        if (Object.keys(formErrors).length === 0) {
            setIsSubmitting(true);
            setServerErrors({});

            try {
                // Enviar datos al backend de Laravel
                const response = await fetch('/api/contact/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    setIsSubmitted(true);
                    
                    // Resetear formulario
                    setFormData({
                        name: '',
                        email: '',
                        phone: '',
                        company: '',
                        service: '',
                        subject: '',
                        message: ''
                    });

                    // Ocultar mensaje de éxito después de 5 segundos
                    setTimeout(() => {
                        setIsSubmitted(false);
                    }, 5000);

                } else {
                    // Manejar errores del servidor
                    if (data.errors) {
                        setServerErrors(data.errors);
                    } else {
                        alert(data.message || 'Error al enviar el formulario');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error de conexión. Por favor, intenta nuevamente.');
            } finally {
                setIsSubmitting(false);
            }
        } else {
            setErrors(formErrors);
        }
    };

    // Combinar errores del frontend y backend
    const getError = (field: keyof FormErrors): string | undefined => {
        return serverErrors[field] || errors[field];
    };

    const hasError = (fieldName: keyof FormErrors): boolean => {
        return getError(fieldName) !== undefined;
    };

    return (
        <>
            <Head title={pageTitle}>
                <meta name="description" content={metaDescription} />
            </Head>
            
            <Navbar />

            {/* Hero Section */}
            <section className="contact-hero">
                <div className="contact-container">
                    <div className="contact-hero-content">
                        <h1>Transforma tu Negocio con Nuestra Experiencia</h1>
                        <p className="hero-subtitle">
                            ¿Listo para llevar tu infraestructura IT al siguiente nivel? 
                            Nuestro equipo de expertos está aquí para ayudarte.
                        </p>
                    </div>
                </div>
            </section>

            {/* Main Contact Section */}
            <section className="contact-main-section">
                <div className="contact-container">
                    <div className="contact-layout">
                        {/* Left Panel - Contact Info */}
                        <div className="contact-info-sidebar">
                            <div className="contact-info-header">
                                <h2>Información de Contacto</h2>
                                <p>Estamos disponibles para responder tus consultas</p>
                            </div>

                            <div className="contact-details">
                                <div className="contact-detail-item">
                                    <div className="contact-icon-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                            <path d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3>Email</h3>
                                        <a href={`mailto:${contactInfo.email}`}>{contactInfo.email}</a>
                                    </div>
                                </div>

                                <div className="contact-detail-item">
                                    <div className="contact-icon-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                            <path d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3>Teléfono</h3>
                                        <a href={`tel:${contactInfo.phone}`}>{contactInfo.phone}</a>
                                    </div>
                                </div>

                                <div className="contact-detail-item">
                                    <div className="contact-icon-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3>Dirección</h3>
                                        <p>{contactInfo.address}</p>
                                    </div>
                                </div>

                                <div className="contact-detail-item">
                                    <div className="contact-icon-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3>Horario de Atención</h3>
                                        <p>{contactInfo.businessHours || 'Lunes a Viernes 9:00 AM - 6:00 PM'}</p>
                                    </div>
                                </div>
                            </div>

                            <div className="emergency-contact">
                                <h3>¿Necesitas Soporte Urgente?</h3>
                                <p>Nuestro equipo de soporte está disponible 24/7 para emergencias técnicas</p>
                                <a href={`tel:${contactInfo.phone}`} className="emergency-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                        <path d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                    </svg>
                                    Llamar Ahora
                                </a>
                            </div>
                        </div>

                        {/* Right Panel - Form */}
                        <div className="contact-form-container">
                            <div className="form-tabs">
                                <button 
                                    className={`form-tab ${activeTab === 'form' ? 'active' : ''}`}
                                    onClick={() => setActiveTab('form')}
                                >
                                    Enviar Mensaje
                                </button>
                                <button 
                                    className={`form-tab ${activeTab === 'schedule' ? 'active' : ''}`}
                                    onClick={() => setActiveTab('schedule')}
                                >
                                    Agendar Reunión
                                </button>
                            </div>

                            <div className="form-content">
                                {activeTab === 'form' ? (
                                    <form onSubmit={handleSubmit} className="contact-form" noValidate>
                                        {isSubmitted && (
                                            <div className="success-message">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                    <path fillRule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clipRule="evenodd" />
                                                </svg>
                                                <div>
                                                    <h4>¡Mensaje Enviado con Éxito!</h4>
                                                    <p>Te contactaremos en menos de 24 horas.</p>
                                                </div>
                                            </div>
                                        )}

                                        <div className="form-grid">
                                            <div className="form-group">
                                                <label htmlFor="name">
                                                    Nombre Completo *
                                                    {hasError('name') && <span className="error-dot">!</span>}
                                                </label>
                                                <input
                                                    type="text"
                                                    id="name"
                                                    name="name"
                                                    value={formData.name}
                                                    onChange={handleChange}
                                                    className={`form-input ${hasError('name') ? 'error' : ''}`}
                                                    placeholder="Juan Pérez"
                                                />
                                                {getError('name') && (
                                                    <span className="error-text">{getError('name')}</span>
                                                )}
                                            </div>

                                            <div className="form-group">
                                                <label htmlFor="email">
                                                    Email *
                                                    {hasError('email') && <span className="error-dot">!</span>}
                                                </label>
                                                <input
                                                    type="email"
                                                    id="email"
                                                    name="email"
                                                    value={formData.email}
                                                    onChange={handleChange}
                                                    className={`form-input ${hasError('email') ? 'error' : ''}`}
                                                    placeholder="juan@empresa.com"
                                                />
                                                {getError('email') && (
                                                    <span className="error-text">{getError('email')}</span>
                                                )}
                                            </div>

                                            <div className="form-group">
                                                <label htmlFor="phone">
                                                    Teléfono *
                                                    {hasError('phone') && <span className="error-dot">!</span>}
                                                </label>
                                                <input
                                                    type="tel"
                                                    id="phone"
                                                    name="phone"
                                                    value={formData.phone}
                                                    onChange={handleChange}
                                                    className={`form-input ${hasError('phone') ? 'error' : ''}`}
                                                    placeholder="+58 412 123 4567"
                                                />
                                                {getError('phone') && (
                                                    <span className="error-text">{getError('phone')}</span>
                                                )}
                                            </div>

                                            <div className="form-group">
                                                <label htmlFor="company">Empresa</label>
                                                <input
                                                    type="text"
                                                    id="company"
                                                    name="company"
                                                    value={formData.company}
                                                    onChange={handleChange}
                                                    className="form-input"
                                                    placeholder="Nombre de la empresa"
                                                />
                                            </div>

                                            <div className="form-group">
                                                <label htmlFor="service">Servicio de Interés</label>
                                                <select
                                                    id="service"
                                                    name="service"
                                                    value={formData.service}
                                                    onChange={handleChange}
                                                    className="form-select"
                                                >
                                                    <option value="">Selecciona un servicio</option>
                                                    {availableServices.map(service => (
                                                        <option key={service.id} value={service.id}>
                                                            {service.name}
                                                        </option>
                                                    ))}
                                                </select>
                                            </div>

                                            <div className="form-group">
                                                <label htmlFor="subject">
                                                    Asunto *
                                                    {hasError('subject') && <span className="error-dot">!</span>}
                                                </label>
                                                <input
                                                    type="text"
                                                    id="subject"
                                                    name="subject"
                                                    value={formData.subject}
                                                    onChange={handleChange}
                                                    className={`form-input ${hasError('subject') ? 'error' : ''}`}
                                                    placeholder="Ej: Consulta sobre administración de sistemas"
                                                />
                                                {getError('subject') && (
                                                    <span className="error-text">{getError('subject')}</span>
                                                )}
                                            </div>

                                            <div className="form-group full-width">
                                                <label htmlFor="message">
                                                    Mensaje *
                                                    {hasError('message') && <span className="error-dot">!</span>}
                                                </label>
                                                <textarea
                                                    id="message"
                                                    name="message"
                                                    value={formData.message}
                                                    onChange={handleChange}
                                                    className={`form-textarea ${hasError('message') ? 'error' : ''}`}
                                                    placeholder="Describe tu consulta o proyecto..."
                                                    rows={6}
                                                    maxLength={500}
                                                />
                                                <div className="char-count">
                                                    {formData.message.length}/500 caracteres
                                                </div>
                                                {getError('message') && (
                                                    <span className="error-text">{getError('message')}</span>
                                                )}
                                            </div>
                                        </div>

                                        <div className="form-footer">
                                            <button
                                                type="submit"
                                                className="submit-btn"
                                                disabled={isSubmitting}
                                            >
                                                {isSubmitting ? (
                                                    <>
                                                        <span className="spinner"></span>
                                                        Enviando...
                                                    </>
                                                ) : (
                                                    'Enviar Mensaje'
                                                )}
                                            </button>
                                            <p className="form-note">
                                                * Campos obligatorios. Tu información está segura con nosotros.
                                            </p>
                                        </div>
                                    </form>
                                ) : (
                                    <div className="schedule-section">
                                        <div className="schedule-info">
                                            <h3>Agenda una Reunión</h3>
                                            <p>Reserva un tiempo con nuestro equipo de expertos para una consulta personalizada</p>
                                        </div>
                                        <div className="schedule-options">
                                            <div className="schedule-card">
                                                <h4>Consulta Inicial</h4>
                                                <p>30 minutos - Evaluación de necesidades</p>
                                                <button className="schedule-btn">Agendar</button>
                                            </div>
                                            <div className="schedule-card">
                                                <h4>Reunión Técnica</h4>
                                                <p>60 minutos - Análisis detallado</p>
                                                <button className="schedule-btn">Agendar</button>
                                            </div>
                                            <div className="schedule-card">
                                                <h4>Presentación de Solución</h4>
                                                <p>45 minutos - Propuesta personalizada</p>
                                                <button className="schedule-btn">Agendar</button>
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* FAQ Section */}
            <section className="contact-faq">
                <div className="contact-container">
                    <h2>Preguntas Frecuentes</h2>
                    <div className="faq-grid">
                        <div className="faq-card">
                            <h3>¿Cuál es el tiempo de respuesta?</h3>
                            <p>Respondemos todas las consultas en menos de 24 horas hábiles. Para emergencias, nuestro soporte 24/7 está disponible inmediatamente.</p>
                        </div>
                        <div className="faq-card">
                            <h3>¿Ofrecen soporte técnico remoto?</h3>
                            <p>Sí, nuestro equipo puede conectarse de forma segura a tus sistemas para diagnóstico y solución de problemas en tiempo real.</p>
                        </div>
                        <div className="faq-card">
                            <h3>¿Trabajan con empresas internacionales?</h3>
                            <p>Absolutamente. Ofrecemos soporte en múltiples zonas horarias y tenemos experiencia trabajando con clientes globales.</p>
                        </div>
                        <div className="faq-card">
                            <h3>¿Cuál es el proceso de contratación?</h3>
                            <p>1. Consulta inicial 2. Análisis de necesidades 3. Propuesta personalizada 4. Implementación 5. Soporte continuo.</p>
                        </div>
                    </div>
                </div>
            </section>

            <Footer />
        </>
    );
};

export default Contact;