import React, { useMemo, useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    FaInstagram,
    FaFacebookF,
    FaWhatsapp,
    FaTiktok
} from 'react-icons/fa6';

export default function FloatingSocialBar({ site, dominio, siteSlug, seccionesData, estilos }) {
    // 1. Detección de scroll: solo visible al pasar la sección Hero
    const [isVisible, setIsVisible] = useState(false);

    useEffect(() => {
        const handleScroll = () => {
            // 1. Umbral superior: solo visible después de pasar la sección Hero
            const heroEl = document.getElementById('hero')
                || document.querySelector('[data-section="hero"]')
                || document.querySelector('[data-section="inicio"]')
                || document.querySelector('section');

            const heroHeight = heroEl ? heroEl.offsetHeight : 550;
            const heroThreshold = Math.max(heroHeight * 0.55, 380);

            if (window.scrollY <= heroThreshold) {
                setIsVisible(false);
                return;
            }

            // 2. Umbral inferior: se guarda/oculta al llegar al Banner Pie o Footer
            const pieEl = document.getElementById('baner-pie')
                || document.getElementById('banner-pie')
                || document.getElementById('bannerpie')
                || document.querySelector('[data-section="baner-pie"]')
                || document.querySelector('[data-section="banner-pie"]')
                || document.querySelector('footer');

            if (pieEl) {
                const rect = pieEl.getBoundingClientRect();
                // Si la parte superior del banner pie o footer alcanza la altura de la barra (mitad de pantalla)
                if (rect.top <= window.innerHeight * 0.55) {
                    setIsVisible(false);
                    return;
                }
            }

            setIsVisible(true);
        };

        handleScroll();
        window.addEventListener('scroll', handleScroll, { passive: true });
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    // 2. Verificar si la barra lateral está habilitada por estilos o configuración admin
    const isActivo = estilos?.barra_lateral_redes?.activo !== false;

    // 3. Extraer y formatear las redes sociales en el orden solicitado
    const socialItems = useMemo(() => {
        if (!isActivo) return [];

        const redesConfig = estilos?.redes_sociales || {};

        // Helper para normalizar enlaces
        const formatUrl = (val, prefix = '') => {
            if (!val) return null;
            const str = String(val).trim();
            if (!str) return null;
            if (str.startsWith('http://') || str.startsWith('https://')) return str;
            if (str.startsWith('@')) return `${prefix}${str.slice(1)}`;
            return `${prefix}${str}`;
        };

        // Fallback desde Contacto si no está en estilos.redes_sociales
        const contactoSec = seccionesData?.contacto || seccionesData?.['contacto'] || null;
        const cmsRedes = contactoSec?.contenido?.find((c) => (c.label || '').toLowerCase() === 'redes')?.valor || [];
        const cmsRedesArray = Array.isArray(cmsRedes) ? cmsRedes : (typeof cmsRedes === 'object' && cmsRedes !== null ? Object.values(cmsRedes) : []);

        const findCmsRed = (keyword) => {
            const found = cmsRedesArray.find((r) => {
                const icono = String(r?.Icono || r?.icono || '').toLowerCase();
                const texto = String(r?.Texto || r?.texto || '').toLowerCase();
                return icono.includes(keyword) || texto.includes(keyword);
            });
            return found?.Texto || found?.texto || found?.url || null;
        };

        // Fallback para WhatsApp desde acciones_nav o contacto
        const navSec = seccionesData?.nav || seccionesData?.['nav'] || null;
        const navActions = navSec?.contenido?.find((c) => (c.label || '').toLowerCase() === 'accion_nav')?.valor || [];
        const allNavActions = [...(Array.isArray(estilos?.acciones_nav) ? estilos.acciones_nav : []), ...(Array.isArray(navActions) ? navActions : [])];
        const waAction = allNavActions.find((a) => String(a?.icono || a?.icon || '').toLowerCase().includes('whatsapp'));
        const waActionNum = waAction?.texto || waAction?.Texto || null;

        // --- 1. INSTAGRAM ---
        const rawIg = redesConfig.instagram || findCmsRed('insta');
        const igUrl = formatUrl(rawIg, 'https://instagram.com/');

        // --- 2. FACEBOOK ---
        const rawFb = redesConfig.facebook || findCmsRed('face');
        const fbUrl = formatUrl(rawFb, 'https://facebook.com/');

        // --- 3. WHATSAPP ---
        const rawWa = redesConfig.whatsapp || waActionNum || findCmsRed('what');
        let waUrl = null;
        if (rawWa) {
            const strWa = String(rawWa).trim();
            if (strWa.startsWith('http://') || strWa.startsWith('https://')) {
                waUrl = strWa;
            } else {
                const cleanNum = strWa.replace(/\D/g, '');
                if (cleanNum) {
                    const finalNum = cleanNum.length === 9 ? `51${cleanNum}` : cleanNum;
                    const nombreSitio = site?.nombre || 'la tienda';
                    waUrl = `https://wa.me/${finalNum}?text=${encodeURIComponent(`Hola! Deseo comunicarme con ${nombreSitio}.`)}`;
                }
            }
        }

        // --- 4. TIKTOK (Sustituyendo a LinkedIn) ---
        const rawTt = redesConfig.tiktok || findCmsRed('tiktok');
        const ttUrl = formatUrl(rawTt, 'https://tiktok.com/@');

        // Lista ordenada: Instagram -> Facebook -> WhatsApp -> TikTok
        const items = [
            {
                id: 'instagram',
                nombre: 'Instagram',
                icono: FaInstagram,
                url: igUrl,
                bgHover: 'hover:bg-white/20',
                colorIcon: 'group-hover:scale-115'
            },
            {
                id: 'facebook',
                nombre: 'Facebook',
                icono: FaFacebookF,
                url: fbUrl,
                bgHover: 'hover:bg-white/20',
                colorIcon: 'group-hover:scale-115'
            },
            {
                id: 'whatsapp',
                nombre: 'WhatsApp',
                icono: FaWhatsapp,
                url: waUrl,
                bgHover: 'hover:bg-white/20',
                colorIcon: 'group-hover:scale-115'
            },
            {
                id: 'tiktok',
                nombre: 'TikTok',
                icono: FaTiktok,
                url: ttUrl,
                bgHover: 'hover:bg-white/20',
                colorIcon: 'group-hover:scale-115'
            }
        ];

        // Filtrar solo las redes que tengan enlace válido
        return items.filter((item) => Boolean(item.url));
    }, [estilos, seccionesData, site, isActivo]);

    // Si no está activa o no hay redes con enlace, no renderizar
    if (!isActivo || socialItems.length === 0) {
        return null;
    }

    // Color primario del proyecto para el fondo de la píldora flotante
    const colorPrimario = estilos?.color_primario || 'var(--color-primario, #1854a4)';

    return (
        <AnimatePresence>
            {isVisible && (
                <motion.aside
                    initial={{ opacity: 0, x: -35, scale: 0.85 }}
                    animate={{ opacity: 1, x: 0, scale: 1 }}
                    exit={{ opacity: 0, x: -35, scale: 0.85 }}
                    transition={{ duration: 0.28, ease: [0.16, 1, 0.3, 1] }}
                    aria-label="Barra Flotante de Redes Sociales"
                    className="hidden md:block fixed left-4 lg:left-6 top-1/2 -translate-y-1/2 z-40 select-none"
                >
                    {/* Cápsula flotante estilizada separada del borde (Solo Desktop / Tablet) */}
                    <div
                        style={{ backgroundColor: colorPrimario }}
                        className="flex flex-col items-center py-3.5 px-2 rounded-full shadow-2xl shadow-black/40 border border-white/25 backdrop-blur-md gap-3"
                    >
                        {socialItems.map((item) => {
                            const IconComponent = item.icono;
                            return (
                                <a
                                    key={item.id}
                                    href={item.url}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label={`Visitar nuestro perfil de ${item.nombre}`}
                                    className={`group relative p-2.5 rounded-full text-white transition-all duration-200 active:scale-95 flex items-center justify-center ${item.bgHover}`}
                                >
                                    <IconComponent className={`h-5 w-5 transition-transform duration-200 ${item.colorIcon}`} />

                                    {/* Tooltip flotante hacia la derecha (Desktop) */}
                                    <span className="pointer-events-none absolute left-full ml-3 flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-900/95 backdrop-blur-md text-white text-[10px] font-extrabold tracking-wider uppercase shadow-xl opacity-0 group-hover:opacity-100 group-hover:translate-x-0 -translate-x-2 transition-all duration-200 whitespace-nowrap z-50 border border-white/10">
                                        <span>{item.nombre}</span>
                                        {/* Flechita del tooltip */}
                                        <span className="absolute right-full top-1/2 -translate-y-1/2 border-4 border-transparent border-r-gray-900/95" />
                                    </span>
                                </a>
                            );
                        })}
                    </div>
                </motion.aside>
            )}
        </AnimatePresence>
    );
}
