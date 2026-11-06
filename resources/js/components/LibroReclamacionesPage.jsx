import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import {
    FaBookOpen,
    FaArrowLeft,
    FaHouse,
    FaAngleRight,
    FaUserCheck,
    FaBoxOpen,
    FaFileLines,
    FaCheck,
    FaPrint,
    FaCircleInfo,
    FaShieldHalved,
    FaTriangleExclamation,
    FaSpinner
} from 'react-icons/fa6';

export default function LibroReclamacionesPage({ site, dominio, siteSlug, estilos = {} }) {
    const configReclamaciones = estilos?.libro_reclamaciones || site?.estilos?.libro_reclamaciones || {};
    const siteName = site?.nombre || 'Nuestra Tienda';
    const ruc = configReclamaciones?.ruc || estilos?.ruc || site?.ruc || '20600000000';
    const razonSocial = configReclamaciones?.razon_social || estilos?.razon_social || site?.razon_social || siteName;
    const direccionLegal = configReclamaciones?.direccion_legal || site?.direccion || estilos?.direccion_legal || 'Av. Principal N° 123, Lima - Perú';
    const mensajeInformativo = configReclamaciones?.mensaje_informativo || '';

    const homeUrl = (dominio && String(dominio).trim() !== '')
        ? (siteSlug ? `/${dominio}/${siteSlug}/Inicio` : `/${dominio}/Inicio`)
        : '/Inicio';

    const [form, setForm] = useState({
        site_id: site?.id || 1,
        tipo: 'reclamo', // 'reclamo' o 'queja'
        tipo_documento: 'DNI',
        numero_documento: '',
        nombres: '',
        apellidos: '',
        razon_social: '',
        email: '',
        telefono: '',
        departamento: 'Lima',
        provincia: 'Lima',
        distrito: '',
        direccion: '',
        es_menor_edad: false,
        apoderado_nombres: '',
        apoderado_tipo_documento: 'DNI',
        apoderado_numero_documento: '',
        apoderado_email: '',
        apoderado_telefono: '',
        tipo_bien: 'producto', // 'producto' o 'servicio'
        monto_reclamado: '',
        moneda: 'PEN',
        numero_pedido: '',
        descripcion_bien: '',
        detalle: '',
        pedido: '',
        acepta_terminos: true,
    });

    const [loading, setLoading] = useState(false);
    const [errors, setErrors] = useState({});
    const [successData, setSuccessData] = useState(null);

    const handleChange = (e) => {
        const { name, value, type, checked } = e.target;
        setForm(prev => ({
            ...prev,
            [name]: type === 'checkbox' ? checked : value
        }));
        if (errors[name]) {
            setErrors(prev => {
                const next = { ...prev };
                delete next[name];
                return next;
            });
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setErrors({});

        try {
            // Obtener el token CSRF si existe en el DOM
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : '';

            const response = await fetch('/libro-de-reclamaciones', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    ...form,
                    site_id: site?.id || form.site_id,
                }),
            });

            const data = await response.json();

            if (response.ok && data.success) {
                setSuccessData(data.data);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                if (data.errors) {
                    setErrors(data.errors);
                } else {
                    alert(data.message || 'Ocurrió un error al registrar la reclamación.');
                }
            }
        } catch (error) {
            console.error('Error al enviar reclamación:', error);
            alert('Error de conexión al enviar el formulario. Por favor intente nuevamente.');
        } finally {
            setLoading(false);
        }
    };

    const handlePrint = () => {
        if (typeof window !== 'undefined') {
            window.print();
        }
    };

    return (
        <div className="min-h-screen bg-neutral-50 text-neutral-900 font-sans antialiased selection:bg-[var(--color-primario)] selection:text-white">
            <Head title={`Libro de Reclamaciones | ${siteName}`} />

            {/* ESTILOS DE IMPRESIÓN LIMPIA (@media print) */}
            <style>{`
                @media print {
                    header, nav, footer, .no-print, [role="navigation"], button {
                        display: none !important;
                    }
                    body, main {
                        background: #ffffff !important;
                        color: #0f172a !important;
                        padding: 0 !important;
                        margin: 0 !important;
                    }
                    .print-sheet {
                        box-shadow: none !important;
                        border: 1px solid #cbd5e1 !important;
                        margin: 0 !important;
                        padding: 20px !important;
                    }
                }
            `}</style>

            {/* ENCABEZADO SUPERIOR */}
            <header className="bg-white border-b border-neutral-200/80 shadow-2xs py-6 sm:py-8 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
                <div
                    className="absolute -top-32 right-0 w-96 h-96 rounded-full blur-3xl opacity-10 pointer-events-none"
                    style={{ backgroundColor: 'var(--color-primario)' }}
                />

                <div className="max-w-5xl mx-auto space-y-6 relative z-10">

                    {/* Breadcrumbs y Volver */}
                    <div className="flex flex-wrap items-center justify-between gap-3 text-xs text-neutral-500 no-print">
                        <nav className="flex items-center gap-2">
                            <Link href={homeUrl} className="inline-flex items-center gap-1.5 hover:text-neutral-900 transition font-medium">
                                <FaHouse className="h-3.5 w-3.5 text-neutral-400" />
                                <span>Inicio</span>
                            </Link>
                            <FaAngleRight className="h-2.5 w-2.5 opacity-40" />
                            <span className="font-semibold text-neutral-700">Marco Legal</span>
                            <FaAngleRight className="h-2.5 w-2.5 opacity-40" />
                            <span
                                className="font-bold truncate"
                                style={{ color: 'var(--color-primario)' }}
                            >
                                Libro de Reclamaciones
                            </span>
                        </nav>

                        <Link
                            href={homeUrl}
                            className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-neutral-900 hover:bg-neutral-800 text-white text-xs font-bold transition shadow-xs"
                        >
                            <FaArrowLeft className="h-3 w-3" />
                            <span>Volver a la Tienda</span>
                        </Link>
                    </div>

                    {/* Título e Insignia */}
                    <div className="space-y-3">
                        <div
                            className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[11px] font-bold tracking-wider uppercase border shadow-2xs"
                            style={{
                                backgroundColor: 'color-mix(in srgb, var(--color-primario) 10%, transparent)',
                                borderColor: 'color-mix(in srgb, var(--color-primario) 30%, transparent)',
                                color: 'var(--color-primario)'
                            }}
                        >
                            <FaBookOpen className="h-3.5 w-3.5" />
                            <span>LEY N° 29571 - CÓDIGO DE PROTECCIÓN Y DEFENSA DEL CONSUMIDOR</span>
                        </div>

                        <h1 className="text-3xl sm:text-4xl font-black text-neutral-900 tracking-tight">
                            Libro de Reclamaciones Virtual
                        </h1>

                        <p className="text-sm sm:text-base text-neutral-600 leading-relaxed max-w-3xl">
                            Conforme a lo establecido en el Código de Protección y Defensa del Consumidor, ponemos a su disposición nuestra Hoja de Reclamación Digital para registrar su reclamo o queja.
                        </p>
                    </div>

                    {/* Datos Legales del Proveedor (Ficha Oficial) */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-3 p-4 rounded-2xl bg-neutral-100/80 border border-neutral-200/70 text-xs">
                        <div>
                            <span className="block font-bold text-neutral-500 uppercase text-[10px] tracking-wider">Razón Social</span>
                            <span className="font-extrabold text-neutral-900 truncate block">{razonSocial}</span>
                        </div>
                        <div>
                            <span className="block font-bold text-neutral-500 uppercase text-[10px] tracking-wider">RUC</span>
                            <span className="font-extrabold text-neutral-900">{ruc}</span>
                        </div>
                        <div>
                            <span className="block font-bold text-neutral-500 uppercase text-[10px] tracking-wider">Domicilio Legal</span>
                            <span className="font-medium text-neutral-700 truncate block">{direccionLegal}</span>
                        </div>
                    </div>

                </div>
            </header>

            {/* CONTENIDO PRINCIPAL */}
            <main className="max-w-5xl mx-auto py-10 sm:py-14 px-4 sm:px-6 lg:px-8">

                {/* --- PANTALLA DE ÉXITO CUANDO SE ENVÍA LA RECLAMACIÓN --- */}
                {successData ? (
                    <div className="print-sheet bg-white p-8 sm:p-12 rounded-3xl border border-neutral-200 shadow-md space-y-8 max-w-3xl mx-auto text-center">
                        <div
                            className="h-20 w-20 rounded-full mx-auto flex items-center justify-center text-3xl shadow-inner"
                            style={{
                                backgroundColor: 'color-mix(in srgb, var(--color-primario) 12%, transparent)',
                                color: 'var(--color-primario)'
                            }}
                        >
                            <FaCheck className="h-10 w-10" />
                        </div>

                        <div className="space-y-2">
                            <span className="text-xs font-bold uppercase tracking-widest text-emerald-600">
                                Registro Exitoso
                            </span>
                            <h2 className="text-2xl sm:text-3xl font-black text-neutral-900">
                                Su Hoja de Reclamación ha sido registrada
                            </h2>
                            <p className="text-xs sm:text-sm text-neutral-600 max-w-lg mx-auto leading-relaxed">
                                Hemos remitido una copia íntegra con valor legal a su correo electrónico: <strong className="text-neutral-900">{successData.email_notificado}</strong>.
                            </p>
                        </div>

                        {/* Ficha de Código Correlativo */}
                        <div className="p-6 rounded-2xl bg-neutral-50 border border-neutral-200 space-y-3 max-w-md mx-auto">
                            <span className="text-[11px] font-bold uppercase text-neutral-500 tracking-wider">
                                Código Único de Registro
                            </span>
                            <div
                                className="text-2xl sm:text-3xl font-black tracking-wider font-mono py-2 px-4 rounded-xl text-white inline-block shadow-xs"
                                style={{ backgroundColor: 'var(--color-primario)' }}
                            >
                                {successData.codigo}
                            </div>
                            <div className="text-xs text-neutral-600 pt-1 space-y-1">
                                <div><strong>Fecha de Registro:</strong> {successData.fecha_registro}</div>
                                <div><strong>Fecha Límite Legal de Respuesta:</strong> {successData.fecha_limite}</div>
                            </div>
                        </div>

                        <div className="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs text-left space-y-1">
                            <div className="font-bold flex items-center gap-1.5">
                                <FaCircleInfo className="h-4 w-4 text-amber-600" />
                                <span>Plazo legal de atención:</span>
                            </div>
                            <p className="text-amber-800 leading-relaxed text-[11px]">
                                Conforme al D.S. N° 011-2011-PCM y modificatorias, la empresa responderá formalmente a su requerimiento dentro del plazo máximo de <strong>15 días hábiles</strong>.
                            </p>
                        </div>

                        {/* Botones de Acción */}
                        <div className="pt-4 flex flex-wrap items-center justify-center gap-3 no-print">
                            <button
                                type="button"
                                onClick={handlePrint}
                                className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-neutral-100 hover:bg-neutral-200 text-neutral-800 text-xs font-bold transition border border-neutral-300 shadow-2xs"
                            >
                                <FaPrint className="h-3.5 w-3.5" />
                                <span>Imprimir Constancia</span>
                            </button>
                            <Link
                                href={homeUrl}
                                className="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-white text-xs font-bold transition shadow-xs hover:opacity-90"
                                style={{ backgroundColor: 'var(--color-primario)' }}
                            >
                                <FaHouse className="h-3.5 w-3.5" />
                                <span>Volver a la Tienda</span>
                            </Link>
                        </div>
                    </div>
                ) : (
                    /* --- FORMULARIO OFICIAL DE HOJA DE RECLAMACIÓN --- */
                    <form onSubmit={handleSubmit} className="space-y-8">

                        {/* GUÍA DIDÁCTICA: RECLAMO VS QUEJA */}
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div
                                onClick={() => setForm(prev => ({ ...prev, tipo: 'reclamo' }))}
                                className={`p-5 rounded-2xl border-2 transition-all cursor-pointer space-y-2 ${form.tipo === 'reclamo'
                                    ? 'bg-white shadow-md'
                                    : 'bg-white/60 hover:bg-white border-neutral-200 opacity-80'
                                    }`}
                                style={form.tipo === 'reclamo' ? {
                                    borderColor: 'var(--color-primario)'
                                } : {}}
                            >
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <div
                                            className={`h-4 w-4 rounded-full border-2 flex items-center justify-center ${form.tipo === 'reclamo' ? 'border-[var(--color-primario)]' : 'border-neutral-300'}`}
                                        >
                                            {form.tipo === 'reclamo' && (
                                                <div className="h-2 w-2 rounded-full" style={{ backgroundColor: 'var(--color-primario)' }} />
                                            )}
                                        </div>
                                        <span className="font-extrabold text-sm text-neutral-900 uppercase">1. Reclamo</span>
                                    </div>
                                    <span className="text-[10px] font-bold px-2 py-0.5 rounded-full bg-neutral-100 text-neutral-600">
                                        Productos o Servicios
                                    </span>
                                </div>
                                <p className="text-xs text-neutral-600 leading-relaxed">
                                    Disconformidad relacionada a los <strong>productos o servicios adquiridos</strong> (ej. defecto de fábrica, fallo técnico, retraso en entrega o inconformidad comercial).
                                </p>
                            </div>

                            <div
                                onClick={() => setForm(prev => ({ ...prev, tipo: 'queja' }))}
                                className={`p-5 rounded-2xl border-2 transition-all cursor-pointer space-y-2 ${form.tipo === 'queja'
                                    ? 'bg-white shadow-md'
                                    : 'bg-white/60 hover:bg-white border-neutral-200 opacity-80'
                                    }`}
                                style={form.tipo === 'queja' ? {
                                    borderColor: 'var(--color-primario)'
                                } : {}}
                            >
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <div
                                            className={`h-4 w-4 rounded-full border-2 flex items-center justify-center ${form.tipo === 'queja' ? 'border-[var(--color-primario)]' : 'border-neutral-300'}`}
                                        >
                                            {form.tipo === 'queja' && (
                                                <div className="h-2 w-2 rounded-full" style={{ backgroundColor: 'var(--color-primario)' }} />
                                            )}
                                        </div>
                                        <span className="font-extrabold text-sm text-neutral-900 uppercase">2. Queja</span>
                                    </div>
                                    <span className="text-[10px] font-bold px-2 py-0.5 rounded-full bg-neutral-100 text-neutral-600">
                                        Atención al Cliente
                                    </span>
                                </div>
                                <p className="text-xs text-neutral-600 leading-relaxed">
                                    Malestar o descontento respecto a la <strong>atención recibida</strong> por parte del personal, mala comunicación o trato inadecuado (no vinculado al producto en sí).
                                </p>
                            </div>
                        </div>

                        {/* BLOQUE 1: IDENTIFICACIÓN DEL CONSUMIDOR */}
                        <div className="bg-white p-6 sm:p-8 rounded-3xl border border-neutral-200/80 shadow-xs space-y-6">
                            <div className="flex items-center gap-3 pb-4 border-b border-neutral-100">
                                <div
                                    className="h-8 w-8 rounded-xl font-bold flex items-center justify-center text-xs text-white"
                                    style={{ backgroundColor: 'var(--color-primario)' }}
                                >
                                    1
                                </div>
                                <div>
                                    <h2 className="text-base sm:text-lg font-extrabold text-neutral-900">
                                        Identificación del Consumidor Reclamante
                                    </h2>
                                    <p className="text-xs text-neutral-500">
                                        Ingrese sus datos personales o de la empresa que representa.
                                    </p>
                                </div>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label className="block text-xs font-bold text-neutral-700 mb-1">
                                        Tipo de Documento *
                                    </label>
                                    <select
                                        name="tipo_documento"
                                        value={form.tipo_documento}
                                        onChange={handleChange}
                                        className="w-full px-3.5 py-2.5 rounded-xl text-xs bg-neutral-50 border border-neutral-200 focus:bg-white focus:outline-none focus:border-[var(--color-primario)]"
                                    >
                                        <option value="DNI">DNI (Documento Nacional de Identidad)</option>
                                        <option value="CE">Carné de Extranjería</option>
                                        <option value="RUC">RUC</option>
                                        <option value="Pasaporte">Pasaporte</option>
                                    </select>
                                </div>

                                <div>
                                    <label className="block text-xs font-bold text-neutral-700 mb-1">
                                        Número de Documento *
                                    </label>
                                    <input
                                        type="text"
                                        name="numero_documento"
                                        value={form.numero_documento}
                                        onChange={handleChange}
                                        placeholder="Ej. 70809010"
                                        className={`w-full px-3.5 py-2.5 rounded-xl text-xs bg-neutral-50 border ${errors.numero_documento ? 'border-red-400 bg-red-50' : 'border-neutral-200'} focus:bg-white focus:outline-none focus:border-[var(--color-primario)]`}
                                        required
                                    />
                                    {errors.numero_documento && (
                                        <p className="text-[11px] text-red-600 mt-1">{errors.numero_documento[0]}</p>
                                    )}
                                </div>

                                {form.tipo_documento === 'RUC' ? (
                                    <div className="sm:col-span-2 lg:col-span-1">
                                        <label className="block text-xs font-bold text-neutral-700 mb-1">
                                            Razón Social *
                                        </label>
                                        <input
                                            type="text"
                                            name="razon_social"
                                            value={form.razon_social}
                                            onChange={handleChange}
                                            placeholder="Nombre de la empresa"
                                            className="w-full px-3.5 py-2.5 rounded-xl text-xs bg-neutral-50 border border-neutral-200 focus:bg-white focus:outline-none focus:border-[var(--color-primario)]"
                                            required
                                        />
                                    </div>
                                ) : (
                                    <>
                                        <div>
                                            <label className="block text-xs font-bold text-neutral-700 mb-1">
                                                Nombres *
                                            </label>
                                            <input
                                                type="text"
                                                name="nombres"
                                                value={form.nombres}
                                                onChange={handleChange}
                                                placeholder="Nombres completos"
                                                className={`w-full px-3.5 py-2.5 rounded-xl text-xs bg-neutral-50 border ${errors.nombres ? 'border-red-400 bg-red-50' : 'border-neutral-200'} focus:bg-white focus:outline-none focus:border-[var(--color-primario)]`}
                                                required
                                            />
                                        </div>
                                        <div>
                                            <label className="block text-xs font-bold text-neutral-700 mb-1">
                                                Apellidos
                                            </label>
                                            <input
                                                type="text"
                                                name="apellidos"
                                                value={form.apellidos}
                                                onChange={handleChange}
                                                placeholder="Apellidos completos"
                                                className="w-full px-3.5 py-2.5 rounded-xl text-xs bg-neutral-50 border border-neutral-200 focus:bg-white focus:outline-none focus:border-[var(--color-primario)]"
                                            />
                                        </div>
                                    </>
                                )}

                                <div>
                                    <label className="block text-xs font-bold text-neutral-700 mb-1">
                                        Correo Electrónico *
                                    </label>
                                    <input
                                        type="email"
                                        name="email"
                                        value={form.email}
                                        onChange={handleChange}
                                        placeholder="correo@ejemplo.com"
                                        className={`w-full px-3.5 py-2.5 rounded-xl text-xs bg-neutral-50 border ${errors.email ? 'border-red-400 bg-red-50' : 'border-neutral-200'} focus:bg-white focus:outline-none focus:border-[var(--color-primario)]`}
                                        required
                                    />
                                    {errors.email && (
                                        <p className="text-[11px] text-red-600 mt-1">{errors.email[0]}</p>
                                    )}
                                </div>

                                <div>
                                    <label className="block text-xs font-bold text-neutral-700 mb-1">
                                        Teléfono / Celular *
                                    </label>
                                    <input
                                        type="tel"
                                        name="telefono"
                                        value={form.telefono}
                                        onChange={handleChange}
                                        placeholder="Ej. 994063340"
                                        className={`w-full px-3.5 py-2.5 rounded-xl text-xs bg-neutral-50 border ${errors.telefono ? 'border-red-400 bg-red-50' : 'border-neutral-200'} focus:bg-white focus:outline-none focus:border-[var(--color-primario)]`}
                                        required
                                    />
                                </div>

                                <div className="sm:col-span-2 lg:col-span-3">
                                    <label className="block text-xs font-bold text-neutral-700 mb-1">
                                        Dirección / Domicilio *
                                    </label>
                                    <input
                                        type="text"
                                        name="direccion"
                                        value={form.direccion}
                                        onChange={handleChange}
                                        placeholder="Av. / Jr. / Calle, Número, Urbanización, Distrito"
                                        className={`w-full px-3.5 py-2.5 rounded-xl text-xs bg-neutral-50 border ${errors.direccion ? 'border-red-400 bg-red-50' : 'border-neutral-200'} focus:bg-white focus:outline-none focus:border-[var(--color-primario)]`}
                                        required
                                    />
                                </div>
                            </div>

                            {/* Checkbox Menor de Edad */}
                            <div className="pt-2 border-t border-neutral-100">
                                <label className="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-neutral-700">
                                    <input
                                        type="checkbox"
                                        name="es_menor_edad"
                                        checked={form.es_menor_edad}
                                        onChange={handleChange}
                                        className="rounded border-neutral-300 text-neutral-900 focus:ring-0"
                                    />
                                    <span>El consumidor reclamante es menor de edad (completar datos de apoderado)</span>
                                </label>

                                {form.es_menor_edad && (
                                    <div className="mt-4 p-4 rounded-2xl bg-amber-50/70 border border-amber-200 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div className="sm:col-span-2">
                                            <span className="block text-xs font-bold text-amber-900 mb-1">
                                                Nombres y Apellidos del Padre, Madre o Tutor *
                                            </span>
                                            <input
                                                type="text"
                                                name="apoderado_nombres"
                                                value={form.apoderado_nombres}
                                                onChange={handleChange}
                                                placeholder="Nombre completo del apoderado"
                                                className="w-full px-3.5 py-2 rounded-xl text-xs bg-white border border-amber-300"
                                                required={form.es_menor_edad}
                                            />
                                        </div>
                                        <div>
                                            <span className="block text-xs font-bold text-amber-900 mb-1">
                                                Documento del Apoderado
                                            </span>
                                            <input
                                                type="text"
                                                name="apoderado_numero_documento"
                                                value={form.apoderado_numero_documento}
                                                onChange={handleChange}
                                                placeholder="N° de DNI / CE"
                                                className="w-full px-3.5 py-2 rounded-xl text-xs bg-white border border-amber-300"
                                            />
                                        </div>
                                        <div>
                                            <span className="block text-xs font-bold text-amber-900 mb-1">
                                                Teléfono del Apoderado
                                            </span>
                                            <input
                                                type="tel"
                                                name="apoderado_telefono"
                                                value={form.apoderado_telefono}
                                                onChange={handleChange}
                                                placeholder="Teléfono de contacto"
                                                className="w-full px-3.5 py-2 rounded-xl text-xs bg-white border border-amber-300"
                                            />
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* BLOQUE 2: IDENTIFICACIÓN DEL BIEN CONTRATADO */}
                        <div className="bg-white p-6 sm:p-8 rounded-3xl border border-neutral-200/80 shadow-xs space-y-6">
                            <div className="flex items-center gap-3 pb-4 border-b border-neutral-100">
                                <div
                                    className="h-8 w-8 rounded-xl font-bold flex items-center justify-center text-xs text-white"
                                    style={{ backgroundColor: 'var(--color-primario)' }}
                                >
                                    2
                                </div>
                                <div>
                                    <h2 className="text-base sm:text-lg font-extrabold text-neutral-900">
                                        Identificación del Bien Contratado
                                    </h2>
                                    <p className="text-xs text-neutral-500">
                                        Especifique el producto o servicio que origina la reclamación.
                                    </p>
                                </div>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label className="block text-xs font-bold text-neutral-700 mb-1">
                                        Tipo de Bien *
                                    </label>
                                    <div className="grid grid-cols-2 gap-2">
                                        <button
                                            type="button"
                                            onClick={() => setForm(prev => ({ ...prev, tipo_bien: 'producto' }))}
                                            className={`py-2 px-3 rounded-xl text-xs font-bold transition border cursor-pointer ${form.tipo_bien === 'producto'
                                                ? 'bg-neutral-900 text-white border-neutral-900 shadow-2xs'
                                                : 'bg-neutral-50 text-neutral-700 border-neutral-200 hover:bg-neutral-100'
                                                }`}
                                        >
                                            Producto
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => setForm(prev => ({ ...prev, tipo_bien: 'servicio' }))}
                                            className={`py-2 px-3 rounded-xl text-xs font-bold transition border cursor-pointer ${form.tipo_bien === 'servicio'
                                                ? 'bg-neutral-900 text-white border-neutral-900 shadow-2xs'
                                                : 'bg-neutral-50 text-neutral-700 border-neutral-200 hover:bg-neutral-100'
                                                }`}
                                        >
                                            Servicio
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-xs font-bold text-neutral-700 mb-1">
                                        Monto Reclamado (Opcional)
                                    </label>
                                    <div className="flex rounded-xl overflow-hidden border border-neutral-200 bg-neutral-50 focus-within:border-[var(--color-primario)] focus-within:bg-white">
                                        <select
                                            name="moneda"
                                            value={form.moneda}
                                            onChange={handleChange}
                                            className="px-2.5 py-2 text-xs bg-transparent border-r border-neutral-200 font-bold focus:outline-none"
                                        >
                                            <option value="PEN">S/ (PEN)</option>
                                            <option value="USD">$ (USD)</option>
                                        </select>
                                        <input
                                            type="number"
                                            step="0.01"
                                            name="monto_reclamado"
                                            value={form.monto_reclamado}
                                            onChange={handleChange}
                                            placeholder="0.00"
                                            className="w-full px-3 py-2 text-xs bg-transparent focus:outline-none"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-xs font-bold text-neutral-700 mb-1">
                                        N° Pedido / Boleta / Factura
                                    </label>
                                    <input
                                        type="text"
                                        name="numero_pedido"
                                        value={form.numero_pedido}
                                        onChange={handleChange}
                                        placeholder="Ej. PED-2026-0421"
                                        className="w-full px-3.5 py-2.5 rounded-xl text-xs bg-neutral-50 border border-neutral-200 focus:bg-white focus:outline-none focus:border-[var(--color-primario)]"
                                    />
                                </div>

                                <div className="sm:col-span-2 lg:col-span-3">
                                    <label className="block text-xs font-bold text-neutral-700 mb-1">
                                        Descripción del Producto o Servicio *
                                    </label>
                                    <textarea
                                        name="descripcion_bien"
                                        rows={2}
                                        value={form.descripcion_bien}
                                        onChange={handleChange}
                                        placeholder="Indique marca, modelo, código o descripción detallada del producto o servicio adquirido..."
                                        className={`w-full px-3.5 py-2.5 rounded-xl text-xs bg-neutral-50 border ${errors.descripcion_bien ? 'border-red-400 bg-red-50' : 'border-neutral-200'} focus:bg-white focus:outline-none focus:border-[var(--color-primario)]`}
                                        required
                                    />
                                    {errors.descripcion_bien && (
                                        <p className="text-[11px] text-red-600 mt-1">{errors.descripcion_bien[0]}</p>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* BLOQUE 3: DETALLE DE LA RECLAMACIÓN Y PEDIDO */}
                        <div className="bg-white p-6 sm:p-8 rounded-3xl border border-neutral-200/80 shadow-xs space-y-6">
                            <div className="flex items-center gap-3 pb-4 border-b border-neutral-100">
                                <div
                                    className="h-8 w-8 rounded-xl font-bold flex items-center justify-center text-xs text-white"
                                    style={{ backgroundColor: 'var(--color-primario)' }}
                                >
                                    3
                                </div>
                                <div>
                                    <h2 className="text-base sm:text-lg font-extrabold text-neutral-900">
                                        Detalle de la {form.tipo === 'queja' ? 'Queja' : 'Reclamación'} y Pedido
                                    </h2>
                                    <p className="text-xs text-neutral-500">
                                        Explique de forma clara los hechos y la solución que solicita.
                                    </p>
                                </div>
                            </div>

                            <div className="space-y-4">
                                <div>
                                    <div className="flex items-center justify-between mb-1">
                                        <label className="block text-xs font-bold text-neutral-700">
                                            Detalle de los Hechos ({form.tipo === 'queja' ? 'Queja' : 'Reclamo'}) *
                                        </label>
                                        <span className="text-[11px] text-neutral-400">
                                            {form.detalle.length} / 4000 caracteres
                                        </span>
                                    </div>
                                    <textarea
                                        name="detalle"
                                        rows={4}
                                        value={form.detalle}
                                        onChange={handleChange}
                                        placeholder="Describa con claridad lo sucedido, fechas relevantes y motivos de su inconformidad..."
                                        className={`w-full px-3.5 py-2.5 rounded-xl text-xs bg-neutral-50 border ${errors.detalle ? 'border-red-400 bg-red-50' : 'border-neutral-200'} focus:bg-white focus:outline-none focus:border-[var(--color-primario)]`}
                                        required
                                    />
                                    {errors.detalle && (
                                        <p className="text-[11px] text-red-600 mt-1">{errors.detalle[0]}</p>
                                    )}
                                </div>

                                <div>
                                    <div className="flex items-center justify-between mb-1">
                                        <label className="block text-xs font-bold text-neutral-700">
                                            Pedido Concreto del Consumidor (Solución solicitada) *
                                        </label>
                                        <span className="text-[11px] text-neutral-400">
                                            {form.pedido.length} / 2000 caracteres
                                        </span>
                                    </div>
                                    <textarea
                                        name="pedido"
                                        rows={3}
                                        value={form.pedido}
                                        onChange={handleChange}
                                        placeholder="Indique con precisión qué espera de la empresa (ej. cambio de producto, reparación técnica, nota de crédito, disculpas formales)..."
                                        className={`w-full px-3.5 py-2.5 rounded-xl text-xs bg-neutral-50 border ${errors.pedido ? 'border-red-400 bg-red-50' : 'border-neutral-200'} focus:bg-white focus:outline-none focus:border-[var(--color-primario)]`}
                                        required
                                    />
                                    {errors.pedido && (
                                        <p className="text-[11px] text-red-600 mt-1">{errors.pedido[0]}</p>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* BLOQUE 4: DECLARACIÓN Y ENVÍO */}
                        <div className="bg-white p-6 sm:p-8 rounded-3xl border border-neutral-200/80 shadow-xs space-y-6">
                            <div className="p-4 rounded-2xl bg-neutral-50 border border-neutral-200/80 text-neutral-600 text-[11px] leading-relaxed space-y-2">
                                <div className="flex items-center gap-1.5 font-bold text-neutral-800">
                                    <FaShieldHalved className="h-3.5 w-3.5 text-neutral-600" />
                                    <span>Declaración de Notificación y Veracidad</span>
                                </div>
                                <p>
                                    Declaro bajo juramento que los datos consignados en la presente Hoja de Reclamación son verdaderos y fidedignos. Autorizo a <strong>{siteName}</strong> a remitir la respuesta formal a través del correo electrónico consignado.
                                </p>
                            </div>

                            <label className="flex items-start gap-2.5 cursor-pointer text-xs text-neutral-700">
                                <input
                                    type="checkbox"
                                    name="acepta_terminos"
                                    checked={form.acepta_terminos}
                                    onChange={handleChange}
                                    required
                                    className="mt-0.5 rounded border-neutral-300 text-neutral-900 focus:ring-0"
                                />
                                <span>
                                    He leído y acepto los términos de registro del Libro de Reclamaciones y confirmo la veracidad de la información ingresada.
                                </span>
                            </label>

                            <div className="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                                <span className="text-[11px] text-neutral-500">
                                    * Todos los campos marcados con asterisco son obligatorios por ley.
                                </span>

                                <button
                                    type="submit"
                                    disabled={loading}
                                    className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl text-white font-extrabold text-sm transition shadow-md hover:opacity-95 disabled:opacity-50 cursor-pointer"
                                    style={{ backgroundColor: 'var(--color-primario)' }}
                                >
                                    {loading ? (
                                        <>
                                            <FaSpinner className="h-4 w-4 animate-spin" />
                                            <span>Registrando Reclamación...</span>
                                        </>
                                    ) : (
                                        <>
                                            <FaBookOpen className="h-4 w-4" />
                                            <span>Enviar Hoja de Reclamación</span>
                                        </>
                                    )}
                                </button>
                            </div>
                        </div>

                    </form>
                )}

            </main>
        </div>
    );
}

