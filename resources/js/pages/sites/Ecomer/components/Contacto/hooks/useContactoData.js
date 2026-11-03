import { useMemo } from 'react';

export function useContactoData(seccion, seccionesData, site, estilos) {
    const findSeccion = (name) => {
        if (!seccionesData) return null;
        const target = String(name || '').toLowerCase().trim();

        if (Array.isArray(seccionesData)) {
            return (
                seccionesData.find(
                    (s) =>
                        String(s.slug || '').toLowerCase() === target ||
                        String(s.nombre || '').toLowerCase() === target
                ) || null
            );
        }
        if (typeof seccionesData === 'object') {
            for (const key in seccionesData) {
                const sec = seccionesData[key];
                if (
                    String(sec?.slug || '').toLowerCase() === target ||
                    String(sec?.nombre || '').toLowerCase() === target ||
                    String(key).toLowerCase() === target
                ) {
                    return sec;
                }
            }
        }
        return null;
    };

    const contacto = seccion || findSeccion('contacto') || findSeccion('contactos') || null;

    const dataMemo = useMemo(() => {
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

        // --- TARJETAS DE PAGO INFERIORES ---
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

        // --- SEDES / SUCURSALES (SWITCH DE UBICACIÓN & MAPA) ---
        const rawSedes = data?.Sedes || data?.sedes;
        let cmsSedes = [];
        if (Array.isArray(rawSedes)) {
            cmsSedes = rawSedes;
        } else if (rawSedes && typeof rawSedes === 'object') {
            cmsSedes = Object.values(rawSedes);
        }

        let sedesList = [];
        if (cmsSedes.length > 0) {
            sedesList = cmsSedes.map((s, idx) => {
                const tit = s.Titulo || s.titulo || `Sede ${idx + 1}`;
                const dir = s.Direccion || s.direccion || '';
                const ref = s.Referencia || s.referencia || '';
                const mapQuery = s.Mapa || s.mapa || dir;
                const embed = mapQuery
                    ? (mapQuery.includes('iframe') || mapQuery.startsWith('http')
                        ? (mapQuery.startsWith('http') && mapQuery.includes('output=embed') ? mapQuery : `https://maps.google.com/maps?q=${encodeURIComponent(mapQuery)}&t=&z=16&ie=UTF8&iwloc=&output=embed`)
                        : `https://maps.google.com/maps?q=${encodeURIComponent(mapQuery)}&t=&z=16&ie=UTF8&iwloc=&output=embed`)
                    : null;
                const rawLink = s.Boton_Enlace || s.boton_enlace;
                const link = rawLink
                    ? (rawLink.startsWith('http') ? rawLink : `https://maps.google.com/?q=${encodeURIComponent(rawLink)}`)
                    : (dir ? `https://maps.google.com/?q=${encodeURIComponent(dir)}` : 'https://maps.google.com/');

                return {
                    id: idx,
                    titulo: tit,
                    direccion: dir,
                    referencia: ref,
                    embedUrl: embed,
                    botonEnlace: link,
                    telefono: s.Telefono || s.telefono || null
                };
            }).filter((s) => s.titulo || s.direccion);
        }

        if (sedesList.length === 0) {
            sedesList = [
                {
                    id: 0,
                    titulo: 'Tienda Central',
                    direccion: direccion || 'Jr. Huarochirí Nº592, CC. Plaza Ferretero',
                    referencia: referenciaComercial || 'Visítanos en nuestra tienda central en Jr. Huarochirí Nº592, CC. Plaza Ferretero.',
                    embedUrl: googleMapEmbedUrl,
                    botonEnlace: mapaUrlConfig || (direccion ? `https://maps.google.com/?q=${encodeURIComponent(direccion)}` : 'https://maps.google.com/'),
                    telefono: waNum || null
                }
            ];
        }

        // --- TELÉFONOS / LÍNEAS POR DEPARTAMENTO ---
        const phoneActions = globalActions.filter((a) => {
            const ico = String(a?.icono || a?.icon || '').toLowerCase();
            const txt = String(a?.texto || a?.Texto || '');
            return (ico.includes('phone') || ico.includes('whatsapp') || /\d{6,}/.test(txt)) && txt;
        });

        let lineasLlamanos = [];
        const rawCardLlamanos = cmsCards.find((c) => {
            const t = String(c?.Titulo || c?.titulo || '').toLowerCase();
            const ico = String(c?.Icono || c?.icono || '').toLowerCase();
            return t.includes('llámanos') || t.includes('llamanos') || t.includes('teléfono') || ico.includes('phone');
        });

        if (rawCardLlamanos?.Lineas) {
            const rawL = rawCardLlamanos.Lineas;
            const arrL = Array.isArray(rawL) ? rawL : Object.values(rawL);
            lineasLlamanos = arrL.map((l, i) => ({
                etiqueta: l.Etiqueta || l.etiqueta || `Línea ${i + 1}`,
                numero: l.Numero || l.numero || '',
                horario: l.Horario || l.horario || null,
                enlace: l.Enlace || l.enlace || (l.Numero ? `tel:${String(l.Numero).replace(/[^\d+]/g, '')}` : '#')
            })).filter((l) => l.numero);
        }

        if (lineasLlamanos.length === 0 && phoneActions.length >= 2) {
            lineasLlamanos = phoneActions.map((a, i) => ({
                etiqueta: a.Label || a.label || (i === 0 ? 'Ventas' : 'Atención al Cliente'),
                numero: a.texto || a.Texto,
                horario: horarioTexto || null,
                enlace: `tel:${String(a.texto || a.Texto).replace(/[^\d+]/g, '')}`
            }));
        }

        const redPrincipal = redesFinales[0] || {
            nombre: 'Facebook',
            icono: 'FaFacebook',
            url: 'https://facebook.com',
            handle: 'facebook.com/empresa'
        };

        return {
            titulo,
            descripcion,
            referenciaComercial,
            direccion,
            mapaUrlConfig,
            redesFinales,
            redPrincipal,
            imagenHero,
            responsiveImg,
            globalActions,
            waNum,
            cuentasPago,
            googleMapEmbedUrl,
            cardsList,
            tarjetasPagoList,
            sedesList,
            lineasLlamanos
        };
    }, [contacto, seccion, site, estilos]);

    return {
        contacto,
        loading: false,
        ...dataMemo
    };
}
