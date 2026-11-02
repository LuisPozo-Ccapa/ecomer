import React, { useState, useMemo } from 'react';
import { motion } from 'framer-motion';
import DynamicIcon from '@/components/DynamicIcon';
import LegalModal from '@/components/LegalModal';
import { useContactoData } from './hooks/useContactoData';
import {
    FaArrowUpRightFromSquare,
    FaLocationDot,
    FaEnvelope,
    FaPhone,
    FaWhatsapp,
    FaBullhorn,
    FaCheck,
    FaSpinner,
    FaMoneyBillWave,
    FaBuildingColumns,
    FaMobileScreenButton,
    FaCreditCard
} from 'react-icons/fa6';

export default function SectionContacto({ site, seccion, seccionesData, estilos, productos = [] }) {
    const { contacto } = useContactoData(seccion, seccionesData);
    const [legalModalOpen, setLegalModalOpen] = useState(false);
    const [aceptoPoliticas, setAceptoPoliticas] = useState(false);

    // Form state
    const [enviado, setEnviado] = useState(false);
    const [enviando, setEnviando] = useState(false);
    const [errorMsg, setErrorMsg] = useState(null);

    const memoData = useMemo(() => {
        const rawValor = contacto?.valor || contacto?.contenido?.find((c) => (c.label || '').toLowerCase() === 'contacto')?.valor;
        const data = Array.isArray(rawValor) ? rawValor[0] : (rawValor || {});

        const directTitulo = contacto?.contenido?.find((c) => (c.label || '').toLowerCase() === 'titulo')?.valor;
        const directDesc = contacto?.contenido?.find((c) => {
            const l = (c.label || '').toLowerCase();
            return l === 'descripcion' || l === 'descripción' || l === 'subtitulo';
        })?.valor;

        const titulo = data?.Titulo || data?.titulo || (typeof directTitulo === 'string' ? directTitulo : null) || 'Cotiza con nosotros directo de fábrica';
        const descripcion = data?.Descripcion || data?.descripcion || (typeof directDesc === 'string' ? directDesc : null) || 'Equipa tu negocio o renueva con la mejor calidad y atención directa. Precios de fábrica y asesoría comercial personalizada.';
        const referenciaComercial = data?.Referencia || data?.referencia || null;

        const extractUrl = (val) => {
            if (!val) return null;
            if (typeof val === 'string') return val.trim();
            if (Array.isArray(val)) {
                if (val.length === 0) return null;
                const first = val[0];
                return typeof first === 'string' ? first.trim() : extractUrl(first);
            }
            if (typeof val === 'object' && val !== null) {
                const possibleUrl = val.url || val.src || val.path || val.Imagen || val.imagen || Object.values(val)[0];
                return extractUrl(possibleUrl);
            }
            return null;
        };

        const getResponsiveFromObj = (obj) => {
            if (!obj || typeof obj !== 'object') return null;
            const pc = extractUrl(obj.Imagen_pc || obj.imagen_pc || obj.pc || obj.Imagen_PC || obj.imagen_desktop);
            const tablet = extractUrl(obj.Imagen_tablet || obj.imagen_tablet || obj.tablet || obj.Imagen_Tablet);
            const cel = extractUrl(obj.Imagen_cel || obj.imagen_cel || obj.cel || obj.Imagen_Cel || obj.imagen_mobile);
            if (pc || tablet || cel) {
                return { pc, tablet, cel };
            }
            return null;
        };

        const directImgItem = contacto?.contenido?.find((c) => {
            const l = (c.label || '').toLowerCase();
            return l === 'imagen' || l === 'imagenes' || l === 'imagens' || l === 'foto' || l === 'portada' || l === 'banner' || c.tipo === 'imagen' || c.tipo === 'grupo';
        });
        const directImgVal = directImgItem?.valor;

        const rawImagenes = data?.Imagenes || data?.imagenes || data?.Imagens || data?.imagens || directImgVal;
        let responsiveImg = null;
        if (Array.isArray(rawImagenes) && rawImagenes.length > 0) {
            const firstSlide = rawImagenes[0];
            if (typeof firstSlide === 'object' && firstSlide !== null) {
                responsiveImg = getResponsiveFromObj(firstSlide);
            }
            if (!responsiveImg) {
                const singleUrl = extractUrl(rawImagenes);
                if (singleUrl) {
                    responsiveImg = { pc: singleUrl, tablet: singleUrl, cel: singleUrl };
                }
            }
        } else if (rawImagenes && typeof rawImagenes === 'object') {
            responsiveImg = getResponsiveFromObj(rawImagenes);
        }

        if (!responsiveImg) {
            responsiveImg = getResponsiveFromObj(data);
        }

        const fallbackImg = extractUrl(
            data?.Imagen ||
            data?.imagen ||
            data?.Imagenes ||
            data?.imagenes ||
            data?.Imagens ||
            data?.imagens ||
            data?.Foto ||
            data?.foto ||
            directImgVal ||
            seccion?.imagen ||
            site?.imagen
        );

        const imagenHero = responsiveImg?.pc || responsiveImg?.tablet || responsiveImg?.cel || fallbackImg || 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?q=80&w=1800&auto=format&fit=crop';

        const direccion = estilos?.direccion
            || site?.estilos?.direccion
            || data?.Mapa
            || data?.mapa
            || null;
        const mapaUrlConfig = estilos?.mapa_url || site?.estilos?.mapa_url;

        const redesConfig = estilos?.redes_sociales || site?.estilos?.redes_sociales || {};

        const formatSocialUrl = (val, prefix = '') => {
            if (!val) return null;
            const str = String(val).trim();
            if (!str) return null;
            if (str.startsWith('http://') || str.startsWith('https://')) return str;
            if (str.startsWith('@')) return `${prefix}${str.slice(1)}`;
            return `${prefix}${str}`;
        };

        const redesList = [];
        if (redesConfig.facebook) redesList.push({ nombre: 'Facebook', icono: 'FaFacebook', url: formatSocialUrl(redesConfig.facebook, 'https://facebook.com/'), handle: redesConfig.facebook });
        if (redesConfig.instagram) redesList.push({ nombre: 'Instagram', icono: 'FaInstagram', url: formatSocialUrl(redesConfig.instagram, 'https://instagram.com/'), handle: redesConfig.instagram });
        if (redesConfig.tiktok) redesList.push({ nombre: 'TikTok', icono: 'FaTiktok', url: formatSocialUrl(redesConfig.tiktok, 'https://tiktok.com/@'), handle: redesConfig.tiktok });
        if (redesConfig.youtube) redesList.push({ nombre: 'YouTube', icono: 'FaYoutube', url: formatSocialUrl(redesConfig.youtube, 'https://youtube.com/@'), handle: redesConfig.youtube });
        if (redesConfig.twitter) redesList.push({ nombre: 'X (Twitter)', icono: 'FaXTwitter', url: formatSocialUrl(redesConfig.twitter, 'https://x.com/'), handle: redesConfig.twitter });

        const cmsRedes = Array.isArray(data?.Redes) ? data.Redes : (Array.isArray(data?.redes) ? data.redes : []);
        const redesFinales = redesList.length > 0 ? redesList : cmsRedes.map(r => ({
            nombre: r.nombre || 'Red Social',
            icono: r.Icono || r.icono || 'FaShareNodes',
            url: r.Texto || r.texto || '#',
            handle: r.Texto || r.texto || ''
        }));

        const globalActions = Array.isArray(estilos?.acciones_nav)
            ? estilos.acciones_nav
            : (Array.isArray(site?.estilos?.acciones_nav) ? site.estilos.acciones_nav : []);

        const waFromActions = globalActions.find((a) => {
            const ico = (a.icono || a.icon || '').toLowerCase();
            const txt = (a.texto || a.Texto || '').toLowerCase();
            return ico.includes('whatsapp') || txt.includes('wa.me');
        });

        const rawWa = redesConfig.whatsapp || waFromActions?.texto || waFromActions?.Texto || '';
        const firstSegment = String(rawWa).split(/[\r\n/;,|]+/).map((s) => s.trim()).filter(Boolean)[0] || String(rawWa);
        const cleanDigits = firstSegment.replace(/\D/g, '');
        const waNum = cleanDigits ? (cleanDigits.length === 9 ? '51' + cleanDigits : cleanDigits) : null;

        // Teléfono y Correo para fallback
        const phoneItem = globalActions.find(a => {
            const ico = (a.icono || a.icon || '').toLowerCase();
            const txt = (a.texto || a.Texto || '').toLowerCase();
            return (ico.includes('phone') || ico.includes('headset') || ico.includes('tel')) && !ico.includes('whatsapp');
        });
        const phoneNavTexto = phoneItem?.texto || phoneItem?.Texto || null;

        const emailItem = globalActions.find(a => {
            const ico = (a.icono || a.icon || '').toLowerCase();
            const txt = (a.texto || a.Texto || '').toLowerCase();
            return ico.includes('envelope') || ico.includes('mail') || txt.includes('@');
        });
        const emailNavTexto = emailItem?.texto || emailItem?.Texto || site?.email || null;

        const horarioItem = globalActions.find(a => {
            const ico = (a.icono || a.icon || '').toLowerCase();
            return ico.includes('clock') || ico.includes('calendar');
        });
        const horarioTexto = horarioItem?.texto || horarioItem?.Texto || 'Lun a Vie (09:00 am - 07:00 pm)';

        const rawCuentas = estilos?.cuentas_pago || site?.estilos?.cuentas_pago || [];
        const cuentasPago = Array.isArray(rawCuentas)
            ? rawCuentas
            : (rawCuentas && typeof rawCuentas === 'object' ? Object.values(rawCuentas) : []);

        const googleMapEmbedUrl = mapaUrlConfig && (mapaUrlConfig.includes('google.com/maps/embed') || mapaUrlConfig.includes('output=embed'))
            ? mapaUrlConfig
            : (direccion ? `https://maps.google.com/maps?q=${encodeURIComponent(direccion)}&t=&z=15&ie=UTF8&iwloc=&output=embed` : null);

        // --- CARDS SUPERIORES: MODIFICABLES DESDE CMS O FALLBACK AUTOMÁTICO ---
        const rawCards = data?.Cards || data?.cards;
        let cmsCards = [];
        if (Array.isArray(rawCards)) {
            cmsCards = rawCards;
        } else if (rawCards && typeof rawCards === 'object') {
            cmsCards = Object.values(rawCards);
        }

        let cardsList = [];
        if (cmsCards.length > 0) {
            cardsList = cmsCards.map((c, idx) => {
                const img = extractUrl(c.Imagen || c.imagen);
                const ico = c.Icono || c.icono || (idx === 0 ? 'FaPhone' : idx === 1 ? 'FaEnvelope' : 'FaLocationDot');
                const title = c.Titulo || c.titulo || '';
                const desc = c.Descripcion || c.descripcion || '';
                const btnTxt = c.Boton_Texto || c.boton_texto || c.boton || c.Boton || '';
                const btnLink = c.Boton_Enlace || c.boton_enlace || c.enlace || c.url || '#';

                return {
                    id: idx,
                    icono: ico,
                    imagen: img,
                    titulo: title,
                    descripcion: desc,
                    botonTexto: btnTxt,
                    botonEnlace: btnLink,
                    estilo: idx === 0 ? 'destacado' : 'oscuro'
                };
            });
        } else {
            // Fallback inteligente fiel a la referencia
            cardsList = [
                {
                    id: 0,
                    icono: 'FaPhone',
                    imagen: null,
                    titulo: 'Llámanos',
                    descripcion: horarioTexto,
                    botonTexto: phoneNavTexto || (waNum ? `+${waNum}` : '+51 984 150 060'),
                    botonEnlace: phoneNavTexto ? `tel:${phoneNavTexto.replace(/[^\d+]/g, '')}` : (waNum ? `tel:${waNum}` : '#'),
                    estilo: 'destacado'
                },
                {
                    id: 1,
                    icono: 'FaEnvelope',
                    imagen: null,
                    titulo: 'Escríbenos',
                    descripcion: 'Atención rápida para ventas corporativas y cotizaciones',
                    botonTexto: 'Enviar correo',
                    botonEnlace: emailNavTexto ? `mailto:${emailNavTexto}` : '#',
                    estilo: 'oscuro'
                },
                {
                    id: 2,
                    icono: 'FaLocationDot',
                    imagen: null,
                    titulo: 'Visítanos',
                    descripcion: direccion ? `Fábrica / Tienda: ${direccion}` : 'Visita nuestro taller y tienda principal',
                    botonTexto: 'Ver en Maps',
                    botonEnlace: mapaUrlConfig || (direccion ? `https://maps.google.com/maps?q=${encodeURIComponent(direccion)}` : '#'),
                    estilo: 'oscuro'
                }
            ];
        }

        // --- TARJETAS DE PAGO INFERIORES: MODIFICABLES DESDE CMS O FALLBACK AUTOMÁTICO ---
        const rawTarjetasPago = data?.Tarjetas_Pago || data?.tarjetas_pago;
        let cmsTarjetasPago = [];
        if (Array.isArray(rawTarjetasPago)) {
            cmsTarjetasPago = rawTarjetasPago;
        } else if (rawTarjetasPago && typeof rawTarjetasPago === 'object') {
            cmsTarjetasPago = Object.values(rawTarjetasPago);
        }

        let tarjetasPagoList = [];
        if (cmsTarjetasPago.length > 0) {
            tarjetasPagoList = cmsTarjetasPago.map((tp, idx) => {
                const img = extractUrl(tp.Imagen || tp.imagen);
                const ico = tp.Icono || tp.icono || (idx === 0 ? 'FaMoneyBillWave' : idx === 1 ? 'FaBuildingColumns' : 'FaMobileScreenButton');
                const title = tp.Titulo || tp.titulo || 'Método de Pago';
                const desc = tp.Descripcion || tp.descripcion || '';

                return {
                    id: idx,
                    icono: ico,
                    imagen: img,
                    titulo: title,
                    descripcion: desc,
                };
            });
        } else {
            // Fallback idéntico a la referencia
            tarjetasPagoList = [
                {
                    id: 0,
                    icono: 'FaMoneyBillWave',
                    imagen: null,
                    titulo: 'Efectivo',
                    descripcion: 'Pago en nuestra oficina al momento de la compra',
                },
                {
                    id: 1,
                    icono: 'FaBuildingColumns',
                    imagen: null,
                    titulo: 'Transferencia Bancaria',
                    descripcion: 'Transferencias desde cualquier entidad bancaria.',
                },
                {
                    id: 2,
                    icono: 'FaMobileScreenButton',
                    imagen: null,
                    titulo: 'Pago Fácil',
                    descripcion: 'Pago rápido y seguro desde tu aplicativo.',
                },
            ];
        }

        return {
            titulo,
            descripcion,
            referenciaComercial,
            direccion,
            mapaUrlConfig,
            redesFinales,
            imagenHero,
            responsiveImg,
            globalActions,
            waNum,
            cuentasPago,
            googleMapEmbedUrl,
            cardsList,
            tarjetasPagoList
        };
    }, [contacto, seccion, site, estilos]);

    const {
        titulo,
        descripcion,
        referenciaComercial,
        direccion,
        redesFinales,
        imagenHero,
        responsiveImg,
        waNum,
        cuentasPago,
        googleMapEmbedUrl,
        cardsList,
        tarjetasPagoList
    } = memoData;

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (enviando || enviado) return;

        const form = e.currentTarget;
        const formData = new FormData(form);

        const nombre = formData.get('nombre') || '';
        const empresa = formData.get('empresa') || '';
        const telefono = formData.get('telefono') || '';
        const email = formData.get('email') || '';
        const sector = formData.get('sector') || '';
        const producto = formData.get('producto') || '';
        const mensaje = formData.get('mensaje') || '';

        setEnviando(true);
        setErrorMsg(null);

        const navContenido = seccionesData?.nav?.contenido || [];
        const logoNavItem = Array.isArray(navContenido)
            ? navContenido.find((c) => {
                const l = (c.label || '').toLowerCase();
                return l === 'logo' || l === 'logo_nav' || l.includes('logo');
            })
            : null;
        const logoUrl = site?.imagen || logoNavItem?.valor || estilos?.logo || null;

        try {
            const res = await fetch('/api/contacto', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    nombre,
                    empresa,
                    telefono,
                    email,
                    sector,
                    producto,
                    mensaje,
                    site_id: site?.id || null,
                    sitio_nombre: site?.nombre || null,
                    sitio_logo: logoUrl || null
                })
            });

            const data = await res.json();

            if (res.ok) {
                setEnviado(true);
            } else {
                setErrorMsg(data.message || 'Ocurrió un error al enviar tu consulta. Por favor inténtalo de nuevo.');
            }
        } catch (err) {
            setErrorMsg('No pudimos conectar con el servidor. Intenta enviando tu mensaje por WhatsApp.');
        } finally {
            setEnviando(false);
        }
    };

    const redPrincipal = redesFinales[0] || {
        nombre: 'Facebook',
        icono: 'FaFacebook',
        url: 'https://facebook.com',
        handle: 'facebook.com/empresa'
    };

    return (
        <div className="relative w-full bg-[#f8f9fa] text-gray-900 font-sans antialiased overflow-hidden">

            {/* ========================================================
                1. HERO HEADER CON OVERLAY OSCURO Y CTA DE WHATSAPP
            ======================================================== */}
            <section className="relative w-full min-h-[480px] lg:min-h-[520px] flex flex-col justify-center overflow-hidden bg-black pb-36 sm:pb-44 pt-20 sm:pt-28">
                {/* Background Image con overlay */}
                <picture className="absolute inset-0 h-full w-full pointer-events-none">
                    {responsiveImg?.cel && <source media="(max-width: 639.98px)" srcSet={responsiveImg.cel} />}
                    {responsiveImg?.tablet && <source media="(max-width: 1023.98px)" srcSet={responsiveImg.tablet} />}
                    <img
                        src={imagenHero}
                        alt={titulo}
                        fetchPriority="high"
                        decoding="async"
                        className="h-full w-full object-cover object-center opacity-45 scale-105 transition-transform duration-700"
                    />
                </picture>

                {/* Gradientes oscuros para alto contraste */}
                <div className="absolute inset-0 bg-gradient-to-r from-black/90 via-black/70 to-black/35" />
                <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/30" />

                <div className="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 w-full">
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.6 }}
                        className="max-w-2xl"
                    >
                        <h1 className="text-3xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-[1.15] drop-shadow-lg">
                            {titulo}
                        </h1>

                        {descripcion && (
                            <p className="mt-4 text-sm sm:text-base lg:text-lg text-gray-300 font-normal leading-relaxed max-w-xl">
                                {descripcion}
                            </p>
                        )}

                        {/* Botón CTA Cotizar por WhatsApp */}
                        {waNum && (
                            <div className="mt-6 sm:mt-8 flex items-center gap-4">
                                <a
                                    href={`https://wa.me/${waNum}?text=${encodeURIComponent('Hola, deseo cotizar información y recibir asesoría comercial.')}`}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="inline-flex items-center gap-2.5 rounded-2xl bg-white hover:bg-neutral-100 text-gray-900 font-extrabold px-6 py-3 text-xs sm:text-sm tracking-wide shadow-2xl transition-all duration-200 hover:scale-105 active:scale-95 group border border-white/60"
                                >
                                    <span>Cotizar por WhatsApp</span>
                                    <FaArrowUpRightFromSquare className="h-3.5 w-3.5 text-gray-700 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" />
                                </a>
                            </div>
                        )}
                    </motion.div>
                </div>
            </section>

            {/* ========================================================
                2. CARDS FLOTANTES SUPERIORES (LLÁMANOS, ESCRÍBENOS, VISÍTANOS)
                   Efecto Glassmorphism / Difuminado Translúcido (backdrop-blur)
            ======================================================== */}
            <div className="relative z-30 -mt-28 sm:-mt-36 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="grid grid-cols-1 md:grid-cols-3 gap-5 lg:gap-6">
                    {cardsList.map((card, idx) => {
                        const isGold = idx === 0 || card.estilo === 'destacado';
                        const isNavy = idx === 1;
                        const isBlack = idx >= 2;

                        return (
                            <motion.div
                                key={card.id ?? idx}
                                initial={{ opacity: 0, y: 25 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ duration: 0.5, delay: idx * 0.1 }}
                                className="relative rounded-[2rem] p-6 sm:p-7 bg-white/75 hover:bg-white/85 backdrop-blur-2xl border border-white/90 shadow-[0_20px_45px_-12px_rgba(0,0,0,0.18)] flex flex-col justify-between hover:shadow-[0_25px_55px_-10px_rgba(0,0,0,0.25)] hover:-translate-y-1.5 transition-all duration-300 group"
                            >
                                <div>
                                    {/* Icono o Imagen personalizada */}
                                    <div className="h-12 w-12 rounded-2xl bg-black/[0.06] backdrop-blur-md flex items-center justify-center text-gray-900 mb-4 border border-white/60 shadow-2xs">
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

                                    <h3 className="text-xl font-extrabold text-gray-900 tracking-tight">
                                        {card.titulo}
                                    </h3>

                                    <p className="mt-1.5 text-xs text-gray-600 font-medium leading-relaxed min-h-[36px]">
                                        {card.descripcion}
                                    </p>
                                </div>

                                {/* Píldora de acción rápida con sombras de color según la referencia */}
                                <div className="mt-5 pt-2">
                                    {isGold ? (
                                        <a
                                            href={card.botonEnlace}
                                            target={card.botonEnlace.startsWith('http') ? '_blank' : '_self'}
                                            rel={card.botonEnlace.startsWith('http') ? 'noopener noreferrer' : undefined}
                                            className="inline-flex items-center justify-center gap-2 w-full sm:w-auto rounded-xl px-5 py-2.5 text-xs font-bold text-white transition-all shadow-md shadow-[#B88A38]/30 hover:brightness-110 hover:shadow-lg active:scale-95"
                                            style={{ backgroundColor: '#B88A38' }}
                                        >
                                            <DynamicIcon name={card.icono} className="h-3.5 w-3.5 text-white/95" />
                                            <span className="truncate">{card.botonTexto}</span>
                                        </a>
                                    ) : isNavy ? (
                                        <a
                                            href={card.botonEnlace}
                                            target={card.botonEnlace.startsWith('http') ? '_blank' : '_self'}
                                            rel={card.botonEnlace.startsWith('http') ? 'noopener noreferrer' : undefined}
                                            className="inline-flex items-center justify-center gap-2 w-full sm:w-auto rounded-xl bg-[#122238] hover:bg-[#0c1828] text-white px-5 py-2.5 text-xs font-bold transition-all shadow-md shadow-[#122238]/30 hover:shadow-lg hover:scale-[1.02] active:scale-95"
                                        >
                                            <DynamicIcon name={card.icono} className="h-3.5 w-3.5 text-neutral-300" />
                                            <span className="truncate">{card.botonTexto}</span>
                                        </a>
                                    ) : (
                                        <a
                                            href={card.botonEnlace}
                                            target={card.botonEnlace.startsWith('http') ? '_blank' : '_self'}
                                            rel={card.botonEnlace.startsWith('http') ? 'noopener noreferrer' : undefined}
                                            className="inline-flex items-center justify-center gap-2 w-full sm:w-auto rounded-xl bg-black hover:bg-neutral-800 text-white px-5 py-2.5 text-xs font-bold transition-all shadow-md shadow-black/30 hover:shadow-lg hover:scale-[1.02] active:scale-95"
                                        >
                                            <DynamicIcon name={card.icono} className="h-3.5 w-3.5 text-neutral-300" />
                                            <span className="truncate">{card.botonTexto}</span>
                                        </a>
                                    )}
                                </div>
                            </motion.div>
                        );
                    })}
                </div>
            </div>

            {/* ========================================================
                3. SECCIÓN MEDIA: FORMULARIO B2B/B2C + TARJETA DARK UBICACIÓN
            ======================================================== */}
            <section className="relative z-20 py-12 sm:py-16">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-stretch">

                        {/* COLUMNA IZQUIERDA: FORMULARIO "Cotiza tu pedido aquí" */}
                        <div className="lg:col-span-7 flex flex-col">
                            <div className="bg-white rounded-3xl p-6 sm:p-10 border border-gray-200/70 shadow-sm flex-1 flex flex-col justify-between">
                                <div>
                                    <h2 className="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mb-2">
                                        Cotiza tu pedido aquí
                                    </h2>
                                    <p className="text-xs sm:text-sm text-gray-500 mb-6">
                                        Completa el formulario y un asesor comercial se pondrá en contacto contigo de inmediato.
                                    </p>

                                    {enviado ? (
                                        <div className="py-12 px-6 rounded-2xl bg-emerald-50 border border-emerald-200 text-center space-y-3">
                                            <div className="h-12 w-12 rounded-full bg-emerald-600 text-white flex items-center justify-center mx-auto shadow-md">
                                                <FaCheck className="h-6 w-6" />
                                            </div>
                                            <h3 className="text-lg font-bold text-emerald-950">
                                                ¡Solicitud Enviada con Éxito!
                                            </h3>
                                            <p className="text-xs text-emerald-800 max-w-md mx-auto">
                                                Gracias por comunicarte con nosotros. Nuestro equipo revisará tus requerimientos y te responderá en el menor tiempo posible.
                                            </p>
                                            <button
                                                type="button"
                                                onClick={() => setEnviado(false)}
                                                className="mt-4 inline-block text-xs font-semibold text-emerald-700 underline hover:text-emerald-900 cursor-pointer"
                                            >
                                                Enviar otra cotización
                                            </button>
                                        </div>
                                    ) : (
                                        <form onSubmit={handleSubmit} className="space-y-4">
                                            {errorMsg && (
                                                <div className="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-medium">
                                                    {errorMsg}
                                                </div>
                                            )}

                                            {/* FILA 1: Nombre / Empresa */}
                                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label htmlFor="form-nombre" className="block text-xs font-bold text-gray-700 mb-1">
                                                        Nombre completo o Razón Social <span className="text-red-500">*</span>
                                                    </label>
                                                    <input
                                                        id="form-nombre"
                                                        type="text"
                                                        name="nombre"
                                                        required
                                                        placeholder="Ingresa tu nombre"
                                                        className="w-full rounded-xl border border-gray-200 bg-gray-50/70 px-4 py-2.5 text-xs text-gray-900 placeholder-gray-400 outline-none transition focus:border-gray-900 focus:bg-white focus:ring-1 focus:ring-gray-900"
                                                    />
                                                </div>

                                                <div>
                                                    <label htmlFor="form-empresa" className="block text-xs font-bold text-gray-700 mb-1">
                                                        Empresa / Institución
                                                    </label>
                                                    <input
                                                        id="form-empresa"
                                                        type="text"
                                                        name="empresa"
                                                        placeholder="Nombre de la Empresa"
                                                        className="w-full rounded-xl border border-gray-200 bg-gray-50/70 px-4 py-2.5 text-xs text-gray-900 placeholder-gray-400 outline-none transition focus:border-gray-900 focus:bg-white focus:ring-1 focus:ring-gray-900"
                                                    />
                                                </div>
                                            </div>

                                            {/* FILA 2: Teléfono / Correo */}
                                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label htmlFor="form-telefono" className="block text-xs font-bold text-gray-700 mb-1">
                                                        Teléfono / WhatsApp <span className="text-red-500">*</span>
                                                    </label>
                                                    <input
                                                        id="form-telefono"
                                                        type="tel"
                                                        name="telefono"
                                                        required
                                                        placeholder="+51 987 654 321"
                                                        className="w-full rounded-xl border border-gray-200 bg-gray-50/70 px-4 py-2.5 text-xs text-gray-900 placeholder-gray-400 outline-none transition focus:border-gray-900 focus:bg-white focus:ring-1 focus:ring-gray-900"
                                                    />
                                                </div>

                                                <div>
                                                    <label htmlFor="form-email" className="block text-xs font-bold text-gray-700 mb-1">
                                                        Correo electrónico
                                                    </label>
                                                    <input
                                                        id="form-email"
                                                        type="email"
                                                        name="email"
                                                        placeholder="correo@ejemplo.com"
                                                        className="w-full rounded-xl border border-gray-200 bg-gray-50/70 px-4 py-2.5 text-xs text-gray-900 placeholder-gray-400 outline-none transition focus:border-gray-900 focus:bg-white focus:ring-1 focus:ring-gray-900"
                                                    />
                                                </div>
                                            </div>

                                            {/* FILA 3: Sector / Producto de Interés */}
                                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label htmlFor="form-sector" className="block text-xs font-bold text-gray-700 mb-1">
                                                        Sector <span className="text-red-500">*</span>
                                                    </label>
                                                    <select
                                                        id="form-sector"
                                                        name="sector"
                                                        required
                                                        defaultValue=""
                                                        className="w-full rounded-xl border border-gray-200 bg-gray-50/70 px-4 py-2.5 text-xs text-gray-900 outline-none transition focus:border-gray-900 focus:bg-white focus:ring-1 focus:ring-gray-900"
                                                    >
                                                        <option value="" disabled>- Seleccione una Opción -</option>
                                                        <option value="Minorista / Consumo Personal">Minorista / Consumo Personal</option>
                                                        <option value="Distribuidor / Mayorista">Distribuidor / Mayorista</option>
                                                        <option value="Empresa / Corporativo">Empresa / Corporativo</option>
                                                        <option value="Hotelería / Servicios">Hotelería / Servicios</option>
                                                        <option value="Institución Pública o Educativa">Institución Pública o Educativa</option>
                                                        <option value="Otro">Otro</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label htmlFor="form-producto" className="block text-xs font-bold text-gray-700 mb-1">
                                                        Producto de Interés <span className="text-red-500">*</span>
                                                    </label>
                                                    {productos && productos.length > 0 ? (
                                                        <select
                                                            id="form-producto"
                                                            name="producto"
                                                            required
                                                            defaultValue=""
                                                            className="w-full rounded-xl border border-gray-200 bg-gray-50/70 px-4 py-2.5 text-xs text-gray-900 outline-none transition focus:border-gray-900 focus:bg-white focus:ring-1 focus:ring-gray-900"
                                                        >
                                                            <option value="" disabled>- Seleccione un Producto -</option>
                                                            {productos.map((prod) => (
                                                                <option key={prod.id || prod.slug} value={prod.nombre}>
                                                                    {prod.nombre}
                                                                </option>
                                                            ))}
                                                            <option value="Pedido Especial / A Medida">Pedido Especial / A Medida</option>
                                                            <option value="Catálogo Completo">Catálogo Completo</option>
                                                        </select>
                                                    ) : (
                                                        <input
                                                            id="form-producto"
                                                            type="text"
                                                            name="producto"
                                                            required
                                                            placeholder="¿Qué producto buscas?"
                                                            className="w-full rounded-xl border border-gray-200 bg-gray-50/70 px-4 py-2.5 text-xs text-gray-900 placeholder-gray-400 outline-none transition focus:border-gray-900 focus:bg-white focus:ring-1 focus:ring-gray-900"
                                                        />
                                                    )}
                                                </div>
                                            </div>

                                            {/* FILA 4: Mensaje */}
                                            <div>
                                                <label htmlFor="form-mensaje" className="block text-xs font-bold text-gray-700 mb-1">
                                                    Mensaje <span className="text-red-500">*</span>
                                                </label>
                                                <textarea
                                                    id="form-mensaje"
                                                    name="mensaje"
                                                    required
                                                    rows={4}
                                                    placeholder="Cuéntanos qué necesitas..."
                                                    className="w-full rounded-xl border border-gray-200 bg-gray-50/70 px-4 py-3 text-xs text-gray-900 placeholder-gray-400 outline-none transition focus:border-gray-900 focus:bg-white focus:ring-1 focus:ring-gray-900 resize-none"
                                                />
                                            </div>

                                            {/* FILA 5: Checkbox Políticas de Privacidad */}
                                            <div className="flex items-center gap-2 pt-1">
                                                <input
                                                    id="form-politicas"
                                                    type="checkbox"
                                                    required
                                                    checked={aceptoPoliticas}
                                                    onChange={(e) => setAceptoPoliticas(e.target.checked)}
                                                    className="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900 cursor-pointer"
                                                />
                                                <label htmlFor="form-politicas" className="text-xs text-gray-600 select-none cursor-pointer">
                                                    He leído y acepto las{' '}
                                                    <button
                                                        type="button"
                                                        onClick={() => setLegalModalOpen(true)}
                                                        className="text-gray-900 font-bold underline hover:text-black cursor-pointer"
                                                    >
                                                        Políticas de Privacidad
                                                    </button>
                                                </label>
                                            </div>

                                            {/* Botón Submit */}
                                            <div className="pt-2">
                                                <button
                                                    type="submit"
                                                    disabled={enviando}
                                                    className="w-full rounded-xl py-3.5 px-6 text-xs sm:text-sm font-bold text-white bg-[#10223b] hover:bg-[#0b1728] shadow-md transition-all duration-200 cursor-pointer flex items-center justify-center gap-2 hover:scale-[1.01] active:scale-[0.99] disabled:opacity-70"
                                                >
                                                    {enviando ? (
                                                        <>
                                                            <FaSpinner className="h-4 w-4 animate-spin text-white" />
                                                            <span>Enviando solicitud...</span>
                                                        </>
                                                    ) : (
                                                        <span>Solicitar información</span>
                                                    )}
                                                </button>
                                            </div>
                                        </form>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* COLUMNA DERECHA: TARJETA OSCURA "NUESTRA UBICACIÓN" */}
                        <div className="lg:col-span-5 flex flex-col">
                            <div className="bg-[#181926] text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-gray-800 flex-1 flex flex-col justify-between">
                                <div>
                                    <h3 className="text-2xl font-bold text-white tracking-tight">
                                        Nuestra <span className="font-extrabold text-white">Ubicación</span>
                                    </h3>

                                    {/* Mapa interactivo embebido */}
                                    <div className="mt-5 h-64 sm:h-72 w-full rounded-2xl overflow-hidden border border-white/10 shadow-lg relative bg-neutral-900">
                                        {googleMapEmbedUrl ? (
                                            <iframe
                                                src={googleMapEmbedUrl}
                                                title={`Ubicación de ${site?.nombre || 'la tienda'}`}
                                                className="h-full w-full border-0"
                                                loading="lazy"
                                                allowFullScreen
                                            />
                                        ) : (
                                            <div className="h-full w-full flex flex-col items-center justify-center p-6 text-center text-neutral-400 space-y-2">
                                                <FaLocationDot className="h-8 w-8 text-neutral-500" />
                                                <span className="text-xs font-medium">Ubicación disponible en tienda física</span>
                                                {direccion && (
                                                    <span className="text-xs text-white/90 font-semibold">{direccion}</span>
                                                )}
                                            </div>
                                        )}
                                    </div>

                                    {/* Referencia Comercial */}
                                    <div className="mt-4 p-4 rounded-2xl bg-white/5 border border-white/10 flex items-start gap-3">
                                        <div className="h-8 w-8 rounded-xl bg-white/10 flex items-center justify-center shrink-0 text-white mt-0.5">
                                            <FaBullhorn className="h-4 w-4 text-white" />
                                        </div>
                                        <div>
                                            <span className="block text-[11px] font-bold text-white uppercase tracking-wider">
                                                Referencia comercial
                                            </span>
                                            <p className="text-xs text-neutral-300 mt-0.5 leading-relaxed">
                                                {referenciaComercial || (direccion ? `Visítanos en nuestra tienda: ${direccion}` : 'Visítanos en nuestra tienda central para atención presencial.')}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {/* Enlace Oficial a Red Social */}
                                <div className="mt-5 pt-2">
                                    <a
                                        href={redPrincipal.url}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="w-full rounded-2xl bg-white hover:bg-neutral-100 text-gray-900 py-3 px-4 font-bold text-xs transition-colors flex items-center justify-center gap-2 shadow-sm"
                                    >
                                        <DynamicIcon name={redPrincipal.icono} className="h-4 w-4 text-blue-600 shrink-0" />
                                        <span className="truncate">{redPrincipal.handle || redPrincipal.nombre}</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            {/* ========================================================
                4. SECCIÓN INFERIOR: MÉTODOS DE PAGO SEGUROS & ASESOR WHATSAPP
                   Todas las 4 cards con el mismo tamaño y diseño alineado
            ======================================================== */}
            <section className="relative z-20 pb-16 sm:pb-20">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="bg-[#122238] text-white rounded-[2rem] p-5 sm:p-6 lg:p-7 shadow-2xl border border-white/10 flex flex-col xl:flex-row items-center gap-6">

                        {/* Bloque Izquierdo: Información y Título */}
                        <div className="shrink-0 w-full xl:w-52 space-y-1 text-center xl:text-left">
                            <div className="inline-flex items-center gap-1.5 text-[11px] font-bold text-neutral-300 uppercase tracking-wider">
                                <FaCreditCard className="h-3.5 w-3.5 text-neutral-300" />
                                <span>Forma de Pago</span>
                            </div>
                            <h4 className="text-lg sm:text-xl font-black text-white leading-tight">
                                Métodos de Pago Seguros
                            </h4>
                            <p className="text-[11px] text-neutral-300/80 leading-snug">
                                Elige la opción que mejor se adecue a ti.
                            </p>
                        </div>

                        {/* Grid de 4 Cards del mismo tamaño exacto */}
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 flex-1 w-full items-stretch">

                            {/* Tarjetas de Métodos de Pago dinámicas y editables desde el Admin */}
                            {tarjetasPagoList.map((tp, idx) => {
                                const bgIcon = idx === 0 ? 'bg-emerald-100 text-emerald-600' : idx === 1 ? 'bg-blue-100 text-blue-600' : 'bg-purple-100 text-purple-600';

                                return (
                                    <div
                                        key={tp.id ?? idx}
                                        className="h-full min-h-[115px] flex flex-col justify-between p-4 rounded-2xl bg-[#fafafa] text-gray-900 border border-white/20 shadow-sm hover:shadow-md transition-shadow"
                                    >
                                        <div className="flex items-center gap-2.5">
                                            <div className={`h-9 w-9 rounded-xl flex items-center justify-center shrink-0 ${bgIcon}`}>
                                                {tp.imagen ? (
                                                    <img
                                                        src={tp.imagen.startsWith('http') || tp.imagen.startsWith('/') ? tp.imagen : `/storage/${tp.imagen}`}
                                                        alt={tp.titulo}
                                                        className="h-5 w-5 object-contain"
                                                    />
                                                ) : (
                                                    <DynamicIcon name={tp.icono} className="h-4 w-4" />
                                                )}
                                            </div>
                                            <span className="text-sm font-extrabold text-gray-900 leading-tight">
                                                {tp.titulo}
                                            </span>
                                        </div>
                                        <p className="text-[11px] text-gray-500 font-medium leading-tight mt-2">
                                            {tp.descripcion}
                                        </p>
                                    </div>
                                );
                            })}

                            {/* Card 4: Asesor WhatsApp (Mismo tamaño exacto) */}
                            <div className="h-full min-h-[115px] flex flex-col justify-between p-3.5 sm:p-4 rounded-2xl bg-[#B88A38] text-white shadow-md">
                                <div className="flex items-center gap-2">
                                    <div className="h-8 w-8 rounded-full bg-white flex items-center justify-center shrink-0 shadow-xs">
                                        <FaWhatsapp className="h-5 w-5 text-emerald-600" />
                                    </div>
                                    <span className="text-[11px] font-bold text-white leading-tight">
                                        ¿Prefieres Hablar con Asesor?
                                    </span>
                                </div>
                                <div className="mt-2 pt-1">
                                    {waNum ? (
                                        <a
                                            href={`https://wa.me/${waNum}?text=${encodeURIComponent('Hola, deseo comunicarme directamente con un asesor comercial.')}`}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-black hover:bg-neutral-900 text-white py-2 px-3 text-[11px] font-bold transition-all shadow-sm active:scale-95"
                                        >
                                            <FaPhone className="h-3 w-3 text-white" />
                                            <span>Hablar por WhatsApp</span>
                                        </a>
                                    ) : (
                                        <span className="text-[11px] font-bold text-white/90">Atención personalizada</span>
                                    )}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>

            {/* Modal de Políticas de Privacidad */}
            <LegalModal
                isOpen={legalModalOpen}
                onClose={() => setLegalModalOpen(false)}
                defaultTab="privacidad"
                site={site}
            />

        </div>
    );
}
