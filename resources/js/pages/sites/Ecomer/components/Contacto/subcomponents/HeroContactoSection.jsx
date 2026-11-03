import React from 'react';
import { motion } from 'framer-motion';
import { FaArrowUpRightFromSquare } from 'react-icons/fa6';

export default function HeroContactoSection({
    titulo,
    descripcion,
    imagenHero,
    responsiveImg,
    waNum
}) {
    return (
        <section className="relative w-full min-h-[480px] lg:min-h-[520px] flex flex-col justify-center overflow-hidden bg-black pb-36 sm:pb-44 pt-20 sm:pt-28">
            {/* Background Image con overlay */}
            <picture className="absolute inset-0 h-full w-full pointer-events-none">
                {responsiveImg?.cel && <source media="(max-width: 639.98px)" srcSet={responsiveImg.cel} />}
                {responsiveImg?.tablet && <source media="(max-width: 1023.98px)" srcSet={responsiveImg.tablet} />}
                <img
                    src={imagenHero}
                    alt={titulo}
                    fetchPriority="high"
                    decoding="async"
                    className="h-full w-full object-cover object-center opacity-45 scale-105 transition-transform duration-700"
                />
            </picture>

            {/* Gradientes oscuros para alto contraste */}
            <div className="absolute inset-0 bg-gradient-to-r from-black/90 via-black/70 to-black/35" />
            <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/30" />

            <div className="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 w-full">
                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.6 }}
                    className="max-w-2xl"
                >
                    <h1 className="text-3xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-[1.15] drop-shadow-lg">
                        {titulo}
                    </h1>

                    {descripcion && (
                        <p className="mt-4 text-sm sm:text-base lg:text-lg text-gray-300 font-normal leading-relaxed max-w-xl">
                            {descripcion}
                        </p>
                    )}

                    {/* Botón CTA Cotizar por WhatsApp */}
                    {waNum && (
                        <div className="mt-6 sm:mt-8 flex items-center gap-4">
                            <a
                                href={`https://wa.me/${waNum}?text=${encodeURIComponent('Hola, deseo cotizar información y recibir asesoría comercial.')}`}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="inline-flex items-center gap-2.5 rounded-2xl bg-white hover:bg-neutral-100 text-gray-900 font-extrabold px-6 py-3 text-xs sm:text-sm tracking-wide shadow-2xl transition-all duration-200 hover:scale-105 active:scale-95 group border border-white/60"
                            >
                                <span>Cotizar por WhatsApp</span>
                                <FaArrowUpRightFromSquare className="h-3.5 w-3.5 text-gray-700 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" />
                            </a>
                        </div>
                    )}
                </motion.div>
            </div>
        </section>
    );
}

