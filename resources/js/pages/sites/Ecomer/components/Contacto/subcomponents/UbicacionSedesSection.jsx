import React from 'react';
import { FaArrowUpRightFromSquare, FaLocationDot, FaBullhorn } from 'react-icons/fa6';
import DynamicIcon from '@/components/DynamicIcon';

export default function UbicacionSedesSection({
    sedesList = [],
    sedeActivaIndex = 0,
    setSedeActivaIndex,
    googleMapEmbedUrl,
    referenciaComercial,
    redesFinales = [],
    site
}) {
    const currentSede = sedesList[sedeActivaIndex] || sedesList[0] || {};

    return (
        <div className="bg-[#181926] text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-gray-800 flex-1 flex flex-col justify-between">
            <div>
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
                    <h3 className="text-2xl font-bold text-white tracking-tight">
                        Nuestra <span className="font-extrabold text-white">Ubicación</span>
                    </h3>
                    {currentSede.botonEnlace && (
                        <a
                            href={currentSede.botonEnlace}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-1.5 text-[11px] font-bold text-neutral-300 hover:text-white transition-colors py-1.5 px-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 w-fit"
                        >
                            <span>Abrir en Maps</span>
                            <FaArrowUpRightFromSquare className="h-3 w-3" />
                        </a>
                    )}
                </div>

                {/* SWITCH DE SEDES / SUCURSALES (TABS TIPO PILL) */}
                {sedesList.length > 1 && (
                    <div className="mt-4 flex items-center gap-1.5 p-1 rounded-2xl bg-white/5 border border-white/10 overflow-x-auto no-scrollbar">
                        {sedesList.map((sede, sIdx) => {
                            const isSedeActive = sedeActivaIndex === sIdx;
                            return (
                                <button
                                    key={sede.id ?? sIdx}
                                    type="button"
                                    onClick={() => setSedeActivaIndex(sIdx)}
                                    className={`px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all duration-200 cursor-pointer flex items-center gap-1.5 ${isSedeActive
                                        ? 'bg-white text-gray-900 shadow-md font-extrabold scale-102'
                                        : 'text-neutral-300 hover:text-white hover:bg-white/10'
                                        }`}
                                >
                                    <FaLocationDot className={`h-3 w-3 ${isSedeActive ? 'text-red-600' : 'text-neutral-400'}`} />
                                    <span>{sede.titulo}</span>
                                </button>
                            );
                        })}
                    </div>
                )}

                {/* Mapa interactivo embebido dinámico */}
                <div className="mt-4 h-64 sm:h-72 w-full rounded-2xl overflow-hidden border border-white/10 shadow-lg relative bg-neutral-900">
                    {currentSede.embedUrl || googleMapEmbedUrl ? (
                        <iframe
                            key={currentSede.embedUrl || googleMapEmbedUrl}
                            src={currentSede.embedUrl || googleMapEmbedUrl}
                            title={`Ubicación de ${currentSede.titulo || site?.nombre || 'la tienda'}`}
                            className="h-full w-full border-0"
                            loading="lazy"
                            allowFullScreen
                        />
                    ) : (
                        <div className="h-full w-full flex flex-col items-center justify-center p-6 text-center text-neutral-400 space-y-2">
                            <FaLocationDot className="h-8 w-8 text-neutral-500" />
                            <span className="text-xs font-medium">Ubicación disponible en tienda física</span>
                            {currentSede.direccion && (
                                <span className="text-xs text-white/90 font-semibold">{currentSede.direccion}</span>
                            )}
                        </div>
                    )}
                </div>

                {/* Referencia Comercial de la Sede seleccionada */}
                <div className="mt-4 p-4 rounded-2xl bg-white/5 border border-white/10 flex items-start gap-3">
                    <div className="h-8 w-8 rounded-xl bg-white/10 flex items-center justify-center shrink-0 text-white mt-0.5">
                        <FaBullhorn className="h-4 w-4 text-white" />
                    </div>
                    <div>
                        <div className="flex items-center gap-2">
                            <span className="block text-[11px] font-bold text-white uppercase tracking-wider">
                                Referencia comercial
                            </span>
                            {sedesList.length > 1 && (
                                <span
                                    className="text-[10px] font-extrabold uppercase"
                                    style={{ color: 'var(--color-acento, var(--color-primario))' }}
                                >
                                    • {currentSede.titulo}
                                </span>
                            )}
                        </div>
                        <p className="text-xs text-neutral-300 mt-1 leading-relaxed">
                            {currentSede.referencia || referenciaComercial || (currentSede.direccion ? `Visítanos en nuestra sede: ${currentSede.direccion}` : 'Visítanos en nuestra tienda central para atención presencial.')}
                        </p>
                        {currentSede.direccion && (
                            <p className="text-[11px] text-neutral-400 mt-1 font-mono">
                                📍 {currentSede.direccion}
                            </p>
                        )}
                    </div>
                </div>
            </div>

            {/* Franja Horizontal con Iconos Circulares Premium (Opción 2) */}
            {redesFinales && redesFinales.length > 0 && (
                <div className="mt-5 p-3 sm:p-3.5 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-between gap-3">
                    <div className="flex items-center gap-2 min-w-0">
                        <span className="h-2 w-2 rounded-full bg-emerald-400 animate-pulse shrink-0" />
                        <span className="text-xs font-bold text-neutral-300 tracking-wide truncate">
                            Síguenos en nuestras redes
                        </span>
                    </div>

                    <div className="flex items-center gap-2 shrink-0">
                        {redesFinales.map((red, rIdx) => (
                            <a
                                key={rIdx}
                                href={red.url}
                                target="_blank"
                                rel="noopener noreferrer"
                                title={red.nombre}
                                aria-label={red.nombre}
                                className={`h-9 w-9 rounded-xl bg-white/10 border border-white/10 flex items-center justify-center text-white transition-all duration-200 hover:scale-110 active:scale-95 shadow-sm group ${red.color || 'hover:bg-white hover:text-gray-900'}`}
                            >
                                <DynamicIcon name={red.icono} className="h-4 w-4 transition-transform group-hover:scale-110" />
                            </a>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}

