import React from 'react';
import { FaCreditCard, FaWhatsapp, FaPhone } from 'react-icons/fa6';
import DynamicIcon from '@/components/DynamicIcon';

export default function MediosPagoSection({
    tarjetasPagoList = [],
    waNum
}) {
    return (
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

                        {/* Tarjetas de Métodos de Pago dinámicas */}
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

                        {/* Card 4: Asesor WhatsApp */}
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
    );
}

