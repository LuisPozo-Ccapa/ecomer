import React from 'react';
import { motion } from 'framer-motion';
import DynamicIcon from '@/components/DynamicIcon';

export default function CardsContactoSection({
    cardsList = [],
    sedesList = [],
    lineasLlamanos = [],
    sedeActivaIndex = 0,
    setSedeActivaIndex,
    lineaActivaIndex = 0,
    setLineaActivaIndex
}) {
    const sedeActual = sedesList[sedeActivaIndex] || sedesList[0] || {};
    const lineaActual = lineasLlamanos[lineaActivaIndex] || lineasLlamanos[0] || null;

    return (
        <div className="relative z-30 -mt-28 sm:-mt-36 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-5 lg:gap-6">
                {cardsList.map((card, idx) => {
                    const isGold = idx === 0 || card.estilo === 'destacado';
                    const isNavy = idx === 1;
                    const isBlack = idx >= 2;
                    const accentColor = card.color || 'var(--color-acento, var(--color-primario))';

                    // Valores dinámicos según el Switch activo
                    const displayTitle = card.titulo;
                    const displayDesc = isGold && lineaActual?.horario
                        ? lineaActual.horario
                        : isBlack && (sedeActual?.direccion || sedeActual?.referencia)
                            ? (sedeActual.direccion
                                ? (sedeActual.direccion.toLowerCase().startsWith('tienda') ? sedeActual.direccion : `Tienda: ${sedeActual.direccion}`)
                                : sedeActual.referencia)
                            : card.descripcion;

                    const displayBtnTxt = isGold && lineaActual?.numero
                        ? lineaActual.numero
                        : isBlack && sedesList.length > 1
                            ? 'Ver en Maps'
                            : card.botonTexto;

                    const displayBtnEnlace = isGold && lineaActual?.enlace
                        ? lineaActual.enlace
                        : isBlack && sedeActual?.botonEnlace
                            ? sedeActual.botonEnlace
                            : card.botonEnlace;

                    return (
                        <motion.div
                            key={card.id ?? idx}
                            initial={{ opacity: 0, y: 25 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.5, delay: idx * 0.1 }}
                            className="relative rounded-[2rem] p-6 sm:p-7 bg-white/75 hover:bg-white/85 backdrop-blur-2xl border border-white/90 shadow-[0_20px_45px_-12px_rgba(0,0,0,0.18)] flex flex-col justify-between hover:shadow-[0_25px_55px_-10px_rgba(0,0,0,0.25)] hover:-translate-y-1.5 transition-all duration-300 group"
                        >
                            <div>
                                <div className="flex items-center justify-between gap-2 mb-4">
                                    {/* Icono o Imagen personalizada */}
                                    <div className="h-12 w-12 rounded-2xl bg-black/[0.06] backdrop-blur-md flex items-center justify-center text-gray-900 border border-white/60 shadow-2xs">
                                        {card.imagen ? (
                                            <img
                                                src={card.imagen.startsWith('http') || card.imagen.startsWith('/') ? card.imagen : `/storage/${card.imagen}`}
                                                alt={card.titulo}
                                                className="h-7 w-7 object-contain"
                                            />
                                        ) : (
                                            <DynamicIcon name={card.icono} className="h-5 w-5 text-gray-900" />
                                        )}
                                    </div>

                                    {/* MINI SWITCH DE LÍNEAS / DEPARTAMENTOS EN CARD LLÁMANOS */}
                                    {isGold && lineasLlamanos.length > 1 && (
                                        <div className="flex items-center gap-1 p-1 rounded-xl bg-black/[0.05] border border-black/5">
                                            {lineasLlamanos.map((linea, lIdx) => (
                                                <button
                                                    key={lIdx}
                                                    type="button"
                                                    onClick={() => setLineaActivaIndex(lIdx)}
                                                    className={`px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider transition-all duration-200 cursor-pointer ${lineaActivaIndex === lIdx
                                                                ? 'text-white shadow-xs'
                                                                : 'text-gray-600 hover:text-gray-900'
                                                    }`}
                                                    style={lineaActivaIndex === lIdx ? { backgroundColor: accentColor } : {}}
                                                >
                                                    {linea.etiqueta}
                                                </button>
                                            ))}
                                        </div>
                                    )}

                                    {/* MINI SWITCH DE SEDES EN CARD VISÍTANOS */}
                                    {isBlack && sedesList.length > 1 && (
                                        <div className="flex items-center gap-1 p-1 rounded-xl bg-black/[0.05] border border-black/5">
                                            {sedesList.map((sede, sIdx) => (
                                                <button
                                                    key={sIdx}
                                                    type="button"
                                                    onClick={() => setSedeActivaIndex(sIdx)}
                                                    className={`px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider transition-all duration-200 cursor-pointer ${sedeActivaIndex === sIdx
                                                            ? 'bg-black text-white shadow-xs'
                                                            : 'text-gray-600 hover:text-gray-900'
                                                        }`}
                                                >
                                                    {sede.titulo}
                                                </button>
                                            ))}
                                        </div>
                                    )}
                                </div>

                                <h3 className="text-xl font-extrabold text-gray-900 tracking-tight">
                                    {displayTitle}
                                </h3>

                                <p className="mt-1.5 text-xs text-gray-600 font-medium leading-relaxed min-h-[36px] transition-all duration-200">
                                    {displayDesc}
                                </p>
                            </div>

                            {/* Píldora de acción rápida */}
                            <div className="mt-5 pt-2">
                                {isGold ? (
                                    <a
                                        href={displayBtnEnlace}
                                        target={displayBtnEnlace.startsWith('http') ? '_blank' : '_self'}
                                        rel={displayBtnEnlace.startsWith('http') ? 'noopener noreferrer' : undefined}
                                        className="inline-flex items-center justify-center gap-2 w-full sm:w-auto rounded-xl px-5 py-2.5 text-xs font-bold text-white transition-all shadow-md hover:brightness-110 hover:shadow-lg active:scale-95 cursor-pointer"
                                        style={{ backgroundColor: accentColor }}
                                    >
                                        <DynamicIcon name={card.icono} className="h-3.5 w-3.5 text-white/95" />
                                        <span className="truncate">{displayBtnTxt}</span>
                                    </a>
                                ) : isNavy ? (
                                    <a
                                        href={displayBtnEnlace}
                                        target={displayBtnEnlace.startsWith('http') ? '_blank' : '_self'}
                                        rel={displayBtnEnlace.startsWith('http') ? 'noopener noreferrer' : undefined}
                                        className="inline-flex items-center justify-center gap-2 w-full sm:w-auto rounded-xl bg-[#122238] hover:bg-[#0c1828] text-white px-5 py-2.5 text-xs font-bold transition-all shadow-md shadow-[#122238]/30 hover:shadow-lg hover:scale-[1.02] active:scale-95"
                                    >
                                        <DynamicIcon name={card.icono} className="h-3.5 w-3.5 text-neutral-300" />
                                        <span className="truncate">{displayBtnTxt}</span>
                                    </a>
                                ) : (
                                    <a
                                        href={displayBtnEnlace}
                                        target={displayBtnEnlace.startsWith('http') ? '_blank' : '_self'}
                                        rel={displayBtnEnlace.startsWith('http') ? 'noopener noreferrer' : undefined}
                                        className="inline-flex items-center justify-center gap-2 w-full sm:w-auto rounded-xl bg-black hover:bg-neutral-800 text-white px-5 py-2.5 text-xs font-bold transition-all shadow-md shadow-black/30 hover:shadow-lg hover:scale-[1.02] active:scale-95"
                                    >
                                        <DynamicIcon name={card.icono} className="h-3.5 w-3.5 text-neutral-300" />
                                        <span className="truncate">{displayBtnTxt}</span>
                                    </a>
                                )}
                            </div>
                        </motion.div>
                    );
                })}
            </div>
        </div>
    );
}

