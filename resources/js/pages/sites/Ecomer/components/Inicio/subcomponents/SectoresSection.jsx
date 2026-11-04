import React, { useState } from 'react';
import { router } from '@inertiajs/react';

export default function SectoresSection({ seccionData }) {
    if (!seccionData) return null;

    const [hoveredIndex, setHoveredIndex] = useState(null);

    // Helpers para extraer valores del contenido
    const getItem = (label) =>
        seccionData?.contenido?.find(
            (item) => item.label?.toLowerCase() === label.toLowerCase()
        );

    const subtitulo = getItem('subtitulo')?.valor || 'SECTORES';
    const rawTitulo = getItem('titulo')?.valor || 'Soluciones hidráulicas para tu sector';

    // Obtener las tarjetas de sectores (tipo grupo array)
    const sectoresGroup =
        getItem('sectores') ||
        seccionData?.contenido?.find((c) => c.tipo === 'grupo') ||
        null;

    const rawItems = sectoresGroup?.valor || [];

    // Normalizar elementos de sectores
    const sectores = Array.isArray(rawItems)
        ? rawItems
            .map((item) => {
                if (!item || typeof item !== 'object') return null;

                let imgUrl = item.imagen;
                if (Array.isArray(imgUrl)) {
                    imgUrl = imgUrl[0];
                }

                // Ajustar ruta si no viene con protocolo completo
                if (typeof imgUrl === 'string' && !imgUrl.startsWith('http') && !imgUrl.startsWith('/')) {
                    imgUrl = `/storage/${imgUrl.replace(/^\/?storage\//, '')}`;
                }

                return {
                    imagen: imgUrl || '',
                    titulo: item.titulo || '',
                    badge: item.badge || '',
                    descripcion: item.descripcion || '',
                    botonTexto: item.boton_texto || item.botonTexto || 'Ver soluciones',
                    enlace: item.enlace || '/productos',
                };
            })
            .filter((item) => item && (item.titulo || item.imagen))
        : [];

    // Si no está configurado o no tiene tarjetas, NO se muestra
    if (sectores.length === 0) {
        return null;
    }

    // Resaltar palabras en el título (ej: "tu sector" con acento de marca o dorado)
    // Título en color negro con acento en el color principal
    // Título en color negro con acento en las últimas dos palabras con el color principal
    // Título en color negro con acento en las últimas dos palabras en el color principal
    const renderTitulo = () => {
        if (!rawTitulo) return null;

        // Si incluye "tu sector"
        const regex = /(tu sector|tu industria)/i;
        const match = rawTitulo.match(regex);
        const trimmed = rawTitulo.trim();
        const words = trimmed.split(/\s+/);

        if (match) {
            const parts = rawTitulo.split(regex);
            // Si tiene 2 o más palabras, las últimas dos palabras toman el color principal (ej. "tu sector")
            if (words.length >= 2) {
                const base = words.slice(0, -2).join(' ');
                const accent = words.slice(-2).join(' ');
                return (
                    <>
                        {parts[0]}
                        <span className="text-amber-500">{match[0]}</span>
                        {parts[2] || ''}
                    </>
        if (words.length >= 2) {
                    const base = words.slice(0, -2).join(' ');
                    const accent = words.slice(-2).join(' ');
                    return (
                        <span className="text-slate-950">
                            {base ? `${base} ` : ''}
                            <span style={{ color: 'var(--color-primario, #f59e0b)' }}>
                                {accent}
                            </span>
                        </span>
                    );
                }
                const base = words.slice(0, -2).join(' ');
                const accent = words.slice(-2).join(' ');
                return (
                    <span className="text-slate-950">
                        {base ? `${base} ` : ''}
                        <span style={{ color: 'var(--color-primario, #f59e0b)' }}>
                            {accent}
                        </span>
                    </span>
                );
            }
        }

        // Si no coincide exactamente, divide la última o últimas dos palabras
        const words = rawTitulo.trim().split(' ');
        if (words.length > 2) {
            const base = words.slice(0, -2).join(' ');
            const accent = words.slice(-2).join(' ');
            // Si es una sola palabra, toma las 2 últimas letras
            if (trimmed.length > 2) {
                const base = trimmed.slice(0, -2);
                const accent = trimmed.slice(-2);
                return (
                    <>
                        {base} <span className="text-amber-500">{accent}</span>
                    </>
        if (trimmed.length > 2) {
                    const base = trimmed.slice(0, -2);
                    const accent = trimmed.slice(-2);
                    return (
                        <span className="text-slate-950">
                            {base}
                            <span style={{ color: 'var(--color-primario, #f59e0b)' }}>
                                {accent}
                            </span>
                        </span>
                    );
                }
            );
}

return rawTitulo;
return (
    <span style={{ color: 'var(--color-primario, #f59e0b)' }}>
        {trimmed}
    </span>
);
};
return (
    <span style={{ color: 'var(--color-primario, #f59e0b)' }}>
        {trimmed}
    </span>
);
return (
    <span style={{ color: 'var(--color-primario, #f59e0b)' }}>
        {trimmed}
    </span>
);
    };

const handleCardClick = (enlace) => {
    if (!enlace) return;
    const handleCardClick = (enlace) => {
        if (!enlace) return;

        if (enlace.startsWith('http://') || enlace.startsWith('https://')) {
            window.open(enlace, '_blank', 'noopener,noreferrer');
        } else {
            router.visit(enlace);
        }
    };
    if (enlace.startsWith('http://') || enlace.startsWith('https://')) {
        window.open(enlace, '_blank', 'noopener,noreferrer');
    } else {
        router.visit(enlace);
    }
};

return (
    <section className="w-full py-8 md:py-14 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {/* Encabezado */}
            <div className="mb-6 md:mb-10 text-left">
                {subtitulo && (
                    <span className="text-xs sm:text-sm font-extrabold uppercase tracking-widest text-amber-500 block mb-1">
                        return (
                        <section className="w-full py-8 md:py-14 bg-white">
                            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                {/* Encabezado */}
                                <div className="mb-6 md:mb-10 text-left">
                                    {subtitulo && (
                                        <span
                                            style={{ color: 'var(--color-primario, #f59e0b)' }}
                                            className="text-xs sm:text-sm font-extrabold uppercase tracking-widest block mb-1.5"
                                        >
                                            {subtitulo}
                                        </span>
                                    )}
                                    <h2 className="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight leading-tight">
                                        {renderTitulo()}
                                    </h2>
                                </div>

                                {/* Vista Mobile: Grid 2x2 */}
                                <div className="grid grid-cols-2 gap-3 sm:gap-4 md:hidden">
                                    {sectores.map((sector, index) => (
                                        <div
                                            key={`mobile-sector-${index}`}
                                            onClick={() => handleCardClick(sector.enlace)}
                                            className="relative h-60 sm:h-72 rounded-2xl overflow-hidden cursor-pointer shadow-sm active:scale-95 transition-transform duration-200 group bg-slate-900"
                                        >
                                            {sector.imagen && (
                                                <img
                                                    src={sector.imagen}
                                                    alt={sector.titulo}
                                                    loading="lazy"
                                                    decoding="async"
                                                    className="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                />
                                            )}

                                            <div className="absolute inset-0 bg-gradient-to-t from-black/95 via-black/55 to-black/10" />

                                            <div className="absolute bottom-0 inset-x-0 p-3.5 flex flex-col justify-end z-10">
                                                <h3 className="text-sm sm:text-base font-bold text-white leading-tight mb-1">
                                                    {sector.titulo}
                                                </h3>
                                                {sector.descripcion && (
                                                    <p className="text-[11px] sm:text-xs text-gray-300 line-clamp-2 leading-relaxed font-normal">
                                                        {sector.descripcion}
                                                    </p>
                                                )}
                                            </div>
                                        </div>
                                    ))}
                                </div>

                                {/* Vista Desktop: Expanding Flex Accordion */}
                                <div
                                    className="hidden md:flex flex-row gap-4 h-[450px] lg:h-[480px] w-full"
                                    onMouseLeave={() => setHoveredIndex(null)}
                                >
                                    {sectores.map((sector, index) => {
                                        const isHovered = hoveredIndex === index;

                                        return (
                                            <section className="w-full py-8 md:py-14 bg-white">
                                                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                                    {/* Encabezado */}
                                                    <div className="mb-6 md:mb-10 text-left">
                                                        {subtitulo && (
                                                            <div
                                                                key={`desktop-sector-${index}`}
                                                                onMouseEnter={() => setHoveredIndex(index)}
                                                                onClick={() => handleCardClick(sector.enlace)}
                                                                style={{
                                                                    flex: isHovered ? 2.3 : 1,
                                                                }}
                                                                className="relative rounded-3xl overflow-hidden cursor-pointer shadow-md group transition-[flex] duration-500 ease-out bg-slate-900"
                                                            >
                                                                {sector.imagen && (
                                                                    <img
                                                                        src={sector.imagen}
                                                                        alt={sector.titulo}
                                                                        loading="lazy"
                                                                        decoding="async"
                                                                        className="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                                                                    />
                                                                )}

                                                                <div
                                                                    className={`absolute inset-0 transition-opacity duration-500 bg-gradient-to-t from-black/95 via-black/50 to-black/15 ${isHovered ? 'opacity-95' : 'opacity-85'
                                                                        }`}
                                                                />

                                                                {sector.badge && (
                                    <div
                                        className={`absolute top-5 left-5 z-20 transition-all duration-300 ${isHovered
                                                ? 'opacity-100 translate-y-0'
                                                : 'opacity-0 -translate-y-2 pointer-events-none'
                                            }`}
                                    >
                                        <span
                                            style={{ color: 'var(--color-primario, #f59e0b)' }}
                                            className="text-xs sm:text-sm font-extrabold uppercase tracking-widest block mb-1.5"
                                            style={{ backgroundColor: 'var(--color-primario, #f59e0b)' }}
                                            className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-white font-black text-xs tracking-wider uppercase shadow-lg"
                                        >
                                            {subtitulo}
                                            <span className="w-2 h-2 rounded-full bg-white animate-pulse" />
                                            {sector.badge}
                                        </span>
                                    )}
                                    <h2 className="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                                        <h2 className="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight leading-tight">
                                            {renderTitulo()}
                                        </h2>
                                </div>
                                <h2 className="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight leading-tight">
                                    {renderTitulo()}
                                </h2>
                            </div>
                                    </div>
                                )}

                                                    {/* Vista Mobile: Grid 2x2 */}
                                                    <div className="grid grid-cols-2 gap-3 sm:gap-4 md:hidden">
                                                        {sectores.map((sector, index) => (
                                                            <div className="absolute bottom-0 inset-x-0 p-6 lg:p-7 flex flex-col justify-end z-20">
                                                                <h3 className="text-xl lg:text-2xl font-bold text-white tracking-tight leading-snug mb-2 drop-shadow-sm">
                                                                    {sector.titulo}
                                                                </h3>

                                                                <div
                                                                    key={`mobile-sector-${index}`}
                                                                    onClick={() => handleCardClick(sector.enlace)}
                                                                    className="relative h-60 sm:h-72 rounded-2xl overflow-hidden cursor-pointer shadow-sm active:scale-95 transition-transform duration-200 group bg-slate-900"
                                                                    className={`transition-all duration-500 overflow-hidden ${isHovered
                                                                        ? 'max-h-40 opacity-100 mt-1'
                                                                        : 'max-h-0 opacity-0 mt-0 pointer-events-none'
                                                                        }`}
                                                                >
                                                                    {/* Imagen de fondo */}
                                                                    {sector.imagen && (
                                                                        <img
                                                                            src={sector.imagen}
                                                                            alt={sector.titulo}
                                                                            loading="lazy"
                                                                            decoding="async"
                                                                            className="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                                        />
                                        {sector.descripcion && (
                                                                        <p className="text-sm text-gray-200 line-clamp-3 leading-relaxed mb-4 font-normal">
                                                                            {sector.descripcion}
                                                                        </p>
                                                                    )}
                                                                    {/* Vista Mobile: Grid 2x2 */}
                                                                    <div className="grid grid-cols-2 gap-3 sm:gap-4 md:hidden">
                                                                        {sectores.map((sector, index) => (
                                                                            <div
                                                                                key={`mobile-sector-${index}`}
                                                                                onClick={() => handleCardClick(sector.enlace)}
                                                                                className="relative h-60 sm:h-72 rounded-2xl overflow-hidden cursor-pointer shadow-sm active:scale-95 transition-transform duration-200 group bg-slate-900"
                                                                            >
                                                                                {/* Imagen de fondo */}
                                                                                {sector.imagen && (
                                                                                    <img
                                                                                        src={sector.imagen}
                                                                                        alt={sector.titulo}
                                                                                        loading="lazy"
                                                                                        decoding="async"
                                                                                        className="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                                                    />
                                                                                )}

                                                                                {/* Gradiente oscuro inferior */}
                                                                                <div className="absolute inset-0 bg-gradient-to-t from-black/95 via-black/55 to-black/10" />
                                                                                {/* Gradiente oscuro inferior */}
                                                                                <div className="absolute inset-0 bg-gradient-to-t from-black/95 via-black/55 to-black/10" />

                                                                                {/* Contenido inferior */}
                                                                                <div className="absolute bottom-0 inset-x-0 p-3.5 flex flex-col justify-end z-10">
                                                                                    <h3 className="text-sm sm:text-base font-bold text-white leading-tight mb-1">
                                                                                        {sector.titulo}
                                                                                    </h3>
                                                                                    {sector.descripcion && (
                                                                                        <p className="text-[11px] sm:text-xs text-gray-300 line-clamp-2 leading-relaxed font-normal">
                                                                                            {sector.descripcion}
                                                                                        </p>
                                                                                    )}
                                                                                </div>
                                                                            </div>
                                                                        ))}
                                                                        <div
                                                                            style={{ color: 'var(--color-primario, #f59e0b)' }}
                                                                            className="inline-flex items-center gap-2 hover:brightness-110 font-bold text-sm tracking-wide transition-all"
                                                                        >
                                                                            <span>{sector.botonTexto}</span>
                                                                            <span className="text-lg transition-transform duration-300 group-hover:translate-x-1.5">
                                                                                &rarr;
                                                                            </span>
                                                                        </div>
                                                                        {/* Contenido inferior */}
                                                                        <div className="absolute bottom-0 inset-x-0 p-3.5 flex flex-col justify-end z-10">
                                                                            <h3 className="text-sm sm:text-base font-bold text-white leading-tight mb-1">
                                                                                {sector.titulo}
                                                                            </h3>
                                                                            {sector.descripcion && (
                                                                                <p className="text-[11px] sm:text-xs text-gray-300 line-clamp-2 leading-relaxed font-normal">
                                                                                    {sector.descripcion}
                                                                                </p>
                                                                            )}
                                                                        </div>
                                                                    </div>
                                ))}
                                                                </div>
                                                            </div>

                            {/* Vista Desktop: Expanding Flex Accordion */ }
                                                            < div
                                className = "hidden md:flex flex-row gap-4 h-[450px] lg:h-[480px] w-full"
                                onMouseLeave = {() => setHoveredIndex(null)}
                            >
                                                        {sectores.map((sector, index) => {
                                                            const isHovered = hoveredIndex === index;
                                                            {/* Vista Desktop: Expanding Flex Accordion */ }
                                                            <div
                                                                className="hidden md:flex flex-row gap-4 h-[450px] lg:h-[480px] w-full"
                                                                onMouseLeave={() => setHoveredIndex(null)}
                                                            >
                                                                {sectores.map((sector, index) => {
                                                                    const isHovered = hoveredIndex === index;

                                                                    return (
                                                                        <div
                                                                            key={`desktop-sector-${index}`}
                                                                            onMouseEnter={() => setHoveredIndex(index)}
                                                                            onClick={() => handleCardClick(sector.enlace)}
                                                                            style={{
                                                                                flex: isHovered ? 2.3 : 1,
                                                                            }}
                                                                            className="relative rounded-3xl overflow-hidden cursor-pointer shadow-md group transition-[flex] duration-500 ease-out bg-slate-900"
                                                                        >
                                                                            {/* Imagen de fondo con zoom sutil */}
                                                                            {sector.imagen && (
                                                                                <img
                                                                                    src={sector.imagen}
                                                                                    alt={sector.titulo}
                                                                                    loading="lazy"
                                                                                    decoding="async"
                                                                                    className="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                                                                                />
                                                                            )}
                                                                            return (
                                                                            <div
                                                                                key={`desktop-sector-${index}`}
                                                                                onMouseEnter={() => setHoveredIndex(index)}
                                                                                onClick={() => handleCardClick(sector.enlace)}
                                                                                style={{
                                                                                    flex: isHovered ? 2.3 : 1,
                                                                                }}
                                                                                className="relative rounded-3xl overflow-hidden cursor-pointer shadow-md group transition-[flex] duration-500 ease-out bg-slate-900"
                                                                            >
                                                                                {/* Imagen de fondo con zoom sutil */}
                                                                                {sector.imagen && (
                                                                                    <img
                                                                                        src={sector.imagen}
                                                                                        alt={sector.titulo}
                                                                                        loading="lazy"
                                                                                        decoding="async"
                                                                                        className="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                                                                                    />
                                                                                )}

                                                                                {/* Overlay con gradiente dinámico */}
                                                                                <div
                                                                                    className={`absolute inset-0 transition-opacity duration-500 bg-gradient-to-t from-black/95 via-black/50 to-black/15 ${isHovered ? 'opacity-95' : 'opacity-85'
                                                                                        }`}
                                                                                />
                                                                                {/* Overlay con gradiente dinámico */}
                                                                                <div
                                                                                    className={`absolute inset-0 transition-opacity duration-500 bg-gradient-to-t from-black/95 via-black/50 to-black/15 ${isHovered ? 'opacity-95' : 'opacity-85'
                                                                                        }`}
                                                                                />

                                                                                {/* Badge superior visible al hacer hover */}
                                                                                {/* Badge superior con color principal y letras blancas */}
                                                                                {sector.badge && (
                                                                                    <div
                                                                                        className={`absolute top-5 left-5 z-20 transition-all duration-300 ${isHovered
                                                                                            ? 'opacity-100 translate-y-0'
                                                                                            : 'opacity-0 -translate-y-2 pointer-events-none'
                                                                                            }`}
                                                                                    >
                                                                                        <span className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-400 text-slate-950 font-black text-xs tracking-wider uppercase shadow-lg">
                                                                                            <span className="w-2 h-2 rounded-full bg-slate-950 animate-pulse" />
                                                                                            <span
                                                                                                style={{ backgroundColor: 'var(--color-primario, #f59e0b)' }}
                                                                                                className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-white font-black text-xs tracking-wider uppercase shadow-lg"
                                                                                            >
                                                                                                <span className="w-2 h-2 rounded-full bg-white animate-pulse" />
                                                                                                {sector.badge}
                                                                                            </span>
                                                                                    </div>
                                                                                )}
                                                                                {/* Badge superior con color principal y letras blancas */}
                                                                                {sector.badge && (
                                                                                    <div
                                                                                        className={`absolute top-5 left-5 z-20 transition-all duration-300 ${isHovered
                                                                                            ? 'opacity-100 translate-y-0'
                                                                                            : 'opacity-0 -translate-y-2 pointer-events-none'
                                                                                            }`}
                                                                                    >
                                                                                        <span
                                                                                            style={{ backgroundColor: 'var(--color-primario, #f59e0b)' }}
                                                                                            className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-white font-black text-xs tracking-wider uppercase shadow-lg"
                                                                                        >
                                                                                            <span className="w-2 h-2 rounded-full bg-white animate-pulse" />
                                                                                            {sector.badge}
                                                                                        </span>
                                                                                    </div>
                                                                                )}

                                                                                {/* Contenido inferior */}
                                                                                <div className="absolute bottom-0 inset-x-0 p-6 lg:p-7 flex flex-col justify-end z-20">
                                                                                    <h3 className="text-xl lg:text-2xl font-bold text-white tracking-tight leading-snug mb-2 drop-shadow-sm">
                                                                                        {sector.titulo}
                                                                                    </h3>
                                                                                    {/* Contenido inferior */}
                                                                                    <div className="absolute bottom-0 inset-x-0 p-6 lg:p-7 flex flex-col justify-end z-20">
                                                                                        <h3 className="text-xl lg:text-2xl font-bold text-white tracking-tight leading-snug mb-2 drop-shadow-sm">
                                                                                            {sector.titulo}
                                                                                        </h3>

                                                                                        {/* Descripción y botón visibles al expandirse */}
                                                                                        <div
                                                                                            className={`transition-all duration-500 overflow-hidden ${isHovered
                                                                                                ? 'max-h-40 opacity-100 mt-1'
                                                                                                : 'max-h-0 opacity-0 mt-0 pointer-events-none'
                                                                                                }`}
                                                                                        >
                                                                                            {sector.descripcion && (
                                                                                                <p className="text-sm text-gray-200 line-clamp-3 leading-relaxed mb-4 font-normal">
                                                                                                    {sector.descripcion}
                                                                                                </p>
                                                                                            )}
                                                                                            {/* Descripción y botón visibles al expandirse */}
                                                                                            <div
                                                                                                className={`transition-all duration-500 overflow-hidden ${isHovered
                                                                                                    ? 'max-h-40 opacity-100 mt-1'
                                                                                                    : 'max-h-0 opacity-0 mt-0 pointer-events-none'
                                                                                                    }`}
                                                                                            >
                                                                                                {sector.descripcion && (
                                                                                                    <p className="text-sm text-gray-200 line-clamp-3 leading-relaxed mb-4 font-normal">
                                                                                                        {sector.descripcion}
                                                                                                    </p>
                                                                                                )}

                                                                                                <div className="inline-flex items-center gap-2 text-amber-400 hover:text-amber-300 font-bold text-sm tracking-wide transition-colors">
                                                                                                    <div
                                                                                                        style={{ color: 'var(--color-primario, #f59e0b)' }}
                                                                                                        className="inline-flex items-center gap-2 hover:brightness-110 font-bold text-sm tracking-wide transition-all"
                                                                                                    >
                                                                                                        <span>{sector.botonTexto}</span>
                                                                                                        <span className="text-lg transition-transform duration-300 group-hover:translate-x-1.5">
                                                                                                            →
                                                                                                        </span>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        );
                                                                                        <div
                                                                                            style={{ color: 'var(--color-primario, #f59e0b)' }}
                                                                                            className="inline-flex items-center gap-2 hover:brightness-110 font-bold text-sm tracking-wide transition-all"
                                                                                        >
                                                                                            <span>{sector.botonTexto}</span>
                                                                                            <span className="text-lg transition-transform duration-300 group-hover:translate-x-1.5">
                                                                                                →
                                                                                            </span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            );
                                                                            );
                    })}
                                                                        </div>
                </div>
            </div>
                                            </section>
                                        );
                    </section>
                                );
                        </section>
                        );
}

