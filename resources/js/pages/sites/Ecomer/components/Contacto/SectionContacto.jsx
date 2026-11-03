import React, { useState } from 'react';
import { useContactoData } from './hooks/useContactoData';
import HeroContactoSection from './subcomponents/HeroContactoSection';
import CardsContactoSection from './subcomponents/CardsContactoSection';
import FormularioContactoSection from './subcomponents/FormularioContactoSection';
import UbicacionSedesSection from './subcomponents/UbicacionSedesSection';
import MediosPagoSection from './subcomponents/MediosPagoSection';

export default function SectionContacto({ site, seccion, seccionesData, estilos, productos = [] }) {
    const {
        loading,
        titulo,
        descripcion,
        referenciaComercial,
        redesFinales,
        imagenHero,
        responsiveImg,
        waNum,
        googleMapEmbedUrl,
        cardsList,
        tarjetasPagoList,
        sedesList,
        lineasLlamanos
    } = useContactoData(seccion, seccionesData, site, estilos);

    // Switches interactivos sincronizados: Selección de Sede física y Línea telefónica
    const [sedeActivaIndex, setSedeActivaIndex] = useState(0);
    const [lineaActivaIndex, setLineaActivaIndex] = useState(0);

    if (loading) {
        return (
            <div className="flex min-h-[50vh] items-center justify-center p-8">
                <div className="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-[var(--color-primario)]" />
            </div>
        );
    }

    return (
        <div className="relative w-full bg-[#f8f9fa] text-gray-900 font-sans antialiased overflow-hidden">
            {/* 1. Hero Principal con imagen responsiva y CTA a WhatsApp */}
            <HeroContactoSection
                titulo={titulo}
                descripcion={descripcion}
                imagenHero={imagenHero}
                responsiveImg={responsiveImg}
                waNum={waNum}
            />

            {/* 2. Tarjetas Flotantes Superiores (Llámanos, Escríbenos, Visítanos con sus switches) */}
            <CardsContactoSection
                cardsList={cardsList}
                sedesList={sedesList}
                lineasLlamanos={lineasLlamanos}
                sedeActivaIndex={sedeActivaIndex}
                setSedeActivaIndex={setSedeActivaIndex}
                lineaActivaIndex={lineaActivaIndex}
                setLineaActivaIndex={setLineaActivaIndex}
            />

            {/* 3. Sección Media: Formulario B2B/B2C + Tarjeta Oscura de Ubicación & Mapa */}
            <section className="relative z-20 py-12 sm:py-16 content-auto">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-stretch">
                        {/* Columna Izquierda: Formulario Cotiza tu pedido */}
                        <div className="lg:col-span-7 flex flex-col">
                            <FormularioContactoSection
                                site={site}
                                estilos={estilos}
                                seccionesData={seccionesData}
                                productos={productos}
                            />
                        </div>

                        {/* Columna Derecha: Tarjeta Oscura Nuestra Ubicación con Switch de Sedes */}
                        <div className="lg:col-span-5 flex flex-col">
                            <UbicacionSedesSection
                                sedesList={sedesList}
                                sedeActivaIndex={sedeActivaIndex}
                                setSedeActivaIndex={setSedeActivaIndex}
                                googleMapEmbedUrl={googleMapEmbedUrl}
                                referenciaComercial={referenciaComercial}
                                redesFinales={redesFinales}
                                site={site}
                            />
                        </div>
                    </div>
                </div>
            </section>

            {/* 4. Sección Inferior: Métodos de Pago Seguros y Asesor WhatsApp */}
            <div className="content-auto">
                <MediosPagoSection
                    tarjetasPagoList={tarjetasPagoList}
                    waNum={waNum}
                />
            </div>
        </div>
    );
}
