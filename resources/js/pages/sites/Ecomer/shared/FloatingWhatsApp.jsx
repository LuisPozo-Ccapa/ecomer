import React, { useState, useMemo } from 'react';
import DynamicIcon from '@/components/DynamicIcon';

// Helper para parsear líneas de contacto con nombre y número (ej. "edward 916628409")
export function parseWaContactLine(line, defaultItemLabel = 'WhatsApp', lIdx = 0, totalLines = 1) {
    const rawLine = String(line || '').trim();
    if (!rawLine) return null;

    let url = '';
    let cleanNum = '';
    let displayNum = '';
    let extractedName = '';

    const urlMatch = rawLine.match(/(https?:\/\/[^\s]+)/i);
    if (urlMatch) {
        url = urlMatch[0];
        const numFromUrl = url.replace(/\D/g, '');
        cleanNum = numFromUrl ? (numFromUrl.length === 9 ? '51' + numFromUrl : numFromUrl) : '';
        displayNum = numFromUrl ? (numFromUrl.length === 9 ? '+51 ' + numFromUrl : numFromUrl) : url;

        const textWithoutUrl = rawLine.replace(urlMatch[0], '');
        extractedName = textWithoutUrl.replace(/^[\s:\-,\(\)]+|[\s:\-,\(\)]+$/g, '').trim();
    } else {
        const phoneMatch = rawLine.match(/(?:\+?\d[\d\s\-\(\)]{6,}\d|\d{7,})/);
        if (phoneMatch) {
            displayNum = phoneMatch[0].trim();
            const digitsOnly = displayNum.replace(/\D/g, '');
            cleanNum = digitsOnly ? (digitsOnly.length === 9 ? '51' + digitsOnly : digitsOnly) : '';
            url = cleanNum ? `https://wa.me/${cleanNum}` : '';

            const textWithoutPhone = rawLine.replace(phoneMatch[0], '');
            extractedName = textWithoutPhone.replace(/^[\s:\-,\(\)]+|[\s:\-,\(\)]+$/g, '').trim();
        } else {
            const digitsOnly = rawLine.replace(/\D/g, '');
            if (digitsOnly) {
                cleanNum = digitsOnly.length === 9 ? '51' + digitsOnly : digitsOnly;
                url = `https://wa.me/${cleanNum}`;
                displayNum = rawLine;
            } else {
                return null;
            }
        }
    }

    let formattedName = '';
    if (extractedName) {
        formattedName = extractedName.replace(/\b\w/g, (c) => c.toUpperCase());
    }

    const itemLabel = String(defaultItemLabel || 'WhatsApp').trim();
    const isGenericLabel = /^(whatsapp|whatsap|contacto|teléfono|telefono|celular|móvil|movil)$/i.test(itemLabel);

    let finalLabel = '';
    if (formattedName) {
        if (isGenericLabel) {
            finalLabel = formattedName;
        } else {
            const cleanItemLabel = itemLabel.replace(/whatsapp/gi, '').trim();
            if (cleanItemLabel && !formattedName.toLowerCase().includes(cleanItemLabel.toLowerCase())) {
                finalLabel = `${formattedName} (${cleanItemLabel})`;
            } else {
                finalLabel = formattedName;
            }
        }
    } else {
        finalLabel = totalLines > 1 ? `${itemLabel} (${lIdx + 1})` : itemLabel;
    }

    return {
        label: finalLabel,
        name: formattedName,
        numero: displayNum || cleanNum,
        cleanNum,
        url,
    };
}

