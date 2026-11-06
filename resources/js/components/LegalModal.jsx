import React, { useState, useEffect } from 'react';
import {
    FaXmark,
    FaScaleBalanced,
    FaUserShield,
    FaTruckFast,
    FaRotateLeft,
    FaShieldHalved,
    FaAngleRight
} from 'react-icons/fa6';

export function parsePoliticas(estilos, site) {
    let politicas = estilos?.politicas || site?.estilos?.politicas;

    if (!politicas && typeof site?.estilos === 'string') {
        try {
            const parsed = JSON.parse(site.estilos);
            politicas = parsed?.politicas;
        } catch (e) { }
    }

    if (!politicas && typeof estilos === 'string') {
        try {
            const parsed = JSON.parse(estilos);
            politicas = parsed?.politicas;
        } catch (e) { }
    }

    if (typeof politicas === 'string') {
        try {
            politicas = JSON.parse(politicas);
        } catch (e) {
            politicas = {};
        }
    }

    return politicas && typeof politicas === 'object' ? politicas : {};
}

export function getPolicyData(type, customPoliticas = {}) {
    const policyMap = {
        terminos: {
            badge: 'MARCO LEGAL & CONDICIONES DE USO',
            title: 'Términos y Condiciones',
            subtitle: 'Conoce los términos, derechos y obligaciones que rigen la utilización de nuestra plataforma digital.',
            defaultIntro: 'Bienvenido a nuestra plataforma. En el presente documento se establecen las condiciones que regulan el uso de nuestro sitio web y los servicios disponibles.',
            icon: FaScaleBalanced,
            sectionDefs: [
                { key: 'sec_1', num: '1', title: 'Aceptación de los Términos', default: 'Al registrarte como usuario o utilizar la plataforma, aceptas los presentes Términos y Condiciones que regulan el acceso y uso de los servicios disponibles.' },
                { key: 'sec_2', num: '2', title: 'Descripción del Servicio', default: 'Nuestra plataforma proporciona servicios de comercio electrónico para la consulta, cotización y compra de productos y suministros con garantía.' },
                { key: 'sec_3', num: '3', title: 'Registro y Responsabilidad del Usuario', default: 'El usuario es responsable de mantener la confidencialidad de sus credenciales de acceso y de la veracidad de la información proporcionada.' },
                { key: 'sec_4', num: '4', title: 'Contenido de los Anuncios / Productos', default: 'Todos los productos publicados cumplen con los estándares de calidad técnica y especificaciones informadas en sus respectivas fichas.' },
                { key: 'sec_5', num: '5', title: 'Planes y Pagos', default: 'Los precios, métodos de pago y condiciones comerciales de facturación se rigen bajo los acuerdos informados en cada cotización u orden.' },
                { key: 'sec_6', num: '6', title: 'Propiedad Intelectual', default: 'Todos los contenidos, marcas y logotipos de este sitio son propiedad exclusiva de sus respectivos titulares y marcas representadas.' },
                { key: 'sec_7', num: '7', title: 'Limitación de Responsabilidad', default: 'No nos hacemos responsables por fallas ajenas de conectividad o uso inapropiado de los productos fuera de sus especificaciones técnicas de fábrica.' },
                { key: 'sec_8', num: '8', title: 'Cancelación y Suspensión', default: 'Nos reservamos el derecho de suspender operaciones o cuentas que infrinjan los presentes términos o intenten actividades fraudulentas.' },
                { key: 'sec_9', num: '9', title: 'Modificaciones de los Términos', default: 'Podremos actualizar estos términos periódicamente comunicando las modificaciones en este mismo sitio web.' },
                { key: 'sec_10', num: '10', title: 'Contacto', default: 'Si tienes consultas sobre estos Términos y Condiciones, contáctanos a través de nuestros canales oficiales de atención al cliente.' },
            ]
        },
        privacidad: {
            badge: 'SEGURIDAD & PRIVACIDAD DE DATOS',
            title: 'Política de Privacidad',
            subtitle: 'Conoce nuestras prácticas de protección de datos, recopilación responsable y los derechos sobre tu información personal.',
            defaultIntro: 'Valoramos y respetamos la privacidad de nuestros usuarios. Esta política explica cómo recopilamos, utilizamos y protegemos tu información personal.',
            icon: FaUserShield,
            sectionDefs: [
                { key: 'sec_1', num: '1', title: 'Información que Recopilamos', default: 'Recopilamos información proporcionada voluntariamente como nombre, teléfono, dirección y correo electrónico al realizar consultas o pedidos.' },
                { key: 'sec_2', num: '2', title: 'Uso de la Información', default: 'Utilizamos los datos para procesar requerimientos, cotizaciones, despachos de pedidos y brindar atención al cliente personalizada.' },
                { key: 'sec_3', num: '3', title: 'Protección de Datos', default: 'Implementamos rigurosas medidas de seguridad administrativas y tecnológicas para salvaguardar tu información frente a accesos no autorizados.' },
                { key: 'sec_4', num: '4', title: 'Compartición de Información', default: 'No comercializamos ni cedemos tus datos personales a terceros sin tu previo consentimiento expreso, salvo obligación legal.' },
                { key: 'sec_5', num: '5', title: 'Cookies', default: 'Utilizamos cookies para optimizar tu experiencia de navegación, recordar preferencias y analizar estadísticas anónimas de tráfico.' },
                { key: 'sec_6', num: '6', title: 'Derechos del Usuario', default: 'Tienes derecho a acceder, corregir, actualizar o solicitar la supresión de tus datos personales conforme a la legislación de protección de datos.' },
                { key: 'sec_7', num: '7', title: 'Conservación de Datos', default: 'Conservamos la información personal durante el tiempo estrictamente necesario para cumplir las finalidades descritas en esta política.' },
                { key: 'sec_8', num: '8', title: 'Cambios a esta Política', default: 'Nos reservamos el derecho de modificar esta Política de Privacidad en cualquier momento comunicándolo oportunamente en nuestra plataforma.' },
                { key: 'sec_9', num: '9', title: 'Contacto', default: 'Para ejercer tus derechos de privacidad o realizar consultas, escríbenos a nuestros canales oficiales de atención.' },
            ]
        },
        envios: {
            badge: 'DESPACHO & COBERTURA NACIONAL',
            title: 'Política de Envíos',
            subtitle: 'Información detallada sobre plazos de entrega, zonas de cobertura y métodos de despacho.',
            defaultIntro: 'Nos comprometemos a entregar tus productos de manera rápida, segura y oportuna en todo el territorio nacional.',
            icon: FaTruckFast,
            sectionDefs: [
                { key: 'sec_1', num: '1', title: 'Cobertura y Zonas de Envío', default: 'Realizamos envíos a nivel nacional coordinando con las principales agencias de transporte o mediante despacho directo en zonas autorizadas.' },
                { key: 'sec_2', num: '2', title: 'Tiempos y Plazos de Entrega', default: 'Los plazos estándar oscilan entre 24 a 72 horas hábiles según el destino final y la confirmación del pedido y stock.' },
                { key: 'sec_3', num: '3', title: 'Costos y Métodos de Envío', default: 'Los costos de envío se calculan en base al destino, peso y volumen de la carga, informándose oportunamente antes del despacho.' },
                { key: 'sec_4', num: '4', title: 'Recepción de Pedidos', default: 'Es necesario presentar documento de identidad o número de orden al recibir la mercancía y verificar el estado del embalaje.' },
            ]
        },
        devoluciones: {
            badge: 'GARANTÍA & CAMBIOS DE PRODUCTO',
            title: 'Políticas de Devolución',
            subtitle: 'Conoce las condiciones y el procedimiento para solicitar un cambio o devolución de producto.',
            defaultIntro: 'Tu satisfacción es nuestra prioridad. Contamos con políticas claras para cambios y devoluciones de productos garantizados.',
            icon: FaRotateLeft,
            sectionDefs: [
                { key: 'sec_1', num: '1', title: 'Condiciones para Cambios y Devoluciones', default: 'El producto debe encontrarse sin uso, en su empaque original con manuales completos y acompañado de su comprobante de pago emitido.' },
                { key: 'sec_2', num: '2', title: 'Plazos para Devoluciones', default: 'Dispones de un plazo de hasta 7 días calendario tras recibir tu pedido para comunicar y solicitar una revisión de cambio o devolución.' },
                { key: 'sec_3', num: '3', title: 'Proceso y Reembolsos', default: 'Los reembolsos o notas de crédito se procesan tras la inspección técnica del producto dentro de los plazos bancarios comerciales.' },
                { key: 'sec_4', num: '4', title: 'Excepciones', default: 'Componentes fabricados a medida especial, intervenidos por terceros o en remate final no aplican para devolución salvo defecto de fábrica comprobado.' },
            ]
        }
    };

    const config = policyMap[type] || policyMap.terminos;
    const userPolicy = customPoliticas?.[type] || {};

    const intro = typeof userPolicy.intro === 'string' && userPolicy.intro.trim() !== ''
        ? userPolicy.intro.trim()
        : config.defaultIntro;

    const sections = config.sectionDefs
        .map((def) => {
            const val = userPolicy[def.key];
                const content = typeof val === 'string' && val.trim() !== '' ? val.trim() : def.default;
                if (content) {
                    return {
                        id: `${type}-${def.key}`,
                        num: def.num,
                        title: def.title,
                    content
                    };
                }
                return null;
            })
        .filter(Boolean);

    const hasContent = Boolean(intro || sections.length > 0);

    return {
        ...config,
        type,
        intro,
        sections,
        hasContent
    };
}

