import React, { useMemo } from 'react';
import { motion } from 'framer-motion';
import DynamicIcon from '@/components/DynamicIcon';

export default function ValoresSection({ seccionData }) {
    // Si no hay datos de sección o el valor está vacío, no renderizar nada
    if (!seccionData || !seccionData.valor) return null;

    const rawValor = seccionData.valor;
    const data = Array.isArray(rawValor) ? rawValor[0] : (rawValor || {});

    // Extraer datos del CMS
    const sub = data?.sub || data?.Sub || data?.kicker || '';
    const titulo = data?.Titulo || data?.titulo || '';
    const descripcion = data?.Descripcion || data?.descripcion || '';

    // Arreglo de tarjetas (Cards) desde el panel
    const rawCards = Array.isArray(data?.Cards)
        ? data.Cards
        : (Array.isArray(data?.cards) ? data.cards : []);

    // Si no hay tarjetas configuradas y tampoco título, la sección no debe mostrarse
    if (rawCards.length === 0 && !titulo) {
        return null;
    }

    const cards = useMemo(() => {
        return rawCards.map((c, i) => ({
            numero: String(i + 1).padStart(2, '0'),
            icono: c.Icono || c.icono || 'FaAward',
            titulo: c.Titulo || c.titulo || '',
            descripcion: c.Descripcion || c.descripcion || '',
        }));
    }, [rawCards]);

    return (
        <section className="relative overflow-hidden bg-white py-16 sm:py-20 border-t border-gray-100">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                {/* ── Encabezado alineado a la izquierda ── */}
                {(sub || titulo || descripcion) && (
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.5 }}
                        className="mb-10 sm:mb-14 max-w-3xl"
                    >
                        {sub && (
                            <p
                                className="text-xs font-extrabold uppercase tracking-widest text-[#B38029] mb-2"
                                style={{ fontFamily: 'var(--tipografia-texto)' }}
                            >
                                {sub}
                            </p>
                        )}

                        {titulo && (
                            <h2
                                className="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-4"
                                style={{ fontFamily: 'var(--tipografia-titulos)' }}
                            >
                                {titulo}
                            </h2>
                        )}

                        {descripcion && (
                            <p
                                className="text-sm sm:text-base text-gray-600 font-normal leading-relaxed"
                                style={{ fontFamily: 'var(--tipografia-texto)' }}
                            >
                                {descripcion}
                            </p>
                        )}
                    </motion.div>
                )}

                {/* ── Grid de Tarjetas de Valores ── */}
                {cards.length > 0 && (
                    <div className={`grid grid-cols-1 sm:grid-cols-2 ${cards.length >= 4 ? 'lg:grid-cols-4' : cards.length === 3 ? 'lg:grid-cols-3' : 'lg:grid-cols-2'} gap-6 sm:gap-7`}>
                        {cards.map((card, idx) => (
                            <motion.div
                                key={idx}
                                initial={{ opacity: 0, y: 25 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ duration: 0.45, delay: idx * 0.08 }}
                                className="group relative rounded-3xl p-6 sm:p-7 bg-white border border-gray-200/70 shadow-2xs hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between min-h-[220px]"
                            >
                                {/* Insignia con el número "01", "02"... grande y tenue */}
                                <span
                                    className="absolute top-5 right-6 text-3xl sm:text-4xl font-extrabold text-gray-200/90 font-mono select-none group-hover:text-[var(--color-primario)]/25 transition-colors duration-300"
                                    style={{ fontFamily: 'var(--tipografia-titulos)' }}
                                >
                                    {card.numero}
                                </span>

                                <div>
                                    {/* Contenedor del Ícono */}
                                    <div className="mb-6 flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-50 border border-gray-200/60 text-gray-700 shadow-2xs group-hover:bg-[var(--color-primario)] group-hover:text-white group-hover:border-transparent group-hover:scale-105 transition-all duration-300">
                                        <DynamicIcon name={card.icono} className="h-5 w-5" />
                                    </div>

                                    {/* Título en Mayúsculas */}
                                    {card.titulo && (
                                        <h3
                                            className="text-sm sm:text-base font-extrabold uppercase tracking-wider text-gray-900 mb-2 transition-colors duration-300 group-hover:text-[var(--color-primario)]"
                                            style={{ fontFamily: 'var(--tipografia-titulos)' }}
                                        >
                                            {card.titulo}
                                        </h3>
                                    )}

                                    {/* Descripción */}
                                    {card.descripcion && (
                                        <p
                                            className="text-xs sm:text-sm text-gray-600 font-normal leading-relaxed"
                                            style={{ fontFamily: 'var(--tipografia-texto)' }}
                                        >
                                            {card.descripcion}
                                        </p>
                                    )}
                                </div>
                            </motion.div>
                        ))}
                    </div>
                )}
            </div>
        </section>
    );
}
