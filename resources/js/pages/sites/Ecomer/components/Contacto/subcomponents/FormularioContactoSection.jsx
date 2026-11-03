import React, { useState } from 'react';
import { FaCheck, FaSpinner } from 'react-icons/fa6';
import LegalModal from '@/components/LegalModal';

export default function FormularioContactoSection({
    site,
    estilos,
    seccionesData,
    productos = []
}) {
    const [enviado, setEnviado] = useState(false);
    const [enviando, setEnviando] = useState(false);
    const [errorMsg, setErrorMsg] = useState(null);
    const [aceptoPoliticas, setAceptoPoliticas] = useState(false);
    const [legalModalOpen, setLegalModalOpen] = useState(false);

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

    return (
        <div className="bg-white rounded-3xl p-6 sm:p-10 border border-gray-200/70 shadow-sm flex-1 flex flex-col justify-between">
            <div>
                <h2 className="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mb-2">
                    Cotiza tu pedido aquí
                </h2>
                <p className="text-xs sm:text-sm text-gray-500 mb-6 leading-relaxed">
                    Completa el formulario y un asesor comercial se pondrá en contacto contigo de inmediato.
                </p>

                {enviado ? (
                    <div className="rounded-2xl bg-emerald-50 border border-emerald-200 p-6 sm:p-8 text-center my-6">
                        <div className="h-12 w-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                            <FaCheck className="h-6 w-6" />
                        </div>
                        <h3 className="text-lg font-extrabold text-emerald-900">
                            ¡Mensaje enviado con éxito!
                        </h3>
                        <p className="mt-1.5 text-xs text-emerald-700 max-w-md mx-auto">
                            Gracias por escribirnos. Nuestro equipo comercial revisará tus requerimientos y te responderá a la brevedad.
                        </p>
                        <button
                            type="button"
                            onClick={() => setEnviado(false)}
                            className="mt-5 inline-flex items-center justify-center px-4 py-2 text-xs font-bold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white transition-colors cursor-pointer"
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

                        {/* FILA 1: Nombre + Empresa */}
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

                        {/* FILA 2: Teléfono + Correo */}
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
                                        placeholder="Ej: Transformadores, Tableros, etc."
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

            <LegalModal
                isOpen={legalModalOpen}
                onClose={() => setLegalModalOpen(false)}
                defaultTab="privacidad"
                site={site}
            />
        </div>
    );
}

