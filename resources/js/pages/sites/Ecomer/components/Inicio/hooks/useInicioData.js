export function useInicioData(
    dominio,
    siteSlug,
    seccionActiva = null,
    seccionesData = null,
    initialProductosDestacados = null
) {
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
return seccionesData[slugKey];
}

            const dashKey = String(slugKey).toLowerCase().replace(/_/g, '-');

            if (seccionesData[dashKey]) {
return seccionesData[dashKey];
}

            const underKey = String(slugKey).toLowerCase().replace(/-/g, '_');

            if (seccionesData[underKey]) {
return seccionesData[underKey];
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
                return item;
            }
        }

        // 3. Coincidencia EXACTA por clave del objeto normalizada
        if (typeof seccionesData === 'object' && !Array.isArray(seccionesData)) {
            for (const key in seccionesData) {
                if (normalize(key) === target) {
                    return seccionesData[key];
                }
            }
        }

        return null;
    };

    // Identifica las secciones del sitio
    const isInicioSection = seccionActiva?.slug?.toLowerCase() === 'inicio' || seccionActiva?.slug?.toLowerCase() === 'hero';
    const inicio = isInicioSection ? seccionActiva : (findSeccion('inicio') || findSeccion('hero') || null);
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
    const contancto = findSeccion('contacto') || null;

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
        contacto: contancto,
        productosDestacados: initialProductosDestacados || [],
        loading: false,
        error: null,
    };
}