export default function FloatingWhatsApp({ site, dominio, siteSlug, seccionesData, estilos }) {
    const [isOpen, setIsOpen] = useState(false);
    const [selectedIndex, setSelectedIndex] = useState(0);

    // 1. Extraer contactos de WhatsApp y Teléfonos desde la configuración global
    const { contactList, horarioTexto } = useMemo(() => {
        const initialNav = seccionesData?.nav || seccionesData?.['nav'] || null;
        const initialContacto = seccionesData?.contacto || seccionesData?.['contacto'] || null;

        const rawCmsNavActions = initialNav?.contenido?.find((c) => c.label === 'accion_nav')?.valor;
        const cmsNavActions = Array.isArray(rawCmsNavActions) ? rawCmsNavActions : [];
        const globalActions = Array.isArray(estilos?.acciones_nav) ? estilos.acciones_nav : [];
        const combinedNavActions = [...globalActions, ...cmsNavActions];

        const accionesNav = combinedNavActions.filter((item, index, self) =>
            index === self.findIndex((t) => (t.texto || t.Texto) === (item.texto || item.Texto) && (t.icono || t.icon) === (item.icono || item.icon))
        );

        // Detectar horario configurado si existe
        const clockAction = accionesNav.find((a) => (a.icono || a.icon || '').toLowerCase().includes('clock'));
        const horario = clockAction?.texto || clockAction?.Texto || 'Disponible de 9 a 20 horas, de lunes a viernes.';

        // Filtrar acciones de tipo WhatsApp o Teléfono
        const waItems = accionesNav.filter((a) => {
            const ico = (a.icon || a.icono || '').toLowerCase();
            const txt = (a.texto || a.Texto || '').toLowerCase();
            return ico.includes('whatsapp') || ico.includes('phone') || txt.includes('wa.me') || txt.includes('tel:');
        });

        const list = [];
        const seenCleanNums = new Set();

        waItems.forEach((item) => {
            const rawText = item.texto || item.Texto || '';
            const lines = String(rawText).split(/\r?\n/).map((s) => s.trim()).filter(Boolean);
            const itemLabel = item.Label || item.label || 'WhatsApp';

            lines.forEach((line, lIdx) => {
                const parsed = parseWaContactLine(line, itemLabel, lIdx, lines.length);
                if (parsed && parsed.cleanNum) {
                    if (!seenCleanNums.has(parsed.cleanNum)) {
                        seenCleanNums.add(parsed.cleanNum);
                        list.push(parsed);
                    }
                }
            });
        });

        const redesWa = estilos?.redes_sociales?.whatsapp || site?.estilos?.redes_sociales?.whatsapp;
        if (redesWa) {
            const lines = String(redesWa).split(/\r?\n/).map((s) => s.trim()).filter(Boolean);
            lines.forEach((line, lIdx) => {
                const parsed = parseWaContactLine(line, 'WhatsApp Oficial', lIdx, lines.length);
                if (parsed && parsed.cleanNum) {
                    if (!seenCleanNums.has(parsed.cleanNum)) {
                        seenCleanNums.add(parsed.cleanNum);
                        list.unshift(parsed);
                    }
                }
            });
        }

        if (list.length === 0 && initialContacto) {
            const itemContacto = initialContacto?.contenido?.find(
                (c) => c.label?.toLowerCase() === 'whatsap' || c.label?.toLowerCase() === 'whatsapp'
            );
            const rawVal = itemContacto?.enlace || (Array.isArray(itemContacto?.valor) ? itemContacto.valor[0]?.texto : null);
            if (rawVal) {
                const lines = String(rawVal).split(/\r?\n/).map((s) => s.trim()).filter(Boolean);
                lines.forEach((line, lIdx) => {
                    const parsed = parseWaContactLine(line, 'WhatsApp', lIdx, lines.length);
                    if (parsed && parsed.cleanNum && !seenCleanNums.has(parsed.cleanNum)) {
                        seenCleanNums.add(parsed.cleanNum);
                        list.push(parsed);
                    }
                });
            }
        }

        // Si no hay ninguno configurado, fallback genérico
        if (list.length === 0) {
            list.push({
                label: 'Atención al Cliente',
                name: 'Atención',
                numero: '+51 970 665 124',
                cleanNum: '51970665124',
                url: 'https://wa.me/51970665124',
            });
        }

        return { contactList: list, horarioTexto: horario };
    }, [seccionesData, estilos, site]);

    const activeContact = contactList[selectedIndex] || contactList[0];

    // Formatear número visible
    const numeroFormateado = useMemo(() => {
        const raw = activeContact?.numero || activeContact?.cleanNum || '';
        const digits = raw.replace(/\D/g, '');
        if (digits.length === 9) {
            return `+51 ${digits.slice(0, 3)} ${digits.slice(3, 6)} ${digits.slice(6)}`;
        } else if (digits.length === 11 && digits.startsWith('51')) {
            const local = digits.slice(2);
            return `+51 ${local.slice(0, 3)} ${local.slice(3, 6)} ${local.slice(6)}`;
        }
        return activeContact?.numero || activeContact?.cleanNum || '+51 970 665 124';
    }, [activeContact]);

    const telLink = `tel:${activeContact?.cleanNum ? (activeContact.cleanNum.startsWith('51') ? '+' + activeContact.cleanNum : '+51' + activeContact.cleanNum) : '+51970665124'}`;
    const waLink = activeContact?.url || `https://wa.me/${activeContact?.cleanNum || '51970665124'}?text=${encodeURIComponent('Hola, quisiera más información.')}`;

    // Color para el botón de mensaje: si el color secundario es blanco (#ffffff) o vacío, usamos el tono dorado cálido fiel a la referencia
    const colorBotonMensaje = useMemo(() => {
        const sec = (estilos?.color_secundario || site?.estilos?.color_secundario || '').trim().toLowerCase();
        if (!sec || sec === '#ffffff' || sec === '#fff' || sec === 'white' || sec === 'rgb(255, 255, 255)') {
            return '#B88A38'; // Tono dorado de la imagen de referencia
        }
        return estilos?.color_secundario || site?.estilos?.color_secundario || '#B88A38';
    }, [estilos, site]);

    return (
        <div className="fixed bottom-6 right-6 z-40 flex flex-col items-end">
            {/* ── MODAL FLOTANTE (ESTILO TARJETA EXACTA DE REFERENCIA) ── */}
            {isOpen && (
                <div
                    className="mb-4 w-[calc(100vw-2rem)] max-w-[340px] overflow-hidden rounded-[28px] bg-white p-6 shadow-2xl border border-gray-100/90 transition-all duration-300 animate-in fade-in zoom-in-95"
                    style={{ fontFamily: 'var(--tipografia-texto)' }}
                >
                    {/* Selector de Asesores si hay 2 o más números configurados */}
                    {contactList.length > 1 && (
                        <div className="mb-4 flex flex-wrap gap-1.5 p-1 bg-gray-100/80 rounded-2xl">
                            {contactList.map((c, i) => (
                                <button
                                    key={i}
                                    type="button"
                                    onClick={() => setSelectedIndex(i)}
                                    className={`flex-1 text-[11px] font-bold py-1.5 px-2.5 rounded-xl transition-all cursor-pointer truncate ${
                                        selectedIndex === i
                                            ? 'bg-white text-gray-900 shadow-2xs'
                                            : 'text-gray-500 hover:text-gray-900'
                                    }`}
                                >
                                    {c.label || c.name || `Contacto ${i + 1}`}
                                </button>
                            ))}
                        </div>
                    )}

                    {/* ── SECCIÓN 1: LLÁMANOS ── */}
                    <div className="space-y-1.5">
                        <h3
                            className="text-base sm:text-lg font-extrabold text-gray-900 tracking-tight"
                            style={{ fontFamily: 'var(--tipografia-titulos)' }}
                        >
                            Llámanos
                        </h3>
                        <p className="text-xs text-gray-500 leading-snug">
                            {horarioTexto}
                        </p>

                        <div className="pt-2">
                            <a
                                href={telLink}
                                className="group relative w-full flex items-center justify-center gap-2.5 py-3 px-4 rounded-xl font-extrabold text-sm sm:text-base shadow-sm hover:opacity-95 hover:scale-[1.02] active:scale-95 transition-all duration-200"
                                style={{ backgroundColor: 'var(--color-primario, #1c355e)', color: '#ffffff' }}
                            >
                                <DynamicIcon name="FaPhone" className="h-4 w-4 shrink-0 text-white transition-transform duration-200 group-hover:rotate-12" />
                                <span className="tracking-wide font-mono text-white">{numeroFormateado}</span>
                            </a>
                        </div>
                    </div>

                    {/* Divisor sutil */}
                    <div className="my-5 border-t border-gray-100" />

                    {/* ── SECCIÓN 2: ENVÍANOS UN MENSAJE ── */}
                    <div className="space-y-1.5">
                        <h3
                            className="text-base sm:text-lg font-extrabold text-gray-900 tracking-tight"
                            style={{ fontFamily: 'var(--tipografia-titulos)' }}
                        >
                            Envíanos un mensaje
                        </h3>
                        <p className="text-xs text-gray-500 leading-snug">
                            Disponible las 24 horas del día.
                        </p>

                        <div className="pt-2">
                            <a
                                href={waLink}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="group relative w-full flex items-center justify-center gap-2.5 py-3 px-4 rounded-xl font-extrabold text-sm sm:text-base shadow-sm hover:opacity-95 hover:scale-[1.02] active:scale-95 transition-all duration-200"
                                style={{ backgroundColor: colorBotonMensaje, color: '#ffffff' }}
                            >
                                <DynamicIcon name="FaWhatsapp" className="h-5 w-5 shrink-0 text-white transition-transform duration-200 group-hover:scale-110" />
                                <span className="tracking-wide font-mono text-white">{numeroFormateado}</span>
                            </a>
                        </div>
                    </div>

                    {/* ── PIE: TIEMPO DE RESPUESTA ── */}
                    <div className="mt-5 pt-3.5 border-t border-gray-100/70 flex items-center justify-center gap-2 text-xs text-gray-500">
                        <DynamicIcon name="FaClockRotateLeft" className="h-3.5 w-3.5 text-gray-400 shrink-0" />
                        <span>
                            Nuestro tiempo de respuesta: <strong className="font-extrabold text-gray-800">1 hora</strong>
                        </span>
                    </div>
                </div>
            )}

            {/* ESTILOS DE ANIMACIÓN DE PULSO SUAVE */}
            <style>{`
                @keyframes waPulseGlow {
                    0% {
                        box-shadow: 0 0 0 0 color-mix(in srgb, var(--color-primario) 70%, transparent), 0 10px 25px -5px rgba(0, 0, 0, 0.3);
                    }
                    70% {
                        box-shadow: 0 0 0 18px color-mix(in srgb, var(--color-primario) 0%, transparent), 0 10px 25px -5px rgba(0, 0, 0, 0.3);
                    }
                    100% {
                        box-shadow: 0 0 0 0 color-mix(in srgb, var(--color-primario) 0%, transparent), 0 10px 25px -5px rgba(0, 0, 0, 0.3);
                    }
                }
                .wa-pulse-active {
                    animation: waPulseGlow 2.4s infinite ease-out;
                }
            `}</style>

            {/* ── BOTÓN FLOTANTE TRIGGER ── */}
            <button
                type="button"
                onClick={() => setIsOpen(!isOpen)}
                className={`relative flex items-center justify-center rounded-full p-4 text-white shadow-xl transition-all duration-300 hover:scale-110 cursor-pointer ${
                    !isOpen ? 'wa-pulse-active' : ''
                }`}
                style={{
                    backgroundColor: isOpen ? '#22c55e' : 'var(--color-primario)',
                }}
                title={isOpen ? 'Cerrar opciones de contacto' : 'Abrir opciones de contacto'}
                aria-label="Opciones de contacto"
            >
                <div className="relative flex items-center justify-center">
                    {isOpen ? (
                        <DynamicIcon name="FaXmark" className="h-6 w-6 text-white" />
                    ) : (
                        <>
                            <DynamicIcon name="FaWhatsapp" className="h-7 w-7 text-white" />
                            {/* BADGE DE NOTIFICACIÓN */}
                            <span className="absolute -top-3.5 -right-3.5 flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-[10px] font-extrabold text-white shadow-md border-2 border-white">
                                1
                            </span>
                        </>
                    )}
                </div>
            </button>
        </div>
    );
}
