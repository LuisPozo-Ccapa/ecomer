<div class="min-h-screen w-full flex flex-col lg:flex-row bg-[#07090e] text-slate-100 overflow-hidden relative font-sans">
    <!-- Ambient Glows -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-cyan-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Left Column: Showcase & Tenant Monitor -->
    <div class="w-full lg:w-7/12 p-8 lg:p-16 flex flex-col justify-between relative z-10 border-b lg:border-b-0 lg:border-r border-white/[0.08]">
        <!-- Brand -->
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-violet-500 flex items-center justify-center font-extrabold text-white shadow-lg shadow-indigo-500/25">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <span class="font-extrabold tracking-tight text-white text-base">ECOMER</span>
                <span class="text-xs font-mono ml-2 px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-400 font-semibold border border-indigo-500/30">v5.0 Enterprise</span>
            </div>
        </div>

        <!-- Hero text -->
        <div class="my-10 lg:my-0 space-y-6 max-w-xl">
            <h1 class="text-3xl lg:text-4xl font-extrabold text-white tracking-tight leading-tight">
                El panel de control multi-tenant para tu negocio digital.
            </h1>
            <p class="text-slate-400 text-sm leading-relaxed">
                Gestión centralizada de catálogos, pedidos en tiempo real, personalización CMS viva e inventario multi-almacén con rendimiento extremo.
            </p>

            <!-- Tenant live monitor card -->
            <div class="p-4 rounded-2xl bg-white/[0.04] border border-white/[0.08] backdrop-blur-md space-y-2.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shadow-sm shadow-emerald-400"></span>
                        <span class="font-bold text-xs text-white">{{ request()->getHost() }}</span>
                    </div>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/25 font-bold">
                        EN LÍNEA (200 OK)
                    </span>
                </div>
                <div class="text-[11px] text-slate-400">
                    Instancia Multi-Tenant Activa • PHP 8.4 • Inertia + React 19
                </div>
            </div>

            <!-- Mini badges -->
            <div class="flex flex-wrap gap-2 pt-1">
                <span class="text-xs px-3 py-1 rounded-xl bg-white/[0.03] border border-white/[0.06] text-slate-300">⚡ Core Web Vitals 95+</span>
                <span class="text-xs px-3 py-1 rounded-xl bg-white/[0.03] border border-white/[0.06] text-slate-300">🛡️ Filament Shield RBAC</span>
                <span class="text-xs px-3 py-1 rounded-xl bg-white/[0.03] border border-white/[0.06] text-slate-300">📦 WebP Optimizado</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-xs text-slate-500">
            &copy; {{ date('Y') }} Ecomer SaaS Platform. Todos los derechos reservados.
        </div>
    </div>

    <!-- Right Column: Glassmorphic Auth Form -->
    <div class="w-full lg:w-5/12 p-8 lg:p-16 flex items-center justify-center relative z-10">
        <div class="w-full max-w-md p-8 lg:p-10 rounded-3xl bg-[#0e1424]/90 border border-white/[0.08] backdrop-blur-2xl shadow-2xl shadow-black/80 space-y-6">
            <div class="text-center space-y-2">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-gradient-to-tr from-indigo-500 to-violet-500 flex items-center justify-center font-black text-white text-base shadow-lg shadow-indigo-500/25">
                    {{ strtoupper(substr(request()->getHost(), 0, 2)) }}
                </div>
                <h2 class="text-xl font-bold text-white tracking-tight">Acceso Administrativo</h2>
                <p class="text-xs text-slate-400">Ingrese sus credenciales para gestionar este sitio</p>
            </div>

            <div class="fi-login-form-ctn">
                {{ $this->content }}
            </div>

            <div class="text-center pt-2 border-t border-white/[0.06]">
                <span class="text-[11px] text-slate-500">🔒 Conexión segura SSL 256-bit • Filament Shield v5</span>
            </div>
        </div>
    </div>
</div>
