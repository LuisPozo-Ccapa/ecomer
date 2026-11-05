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

    // Silueta orgánica extraída matemáticamente del contorno pixel a pixel de la referencia HPR
    const organicPath = "M 287.1 0 L 295.7 2.7 L 302.9 5.4 L 308.8 8.1 L 312.4 10.8 L 315.6 13.5 L 317.9 16.2 L 320.2 18.8 L 322.4 21.5 L 324.3 24.2 L 325.6 26.9 L 327 29.6 L 328.8 32.3 L 330.6 35 L 332 37.7 L 333.3 40.3 L 335.1 43 L 337 45.7 L 338.8 48.4 L 341 51.1 L 344.2 53.8 L 347.4 56.5 L 351.9 59.2 L 361.4 61.9 L 372.3 64.5 L 381.4 67.2 L 385.5 69.9 L 389.1 72.6 L 391.4 75.3 L 392.7 78 L 393.6 80.7 L 395 83.4 L 396.4 86 L 396.8 88.7 L 397.3 91.4 L 397.3 94.1 L 397.7 96.8 L 398.2 99.5 L 398.6 102.2 L 398.6 104.9 L 398.6 107.6 L 398.6 110.2 L 398.6 112.9 L 398.6 115.6 L 398.6 118.3 L 398.6 121 L 398.6 123.6 L 398.6 126.4 L 398.6 129.1 L 398.6 131.7 L 398.6 134.4 L 398.6 137.1 L 398.6 139.8 L 398.6 142.5 L 398.6 145.2 L 398.6 147.9 L 398.6 150.5 L 398.6 153.3 L 398.6 155.9 L 398.6 158.6 L 398.6 161.3 L 398.6 164 L 398.6 166.7 L 398.6 169.4 L 398.6 172.1 L 398.6 174.8 L 398.6 177.4 L 398.6 180.1 L 398.6 182.8 L 398.6 185.5 L 398.6 188.2 L 398.6 190.9 L 398.6 193.6 L 398.6 196.3 L 398.6 198.9 L 398.6 201.6 L 398.6 204.3 L 398.6 207 L 398.6 209.7 L 398.6 212.4 L 398.6 215 L 398.6 217.8 L 398.6 220.5 L 398.6 223.1 L 398.6 225.8 L 398.6 228.5 L 398.6 231.2 L 398.6 233.9 L 398.6 236.6 L 398.6 239.3 L 398.6 242 L 398.6 244.6 L 399.1 247.3 L 399.1 250 L 399.1 252.7 L 398.6 255.4 L 398.6 258.1 L 398.6 260.8 L 398.6 263.5 L 398.6 266.2 L 398.6 268.8 L 398.6 271.5 L 398.6 274.2 L 398.6 276.9 L 398.6 279.6 L 398.6 282.3 L 398.6 285 L 398.6 287.7 L 398.6 290.3 L 398.6 293 L 398.6 295.7 L 398.6 298.4 L 398.6 301.1 L 398.6 303.8 L 398.6 306.5 L 398.6 309.2 L 398.6 311.9 L 398.6 314.5 L 398.6 317.2 L 398.6 319.9 L 398.6 322.6 L 398.6 325.3 L 398.6 328 L 398.6 330.7 L 398.6 333.3 L 398.6 336 L 398.6 338.7 L 398.6 341.4 L 398.6 344.1 L 398.6 346.8 L 398.6 349.5 L 398.6 352.2 L 398.6 354.9 L 398.6 357.5 L 398.6 360.2 L 398.6 362.9 L 398.6 365.6 L 398.6 368.3 L 398.6 371 L 398.6 373.7 L 398.6 376.4 L 398.6 379.1 L 398.6 381.7 L 398.6 384.4 L 398.6 387.1 L 398.6 389.8 L 398.6 392.5 L 398.2 395.2 L 397.7 397.8 L 397.3 400.6 L 396.8 403.3 L 396.4 405.9 L 395.5 408.6 L 394.6 411.3 L 393.2 414 L 391.8 416.7 L 390 419.4 L 387.8 422 L 384.6 424.8 L 377.3 427.4 L 366.9 430.1 L 356 432.8 L 349.2 435.5 L 345.6 438.2 L 342.8 440.9 L 340.6 443.6 L 338.3 446.3 L 336.5 448.9 L 334.7 451.6 L 332.9 454.3 L 331 457 L 329.2 459.7 L 327.9 462.3 L 326.5 465.1 L 324.7 467.8 L 322.9 470.5 L 320.6 473.1 L 318.4 475.8 L 316.1 478.5 L 313.4 481.2 L 310.2 483.9 L 306.1 486.5 L 284.8 489.3 L 262.1 492 L 238.5 494.6 L 228.6 497.3 L 216.3 500 L 43.5 500 L 28.5 497.3 L 19.9 494.6 L 16.3 492 L 13.6 489.3 L 10 486.5 L 7.3 483.9 L 5.9 481.2 L 5 478.5 L 4.1 475.8 L 3.2 473.1 L 2.3 470.5 L 2.3 467.8 L 1.4 465.1 L 1.4 462.3 L 1.8 459.7 L 1.8 457 L 1.8 454.3 L 1.4 451.6 L 1.4 448.9 L 1.4 446.3 L 1.4 443.6 L 1.4 440.9 L 1.4 438.2 L 1.4 435.5 L 1.4 432.8 L 1.4 430.1 L 1.4 427.4 L 1.4 424.8 L 1.4 422 L 1.4 419.4 L 1.4 416.7 L 1.4 414 L 1.4 411.3 L 1.4 408.6 L 1.4 405.9 L 1.4 403.3 L 1.4 400.6 L 1.4 397.8 L 1.4 395.2 L 1.4 392.5 L 1.4 389.8 L 1.4 387.1 L 1.4 384.4 L 1.4 381.7 L 1.4 379.1 L 1.4 376.4 L 1.4 373.7 L 1.4 371 L 1.4 368.3 L 1.4 365.6 L 1.4 362.9 L 1.4 360.2 L 1.4 357.5 L 1.4 354.9 L 1.4 352.2 L 1.4 349.5 L 1.4 346.8 L 1.4 344.1 L 1.4 341.4 L 1.4 338.7 L 1.4 336 L 1.4 333.3 L 1.4 330.7 L 1.4 328 L 1.4 325.3 L 1.4 322.6 L 1.4 319.9 L 1.4 317.2 L 1.4 314.5 L 1.4 311.9 L 1.4 309.2 L 1.4 306.5 L 1.4 303.8 L 1.4 301.1 L 1.4 298.4 L 1.4 295.7 L 1.4 293 L 0.9 290.3 L 0.5 287.7 L 0.5 285 L 0.9 282.3 L 1.4 279.6 L 1.4 276.9 L 1.4 274.2 L 1.4 271.5 L 1.4 268.8 L 1.4 266.2 L 1.4 263.5 L 1.4 260.8 L 1.4 258.1 L 1.4 255.4 L 1.4 252.7 L 1.4 250 L 1.4 247.3 L 1.4 244.6 L 1.4 242 L 1.4 239.3 L 1.4 236.6 L 1.4 233.9 L 1.4 231.2 L 1.4 228.5 L 1.4 225.8 L 1.4 223.1 L 1.4 220.5 L 1.4 217.8 L 1.4 215 L 1.4 212.4 L 1.4 209.7 L 1.4 207 L 0.9 204.3 L 0.9 201.6 L 0.9 198.9 L 1.4 196.3 L 1.4 193.6 L 1.4 190.9 L 1.4 188.2 L 0.9 185.5 L 0.9 182.8 L 0.9 180.1 L 1.4 177.4 L 1.4 174.8 L 1.4 172.1 L 1.4 169.4 L 0.9 166.7 L 0.5 164 L 0.5 161.3 L 0.9 158.6 L 1.4 155.9 L 1.4 153.3 L 1.4 150.5 L 1.4 147.9 L 1.4 145.2 L 1.4 142.5 L 1.4 139.8 L 1.4 137.1 L 1.4 134.4 L 1.4 131.7 L 1.4 129.1 L 1.4 126.4 L 1.4 123.6 L 1.4 121 L 1.4 118.3 L 1.4 115.6 L 1.4 112.9 L 1.4 110.2 L 1.4 107.6 L 1.4 104.9 L 1.4 102.2 L 1.4 99.5 L 1.4 96.8 L 1.4 94.1 L 1.4 91.4 L 1.4 88.7 L 1.4 86 L 1.4 83.4 L 1.4 80.7 L 1.4 78 L 1.4 75.3 L 1.4 72.6 L 1.4 69.9 L 1.4 67.2 L 1.4 64.5 L 1.4 61.9 L 1.4 59.2 L 1.4 56.5 L 1.4 53.8 L 1.4 51.1 L 1.4 48.4 L 1.4 45.7 L 1.4 43 L 1.8 40.3 L 2.3 37.7 L 2.7 35 L 3.2 32.3 L 3.6 29.6 L 4.5 26.9 L 5.4 24.2 L 6.8 21.5 L 8.6 18.8 L 10.9 16.2 L 13.2 13.5 L 15.9 10.8 L 19 8.1 L 24.9 5.4 L 113.4 2.7 L 284.4 0 Z";

    return (
        <section className="w-full py-12 md:py-20 lg:py-24 bg-white overflow-hidden">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">

                    {/* Columna Izquierda: Silueta orgánica vectorial y Tarjeta flotante */}
                    <div className="lg:col-span-5 flex justify-center lg:justify-start">
                        <div className="relative w-full max-w-[360px] sm:max-w-[420px] aspect-[4/5]">

                            {/* Renderizado vectorial SVG: garantiza que la forma se vea exacta a la referencia original */}
                            <svg
                                viewBox="0 0 400 500"
                                className="w-full h-full drop-shadow-2xl overflow-visible select-none"
                            >
                                <defs>
                                    <clipPath id="hprOrganicShapeClip">
                                        <path d={organicPath} />
                                    </clipPath>

                                    <linearGradient id="hprNavyGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stopColor="#183159" />
                                        <stop offset="50%" stopColor="#112546" />
                                        <stop offset="100%" stopColor="#0a172c" />
                                    </linearGradient>
                                </defs>

                                {/* Silueta base con la forma orgánica idéntica y gradiente corporativo */}
                                <path
                                    d={organicPath}
                                    fill="url(#hprNavyGradient)"
                                />

                                {/* Efectos decorativos sutiles para la forma limpia (sin foto) */}
                                {!imagenUrl && (
                                    <g clipPath="url(#hprOrganicShapeClip)" className="opacity-30">
                                        <circle cx="200" cy="250" r="160" fill="none" stroke="white" strokeWidth="1" strokeOpacity="0.08" />
                                        <circle cx="200" cy="250" r="100" fill="none" stroke="white" strokeWidth="1" strokeOpacity="0.12" />
                                        <circle cx="200" cy="250" r="40" fill="none" stroke="white" strokeWidth="1" strokeOpacity="0.15" />
                                    </g>
                                )}

                                {/* Imagen recortada solo si ha sido subida en Filament */}
                                {imagenUrl && (
                                    <image
                                        href={imagenUrl}
                                        width="400"
                                        height="500"
                                        preserveAspectRatio="xMidYMid slice"
                                        clipPath="url(#hprOrganicShapeClip)"
                                    />
                                )}
                            </svg>

                            {/* Tarjeta Flotante en la esquina inferior izquierda (+20 años) */}
                            {(badgeDestacado || badgeTexto) && (
                                <div className="absolute bottom-4 left-3 sm:bottom-6 sm:left-5 z-20 bg-[#0f2240] text-white px-5 py-4 sm:px-6 sm:py-5 rounded-2xl shadow-2xl max-w-[220px] sm:max-w-[260px] border border-white/10 backdrop-blur-xs">
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