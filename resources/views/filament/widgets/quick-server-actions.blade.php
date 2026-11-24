<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-3 border-b border-gray-200 dark:border-white/[0.08]">
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    Centro de Operaciones del Tenant
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-bold">EN LÍNEA</span>
                </h3>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Acciones rápidas de mantenimiento, rendimiento de medios y enlaces públicos.</p>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-white/[0.04] text-gray-600 dark:text-slate-300 border border-gray-200 dark:border-white/[0.08] font-mono text-[11px]">
                    Host: {{ request()->getHost() }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-4">
            <!-- Acción 1: Ver Tienda -->
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/[0.06] hover:border-indigo-500/30 transition-all flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </div>
                    <div class="font-bold text-xs text-gray-900 dark:text-white">Tienda Pública</div>
                    <p class="text-[11px] text-gray-500 dark:text-slate-400 leading-snug">Visualiza tu tienda online tal como la ven tus clientes en tiempo real.</p>
                </div>
                <div class="pt-3">
                    <a href="{{ url('/') }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                        <span>Abrir tienda</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Acción 2: Catálogo PDF -->
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/[0.06] hover:border-indigo-500/30 transition-all flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="font-bold text-xs text-gray-900 dark:text-white">Catálogo PDF</div>
                    <p class="text-[11px] text-gray-500 dark:text-slate-400 leading-snug">Descarga directa del catálogo de productos para cotizaciones y ventas.</p>
                </div>
                <div class="pt-3">
                    <a href="{{ url('/catalogo/pdf') }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                        <span>Descargar PDF</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Acción 3: Rendimiento WebP -->
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/[0.06] hover:border-indigo-500/30 transition-all flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div class="font-bold text-xs text-gray-900 dark:text-white">Optimización WebP</div>
                    <p class="text-[11px] text-gray-500 dark:text-slate-400 leading-snug">Imágenes comprimidas automáticamente a &lt; 150 KB para Lighthouse 95+.</p>
                </div>
                <div class="pt-3">
                    <span class="text-[10px] font-mono text-emerald-500 font-bold">Activo en segundo plano</span>
                </div>
            </div>

            <!-- Acción 4: Infraestructura -->
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/[0.02] border border-gray-200 dark:border-white/[0.06] hover:border-indigo-500/30 transition-all flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div class="font-bold text-xs text-gray-900 dark:text-white">Seguridad & Motor</div>
                    <p class="text-[11px] text-gray-500 dark:text-slate-400 leading-snug">PHP 8.4 • Shield RBAC • Cifrado SSL 256-bit y arquitectura multi-tenant.</p>
                </div>
                <div class="pt-3">
                    <span class="text-[10px] font-mono text-cyan-500 font-bold">Filament Shield 5.0</span>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
