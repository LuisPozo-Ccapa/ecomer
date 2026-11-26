import React from 'react';
import { motion } from 'framer-motion';

export default function HeroSection({ hero }) {
    const imagen = hero?.imagen;
    const responsiveImg = hero?.responsiveImg;
    const rawTitulo = hero?.titulo;
    const rawDescripcion = hero?.descripcion;

    const titulo = rawTitulo || 'Nuestros /*Servicios*/ Especializados';
    const descripcion = rawDescripcion || 'Soluciones técnicas integrales, asesoría calificada y logística ágil para potenciar los proyectos de tu empresa.';
    const hasBgImage = Boolean(responsiveImg?.pc || responsiveImg?.tablet || responsiveImg?.cel || imagen);

    const renderFormattedTitle = (text) => {
        if (!text) return null;
        const regex = /\/\*(.*?)\*\//g;
        const parts = [];
        let lastIndex = 0;
        let match;

        while ((match = regex.exec(text)) !== null) {
            if (match.index > lastIndex) {
                parts.push({ text: text.slice(lastIndex, match.index), isHighlight: false });
            }
            parts.push({ text: match[1], isHighlight: true });
            lastIndex = regex.lastIndex;
        }
        if (lastIndex < text.length) {
            parts.push({ text: text.slice(lastIndex), isHighlight: false });
        }

        if (parts.length === 0) return <span>{text}</span>;

        return parts.map((part, idx) => (
            <span
                key={idx}
                className={part.isHighlight ? 'text-[var(--color-primario)] drop-shadow-md' : ''}
            >
                {part.text}
            </span>
        ));
    };

    return (
        <section className="relative overflow-hidden bg-black text-white h-[60vh] min-h-[60vh] w-full flex items-center justify-center">
            {hasBgImage ? (
                <div className="absolute inset-0 z-0 h-full w-full">
                    <picture className="h-full w-full block">
                        {responsiveImg?.pc && <source media="(min-width: 1024px)" srcSet={responsiveImg.pc} />}
                        {responsiveImg?.tablet && <source media="(min-width: 640px)" srcSet={responsiveImg.tablet} />}
                        <img
                            src={responsiveImg?.cel || responsiveImg?.tablet || responsiveImg?.pc || imagen}
                            alt={titulo ? titulo.replace(/\/\*|\*\//g, '') : 'Servicios'}
                            fetchPriority="high"
                            decoding="async"
                            loading="eager"
                            className="h-full w-full object-cover brightness-105"
                            style={{
                                width: '100%',
                                height: '100%',
                                objectFit: 'cover',
                                objectPosition: 'center bottom',
                            }}
                        />
                    </picture>
                </div>
            ) : (
                /* Fondo blueprint corporativo cuando no hay imagen cargada */
                <div className="absolute inset-0 z-0 overflow-hidden bg-gradient-to-br from-[#071120] via-[#0b1c36] to-[#12284c]">
                    <div
                        className="pointer-events-none absolute inset-0 opacity-15 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:24px_24px]"
                        aria-hidden="true"
                    />
                    <div className="pointer-events-none absolute -top-40 right-0 h-96 w-96 rounded-full bg-[var(--color-primario)]/20 blur-3xl" />
                    <div className="pointer-events-none absolute -bottom-20 left-0 h-96 w-96 rounded-full bg-blue-600/15 blur-3xl" />
                </div>
            )}

            {/* Overlays de Degradado para legibilidad */}
            <div className="pointer-events-none absolute inset-0 z-0 bg-gradient-to-t from-black via-black/60 to-black/40" />

            {/* Contenido Principal */}
            <div className="relative z-10 mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.6, ease: 'easeOut' }}
                    className="flex flex-col items-center"
                >
                    <span className="inline-block px-3.5 py-1 mb-4 rounded-full text-xs font-bold uppercase tracking-widest bg-white/10 backdrop-blur-md border border-white/20 text-[var(--color-primario)]">
                        Servicios Profesionales
                    </span>

                    <h1
                        className="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white drop-shadow-xl mb-4 leading-tight"
                        style={{ fontFamily: 'var(--tipografia-titulos)' }}
                    >
                        {renderFormattedTitle(titulo)}
                    </h1>

                    {descripcion && (
                        <p
                            className="max-w-2xl mx-auto text-sm sm:text-base lg:text-lg text-gray-200 font-medium leading-relaxed drop-shadow-md"
                            style={{ fontFamily: 'var(--tipografia-texto)' }}
                        >
                            {descripcion}
                        </p>
                    )}
                </motion.div>
            </div>
        </section>
    );
}
