import React from 'react';
import { useNosotrosData } from './hooks/useNosotrosData';
import PortadaSection from './subcomponents/PortadaSection';
import HistoriaSection from './subcomponents/HistoriaSection';
import MisionVisionSection from './subcomponents/MisionVisionSection';
import ValoresSection from './subcomponents/ValoresSection';
import ElegirnosSection from './subcomponents/ElegirnosSection';
import AccionSection from './subcomponents/AccionSection';

export default function SectionNosotros({
    seccion,
    seccionesData,
    dominio,
    siteSlug,
    site,
    estilos,
}) {
    const {
        portada,
        historia,
        misionVision,
        valores,
        elegirnos,
        accion,
        loading,
    } = useNosotrosData(seccion, seccionesData);

    if (loading) {
        return (
            <div className="flex min-h-[50vh] items-center justify-center p-8">
                <div className="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-[var(--color-primario)]" />
            </div>
        );
    }

    return (
        <main className="flex-1">
            {/* Seccion 1: Portada (Sobre Nosotros) */}
            <PortadaSection
                seccionData={portada}
                site={site}
                estilos={estilos}
                dominio={dominio}
                siteSlug={siteSlug}
                seccionesData={seccionesData}
            />

            {/* Seccion 2: Historia (Línea de tiempo cronológica) */}
            <div className="content-auto">
                <HistoriaSection seccionData={historia} />
            </div>

            {/* Seccion 3: Misión / Visión (Tarjetas oscuras con iconos flotantes) */}
            <div className="content-auto">
                <MisionVisionSection seccionData={misionVision} />
            </div>

            {/* Seccion 4: Valores y Compromiso (Cuadrícula de tarjetas con números 01, 02...) */}
            {valores?.valor && (
                <div className="content-auto">
                    <ValoresSection seccionData={valores} />
                </div>
            )}

            {/* Seccion 5: Elegirnos (Cuadrícula de tarjetas blancas con insignia numérica) */}
            <div className="content-auto">
                <ElegirnosSection seccionData={elegirnos} />
            </div>

            {/* Seccion 6: Acción (Llamado a la acción de cierre) */}
            <div className="content-auto">
                <AccionSection
                    seccionData={accion}
                    site={site}
                    estilos={estilos}
                    dominio={dominio}
                    siteSlug={siteSlug}
                    seccionesData={seccionesData}
                />
            </div>
        </main>
    );
}
