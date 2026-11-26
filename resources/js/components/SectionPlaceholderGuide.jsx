import React from 'react';
import DynamicIcon from '@/components/DynamicIcon';

/**
 * Componente unificado de "Cuadrado de Referencia / Guía Visual" (Placeholder)
 * Mantiene la presencia de las secciones activas en tienda cuando aún no se han
 * cargado imágenes o videos desde el panel administrativo.
 *
 * @param {'card' | 'banner' | 'reel' | 'grid' | 'compact'} tipo
 * @param {string} titulo - Título descriptivo de la guía
 * @param {string} subtitulo - Explicación complementaria opcional
 * @param {string} icono - Nombre del icono de DynamicIcon (ej. 'FaImage', 'FaTiktok')
 * @param {string} badge - Etiqueta superior opcional (ej. 'NUEVO', 'OFICIAL', 'GUÍA')
 * @param {string} className - Clases adicionales de tamaño o contenedor
 * @param {React.ReactNode} children - Elementos adicionales opcionales
 */
export default function SectionPlaceholderGuide({
    tipo = 'card',
    titulo = 'Espacio Referencial',
    subtitulo = null,
    icono = 'FaImage',
    badge = null,
    className = '',
    children = null,
}) {
    // 1. BANNER PANORÁMICO (Horizontal para Banner Pie, Banners Intermedios o Cabeceras)
    if (tipo === 'banner') {
        return (
            <div
                className={`relative w-full overflow-hidden rounded-2xl md:rounded-3xl border border-white/10 bg-gradient-to-r from-[#0a172c] via-[#0f2240] to-[#183159] text-white p-6 sm:p-10 md:p-12 flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl ${className}`}
            >
                {/* Patrón de fondo geométrico sutil */}
                <div
                    className="pointer-events-none absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"
                    aria-hidden="true"
                />

                <div className="relative z-10 flex flex-col sm:flex-row items-center sm:items-start md:items-center gap-5 text-center sm:text-left max-w-2xl">
                    <div className="h-16 w-16 sm:h-20 sm:w-20 rounded-2xl border border-white/20 bg-white/5 backdrop-blur-md flex items-center justify-center shrink-0 text-[var(--color-primario,#eab308)] shadow-inner">
                        <DynamicIcon name={icono} className="h-8 w-8 sm:h-10 sm:w-10" />
                    </div>
                    <div>
                        {badge && (
                            <span className="inline-block px-2.5 py-0.5 mb-2 rounded-full text-[10px] font-black uppercase tracking-widest bg-[var(--color-primario,#eab308)] text-gray-950 shadow-xs">
                                {badge}
                            </span>
                        )}
                        <h3 className="text-lg sm:text-xl md:text-2xl font-black tracking-tight text-white leading-snug">
                            {titulo}
                        </h3>
                        {subtitulo && (
                            <p className="text-xs sm:text-sm text-slate-300 mt-1 leading-relaxed">
                                {subtitulo}
                            </p>
                        )}
                    </div>
                </div>

                {children && <div className="relative z-10 shrink-0">{children}</div>}
            </div>
        );
    }

    // 2. VIDEO REEL (Vertical aspect-[9/16] estilo TikTok / Reels)
    if (tipo === 'reel') {
        return (
            <div
                className={`relative w-full aspect-[9/16] rounded-2xl md:rounded-3xl overflow-hidden border border-white/10 bg-gradient-to-b from-[#183159] via-[#0f2240] to-[#0a172c] text-white p-4 flex flex-col justify-between shadow-lg select-none group hover:border-white/30 transition-all duration-300 ${className}`}
            >
                {/* Top Badge */}
                <div className="flex items-center justify-between z-10">
                    <div className="h-8 w-8 rounded-xl border border-white/20 bg-white/10 backdrop-blur-md flex items-center justify-center text-[var(--color-primario,#eab308)]">
                        <DynamicIcon name={icono} className="h-4 w-4" />
                    </div>
                    {badge && (
                        <span className="text-[9px] font-extrabold uppercase tracking-wider bg-white/20 px-2 py-0.5 rounded-full text-slate-200">
                            {badge}
                        </span>
                    )}
                </div>

                {/* Centro: Icono Play */}
                <div className="my-auto flex flex-col items-center justify-center text-center z-10">
                    <div className="h-14 w-14 rounded-full border border-white/30 bg-white/10 backdrop-blur-md flex items-center justify-center text-white mb-2 shadow-lg group-hover:scale-110 transition-transform">
                        <DynamicIcon name="FaPlay" className="h-5 w-5 ml-0.5 text-[var(--color-primario,#eab308)]" />
                    </div>
                    <span className="text-[11px] font-bold text-slate-300 tracking-wide">
                        Video Oficial
                    </span>
                </div>

                {/* Footer Info */}
                <div className="z-10 bg-black/40 backdrop-blur-md p-2.5 rounded-xl border border-white/10">
                    <p className="text-xs font-bold text-white line-clamp-1">
                        {titulo}
                    </p>
                    {subtitulo && (
                        <p className="text-[10px] text-slate-300 line-clamp-1 mt-0.5">
                            {subtitulo}
                        </p>
                    )}
                </div>
            </div>
        );
    }

    // 3. TARJETA COMPACTA (Para celdas de cuadrícula pequeña, galería o módulos secundarios)
    if (tipo === 'compact') {
        return (
            <div
                className={`w-full h-full min-h-[140px] rounded-2xl border border-white/10 bg-gradient-to-br from-[#183159] via-[#0f2240] to-[#0a172c] text-white p-4 flex flex-col items-center justify-center text-center shadow-md ${className}`}
            >
                <div className="h-10 w-10 rounded-xl border border-white/20 bg-white/5 flex items-center justify-center mb-2 text-[var(--color-primario,#eab308)]">
                    <DynamicIcon name={icono} className="h-5 w-5" />
                </div>
                <span className="text-xs font-bold text-white tracking-wide line-clamp-1">
                    {titulo}
                </span>
                {subtitulo && (
                    <span className="text-[10px] text-slate-300 mt-0.5 line-clamp-1">
                        {subtitulo}
                    </span>
                )}
            </div>
        );
    }

    // 4. TARJETA VERTICAL ESTÁNDAR (Default: Sobre Nosotros, Cómo Comprar, FAQ, Misión, etc.)
    return (
        <div
            className={`w-full h-full min-h-[260px] rounded-3xl overflow-hidden border border-white/10 bg-gradient-to-br from-[#1e3a6a] via-[#0f2240] to-[#081427] text-white p-6 sm:p-8 flex flex-col items-center justify-center text-center shadow-xl select-none ${className}`}
        >
            <div className="h-16 w-16 sm:h-20 sm:w-20 rounded-2xl border border-white/20 bg-white/5 backdrop-blur-md flex items-center justify-center mb-4 text-[var(--color-primario,#eab308)] shadow-inner">
                <DynamicIcon name={icono} className="h-8 w-8 sm:h-10 sm:w-10" />
            </div>

            {badge && (
                <span className="inline-block px-3 py-1 mb-2.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-[var(--color-primario,#eab308)] text-gray-950">
                    {badge}
                </span>
            )}

            <h4 className="text-xs sm:text-sm uppercase tracking-widest font-extrabold text-white/95 max-w-xs leading-snug">
                {titulo}
            </h4>

            {subtitulo && (
                <p className="text-xs text-slate-300 max-w-xs mt-2 leading-relaxed">
                    {subtitulo}
                </p>
            )}

            {children && <div className="mt-4">{children}</div>}
        </div>
    );
}
