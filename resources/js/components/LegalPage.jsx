import React, { useState, useEffect, useMemo } from 'react';
import { Head, Link } from '@inertiajs/react';
import {
    FaScaleBalanced,
    FaUserShield,
    FaTruckFast,
    FaRotateLeft,
    FaShieldHalved,
    FaAngleRight,
    FaArrowLeft,
    FaHouse,
    FaPrint,
    FaMagnifyingGlass,
    FaXmark,
    FaLink,
    FaCheck,
    FaClock,
    FaArrowUp,
    FaWhatsapp,
    FaEnvelope,
    FaCircleInfo
} from 'react-icons/fa6';
import { getPolicyData, parsePoliticas } from './LegalModal';

export default function LegalPage({ site, dominio, siteSlug, legalType = 'terminos', estilos = {} }) {
    const [activeTab, setActiveTab] = useState(legalType);
    const [activeSection, setActiveSection] = useState(null);
    const [searchQuery, setSearchQuery] = useState('');
    const [copiedSectionId, setCopiedSectionId] = useState(null);
    const [showBackToTop, setShowBackToTop] = useState(false);

    const siteName = site?.nombre || 'Nuestra Tienda';
    const customPoliticas = parsePoliticas(estilos, site);

    // Sincronizar activeTab cuando cambie la prop legalType desde la navegación
    useEffect(() => {
        if (legalType) {
            setActiveTab(legalType);
            setSearchQuery('');
        }
    }, [legalType]);

    const currentConfig = getPolicyData(activeTab, customPoliticas);
    const HeaderIcon = currentConfig.icon;

    const availableTabs = [
        { id: 'terminos', label: 'Términos y Condiciones', icon: FaScaleBalanced, has: getPolicyData('terminos', customPoliticas).hasContent },
        { id: 'privacidad', label: 'Política de Privacidad', icon: FaUserShield, has: getPolicyData('privacidad', customPoliticas).hasContent },
        { id: 'envios', label: 'Política de Envíos', icon: FaTruckFast, has: getPolicyData('envios', customPoliticas).hasContent },
        { id: 'devoluciones', label: 'Políticas de Devolución', icon: FaRotateLeft, has: getPolicyData('devoluciones', customPoliticas).hasContent },
    ].filter(t => t.has);

    const homeUrl = (dominio && String(dominio).trim() !== '')
        ? (siteSlug ? `/${dominio}/${siteSlug}/Inicio` : `/${dominio}/Inicio`)
        : '/Inicio';

    // Estimación dinámica del tiempo de lectura según palabras
    const estimatedReadingTime = useMemo(() => {
        const introWords = (currentConfig.intro || '').split(/\s+/).filter(Boolean).length;
        const sectionsWords = currentConfig.sections.reduce((acc, s) => {
            return acc + (s.title || '').split(/\s+/).filter(Boolean).length + (s.content || '').split(/\s+/).filter(Boolean).length;
        }, 0);
        const totalWords = introWords + sectionsWords;
        const minutes = Math.max(1, Math.ceil(totalWords / 160));
        return `${minutes} min de lectura`;
    }, [currentConfig]);

    // Filtrado de cláusulas en tiempo real por término de búsqueda
    const filteredSections = useMemo(() => {
        if (!searchQuery.trim()) return currentConfig.sections;
        const query = searchQuery.toLowerCase().trim();
        return currentConfig.sections.filter(sec =>
            (sec.title && sec.title.toLowerCase().includes(query)) ||
            (sec.content && sec.content.toLowerCase().includes(query)) ||
            (sec.num && sec.num.toString().includes(query))
        );
    }, [currentConfig.sections, searchQuery]);

    // ScrollSpy: Detectar la sección visible al hacer scroll
    useEffect(() => {
        if (typeof window === 'undefined') return;

        const handleScroll = () => {
            setShowBackToTop(window.scrollY > 400);

            const sections = currentConfig.sections.map(s => document.getElementById(s.id)).filter(Boolean);
            if (!sections.length) return;

            const scrollPosition = window.scrollY + 220;

            for (let i = sections.length - 1; i >= 0; i--) {
                const sec = sections[i];
                if (sec.offsetTop <= scrollPosition) {
                    setActiveSection(sec.id);
                    return;
                }
            }
            if (sections.length > 0 && window.scrollY < sections[0].offsetTop - 250) {
                setActiveSection(null);
            }
        };

        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll();
        return () => window.removeEventListener('scroll', handleScroll);
    }, [currentConfig.sections]);

    const scrollToSection = (id) => {
        setActiveSection(id);
        const element = document.getElementById(id);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

    const scrollToTop = () => {
        if (typeof window !== 'undefined') {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    };

    const handleCopySectionLink = (secId) => {
        if (typeof window === 'undefined') return;
        const url = `${window.location.origin}${window.location.pathname}#${secId}`;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(() => {
                setCopiedSectionId(secId);
                setTimeout(() => setCopiedSectionId(null), 2400);
            });
        }
    };

    const handlePrint = () => {
        if (typeof window !== 'undefined') {
            window.print();
        }
    };

    // Datos de contacto de la tienda para la card de dudas
    const contactPhone = site?.telefono || estilos?.telefono_contacto || '';
    const cleanPhone = String(contactPhone).replace(/\D/g, '');
    const contactEmail = site?.email || estilos?.email_contacto || '';

    return (
        <div className="min-h-screen bg-neutral-50 text-neutral-900 font-sans antialiased selection:bg-[var(--color-primario)] selection:text-white">
            <Head title={`${currentConfig.title} | ${siteName}`} />

            {/* ESTILOS DE IMPRESIÓN LIMPIA (@media print) */}
            <style>{`
                @media print {
                    header, nav, footer, .no-print, [role="navigation"] {
                        display: none !important;
                    }
                    body, main {
                        background: #ffffff !important;
                        color: #0f172a !important;
                        padding: 0 !important;
                        margin: 0 !important;
                    }
                    .legal-card {
                        box-shadow: none !important;
                        border: 1px solid #e2e8f0 !important;
                        page-break-inside: avoid;
                        margin-bottom: 1.5rem !important;
                    }
                    .print-header {
                        display: block !important;
                        margin-bottom: 2rem;
                    }
                }
            `}</style>

            {/* ENCABEZADO SUPERIOR MODERNO CON BRAND ACCENTS */}
            <header className="bg-white border-b border-neutral-200/80 shadow-2xs py-6 sm:py-8 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
                {/* Glow ambiental sutil con color primario */}
                <div
                    className="absolute -top-32 right-0 w-96 h-96 rounded-full blur-3xl opacity-10 pointer-events-none"
                    style={{ backgroundColor: 'var(--color-primario)' }}
                />

                <div className="max-w-7xl mx-auto space-y-6 relative z-10">

                    {/* Breadcrumbs y Acciones Superiores */}
                    <div className="flex flex-wrap items-center justify-between gap-3 text-xs text-neutral-500 no-print">
                        <nav className="flex items-center gap-2">
                            <Link href={homeUrl} className="inline-flex items-center gap-1.5 hover:text-neutral-900 transition font-medium">
                                <FaHouse className="h-3.5 w-3.5 text-neutral-400" />
                                <span>Inicio</span>
                            </Link>
                            <FaAngleRight className="h-2.5 w-2.5 opacity-40" />
                            <span className="font-semibold text-neutral-700">Marco Legal</span>
                            <FaAngleRight className="h-2.5 w-2.5 opacity-40" />
                            <span
                                className="font-bold truncate"
                                style={{ color: 'var(--color-primario)' }}
                            >
                                {currentConfig.title}
                            </span>
                        </nav>

                        {/* Botones de acción rápida: Imprimir y Volver */}
                        <div className="flex items-center gap-2">
                            <button
                                onClick={handlePrint}
                                type="button"
                                className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-neutral-100 hover:bg-neutral-200 border border-neutral-200 text-neutral-700 text-xs font-semibold transition hover:text-neutral-900 cursor-pointer shadow-2xs"
                                title="Imprimir o guardar en PDF"
                            >
                                <FaPrint className="h-3.5 w-3.5" />
                                <span className="hidden sm:inline">Imprimir / PDF</span>
                            </button>
                            <Link
                                href={homeUrl}
                                className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-neutral-900 hover:bg-neutral-800 text-white text-xs font-bold transition shadow-xs"
                            >
                                <FaArrowLeft className="h-3 w-3" />
                                <span>Volver a la Tienda</span>
                            </Link>
                        </div>
                    </div>

                    {/* Título Principal, Insignia y Metadata */}
                    <div className="flex flex-col md:flex-row md:items-end justify-between gap-6 pt-2">
                        <div className="space-y-3 max-w-3xl">
                            {/* Insignia Oficial con fondo suave derivado de la marca */}
                            <div
                                className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[11px] font-bold tracking-wider uppercase border shadow-2xs"
                                style={{
                                    backgroundColor: 'color-mix(in srgb, var(--color-primario) 10%, transparent)',
                                    borderColor: 'color-mix(in srgb, var(--color-primario) 30%, transparent)',
                                    color: 'var(--color-primario)'
                                }}
                            >
                                <HeaderIcon className="h-3.5 w-3.5" />
                                <span>{currentConfig.badge}</span>
                            </div>

                            <h1 className="text-3xl sm:text-4xl lg:text-5xl font-black text-neutral-900 tracking-tight leading-tight">
                                {currentConfig.title}
                            </h1>

                            <p className="text-sm sm:text-base text-neutral-600 leading-relaxed font-normal">
                                {currentConfig.subtitle}
                            </p>

                            {/* Metadatos: Tiempo de lectura, Vigencia, Estado */}
                            <div className="flex flex-wrap items-center gap-4 text-xs text-neutral-500 pt-1">
                                <span className="inline-flex items-center gap-1.5">
                                    <FaClock className="h-3.5 w-3.5 text-neutral-400" />
                                    <span>{estimatedReadingTime}</span>
                                </span>
                                <span className="text-neutral-300">•</span>
                                <span className="inline-flex items-center gap-1.5">
                                    <span className="h-2 w-2 rounded-full bg-emerald-500 inline-block animate-pulse" />
                                    <span>Vigente para el año {new Date().getFullYear()}</span>
                                </span>
                                <span className="text-neutral-300">•</span>
                                <span className="text-neutral-500 font-medium">
                                    {currentConfig.sections.length} {currentConfig.sections.length === 1 ? 'cláusula' : 'cláusulas oficiales'}
                                </span>
                            </div>
                        </div>
                    </div>

                    {/* BARRA DE PESTAÑAS (SEGMENTED CONTROL MODERNO) */}
                    {availableTabs.length > 1 && (
                        <div className="pt-6 border-t border-neutral-100 no-print">
                            <div className="flex flex-wrap items-center gap-2 p-1 bg-neutral-100/80 rounded-2xl border border-neutral-200/80 w-fit">
                                {availableTabs.map((tab) => {
                                    const TabIcon = tab.icon;
                                    const isActive = activeTab === tab.id;
                                    return (
                                        <button
                                            key={tab.id}
                                            onClick={() => setActiveTab(tab.id)}
                                            className={`px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer ${isActive
                                                ? 'shadow-xs scale-[1.02]'
                                                : 'text-neutral-600 hover:text-neutral-900 hover:bg-white/60'
                                                }`}
                                            style={isActive ? {
                                                backgroundColor: 'var(--color-primario)',
                                                color: '#ffffff'
                                            } : {}}
                                        >
                                            <TabIcon className="h-3.5 w-3.5" />
                                            <span>{tab.label}</span>
                                        </button>
                                    );
                                })}
                            </div>
                        </div>
                    )}

                </div>
            </header>

            {/* CONTENIDO PRINCIPAL */}
            <main className="max-w-7xl mx-auto py-10 sm:py-14 px-4 sm:px-6 lg:px-8">
                {!currentConfig.hasContent ? (
                    <div className="bg-white p-12 rounded-3xl border border-neutral-200 text-center space-y-4 max-w-xl mx-auto my-12 shadow-sm">
                        <div
                            className="h-16 w-16 rounded-2xl flex items-center justify-center mx-auto text-2xl shadow-inner"
                            style={{
                                backgroundColor: 'color-mix(in srgb, var(--color-primario) 12%, transparent)',
                                color: 'var(--color-primario)'
                            }}
                        >
                            <FaScaleBalanced className="h-7 w-7" />
                        </div>
                        <h2 className="font-extrabold text-neutral-900 text-xl">Contenido en redacción</h2>
                        <p className="text-xs sm:text-sm text-neutral-600 leading-relaxed">
                            La tienda aún no ha publicado cláusulas específicas para <strong className="text-neutral-900">{currentConfig.title}</strong> en su panel de administración.
                        </p>
                        <div className="pt-3">
                            <Link
                                href={homeUrl}
                                className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-bold text-xs transition shadow-sm hover:opacity-90"
                                style={{ backgroundColor: 'var(--color-primario)' }}
                            >
                                <FaArrowLeft className="h-3 w-3" />
                                <span>Volver a la tienda</span>
                            </Link>
                        </div>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                        {/* COLUMNA IZQUIERDA: ÍNDICE STICKY Y BUSCADOR (DESKTOP) */}
                        {currentConfig.sections.length > 0 && (
                            <aside className="lg:col-span-4 lg:sticky lg:top-32 space-y-4 no-print order-2 lg:order-1">

                                {/* Buscador rápido de cláusulas */}
                                <div className="bg-white rounded-2xl p-4 border border-neutral-200/80 shadow-2xs space-y-2">
                                    <label htmlFor="legal-search" className="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider">
                                        Buscar en el documento
                                    </label>
                                    <div className="relative">
                                        <FaMagnifyingGlass className="h-3.5 w-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400" />
                                        <input
                                            id="legal-search"
                                            type="text"
                                            value={searchQuery}
                                            onChange={(e) => setSearchQuery(e.target.value)}
                                            placeholder="Ej. garantía, envíos, cancelación..."
                                            className="w-full pl-9 pr-8 py-2 rounded-xl text-xs bg-neutral-50 border border-neutral-200 text-neutral-900 placeholder:text-neutral-400 focus:bg-white focus:outline-none transition"
                                            style={{
                                                outlineColor: 'var(--color-primario)'
                                            }}
                                        />
                                        {searchQuery && (
                                            <button
                                                type="button"
                                                onClick={() => setSearchQuery('')}
                                                className="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-neutral-400 hover:text-neutral-700"
                                                title="Limpiar búsqueda"
                                            >
                                                <FaXmark className="h-3 w-3" />
                                            </button>
                                        )}
                                    </div>
                                    {searchQuery && (
                                        <div className="flex items-center justify-between text-[11px] text-neutral-500 pt-1">
                                            <span>
                                                {filteredSections.length} {filteredSections.length === 1 ? 'coincidencia' : 'coincidencias'}
                                            </span>
                                            <button
                                                type="button"
                                                onClick={() => setSearchQuery('')}
                                                className="text-xs font-semibold hover:underline"
                                                style={{ color: 'var(--color-primario)' }}
                                            >
                                                Ver todas
                                            </button>
                                        </div>
                                    )}
                                </div>

                                {/* Tarjeta de Navegación del Índice */}
                                <div className="bg-white rounded-2xl p-5 border border-neutral-200/80 shadow-2xs space-y-3">
                                    <div className="flex items-center justify-between pb-3 border-b border-neutral-100 text-xs font-bold text-neutral-500 uppercase tracking-wider">
                                        <div className="flex items-center gap-2">
                                            <FaShieldHalved
                                                className="h-3.5 w-3.5"
                                                style={{ color: 'var(--color-primario)' }}
                                            />
                                            <span>ÍNDICE DE CLÁUSULAS</span>
                                        </div>
                                        <span className="text-[10px] font-bold px-2 py-0.5 rounded-full bg-neutral-100 text-neutral-600">
                                            {currentConfig.sections.length}
                                        </span>
                                    </div>

                                    {/* Lista de Secciones con ScrollSpy */}
                                    <nav className="space-y-1 max-h-[55vh] overflow-y-auto pr-1">
                                        {currentConfig.sections.map((sec) => {
                                            const isSelected = activeSection === sec.id;
                                            const isMatchedInSearch = filteredSections.some(f => f.id === sec.id);

                                            return (
                                                <button
                                                    key={sec.id}
                                                    onClick={() => scrollToSection(sec.id)}
                                                    className={`w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-left text-xs transition-all cursor-pointer group ${isSelected
                                                        ? 'font-bold shadow-2xs'
                                                        : isMatchedInSearch
                                                            ? 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-50 font-medium'
                                                            : 'text-neutral-400 opacity-50'
                                                        }`}
                                                    style={isSelected ? {
                                                        backgroundColor: 'color-mix(in srgb, var(--color-primario) 10%, transparent)',
                                                        color: 'var(--color-primario)',
                                                        borderLeft: '3px solid var(--color-primario)'
                                                    } : {}}
                                                >
                                                    <span
                                                        className={`h-5 w-5 rounded-md font-bold text-[10px] flex items-center justify-center shrink-0 transition ${isSelected
                                                            ? 'text-white'
                                                            : 'bg-neutral-100 text-neutral-600 group-hover:bg-neutral-200'
                                                            }`}
                                                        style={isSelected ? { backgroundColor: 'var(--color-primario)' } : {}}
                                                    >
                                                        {sec.num}
                                                    </span>
                                                    <span className="truncate flex-1">{sec.title}</span>
                                                    <FaAngleRight className={`h-2.5 w-2.5 transition shrink-0 ${isSelected ? 'opacity-100 translate-x-0.5' : 'opacity-40 group-hover:opacity-70'}`} />
                                                </button>
                                            );
                                        })}
                                    </nav>

                                    {/* Botón Volver Arriba */}
                                    {showBackToTop && (
                                        <div className="pt-2 border-t border-neutral-100">
                                            <button
                                                type="button"
                                                onClick={scrollToTop}
                                                className="w-full flex items-center justify-center gap-2 py-2 rounded-xl text-xs font-semibold text-neutral-500 hover:text-neutral-900 hover:bg-neutral-50 transition cursor-pointer"
                                            >
                                                <FaArrowUp className="h-3 w-3" />
                                                <span>Subir al inicio</span>
                                            </button>
                                        </div>
                                    )}
                                </div>

                                {/* Card informativa sobre validez legal */}
                                <div className="p-4 rounded-2xl bg-neutral-100/80 border border-neutral-200/60 text-neutral-600 text-[11px] leading-relaxed space-y-2">
                                    <div className="flex items-center gap-1.5 font-bold text-neutral-800">
                                        <FaCircleInfo className="h-3.5 w-3.5 text-neutral-500" />
                                        <span>Validez del Documento</span>
                                    </div>
                                    <p>
                                        El presente documento rige las operaciones comerciales de <strong className="text-neutral-900">{siteName}</strong> conforme a la legislación vigente de comercio electrónico.
                                    </p>
                                </div>

                            </aside>
                        )}

                        {/* COLUMNA DERECHA: TARJETAS DE CONTENIDO */}
                        <div className={currentConfig.sections.length > 0 ? "lg:col-span-8 space-y-6 order-1 lg:order-2" : "lg:col-span-12 space-y-6"}>

                            {/* TARJETA INTRODUCTORIA DESTACADA */}
                            {currentConfig.intro && (
                                <div
                                    className="legal-card bg-white p-6 sm:p-8 rounded-3xl shadow-xs border border-neutral-200/80 relative overflow-hidden space-y-3"
                                    style={{
                                        borderLeft: '5px solid var(--color-primario)'
                                    }}
                                >
                                    <div className="flex items-center justify-between text-xs font-bold text-neutral-500 uppercase tracking-wider">
                                        <span>DECLARACIÓN INICIAL</span>
                                        <span className="text-[11px] font-semibold text-neutral-400">Oficial</span>
                                    </div>
                                    <div className="text-sm sm:text-base text-neutral-700 leading-relaxed font-sans whitespace-pre-line">
                                        {currentConfig.intro}
                                    </div>
                                </div>
                            )}

                            {/* MENSAJE CUANDO LA BÚSQUEDA NO ARROJA RESULTADOS */}
                            {filteredSections.length === 0 && (
                                <div className="bg-white p-8 rounded-3xl border border-neutral-200 text-center space-y-3">
                                    <p className="text-sm text-neutral-600 font-medium">
                                        No se encontraron cláusulas que coincidan con "<strong className="text-neutral-900">{searchQuery}</strong>".
                                    </p>
                                    <button
                                        type="button"
                                        onClick={() => setSearchQuery('')}
                                        className="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white transition shadow-2xs"
                                        style={{ backgroundColor: 'var(--color-primario)' }}
                                    >
                                        <span>Restablecer búsqueda</span>
                                    </button>
                                </div>
                            )}

                            {/* CARDS NUMERADAS DE SECCIONES REGISTRADAS */}
                            {filteredSections.map((sec) => {
                                const isContactoSec = sec.title.toLowerCase().includes('contacto');
                                const isCopied = copiedSectionId === sec.id;

                                return (
                                    <article
                                        key={sec.id}
                                        id={sec.id}
                                        className="legal-card bg-white p-6 sm:p-8 rounded-3xl shadow-xs border border-neutral-200/80 space-y-4 scroll-mt-32 transition-all hover:border-neutral-300 hover:shadow-md group relative"
                                    >
                                        {/* Cabecera de la Cláusula */}
                                        <div className="flex items-start justify-between gap-4 border-b border-neutral-100 pb-4">
                                            <div className="flex items-center gap-3.5">
                                                <span
                                                    className="h-8 w-8 sm:h-9 sm:sm:w-9 rounded-xl font-black text-xs sm:text-sm flex items-center justify-center shrink-0 shadow-xs"
                                                    style={{
                                                        backgroundColor: 'var(--color-primario)',
                                                        color: '#ffffff'
                                                    }}
                                                >
                                                    {sec.num.padStart(2, '0')}
                                                </span>
                                                <h2 className="text-base sm:text-xl font-extrabold text-neutral-900 tracking-tight">
                                                    {sec.title}
                                                </h2>
                                            </div>

                                            {/* Botón discreto para copiar enlace directo a esta cláusula */}
                                            <button
                                                type="button"
                                                onClick={() => handleCopySectionLink(sec.id)}
                                                className={`no-print inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs transition cursor-pointer border ${isCopied
                                                    ? 'bg-emerald-50 border-emerald-200 text-emerald-700 font-bold'
                                                    : 'bg-neutral-50 hover:bg-neutral-100 border-neutral-200 text-neutral-500 hover:text-neutral-900'
                                                    }`}
                                                title="Copiar enlace a esta sección"
                                            >
                                                {isCopied ? (
                                                    <>
                                                        <FaCheck className="h-3 w-3 text-emerald-600" />
                                                        <span className="text-[11px]">¡Copiado!</span>
                                                    </>
                                                ) : (
                                                    <>
                                                        <FaLink className="h-3 w-3 opacity-60" />
                                                        <span className="hidden sm:inline text-[11px] font-medium">Compartir</span>
                                                    </>
                                                )}
                                            </button>
                                        </div>

                                        {/* Texto de la Cláusula */}
                                        <div className="text-xs sm:text-sm text-neutral-600 leading-relaxed sm:leading-loose whitespace-pre-line font-sans pt-1">
                                            {sec.content}
                                        </div>

                                        {/* CTA ESPECIAL SI LA CLÁUSULA ES DE CONTACTO */}
                                        {isContactoSec && (
                                            <div
                                                className="pt-4 border-t border-neutral-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-5 rounded-2xl border"
                                                style={{
                                                    backgroundColor: 'color-mix(in srgb, var(--color-primario) 4%, transparent)',
                                                    borderColor: 'color-mix(in srgb, var(--color-primario) 20%, transparent)'
                                                }}
                                            >
                                                <div className="space-y-1">
                                                    <span className="block font-bold text-sm text-neutral-900">
                                                        ¿Tienes consultas o necesitas asesoría sobre estos términos?
                                                    </span>
                                                    <span className="block text-xs text-neutral-600">
                                                        Estamos a tu disposición a través de nuestros canales oficiales de atención al cliente.
                                                    </span>
                                                </div>

                                                <div className="flex flex-wrap items-center gap-2 shrink-0">
                                                    {cleanPhone && (
                                                        <a
                                                            href={`https://wa.me/51${cleanPhone}?text=${encodeURIComponent(`Hola, tengo una consulta sobre los ${currentConfig.title} de ${siteName}.`)}`}
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white font-bold text-xs transition shadow-xs hover:opacity-90"
                                                            style={{ backgroundColor: 'var(--color-primario)' }}
                                                        >
                                                            <FaWhatsapp className="h-3.5 w-3.5" />
                                                            <span>WhatsApp Oficial</span>
                                                        </a>
                                                    )}
                                                    <a
                                                        href={`${homeUrl}#contacto`}
                                                        className="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white hover:bg-neutral-100 border border-neutral-300 text-neutral-800 font-bold text-xs transition shadow-2xs"
                                                    >
                                                        <span>Formulario de Contacto</span>
                                                        <FaAngleRight className="h-3 w-3" />
                                                    </a>
                                                </div>
                                            </div>
                                        )}
                                    </article>
                                );
                            })}

                            {/* BANNER FINAL DE ATENCIÓN Y RECLAMACIONES */}
                            <div className="no-print bg-neutral-900 text-white p-6 sm:p-8 rounded-3xl shadow-sm flex flex-col sm:flex-row items-center justify-between gap-6">
                                <div className="space-y-1.5 text-center sm:text-left">
                                    <span className="text-xs font-bold uppercase tracking-wider text-neutral-400">
                                        Atención al Consumidor
                                    </span>
                                    <h3 className="text-lg sm:text-xl font-extrabold text-white">
                                        ¿Deseas presentar una solicitud o consultar tu pedido?
                                    </h3>
                                    <p className="text-xs text-neutral-300 max-w-xl leading-relaxed">
                                        {siteName} garantiza la atención rápida y oportuna de todas las consultas comerciales y requerimientos de postventa.
                                    </p>
                                </div>
                                <div className="flex items-center gap-3 shrink-0">
                                    {contactEmail && (
                                        <a
                                            href={`mailto:${contactEmail}`}
                                            className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-neutral-800 hover:bg-neutral-700 text-white font-bold text-xs transition border border-neutral-700"
                                            title="Enviar un correo electrónico"
                                        >
                                            <FaEnvelope className="h-3.5 w-3.5" />
                                            <span className="hidden md:inline">{contactEmail}</span>
                                            <span className="md:hidden">Correo</span>
                                        </a>
                                    )}
                                    <Link
                                        href={homeUrl}
                                        className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs text-white transition shadow-xs hover:opacity-95"
                                        style={{ backgroundColor: 'var(--color-primario)' }}
                                    >
                                        <span>Ir a la Tienda</span>
                                        <FaAngleRight className="h-3 w-3" />
                                    </Link>
                                </div>
                            </div>

                        </div>

                    </div>
                )}
            </main>
        </div>
    );
}
