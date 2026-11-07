import React from 'react';
import { router } from '@inertiajs/react';

export default function SobreNosotrosInicioSection({ seccionData }) {
    if (!seccionData) return null;

    // Helpers para extraer valores del CMS
    const getItem = (label) =>
        seccionData?.contenido?.find(
            (item) => item.label?.toLowerCase() === label.toLowerCase()
        );

    const subtitulo = getItem('subtitulo')?.valor || 'NOSOTROS';
    const rawTitulo = getItem('titulo')?.valor || 'Jóvenes como marca, expertos en oleohidráulica';
    const descripcion = getItem('descripcion')?.valor;

    // Imagen principal (opcional, solo si el usuario la sube desde Filament)
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

    // Tarjeta flotante (Badge destacado)
    const badgeDestacado = getItem('badge_destacado')?.valor || '+20 años';
    const badgeTexto = getItem('badge_texto')?.valor || 'de experiencia técnica en oleohidráulica';

    // Métricas dinámicas (array del repetidor)
    const metricasGroup =
        getItem('metricas') ||
        seccionData?.contenido?.find((c) => c.tipo === 'grupo') ||
        null;
    const rawMetricas = metricasGroup?.valor || [];
    const metricasArray = Array.isArray(rawMetricas)
        ? rawMetricas
        : (rawMetricas && typeof rawMetricas === 'object' ? Object.values(rawMetricas) : []);
    const metricas = metricasArray.length > 0
        ? metricasArray.filter((m) => m && (m.cifra || m.etiqueta))
        : [
            { cifra: '7 años', etiqueta: 'como HPR GROUP' },
            { cifra: '37+ marcas', etiqueta: 'representadas' },
            { cifra: '3 sedes', etiqueta: 'en Lima' },
        ];

    // Botón de acción
    const botonTexto = getItem('boton_texto')?.valor || 'Conócenos';
    const botonEnlace = getItem('boton_enlace')?.valor || '/nosotros';

    // Si no tiene contenido configurado, no se muestra
    if (!rawTitulo && !descripcion) {
        return null;
    }

    // Formatear título resaltando las palabras finales con el color principal
    const renderTitulo = () => {
        if (!rawTitulo) return null;

        const regex = /(expertos en oleohidr[aá]ulica|tu sector|en el rubro)/i;
        const match = rawTitulo.match(regex);

        if (match) {
            const parts = rawTitulo.split(regex);
            return (
                <span className="text-slate-900">
                    {parts[0]}
                    <span style={{ color: 'var(--color-primario, #f59e0b)' }}>
                        {match[0]}
                    </span>
                    {parts[2] || ''}
                </span>
            );
        }

        const words = rawTitulo.trim().split(/\s+/);
        if (words.length > 3) {
            const base = words.slice(0, -3).join(' ');
            const accent = words.slice(-3).join(' ');
            return (
                <span className="text-slate-900">
                    {base}{' '}
                    <span style={{ color: 'var(--color-primario, #f59e0b)' }}>
                        {accent}
                    </span>
                </span>
            );
        }

        return <span className="text-slate-900">{rawTitulo}</span>;
    };

    const handleButtonClick = () => {
        if (!botonEnlace) return;

        if (botonEnlace.startsWith('http://') || botonEnlace.startsWith('https://')) {
            window.open(botonEnlace, '_blank', 'noopener,noreferrer');
        } else {
            router.visit(botonEnlace);
        }
    };

    return (
        <section className="w-full py-12 md:py-20 lg:py-24 bg-white overflow-hidden">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">

                    {/* Columna Izquierda: Imagen rectangular limpia con bordes redondeados y Tarjeta flotante */}
                    <div className="lg:col-span-5 flex justify-center lg:justify-start">
                        <div className="relative w-full max-w-[380px] sm:max-w-[420px] aspect-[4/5] rounded-3xl overflow-hidden shadow-2xl border border-slate-100 bg-[#0f2240]">
                            {imagenUrl ? (
                                <img
                                    src={imagenUrl}
                                    alt={rawTitulo || 'Sobre Nosotros'}
                                    className="w-full h-full object-cover"
                                    loading="lazy"
                                    decoding="async"
                                />
                            ) : (
                                <div className="w-full h-full flex flex-col items-center justify-center p-6 text-center text-white bg-gradient-to-br from-[#183159] via-[#112546] to-[#0a172c]">
                                    <div className="h-16 w-16 rounded-full border border-white/20 flex items-center justify-center mb-3 text-[var(--color-primario,#f59e0b)] font-bold text-xl">
                                        ★
                                    </div>
                                    <span className="text-xs uppercase tracking-widest font-bold opacity-80">
                                        {rawTitulo || 'Sobre Nosotros'}
                                    </span>
                                </div>
                            )}

                            {/* Tarjeta Flotante en la esquina inferior izquierda (+20 años) */}
                            {(badgeDestacado || badgeTexto) && (
                                <div className="absolute bottom-4 left-3 sm:bottom-6 sm:left-5 z-20 bg-[#0f2240]/95 text-white px-5 py-4 sm:px-6 sm:py-5 rounded-2xl shadow-2xl max-w-[220px] sm:max-w-[260px] border border-white/10 backdrop-blur-xs">
                                    {badgeDestacado && (
                                        <div
                                            className="text-2xl sm:text-3xl font-black tracking-tight"
                                            style={{ color: 'var(--color-primario, #f59e0b)' }}
                                        >
                                            {badgeDestacado}
                                        </div>
                                    )}
                                    {badgeTexto && (
                                        <p className="mt-1 text-xs sm:text-sm font-medium text-gray-200 leading-snug">
                                            {badgeTexto}
                                        </p>
                                    )}
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Columna Derecha: Contenido administrable */}
                    <div className="lg:col-span-7 flex flex-col justify-center text-left">
                        {subtitulo && (
                            <span
                                style={{ color: 'var(--color-primario, #f59e0b)' }}
                                className="text-xs sm:text-sm font-extrabold uppercase tracking-widest block mb-2"
                            >
                                {subtitulo}
                            </span>
                        )}

                        <h2 className="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight leading-snug mb-5">
                            {renderTitulo()}
                        </h2>

                        {descripcion && (
                            <p className="text-sm sm:text-base text-slate-600 leading-relaxed font-normal mb-8 max-w-2xl whitespace-pre-line">
                                {descripcion}
                            </p>
                        )}

                        {/* Fila de Métricas dinámicas */}
                        {metricas.length > 0 && (
                            <div className="grid grid-cols-3 gap-4 sm:gap-6 mb-8 max-w-xl">
                                {metricas.map((item, idx) => (
                                    <div key={`metrica-${idx}`} className="flex flex-col">
                                        <span className="text-xl sm:text-2xl lg:text-3xl font-black text-slate-900 tracking-tight">
                                            {item.cifra}
                                        </span>
                                        <span className="text-xs sm:text-sm text-slate-500 font-medium leading-tight mt-0.5">
                                            {item.etiqueta}
                                        </span>
                                    </div>
                                ))}
                            </div>
                        )}

                        {/* Botón CTA administrable */}
                        {botonTexto && (
                            <div>
                                <button
                                    type="button"
                                    onClick={handleButtonClick}
                                    style={{
                                        backgroundColor: 'var(--color-primario, #f59e0b)',
                                    }}
                                    className="inline-flex items-center justify-center px-7 py-3 rounded-xl font-bold text-sm text-slate-950 shadow-md hover:brightness-105 active:scale-95 transition-all duration-200 cursor-pointer"
                                >
                                    {botonTexto}
                                </button>
                            </div>
                        )}
                    </div>

                </div>
            </div>
        </section>
    );
}