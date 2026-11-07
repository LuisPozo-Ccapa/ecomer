import React from 'react';
import { router } from '@inertiajs/react';

export default function ComoComprarSection({ seccionData }) {
    if (!seccionData) return null;

    // Helper para extraer datos del CMS
    const getItem = (label) =>
        seccionData?.contenido?.find(
            (item) => item.label?.toLowerCase() === label.toLowerCase()
        );

    const subtitulo = getItem('subtitulo')?.valor || 'CÓMO COMPRAR';
    const rawTitulo = getItem('titulo')?.valor || 'Cómo solicitar tu cotización';

    // Imagen principal
    let rawImg = getItem('imagen')?.valor;
    if (rawImg && typeof rawImg === 'object' && !Array.isArray(rawImg)) {
        rawImg = Object.values(rawImg)[0];
    }
    if (Array.isArray(rawImg)) rawImg = rawImg[0];
    let imagenUrl = rawImg;
    if (typeof imagenUrl === 'string' && imagenUrl.trim()) {
        if (imagenUrl.includes('localhost') || imagenUrl.includes('127.0.0.1')) {
            imagenUrl = imagenUrl.replace(/^https?:\/\/[^\/]+/, '');
        }
        if (!imagenUrl.startsWith('http') && !imagenUrl.startsWith('/')) {
            imagenUrl = `/storage/${imagenUrl.replace(/^\/?storage\//, '')}`;
        }
    } else {
        imagenUrl = null;
    }

    // Pasos repetidor
    const pasosGroup =
        getItem('pasos') ||
        seccionData?.contenido?.find((c) => c.tipo === 'grupo') ||
        null;
    const rawPasos = pasosGroup?.valor;
    let pasosArray = [];
    if (Array.isArray(rawPasos)) {
        pasosArray = rawPasos;
    } else if (rawPasos && typeof rawPasos === 'object') {
        pasosArray = Object.values(rawPasos);
    } else if (typeof rawPasos === 'string') {
        try {
            const parsed = JSON.parse(rawPasos);
            pasosArray = Array.isArray(parsed) ? parsed : Object.values(parsed);
        } catch (e) {
            pasosArray = [];
        }
    }

    const pasos = pasosArray.length > 0
        ? pasosArray.filter((p) => p && (p.titulo || p.descripcion))
        : [
            {
                numero: '1',
                titulo: 'Solicita',
                descripcion: 'Desde el catálogo (carrito de cotización), el buscador o por WhatsApp.',
            },
            {
                numero: '2',
                titulo: 'Recibe tu cotización',
                descripcion: 'Con producto confirmado, precio y plazo reales.',
            },
            {
                numero: '3',
                titulo: 'Aprueba el pedido',
                descripcion: 'Confirmas y coordinamos el despacho.',
            },
            {
                numero: '4',
                titulo: 'Recibe y factura',
                descripcion: 'Entregamos y emitimos tu comprobante (DNI/CE persona natural · RUC persona jurídica).',
            },
        ];

    // Banner inferior
    const bannerTexto = getItem('banner_texto')?.valor || '¿Compras por volumen para revender? Consúltanos por condiciones mayoristas.';
    const bannerBotonTexto = getItem('banner_boton_texto')?.valor || 'WhatsApp (+51) 986 350 124';
    const bannerBotonEnlace = getItem('banner_boton_enlace')?.valor || 'https://wa.me/51986350124';

    // Condición estricta: si no está configurado (ni título ni pasos), no se muestra
    if (!rawTitulo?.trim() && pasos.length === 0) {
        return null;
    }

    // Resaltado de la palabra principal en el título con el color primario
    const renderTitulo = () => {
        if (!rawTitulo) return null;
        const regex = /(cotizaci[oó]n|tu cotizaci[oó]n)/i;
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

    // Formatear texto del banner con resaltado después del signo '?'
    const renderBannerTexto = () => {
        if (!bannerTexto) return null;
        if (bannerTexto.includes('?')) {
            const parts = bannerTexto.split('?');
            return (
                <p className="text-white text-sm sm:text-base font-medium leading-relaxed">
                    {parts[0]}?{' '}
                    <span className="text-[var(--color-primario,#eab308)] font-bold">
                        {parts.slice(1).join('?').trim()}
                    </span>
                </p>
            );
        }
        return (
            <p className="text-white text-sm sm:text-base font-medium leading-relaxed">
                {bannerTexto}
            </p>
        );
    };

    // Manejar clic en botón de WhatsApp / banner
    const handleBotonClick = () => {
        if (!bannerBotonEnlace) return;
        if (bannerBotonEnlace.startsWith('http') || bannerBotonEnlace.startsWith('wa.me') || bannerBotonEnlace.startsWith('//')) {
            const url = bannerBotonEnlace.startsWith('wa.me') ? `https://${bannerBotonEnlace}` : bannerBotonEnlace;
            window.open(url, '_blank', 'noopener,noreferrer');
        } else {
            router.visit(bannerBotonEnlace);
        }
    };

    return (
        <section className="relative w-full bg-[#f8fafc]/80 py-16 sm:py-20 md:py-24 overflow-hidden border-t border-slate-100">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {/* Cuadrícula superior: Pasos a la izquierda, Foto a la derecha */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                    {/* Columna Izquierda: Encabezado + 4 Pasos (2x2) */}
                    <div className="lg:col-span-7 flex flex-col justify-center">
                        {/* Subtítulo / Tag */}
                        {subtitulo && (
                            <div className="mb-2">
                                <span className="inline-block text-xs sm:text-[13px] font-black uppercase tracking-wider text-[var(--color-primario,#d97706)]">
                                    {subtitulo}
                                </span>
                            </div>
                        )}

                        {/* Título Principal */}
                        {rawTitulo && (
                            <h2 className="text-2xl sm:text-3xl md:text-4xl lg:text-[40px] font-black tracking-tight text-gray-900 leading-[1.15] mb-8 sm:mb-10">
                                {renderTitulo()}
                            </h2>
                        )}

                        {/* Pasos en Cuadrícula 2x2 */}
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-7 sm:gap-y-9">
                            {pasos.map((paso, index) => {
                                const stepNumber = paso.numero || (index + 1);
                                return (
                                    <div key={index} className="flex flex-col items-start">
                                        {/* Badge circular amarillo */}
                                        <div className="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-[var(--color-primario,#eab308)] text-gray-950 font-black text-base sm:text-lg flex items-center justify-center shadow-xs mb-3 select-none">
                                            {stepNumber}
                                        </div>

                                        {/* Título del paso */}
                                        <h3 className="text-base sm:text-lg font-bold text-gray-900 mb-1.5 leading-snug">
                                            {paso.titulo}
                                        </h3>

                                        {/* Descripción */}
                                        {paso.descripcion && (
                                            <p className="text-xs sm:text-sm text-gray-600 leading-relaxed">
                                                {paso.descripcion}
                                            </p>
                                        )}
                                    </div>
                                );
                            })}
                        </div>
                    </div>

                    {/* Columna Derecha: Foto rectangular limpia con esquinas redondeadas */}
                    <div className="lg:col-span-5 flex justify-center lg:justify-end">
                        <div className="relative w-full max-w-[480px] lg:max-w-[540px] aspect-[14/10] sm:aspect-[16/11] rounded-3xl overflow-hidden shadow-xl border border-slate-100 bg-[#0f2240]">
                            {imagenUrl ? (
                                <img
                                    src={imagenUrl}
                                    alt="Cómo comprar"
                                    className="w-full h-full object-cover"
                                    loading="lazy"
                                    decoding="async"
                                />
                            ) : (
                                <div className="w-full h-full flex flex-col items-center justify-center p-6 text-center text-white bg-gradient-to-br from-[#1e3a6a] via-[#0f2240] to-[#081427]">
                                    <div className="h-16 w-16 rounded-2xl border border-white/20 flex items-center justify-center mb-3 text-[var(--color-primario,#eab308)]">
                                        <svg className="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <span className="text-xs uppercase tracking-widest font-bold opacity-90">
                                        Asesoría y Cotización Inmediata
                                    </span>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Banner Inferior Navy (#0f2240) con WhatsApp */}
                {(bannerTexto || (bannerBotonTexto && bannerBotonEnlace)) && (
                    <div className="mt-12 sm:mt-14 md:mt-16 bg-[#0f2240] rounded-2xl md:rounded-3xl p-5 sm:p-6 md:py-5 md:px-8 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-6 border border-white/5">
                        {/* Texto informativo a la izquierda */}
                        <div className="text-center sm:text-left flex-1">
                            {renderBannerTexto()}
                        </div>

                        {/* Botón WhatsApp a la derecha */}
                        {bannerBotonTexto && bannerBotonEnlace && (
                            <button
                                type="button"
                                onClick={handleBotonClick}
                                className="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-xl bg-[var(--color-primario,#eab308)] text-gray-950 font-bold text-sm sm:text-base hover:brightness-105 transition-all duration-200 shadow-md hover:scale-[1.02] active:scale-[0.99] shrink-0 cursor-pointer"
                            >
                                {/* Ícono de WhatsApp SVG */}
                                <svg
                                    className="w-5 h-5 fill-current"
                                    viewBox="0 0 24 24"
                                >
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                </svg>
                                <span>{bannerBotonTexto}</span>
                            </button>
                        )}
                    </div>
                )}
            </div>
        </section>
    );
}

