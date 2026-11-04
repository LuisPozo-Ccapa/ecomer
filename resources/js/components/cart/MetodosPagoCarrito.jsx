import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { FaCopy, FaCheck } from 'react-icons/fa6';

export default function MetodosPagoCarrito({
    estilos = {},
    site = {},
    metodoSeleccionado,
    setMetodoSeleccionado
}) {
    const [copiadoIdx, setCopiadoIdx] = useState(null);

    // 1. Extraer configuraciones de métodos de pago desde estilos o site
    const cfg = estilos?.metodos_pago_carrito || site?.estilos?.metodos_pago_carrito || {};
    const cuentasGenerales = estilos?.cuentas_pago || site?.estilos?.cuentas_pago || [];

    // Transferencia Bancaria
    const transfCfg = cfg?.transferencia || {};
    const transfActivo = transfCfg?.activo !== false;
    const rawCuentas = transfCfg?.cuentas && transfCfg.cuentas.length > 0 ? transfCfg.cuentas : cuentasGenerales;
    const cuentasList = Array.isArray(rawCuentas)
        ? rawCuentas
        : (rawCuentas && typeof rawCuentas === 'object' ? Object.values(rawCuentas) : []);

    const tieneTransferencia = transfActivo && cuentasList.length > 0;

    // Pago con Yape QR
    const yapeCfg = cfg?.yape || {};
    const yapeActivo = yapeCfg?.activo !== false;
    const yapeNumero = yapeCfg?.numero || yapeCfg?.celular || null;
    const yapeQr = yapeCfg?.qr_imagen || yapeCfg?.qr || null;
    const tieneYape = yapeActivo && (Boolean(yapeNumero) || Boolean(yapeQr));

    const ambosActivos = tieneTransferencia && tieneYape;

    // Sincronizar selección si solo hay uno habilitado
    useEffect(() => {
        if (tieneTransferencia && !tieneYape && metodoSeleccionado !== 'transferencia') {
            setMetodoSeleccionado('transferencia');
        } else if (tieneYape && !tieneTransferencia && metodoSeleccionado !== 'yape') {
            setMetodoSeleccionado('yape');
        }
    }, [tieneTransferencia, tieneYape, metodoSeleccionado, setMetodoSeleccionado]);

    // Si ninguno está configurado ni activo, NO mostrar nada (Regla de oro del usuario)
    if (!tieneTransferencia && !tieneYape) {
        return null;
    }

    const handleCopiar = (texto, idx) => {
        if (!texto) return;
        navigator.clipboard?.writeText(String(texto).trim());
        setCopiadoIdx(idx);
        setTimeout(() => setCopiadoIdx(null), 1800);
    };

    const qrSrc = yapeQr
        ? (yapeQr.startsWith('http') || yapeQr.startsWith('/') ? yapeQr : `/storage/${yapeQr}`)
        : null;

    const yapeNumeroFormateado = yapeNumero ? String(yapeNumero).replace(/(\d{3})(\d{3})(\d{3})/, '$1 $2 $3') : '';

    return (
        <div className="space-y-4 pt-4 border-t border-gray-100">
            <h4
                className="text-xs font-bold uppercase tracking-wider text-gray-800"
                style={{ fontFamily: 'var(--tipografia-titulos)' }}
            >
                Métodos de pago
            </h4>

            {/* OPCIÓN 1: TRANSFERENCIA BANCARIA */}
            {tieneTransferencia && (
                <div className="space-y-2.5">
                    {ambosActivos ? (
                        <label
                            onClick={() => setMetodoSeleccionado('transferencia')}
                            className="flex items-start gap-2.5 cursor-pointer select-none group"
                        >
                            <div className="pt-0.5">
                                <div className={`h-4 w-4 rounded-full border flex items-center justify-center transition-all ${
                                    metodoSeleccionado === 'transferencia'
                                        ? 'border-[#25D366] bg-[#25D366]'
                                        : 'border-gray-300 group-hover:border-gray-400 bg-white'
                                }`}>
                                    {metodoSeleccionado === 'transferencia' && (
                                        <div className="h-1.5 w-1.5 rounded-full bg-white" />
                                    )}
                                </div>
                            </div>

                            <div className="min-w-0">
                                <span className={`text-xs font-bold block leading-tight ${
                                    metodoSeleccionado === 'transferencia' ? 'text-gray-900' : 'text-gray-600'
                                }`}>
                                    {transfCfg.titulo || 'Transferencia / Deposito Bancario'}
                                </span>
                                <p className="text-[11px] text-gray-500 mt-0.5 leading-snug">
                                    {transfCfg.descripcion || 'Realiza tu pago directamente desde tu cuenta bancaria,'}
                                </p>
                            </div>
                        </label>
                    ) : (
                        <div>
                            <span className="text-xs font-bold block text-gray-900 leading-tight">
                                {transfCfg.titulo || 'Transferencia / Deposito Bancario'}
                            </span>
                            <p className="text-[11px] text-gray-500 mt-0.5 leading-snug">
                                {transfCfg.descripcion || 'Realiza tu pago directamente desde tu cuenta bancaria,'}
                            </p>
                        </div>
                    )}

                    {/* Despliegue de tarjetas bancarias oscuras */}
                    <AnimatePresence initial={false}>
                        {(metodoSeleccionado === 'transferencia' || !ambosActivos) && (
                            <motion.div
                                initial={{ opacity: 0, height: 0 }}
                                animate={{ opacity: 1, height: 'auto' }}
                                exit={{ opacity: 0, height: 0 }}
                                transition={{ duration: 0.25 }}
                                className={`overflow-hidden space-y-2 ${ambosActivos ? 'pl-6' : ''}`}
                            >
                                {cuentasList.map((cuenta, idx) => {
                                    const label = cuenta.label || cuenta.tipo || 'Cuenta corriente';
                                    const banco = cuenta.banco || cuenta.metodo_pago || 'Banco';
                                    const num = cuenta.numero || cuenta.info || '';

                                    return (
                                        <div
                                            key={idx}
                                            className="bg-[#12131C] text-white rounded-xl p-3 shadow-md border border-gray-800/80 transition-all hover:border-gray-700"
                                        >
                                            <div className="flex items-center justify-between text-[11px] mb-1">
                                                <span className="text-gray-400 font-medium">
                                                    {label}
                                                </span>
                                                <span className="font-extrabold text-white tracking-wide">
                                                    {banco}
                                                </span>
                                            </div>

                                            <div className="flex items-center justify-between gap-2 mt-1">
                                                <span className="text-xs sm:text-sm font-bold tracking-wider font-mono text-neutral-100 select-all">
                                                    {num}
                                                </span>
                                                <button
                                                    type="button"
                                                    onClick={() => handleCopiar(num, idx)}
                                                    title="Copiar número"
                                                    className="p-1 rounded-md text-gray-400 hover:text-white hover:bg-white/10 transition cursor-pointer shrink-0"
                                                >
                                                    {copiadoIdx === idx ? (
                                                        <FaCheck className="h-3 w-3 text-emerald-400" />
                                                    ) : (
                                                        <FaCopy className="h-3 w-3" />
                                                    )}
                                                </button>
                                            </div>
                                        </div>
                                    );
                                })}
                            </motion.div>
                        )}
                    </AnimatePresence>
                </div>
            )}

            {/* OPCIÓN 2: PAGO CON QR YAPE */}
            {tieneYape && (
                <div className="space-y-2.5">
                    {ambosActivos ? (
                        <label
                            onClick={() => setMetodoSeleccionado('yape')}
                            className="flex items-center gap-2.5 cursor-pointer select-none group"
                        >
                            <div className={`h-4 w-4 rounded-full border flex items-center justify-center transition-all ${
                                metodoSeleccionado === 'yape'
                                    ? 'border-[#25D366] bg-[#25D366]'
                                    : 'border-gray-300 group-hover:border-gray-400 bg-white'
                            }`}>
                                {metodoSeleccionado === 'yape' && (
                                    <div className="h-1.5 w-1.5 rounded-full bg-white" />
                                )}
                            </div>

                            <div className="flex items-center gap-2">
                                <span className={`text-xs font-bold leading-tight ${
                                    metodoSeleccionado === 'yape' ? 'text-gray-900' : 'text-gray-600'
                                }`}>
                                    {yapeCfg.titulo || 'Pago con QR YAPE'}
                                </span>
                                {/* Badge oficial Yape */}
                                <span className="h-5 px-1.5 rounded-md bg-[#742284] text-white text-[10px] font-black uppercase tracking-tight flex items-center justify-center shadow-2xs">
                                    yape
                                </span>
                            </div>
                        </label>
                    ) : (
                        <div className="flex items-center gap-2">
                            <span className="text-xs font-bold leading-tight text-gray-900">
                                {yapeCfg.titulo || 'Pago con QR YAPE'}
                            </span>
                            <span className="h-5 px-1.5 rounded-md bg-[#742284] text-white text-[10px] font-black uppercase tracking-tight flex items-center justify-center shadow-2xs">
                                yape
                            </span>
                        </div>
                    )}

                    {/* Despliegue de YAPE: QR + Número + Instrucciones */}
                    <AnimatePresence initial={false}>
                        {(metodoSeleccionado === 'yape' || !ambosActivos) && (
                            <motion.div
                                initial={{ opacity: 0, height: 0 }}
                                animate={{ opacity: 1, height: 'auto' }}
                                exit={{ opacity: 0, height: 0 }}
                                transition={{ duration: 0.25 }}
                                className={`overflow-hidden ${ambosActivos ? 'pl-6' : ''}`}
                            >
                                <div className="bg-white rounded-2xl p-4 border border-purple-100 shadow-sm space-y-3">
                                    {/* Badge con Número */}
                                    {yapeNumero && (
                                        <div className="inline-block bg-[#742284] text-white text-xs font-black px-3 py-1 rounded-lg shadow-xs">
                                            {yapeNumeroFormateado || yapeNumero}
                                        </div>
                                    )}

                                    {/* QR + Texto lateral */}
                                    <div className="flex items-center gap-4">
                                        <div className="h-28 w-28 shrink-0 rounded-xl bg-purple-50 p-1.5 border border-purple-200/80 flex items-center justify-center overflow-hidden">
                                            {qrSrc ? (
                                                <img
                                                    src={qrSrc}
                                                    alt="Código QR YAPE"
                                                    className="h-full w-full object-contain rounded-lg"
                                                />
                                            ) : (
                                                <div className="text-center p-2">
                                                    <span className="text-2xl block">📱</span>
                                                    <span className="text-[10px] font-bold text-purple-900 block mt-1 leading-tight">
                                                        QR YAPE
                                                    </span>
                                                </div>
                                            )}
                                        </div>

                                        <div className="space-y-1">
                                            <p className="text-xs font-bold text-gray-700 leading-snug">
                                                {yapeCfg.instruccion || 'Ingrese a YAPE y escanee este QR'}
                                            </p>
                                            {yapeCfg.titular && (
                                                <p className="text-[11px] text-gray-500 font-medium">
                                                    Titular: <strong className="text-gray-800">{yapeCfg.titular}</strong>
                                                </p>
                                            )}
                                        </div>
                                    </div>

                                    {/* Mensaje de envío de captura por WhatsApp */}
                                    <div className="pt-2 border-t border-purple-50">
                                        <p className="text-xs text-gray-600 font-medium text-center leading-relaxed">
                                            Enviar captura del depósito por WhatsApp al{' '}
                                            <strong className="text-gray-900 font-extrabold">
                                                {yapeNumeroFormateado || yapeNumero || 'número comercial'}
                                            </strong>
                                        </p>
                                    </div>
                                </div>
                            </motion.div>
                        )}
                    </AnimatePresence>
                </div>
            )}
        </div>
    );
}
