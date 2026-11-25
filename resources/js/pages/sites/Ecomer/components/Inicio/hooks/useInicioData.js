export function useInicioData(
    dominio,
    siteSlug,
    seccionActiva = null,
    seccionesData = null,
    initialProductosDestacados = null
) {
    const isActiva = (sec) => {
        if (!sec) return false;
        if (sec.activa === false) return false;
        return true;
    };

    const findSeccion = (slugKey) => {
        if (!seccionesData) {
            return null;
        }

        const normalize = (str) =>
            String(str || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[-_ ]/g, '');

        const target = normalize(slugKey);

        if (!target) {
            return null;
        }

        // 1. Acceso directo por clave de objeto ('sobre-nosotros', 'sobre_nosotros', etc.)
        if (typeof seccionesData === 'object' && !Array.isArray(seccionesData)) {
            if (seccionesData[slugKey]) {
                return isActiva(seccionesData[slugKey]) ? seccionesData[slugKey] : null;
            }

            const dashKey = String(slugKey).toLowerCase().replace(/_/g, '-');

            if (seccionesData[dashKey]) {
                return isActiva(seccionesData[dashKey]) ? seccionesData[dashKey] : null;
            }

            const underKey = String(slugKey).toLowerCase().replace(/-/g, '_');

            if (seccionesData[underKey]) {
                return isActiva(seccionesData[underKey]) ? seccionesData[underKey] : null;
            }
        }

        const list = Array.isArray(seccionesData)
            ? seccionesData
            : Object.values(seccionesData);

        // 2. Coincidencia EXACTA por slug o nombre
        for (const item of list) {
            if (!item) {
                continue;
            }

            const itemSlug = normalize(item.slug || '');
            const itemNom = normalize(item.nombre || '');

            if (itemSlug === target || itemNom === target) {
                return isActiva(item) ? item : null;
            }
        }

        // 3. Coincidencia EXACTA por clave del objeto normalizada
        if (typeof seccionesData === 'object' && !Array.isArray(seccionesData)) {
            for (const key in seccionesData) {
                if (normalize(key) === target) {
                    return isActiva(seccionesData[key]) ? seccionesData[key] : null;
                }
            }
        }

        return null;
    };

    // Identifica las secciones del sitio
    const isInicioSection = seccionActiva?.slug?.toLowerCase() === 'inicio' || seccionActiva?.slug?.toLowerCase() === 'hero';
    const inicioRaw = isInicioSection ? seccionActiva : (findSeccion('inicio') || findSeccion('hero') || null);
    const inicio = isActiva(inicioRaw) ? inicioRaw : null;

    const galeria = findSeccion('galeria') || null;
    const ofertas = findSeccion('ofertas') || null;
    const marcas = findSeccion('marcas') || null;
    const tiktok = findSeccion('tik tok') || null;
    const banerpie = findSeccion('banerpie') || findSeccion('baner_pie') || findSeccion('banerPie') || null;
    const banerproductos = findSeccion('banerproductos') || findSeccion('baner_productos') || findSeccion('banerProductos') || findSeccion('banerproducto') || findSeccion('banerProducto') || null;
    const sectores = findSeccion('sectores') || null;
    const sobreNosotros = findSeccion('sobre_nosotros') || findSeccion('sobre-nosotros') || findSeccion('sobrenosotros') || null;
    const comoComprar = findSeccion('como_comprar') || findSeccion('como-comprar') || findSeccion('comocomprar') || findSeccion('cotizacion') || null;
    const sedes = findSeccion('sedes') || findSeccion('sucursales') || findSeccion('puntos_venta') || findSeccion('puntos-de-venta') || null;
    const faq = findSeccion('faq') || findSeccion('faqs') || findSeccion('preguntas_frecuentes') || findSeccion('preguntas-frecuentes') || null;
    const contancto = findSeccion('contacto') || null;
    const categoria = findSeccion('categoria') || null;
    const productosSec = findSeccion('productos') || findSeccion('tortasdestacadas') || null;

    return {
        inicio,
        hero: inicio,
        galeria,
        ofertas,
        marcas,
        tiktok,
        banerpie,
        banerproductos,
        sectores,
        sobreNosotros,
        comoComprar,
        sedes,
        faq,
        contacto: contancto,
        categoria,
        productosSec,
        productosDestacados: initialProductosDestacados || [],
        loading: false,
        error: null,
    };
}
