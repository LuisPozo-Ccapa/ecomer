import React from 'react';
import SectionPlaceholderGuide from '@/components/SectionPlaceholderGuide';

export default function BanerPieSection({ seccionData }) {
    if (!seccionData) return null;

    const itemImagen = seccionData.contenido?.find(
        (c) => c.label?.toLowerCase() === 'imagen' || c.tipo === 'imagen' || c.label?.toLowerCase() === 'img'
    );

    const val = itemImagen?.valor;
    const imagenUrl = Array.isArray(val) ? val[0] : (typeof val === 'string' ? val : null);
    const enlace = itemImagen?.enlace || seccionData.enlace || null;
    const tituloBanner = seccionData.nombre || '¡Tenemos todo lo que necesitas!';

    const content = imagenUrl ? (
        <div className="w-full max-w-full overflow-hidden">
            <img
                src={imagenUrl}
                alt={tituloBanner}
                width={1200}
                height={400}
                className="w-full h-auto max-w-full object-cover block"
                loading="lazy"
                decoding="async"
            />
        </div>
    ) : (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <SectionPlaceholderGuide
                tipo="banner"
                titulo={tituloBanner}
                subtitulo="Banner promocional y publicitario · Configura tu imagen o enlace desde el panel administrativo"
                icono="FaBullhorn"
                badge="BANNER PUBLICITARIO"
            />
        </div>
    );

    return (
        <section id="baner-pie" className="w-full max-w-full overflow-hidden bg-white">
            {enlace && imagenUrl ? (
                <a
                    href={enlace}
                    target={enlace.startsWith('http') ? '_blank' : '_self'}
                    rel="noopener noreferrer"
                    className="block w-full max-w-full group"
                >
                    {content}
                </a>
            ) : (
                content
            )}
        </section>
    );
}
