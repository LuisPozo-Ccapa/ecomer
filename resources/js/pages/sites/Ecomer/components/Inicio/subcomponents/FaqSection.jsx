import React, { useState } from 'react';
import { AnimatePresence, motion } from 'framer-motion';
import { FaPlus, FaHeadset } from 'react-icons/fa6';

export default function FaqSection({ seccionData }) {
    if (!seccionData) return null;

    // Helper para extraer datos del CMS
    const getItem = (label) =>
        seccionData?.contenido?.find(
            (item) => item.label?.toLowerCase() === label.toLowerCase()
        );

    const subtitulo = getItem('subtitulo')?.valor || 'FAQ';
    const rawTitulo = getItem('titulo')?.valor || 'Preguntas frecuentes';

    // Imagen de asesora / soporte
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

    // FAQs del repetidor
    const faqsGroup =
        getItem('faqs') ||
        seccionData?.contenido?.find((c) => c.tipo === 'grupo') ||
        null;
    const rawFaqs = faqsGroup?.valor;
    let faqsArray = [];
    if (Array.isArray(rawFaqs)) {
        faqsArray = rawFaqs;
    } else if (rawFaqs && typeof rawFaqs === 'object') {
        faqsArray = Object.values(rawFaqs);
    } else if (typeof rawFaqs === 'string') {
        try {
            const parsed = JSON.parse(rawFaqs);
            faqsArray = Array.isArray(parsed) ? parsed : Object.values(parsed);
        } catch (e) {
            faqsArray = [];
        }
    }

    const faqs = (faqsArray.length > 0 ? faqsArray : [
        {
            numero: '01',
            pregunta: '¿Tienen stock para entrega inmediata?',
            respuesta: 'Sí, contamos con un amplio stock en nuestros almacenes y sedes físicas para entrega inmediata en los componentes y productos de mayor rotación.',
        },
        {
            numero: '02',
            pregunta: '¿Realizan envíos a todo el Perú?',
            respuesta: 'Sí, realizamos despachos a nivel nacional a través de las principales agencias de transporte y logística autorizadas, o con entrega directa según tu operación.',
        },
        {
            numero: '03',
            pregunta: '¿Los productos que venden tienen garantía?',
            respuesta: 'Todos nuestros productos y suministros cuentan con garantía directa de fábrica y el respaldo oficial de las marcas representadas.',
        },
        {
            numero: '04',
            pregunta: 'No estoy seguro del componente exacto que necesito. ¿Ofrecen asesoría técnica?',
            respuesta: 'Por supuesto. Nuestro equipo de especialistas técnicos e ingenieros te asesora sin costo para identificar la pieza, reemplazo o solución exacta para tu maquinaria.',
        },
        {
            numero: '05',
            pregunta: '¿Qué métodos de pago aceptan?',
            respuesta: 'Aceptamos transferencias bancarias (BCP, BBVA, Interbank), tarjetas de crédito y débito, pagos digitales (Yape, Plin) y líneas de crédito comercial para clientes corporativos.',
        },
    ]).filter((f) => f && (f.pregunta || f.respuesta));

    // Condición estricta: si no hay título ni faqs, no se muestra nada
    if (!rawTitulo?.trim() && faqs.length === 0) {
        return null;
    }

    // Estado local para acordeón (índice abierto o null)
    const [openIndex, setOpenIndex] = useState(0); // Por defecto el primero abierto

    const toggleFaq = (index) => {
        setOpenIndex((prev) => (prev === index ? null : index));
    };

    // SVG path con corte orgánico suave en la esquina inferior derecha
    const organicFaqPath = 'M 35 0 H 365 Q 400 0, 400 35 V 400 C 400 440, 355 440, 355 470 Q 355 500, 320 500 H 35 Q 0 500, 0 465 V 35 Q 0 0, 35 0 Z';

    return (
        <section className="relative w-full bg-white py-16 sm:py-20 md:py-24 overflow-hidden border-t border-slate-100">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                    {/* COLUMNA IZQUIERDA: FOTO VERTICAL CON RECORTE ORGÁNICO */}
                    <div className="lg:col-span-5 flex justify-center lg:justify-start">
                        <div className="relative w-full max-w-[360px] sm:max-w-[400px]">
                            <svg
                                viewBox="0 0 400 500"
                                className="w-full h-auto drop-shadow-xl"
                                preserveAspectRatio="xMidYMid meet"
                            >
                                <defs>
                                    <clipPath id="faqShapeClip">
                                        <path d={organicFaqPath} />
                                    </clipPath>

                                    <linearGradient id="faqNavyGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stopColor="#1e3a6a" />
                                        <stop offset="50%" stopColor="#0f2240" />
                                        <stop offset="100%" stopColor="#091528" />
                                    </linearGradient>
                                </defs>

                                {/* Base silueta */}
                                <path
                                    d={organicFaqPath}
                                    fill="url(#faqNavyGradient)"
                                />

                                {/* Fallback cuando no hay foto subida aún */}
                                {!imagenUrl && (
                                    <g clipPath="url(#faqShapeClip)">
                                        <circle cx="200" cy="240" r="150" fill="none" stroke="white" strokeWidth="1" strokeOpacity="0.08" />
                                        <circle cx="200" cy="240" r="100" fill="none" stroke="white" strokeWidth="1" strokeOpacity="0.1" />
                                        <circle cx="200" cy="240" r="50" fill="none" stroke="white" strokeWidth="1" strokeOpacity="0.12" />

                                        {/* Ícono de soporte / atención */}
                                        <g transform="translate(168, 180)" fill="none" stroke="currentColor" className="text-[var(--color-primario,#eab308)]">
                                            <circle cx="32" cy="32" r="30" strokeWidth="2.5" strokeOpacity="0.8" />
                                            <path d="M22 28 C22 20, 42 20, 42 28 C42 34, 32 34, 32 40" strokeWidth="2.5" strokeLinecap="round" strokeOpacity="0.8" />
                                            <circle cx="32" cy="48" r="2.5" fill="currentColor" />
                                        </g>

                                        <text
                                            x="200"
                                            y="290"
                                            textAnchor="middle"
                                            fill="white"
                                            fillOpacity="0.95"
                                            fontSize="13"
                                            fontWeight="700"
                                            letterSpacing="1"
                                        >
                                            ASESORÍA Y SOPORTE
                                        </text>
                                    </g>
                                )}

                                {/* Imagen real subida desde Filament */}
                                {imagenUrl && (
                                    <image
                                        href={imagenUrl}
                                        width="400"
                                        height="500"
                                        preserveAspectRatio="xMidYMid slice"
                                        clipPath="url(#faqShapeClip)"
                                    />
                                )}
                            </svg>
                        </div>
                    </div>

                    {/* COLUMNA DERECHA: ENCABEZADO Y ACORDEÓN DE PREGUNTAS */}
                    <div className="lg:col-span-7 flex flex-col justify-center">
                        {/* Subtítulo FAQ */}
                        {subtitulo && (
                            <div className="mb-2">
                                <span className="inline-block text-xs sm:text-[13px] font-black uppercase tracking-wider text-[var(--color-primario,#d97706)]">
                                    {subtitulo}
                                </span>
                            </div>
                        )}

                        {/* Título Principal */}
                        {rawTitulo && (
                            <h2 className="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight text-[#0f2240] leading-tight mb-8">
                                {rawTitulo}
                            </h2>
                        )}

                        {/* Lista de Acordeón */}
                        <div className="space-y-3.5">
                            {faqs.map((faq, index) => {
                                const isOpen = openIndex === index;
                                const numStr = faq.numero || String(index + 1).padStart(2, '0');

                                return (
                                    <div
                                        key={index}
                                        className={`rounded-2xl border transition-all duration-200 overflow-hidden ${
                                            isOpen
                                                ? 'bg-white border-amber-200/80 shadow-md ring-1 ring-amber-100'
                                                : 'bg-white border-gray-100 hover:border-gray-200 shadow-xs'
                                        }`}
                                    >
                                        <button
                                            type="button"
                                            onClick={() => toggleFaq(index)}
                                            className="w-full text-left p-4 sm:p-5 flex items-center justify-between gap-4 cursor-pointer select-none"
                                        >
                                            <div className="flex items-start sm:items-center gap-3 sm:gap-4 min-w-0">
                                                {/* Número '01', '02', etc. */}
                                                <span className="text-xs sm:text-sm font-bold text-gray-400 shrink-0 mt-0.5 sm:mt-0">
                                                    {numStr}
                                                </span>

                                                {/* Pregunta */}
                                                <h3 className="text-sm sm:text-base font-bold text-[#0f2240] leading-snug">
                                                    {faq.pregunta}
                                                </h3>
                                            </div>

                                            {/* Botón Circular con ícono rotativo */}
                                            <div
                                                className={`w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center shrink-0 transition-transform duration-300 ${
                                                    isOpen
                                                        ? 'bg-amber-100 text-[var(--color-primario,#d97706)] rotate-45'
                                                        : 'bg-amber-50/80 text-[var(--color-primario,#d97706)]'
                                                }`}
                                            >
                                                <FaPlus className="w-3 h-3 sm:w-3.5 sm:h-3.5" />
                                            </div>
                                        </button>

                                        {/* Respuesta animada */}
                                        <AnimatePresence initial={false}>
                                            {isOpen && (
                                                <motion.div
                                                    initial={{ height: 0, opacity: 0 }}
                                                    animate={{ height: 'auto', opacity: 1 }}
                                                    exit={{ height: 0, opacity: 0 }}
                                                    transition={{ duration: 0.25, ease: 'easeInOut' }}
                                                >
                                                    <div className="px-4 pb-4 sm:px-5 sm:pb-5 pt-0">
                                                        <p className="text-xs sm:text-sm text-gray-600 leading-relaxed border-t border-gray-100/80 pt-3 pl-7 sm:pl-8">
                                                            {faq.respuesta}
                                                        </p>
                                                    </div>
                                                </motion.div>
                                            )}
                                        </AnimatePresence>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