export default function LegalModal({ isOpen, onClose, defaultTab = 'terminos', site = {}, estilos = {} }) {
    const [activeTab, setActiveTab] = useState(defaultTab);
    const [activeSection, setActiveSection] = useState(null);

    useEffect(() => {
        if (defaultTab) {
            setActiveTab(defaultTab);
        }
    }, [defaultTab, isOpen]);

    if (!isOpen) return null;

    const siteName = site?.nombre || 'Nuestra Tienda';
    const customPoliticas = parsePoliticas(estilos, site);

    // Obtener datos estrictos sin texto inventado
    const currentConfig = getPolicyData(activeTab, customPoliticas);
    const HeaderIcon = currentConfig.icon;

    // Pestañas que realmente tienen contenido ingresado por la tienda
    const availableTabs = [
        { id: 'terminos', label: 'Términos y Condiciones', icon: FaScaleBalanced, has: getPolicyData('terminos', customPoliticas).hasContent },
        { id: 'privacidad', label: 'Política de Privacidad', icon: FaUserShield, has: getPolicyData('privacidad', customPoliticas).hasContent },
        { id: 'envios', label: 'Envíos', icon: FaTruckFast, has: getPolicyData('envios', customPoliticas).hasContent },
        { id: 'devoluciones', label: 'Devoluciones', icon: FaRotateLeft, has: getPolicyData('devoluciones', customPoliticas).hasContent },
    ].filter(t => t.has);

    const scrollToSection = (id) => {
        setActiveSection(id);
        const element = document.getElementById(id);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-neutral-900/40 backdrop-blur-sm p-3 sm:p-6 transition-all duration-300">
            <div
                className="relative w-full max-w-5xl max-h-[92vh] flex flex-col bg-slate-50 rounded-2xl shadow-2xl overflow-hidden border border-neutral-200"
                onClick={(e) => e.stopPropagation()}
            >

                {/* --- CABECERA --- */}
                <div className="relative bg-white text-neutral-900 p-6 sm:p-8 shrink-0 border-b border-neutral-200">
                    <button
                        onClick={onClose}
                        className="absolute top-5 right-5 h-9 w-9 rounded-full bg-neutral-100 hover:bg-neutral-200 text-neutral-600 hover:text-neutral-900 flex items-center justify-center transition focus:outline-none"
                        title="Cerrar"
                    >
                        <FaXmark className="h-5 w-5" />
                    </button>

                    <div className="flex flex-col gap-3 max-w-3xl">
                        <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-[11px] font-bold tracking-wider text-amber-800 uppercase w-fit">
                            <HeaderIcon className="h-3.5 w-3.5 text-amber-600" />
                            <span>{currentConfig.badge}</span>
                        </div>

                        <h2 className="text-2xl sm:text-4xl font-extrabold text-neutral-900 tracking-tight">
                            {currentConfig.title}
                        </h2>

                        <p className="text-xs sm:text-sm text-neutral-600 leading-relaxed max-w-2xl">
                            {currentConfig.subtitle}
                        </p>
                    </div>

                    {/* Tabs disponibles con contenido */}
                    {availableTabs.length > 1 && (
                        <div className="flex flex-wrap items-center gap-2 mt-6 pt-4 border-t border-neutral-100">
                            {availableTabs.map((tab) => {
                                const TabIcon = tab.icon;
                                return (
                                    <button
                                        key={tab.id}
                                        onClick={() => setActiveTab(tab.id)}
                                        className={`px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 ${activeTab === tab.id
                                            ? 'bg-amber-500 text-black shadow-xs font-bold'
                                            : 'bg-neutral-100 text-neutral-700 hover:bg-neutral-200 border border-neutral-200'
                                            }`}
                                    >
                                        <TabIcon className="h-3 w-3" />
                                        <span>{tab.label}</span>
                                    </button>
                                );
                            })}
                        </div>
                    )}
                </div>

                {/* --- CUERPO MODO CLARO --- */}
                <div className="flex-1 overflow-y-auto p-4 sm:p-8 bg-neutral-50/60">
                    {!currentConfig.hasContent ? (
                        <div className="bg-white p-8 rounded-2xl border border-neutral-200 text-center space-y-3">
                            <div className="h-12 w-12 rounded-full bg-neutral-100 text-neutral-400 flex items-center justify-center mx-auto text-xl">
                                ⚖️
                            </div>
                            <h3 className="font-bold text-neutral-800 text-base">Contenido no disponible</h3>
                            <p className="text-xs text-neutral-500 max-w-md mx-auto">
                                La tienda no ha redactado la información correspondiente a esta sección en su panel de administración.
                            </p>
                        </div>
                    ) : (
                        <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

                            {/* ÍNDICE IZQUIERDO */}
                            {currentConfig.sections.length > 0 && (
                                <div className="lg:col-span-4 lg:sticky lg:top-0 space-y-4">
                                    <div className="bg-white rounded-2xl p-5 shadow-xs border border-neutral-200 space-y-4">
                                        <div className="flex items-center gap-2 pb-3 border-b border-neutral-100 text-xs font-bold text-neutral-500 uppercase tracking-wider">
                                            <FaShieldHalved className="h-3.5 w-3.5 text-amber-500" />
                                            <span>ÍNDICE DE CONTENIDO</span>
                                        </div>

                                        <nav className="space-y-1 max-h-[50vh] overflow-y-auto pr-1">
                                            {currentConfig.sections.map((sec) => (
                                                <button
                                                    key={sec.id}
                                                    onClick={() => scrollToSection(sec.id)}
                                                    className={`w-full flex items-center gap-3 px-3 py-2 rounded-xl text-left text-xs transition-all ${activeSection === sec.id
                                                        ? 'bg-amber-50 text-amber-900 font-bold border border-amber-200'
                                                        : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-50 font-medium'
                                                        }`}
                                                >
                                                    <span className="h-5 w-5 rounded-md bg-neutral-100 text-neutral-700 font-bold text-[10px] flex items-center justify-center shrink-0">
                                                        {sec.num}
                                                    </span>
                                                    <span className="truncate flex-1">{sec.title}</span>
                                                    <FaAngleRight className="h-2.5 w-2.5 opacity-40 shrink-0" />
                                                </button>
                                            ))}
                                        </nav>
                                    </div>
                                </div>
                            )}

                            {/* COLUMNA DERECHA DE CARDS */}
                            <div className={currentConfig.sections.length > 0 ? "lg:col-span-8 space-y-6" : "lg:col-span-12 space-y-6"}>

                                {/* TARJETA INTRODUCTORIA (SOLO SI TIENE CONTENIDO REGISTRADO) */}
                                {currentConfig.intro && (
                                    <div className="bg-white p-6 sm:p-7 rounded-2xl shadow-xs border-l-4 border-l-amber-500 border-t border-r border-b border-neutral-200 text-sm text-neutral-700 leading-relaxed whitespace-pre-line font-sans">
                                        {currentConfig.intro}
                                    </div>
                                )}

                                {/* CARDS NUMERADAS DE SECCIONES REGISTRADAS */}
                                {currentConfig.sections.map((sec) => {
                                    const isContactoSec = sec.title.toLowerCase().includes('contacto');

                                    const handleGoToContacto = (e) => {
                                        e.preventDefault();
                                        onClose();
                                        setTimeout(() => {
                                            const contactoEl = document.getElementById('contacto');
                                            if (contactoEl) {
                                                contactoEl.scrollIntoView({ behavior: 'smooth' });
                                            }
                                        }, 200);
                                    };

                                    return (
                                        <div
                                            key={sec.id}
                                            id={sec.id}
                                            className="bg-white p-6 sm:p-7 rounded-2xl shadow-xs border border-neutral-200 space-y-3.5 scroll-mt-6 transition-all hover:border-neutral-300"
                                        >
                                            <div className="flex items-center gap-3 border-b border-neutral-100 pb-3">
                                                <span className="h-7 w-7 rounded-full bg-amber-100 text-amber-800 font-extrabold text-xs flex items-center justify-center shrink-0 shadow-xs">
                                                    {sec.num}
                                                </span>
                                                <h3 className="text-base sm:text-lg font-bold text-neutral-900 tracking-tight">
                                                    {sec.title}
                                                </h3>
                                            </div>

                                            <div className="text-xs sm:text-sm text-neutral-600 leading-relaxed whitespace-pre-line font-sans pt-1">
                                                {sec.content}
                                            </div>

                                            {isContactoSec && (
                                                <div className="pt-3 border-t border-neutral-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-neutral-50 p-4 rounded-xl">
                                                    <div>
                                                        <span className="block font-bold text-xs text-neutral-800">¿Deseas comunicarte directamente?</span>
                                                        <span className="text-xs text-neutral-500">Utiliza nuestro formulario o canales oficiales en la sección de contacto.</span>
                                                    </div>
                                                    <button
                                                        onClick={handleGoToContacto}
                                                        className="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-black font-bold text-xs transition shadow-xs flex items-center gap-1.5 shrink-0"
                                                    >
                                                        <span>Ir a Sección de Contacto</span>
                                                        <FaAngleRight className="h-3 w-3" />
                                                    </button>
                                                </div>
                                            )}
                                        </div>
                                    );
                                })}

                            </div>

                        </div>
                    )}
                </div>

                {/* --- PIE DEL MODAL --- */}
                <div className="bg-white border-t border-neutral-200 px-6 py-4 flex items-center justify-between shrink-0 text-xs text-neutral-500">
                    <div>
                        © {new Date().getFullYear()} <span className="font-semibold text-neutral-700">{siteName}</span>. Todos los derechos reservados.
                    </div>
                    <button
                        onClick={onClose}
                        className="px-4 py-2 rounded-xl bg-neutral-900 text-white font-semibold hover:bg-neutral-800 transition shadow-xs"
                    >
                        Entendido / Cerrar
                    </button>
                </div>

            </div>
        </div>
    );
}
