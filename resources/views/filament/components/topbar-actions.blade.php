<div class="flex items-center gap-2.5 mr-2 select-none">
    <!-- Live Tenant Badge -->
    <div class="hidden sm:flex items-center gap-2 px-2.5 py-1 rounded-lg bg-white/[0.04] dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] text-xs">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shadow-sm shadow-emerald-400"></span>
        <span class="font-semibold text-gray-700 dark:text-slate-200 text-[11px]">{{ request()->getHost() }}</span>
        <span class="text-[9px] text-emerald-500 font-mono bg-emerald-500/10 px-1 py-0.2 rounded border border-emerald-500/20 font-bold">200 OK</span>
    </div>

    <!-- Ver Tienda Pública Button -->
    <a href="{{ url('/') }}" target="_blank" class="flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 transition-all shadow-sm" title="Abrir sitio web en nueva pestaña">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        <span class="hidden md:inline">Ver Tienda</span>
    </a>
</div>
