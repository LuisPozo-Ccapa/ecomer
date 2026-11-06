import React, { useState, useEffect, useRef } from 'react';
import { FaLocationDot, FaClock, FaRoute } from 'react-icons/fa6';

export default function SedesSection({ seccionData }) {
    if (!seccionData) return null;

    // Helper para extraer valores del CMS
    const getItem = (label) =>
        seccionData?.contenido?.find(
            (item) => item.label?.toLowerCase() === label.toLowerCase()
        );

    const subtitulo = getItem('subtitulo')?.valor || 'NUESTRAS SEDES';
    const rawTitulo = getItem('titulo')?.valor || 'Encuentra la sede más cercana a tu operación';

    // Extracción de sedes (repetidor)
    const sedesGroup =
        getItem('sedes') ||
        seccionData?.contenido?.find((c) => c.tipo === 'grupo') ||
        null;

    const rawSedes = sedesGroup?.valor;
    let sedesArray = [];
    if (Array.isArray(rawSedes)) {
        sedesArray = rawSedes;
    } else if (rawSedes && typeof rawSedes === 'object') {
        sedesArray = Object.values(rawSedes);
    } else if (typeof rawSedes === 'string') {
        try {
            const parsed = JSON.parse(rawSedes);
            sedesArray = Array.isArray(parsed) ? parsed : Object.values(parsed);
        } catch (e) {
            sedesArray = [];
        }
    }

    // Normalizar sedes con formato de imagen y datos
    const sedes = (sedesArray.length > 0 ? sedesArray : [
        {
            titulo: 'Sede Central',
            direccion: 'Calle Ignacio Álvarez Thomas 1594, Lima',
            horarios: "Lunes a viernes de 8:00 a. m. a 5:30 p. m.\nSábados de 8:00 a. m. a 1:00 p. m.",
            imagen: null,
            mapa: 'Calle Ignacio Álvarez Thomas 1594, Lima, Perú',
            boton_enlace: 'https://maps.google.com/?q=Calle+Ignacio+Alvarez+Thomas+1594+Lima',
        },
        {
            titulo: 'Punto de venta "Villón"',
            direccion: 'Jr. García Villón 536, Lima – Perú',
            horarios: "Lunes a viernes de 8:00 a. m. a 5:30 p. m.\nSábados de 8:00 a. m. a 1:00 p. m.",
            imagen: null,
            mapa: 'Jr. García Villón 536, Lima, Perú',
            boton_enlace: 'https://maps.google.com/?q=Jr+Garcia+Villon+536+Lima',
        },
        {
            titulo: 'Punto de venta "Acoprom"',
            direccion: 'Av. Argentina 523, Galería Acoprom, Stand I-15, Lima – Perú',
            horarios: "Lunes a viernes de 8:00 a. m. a 5:30 p. m.\nSábados de 8:00 a. m. a 1:00 p. m.",
            imagen: null,
            mapa: 'Av. Argentina 523, Galería Acoprom, Lima, Perú',
            boton_enlace: 'https://maps.google.com/?q=Av+Argentina+523+Lima',
        },
    ]).map((item) => {
        let img = item.imagen;
        if (img && typeof img === 'object' && !Array.isArray(img)) {
            img = Object.values(img)[0];
        }
        if (Array.isArray(img)) img = img[0];
        if (typeof img === 'string' && img.trim()) {
            if (img.includes('localhost') || img.includes('127.0.0.1')) {
                img = img.replace(/^https?:\/\/[^\/]+/, '');
            }
            if (!img.startsWith('http') && !img.startsWith('/')) {
                img = `/storage/${img.replace(/^\/?storage\//, '')}`;
            }
        } else {
            img = null;
        }

        return {
            ...item,
            imagen: img,
            boton_enlace: item.boton_enlace || (item.mapa ? `https://maps.google.com/?q=${encodeURIComponent(item.mapa)}` : (item.direccion ? `https://maps.google.com/?q=${encodeURIComponent(item.direccion)}` : '#')),
        };
    });

    // Callout inferior de atención en provincias
    const bannerCalloutTexto = getItem('banner_callout_texto')?.valor ||
        '¿No estás en Lima? Te atendemos por llamada o videollamada en todo el Perú, con la misma confianza que una visita presencial.';
    const bannerCalloutEnlaceTexto = getItem('banner_callout_enlace_texto')?.valor || 'Atención en provincias ›';
    const bannerCalloutEnlace = getItem('banner_callout_enlace')?.valor || 'https://wa.me/51986350124';

    // Condición estricta: si no hay título ni sedes, no se muestra nada
    if (!rawTitulo?.trim() && sedes.length === 0) {
        return null;
    }

    // --- ESTADOS LOCALES ---
    const [sedeActivaIndex, setSedeActivaIndex] = useState(0);
    const [progress, setProgress] = useState(0); // 0% a 100%
    const [vistaMedia, setVistaMedia] = useState('sede'); // 'sede' | 'mapa'
    const tabsContainerRef = useRef(null);

    const DURATION = 6000; // 6 segundos por sede
    const TICK = 50; // actualización cada 50ms

    // Auto-avance continuo con barra de progreso tipo historias
    useEffect(() => {
        if (sedes.length <= 1) return;

        const interval = setInterval(() => {
            setProgress((prev) => {
                const next = prev + (TICK / DURATION) * 100;
                if (next >= 100) {
                    setSedeActivaIndex((current) => (current + 1) % sedes.length);
                    return 0;
                }
                return next;
            });
        }, TICK);

        return () => clearInterval(interval);
}, [sedes.length, sedeActivaIndex]);

// Resetea el progreso al cambiar de sede manualmente
const handleSelectSede = (index) => {
    setSedeActivaIndex(index);
    setProgress(0);
    setVistaMedia('sede'); // resetear vista a foto al cambiar de sede

    // Scroll automático suave de la pestaña activa en móviles
    if (tabsContainerRef.current) {
        const tabs = tabsContainerRef.current.children;
        if (tabs[index]) {
            tabs[index].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
    }
};

const currentSede = sedes[sedeActivaIndex] || sedes[0] || {};

// URL de Google Maps para el iframe
const getMapEmbedUrl = (sede) => {
    const query = sede.mapa || sede.direccion || 'Lima, Peru';
    if (query.includes('google.com/maps/embed')) return query;
    return `https://maps.google.com/maps?q=${encodeURIComponent(query)}&t=&z=15&ie=UTF8&iwloc=&output=embed`;
};

// Resaltado de "sede más cercana" en el título
const renderTitulo = () => {
    if (!rawTitulo) return null;
    const regex = /(sede m[aá]s cercana|m[aá]s cercana|tu operaci[oó]n)/i;
    const match = rawTitulo.match(regex);
    if (match) {
        const [before, ...afterParts] = rawTitulo.split(match[0]);
        return (
            <>
                {before}
                <span className="text-[var(--color-primario,#eab308)]">
                    {match[0]}
                </span>
                {afterParts.join(match[0])}
            </>
        );
    }
    return rawTitulo;
};

return (
        <section className="relative w-full bg-white py-16 sm:py-20 md:py-24 overflow-hidden border-t border-slate-100">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {/* 1. ENCABEZADO CENTRADO */}
                <div className="text-center max-w-3xl mx-auto mb-10 sm:mb-14">
                    {subtitulo && (
                        <span className="block text-xs sm:text-[13px] font-black uppercase tracking-wider text-[var(--color-primario,#d97706)] mb-2.5">
                            {subtitulo}
                        </span>
                    )}
                    {rawTitulo && (
                        <h2 className="text-2xl sm:text-3xl md:text-4xl lg:text-[42px] font-black tracking-tight text-gray-900 leading-[1.2]">
                            {renderTitulo()}
                        </h2>
                    )}
                </div>

                {/* 2. SELECTOR DE SEDES (TABS CON BARRA DE PROGRESO ANIMADA) */}
                {sedes.length > 0 && (
                    <div
                        ref={tabsContainerRef}
                        className="flex md:grid md:grid-cols-3 items-stretch gap-4 sm:gap-6 mb-8 sm:mb-10 overflow-x-auto no-scrollbar pb-2 pt-1 snap-x snap-mandatory"
                    >
                        {sedes.map((sede, idx) => {
                            const isActive = idx === sedeActivaIndex;

                            return (
                                <button
                                    key={idx}
                                    type="button"
                                    onClick={() => handleSelectSede(idx)}
                                    className={`group text-left flex-1 min-w-[240px] sm:min-w-[280px] md:min-w-0 p-3 sm:p-4 rounded-xl transition-all duration-200 cursor-pointer snap-start flex flex-col justify-between ${isActive ? 'bg-slate-50/80' : 'hover:bg-slate-50/50'
                                        }`}
                                >
                                    <div>
                                        <div className="flex items-start gap-2.5 mb-1.5">
                                            <FaLocationDot
                                                className={`w-4 h-4 mt-0.5 shrink-0 transition-colors duration-200 ${isActive
                                                        ? 'text-[var(--color-primario,#eab308)]'
                                                        : 'text-gray-400 group-hover:text-gray-600'
                                                    }`}
                                            />
                                            {/* Título: se pinta del color principal cuando está activa */}
                                            <h3
                                                className={`text-sm sm:text-base leading-snug transition-colors duration-200 ${isActive
                                                        ? 'text-[var(--color-primario,#eab308)] font-black'
                                                        : 'text-gray-700 font-bold group-hover:text-gray-900'
                                                    }`}
                                            >
                                                {sede.titulo}
                                            </h3>
                                        </div>

                                        {/* Dirección secundaria / subtítulo */}
                                        {sede.direccion && (
                                            <p className="text-xs text-gray-500 pl-6 line-clamp-1 leading-relaxed">
                                                {sede.direccion}
                                            </p>
                                        )}
                                    </div>

                                    {/* Barra de progreso inferior */}
                                    <div className="w-full h-1 bg-gray-200/80 rounded-full overflow-hidden mt-4">
                                        <div
                                            className="h-full bg-[var(--color-primario,#eab308)] rounded-full transition-all duration-75 ease-linear"
                                            style={{
                                                width: isActive ? `${progress}%` : '0%',
                                            }}
                                        />
                                    </div>
                                </button>
                            );
                        })}
                    </div>
                )}

                {/* 3. TARJETA CENTRAL (CARD BLANCA CON SOMBRA Y DIVISIÓN 2 COLUMNAS) */}
                <div className="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 shadow-xl overflow-hidden p-4 sm:p-6 md:p-8">
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">
                        {/* Columna Izquierda: FOTO / MAPA INTERACTIVO CON SWITCH */}
                        <div className="lg:col-span-7">
                            <div className="relative w-full h-[280px] sm:h-[350px] md:h-[400px] rounded-xl sm:rounded-2xl overflow-hidden bg-slate-900 shadow-md">
                                {/* Vista 1: Foto de la Sede */}
                                {vistaMedia === 'sede' && (
                                    <>
                                        {currentSede.imagen ? (
                                            <img
                                                src={currentSede.imagen}
                                                alt={currentSede.titulo}
                                                className="w-full h-full object-cover"
                                                loading="lazy"
                                                decoding="async"
                                            />
                                        ) : (
                                            <div className="w-full h-full flex flex-col items-center justify-center p-6 text-center bg-gradient-to-br from-[#183159] to-[#0f2240] text-white">
                                                <div className="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center mb-4 text-[var(--color-primario,#eab308)]">
                                                    <FaLocationDot className="w-8 h-8" />
                                                </div>
                                                <h4 className="text-xl font-bold tracking-tight mb-1">
                                                    {currentSede.titulo}
                                                </h4>
                                                <p className="text-xs sm:text-sm text-gray-300 max-w-sm">
                                                    {currentSede.direccion || 'Atención presencial en tienda física'}
                                                </p>
                                            </div>
                                        )}
                                    </>
                                )}

                                {/* Vista 2: Google Maps Embebido */}
                                {vistaMedia === 'mapa' && (
                                    <iframe
                                        src={getMapEmbedUrl(currentSede)}
                                        title={`Ubicación de ${currentSede.titulo}`}
                                        className="w-full h-full border-0"
                                        loading="lazy"
                                        allowFullScreen
                                    />
                                )}

                                {/* Switch flotante de modo: [ Sede | Mapa ] */}
                                <div className="absolute bottom-3 left-3 sm:bottom-4 sm:left-4 z-20 flex items-center bg-[#0f2240]/90 backdrop-blur-md p-1 rounded-full border border-white/15 shadow-xl">
                                    <button
                                        type="button"
                                        onClick={() => setVistaMedia('sede')}
                                        className={`px-3.5 py-1.5 rounded-full text-xs font-bold transition-all duration-200 cursor-pointer ${vistaMedia === 'sede'
                                                ? 'bg-[#183159] text-white shadow-xs font-black'
                                                : 'text-gray-300 hover:text-white'
                                            }`}
                                    >
                                        Sede
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => setVistaMedia('mapa')}
                                        className={`px-3.5 py-1.5 rounded-full text-xs font-bold transition-all duration-200 cursor-pointer ${vistaMedia === 'mapa'
                                                ? 'bg-[#183159] text-white shadow-xs font-black'
                                                : 'text-gray-300 hover:text-white'
                                            }`}
                                    >
                                        Mapa
                                    </button>
                                </div>
                            </div>
                        </div>

                        {/* Columna Derecha: INFORMACIÓN Y ACCIÓN */}
                        <div className="lg:col-span-5 flex flex-col justify-center">
                            {/* Nombre de la Sede */}
                            <h3 className="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight mb-6">
                                {currentSede.titulo}
                            </h3>

                            <div className="space-y-6">
                                {/* UBÍCANOS EN */}
                                {currentSede.direccion && (
                                    <div>
                                        <div className="flex items-center gap-2 mb-1.5">
                                            <FaLocationDot className="w-3.5 h-3.5 text-gray-500" />
                                            <span className="text-[11px] font-black uppercase tracking-wider text-gray-700">
                                                UBÍCANOS EN
                                            </span>
                                        </div>
                                        <p className="text-sm sm:text-base text-gray-800 font-medium pl-5.5 leading-relaxed">
                                            {currentSede.direccion}
                                        </p>
                                    </div>
                                )}

                                {/* HORARIOS */}
                                {currentSede.horarios && (
                                    <div>
                                        <div className="flex items-center gap-2 mb-1.5">
                                            <FaClock className="w-3.5 h-3.5 text-gray-500" />
                                            <span className="text-[11px] font-black uppercase tracking-wider text-gray-700">
                                                HORARIOS
                                            </span>
                                        </div>
                                        <div className="text-xs sm:text-sm text-gray-600 pl-5.5 leading-relaxed space-y-1">
                                            {currentSede.horarios.split('\n').map((line, lIdx) => (
                                                <p key={lIdx}>{line}</p>
                                            ))}
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Botón CTA "¿Cómo llegar?" */}
                            {currentSede.boton_enlace && (
                                <div className="mt-8">
                                    <a
                                        href={currentSede.boton_enlace}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-[var(--color-primario,#eab308)] text-gray-950 font-bold text-sm sm:text-base hover:brightness-105 transition-all duration-200 shadow-md hover:scale-[1.02] active:scale-[0.99] cursor-pointer"
                                    >
                                        <FaRoute className="w-4 h-4" />
                                        <span>¿Cómo llegar?</span>
                                    </a>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* 4. BANNER INFERIOR DE ATENCIÓN NACIONAL (CALLOUT) */}
                {(bannerCalloutTexto || (bannerCalloutEnlaceTexto && bannerCalloutEnlace)) && (
                    <div className="mt-10 sm:mt-12 bg-slate-50/90 border border-slate-200/80 rounded-2xl md:rounded-3xl p-6 sm:p-8 text-center max-w-4xl mx-auto shadow-xs">
                        {bannerCalloutTexto && (
                            <p className="text-xs sm:text-sm md:text-base text-gray-700 leading-relaxed font-medium mb-3">
                                {bannerCalloutTexto}
                            </p>
                        )}
                        {bannerCalloutEnlaceTexto && bannerCalloutEnlace && (
                            <a
                                href={bannerCalloutEnlace}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="inline-flex items-center gap-1 text-xs sm:text-sm font-black text-[var(--color-primario,#d97706)] hover:underline cursor-pointer"
                            >
                                <span>{bannerCalloutEnlaceTexto}</span>
                            </a>
                        )}
                    </div>
                )}
            </div>
        </section>
        );
}

