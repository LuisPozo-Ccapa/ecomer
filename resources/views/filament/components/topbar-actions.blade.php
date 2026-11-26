<div 
    x-data="{
        isDirty: false,
        isSaving: false,
        justSaved: false,
        hasForm: false,
        checkForm() {
            this.hasForm = !!document.querySelector('form#form, .fi-resource-edit-record-page form, .fi-form');
        },
        init() {
            this.checkForm();
            // Escuchar cambios de inputs y selecciones en el formulario
            const markDirty = (e) => {
                if (e.target && e.target.closest('form')) {
                    this.isDirty = true;
                }
            };
            window.addEventListener('input', markDirty, { passive: true });
            window.addEventListener('change', markDirty, { passive: true });

            // Atajo universal Ctrl+S / Cmd+S
            window.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
                    if (this.isDirty && this.hasForm) {
                        e.preventDefault();
                        this.save();
                    }
                }
            });

            // Detectar finalización de guardado por eventos de Livewire y Filament
            window.addEventListener('filament-notifications-sent', () => {
                if (this.isSaving) {
                    this.onSaveSuccess();
                }
            });
            document.addEventListener('livewire:init', () => {
                if (window.Livewire) {
                    window.Livewire.hook('commit', ({ component, succeed }) => {
                        succeed(() => {
                            if (this.isSaving) {
                                this.onSaveSuccess();
                            }
                        });
                    });
                }
            });
        },
        save() {
            const form = document.querySelector('form#form') || document.querySelector('.fi-resource-edit-record-page form') || document.querySelector('.fi-form');
            if (!form) return;
            
            this.isSaving = true;
            const submitBtn = form.querySelector('button[type=\'submit\']') || form.querySelector('[wire\\:click*=\'save\']');
            if (submitBtn) {
                submitBtn.click();
            } else {
                form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
            }
            
            // Timeout de seguridad en caso de que no haya evento de notificación
            setTimeout(() => {
                if (this.isSaving) {
                    this.onSaveSuccess();
                }
            }, 1500);
        },
        onSaveSuccess() {
            this.isSaving = false;
            this.isDirty = false;
            this.justSaved = true;
            setTimeout(() => {
                this.justSaved = false;
            }, 2200);
        }
    }"
    class="flex items-center gap-2.5 mr-2 select-none"
>
    <!-- Botón Dinámico de Guardar en Navbar (Aparece únicamente cuando hay cambios) -->
    <div 
        x-show="isDirty || isSaving || justSaved"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-90 -translate-y-1"
        class="flex items-center gap-1.5"
    >
        <button
            type="button"
            @click="save()"
            :disabled="isSaving"
            class="flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-bold shadow-md transition-all transform active:scale-95 cursor-pointer"
            :class="{
                'bg-emerald-600 hover:bg-emerald-500 text-white shadow-emerald-500/25 border border-emerald-400/40': justSaved,
                'bg-indigo-600 hover:bg-indigo-500 text-white shadow-indigo-500/30 border border-indigo-400/40': isDirty && !isSaving && !justSaved,
                'bg-indigo-700/80 text-white/80 cursor-wait': isSaving
            }"
            title="Guardar cambios (Ctrl + S)"
        >
            <!-- Estado Guardando -->
            <template x-if="isSaving">
                <span class="flex items-center gap-1.5">
                    <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Guardando...</span>
                </span>
            </template>

            <!-- Estado Guardado con Éxito -->
            <template x-if="justSaved">
                <span class="flex items-center gap-1.5 text-emerald-100">
                    <svg class="w-3.5 h-3.5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>¡Guardado!</span>
                </span>
            </template>

            <!-- Estado Cambios Pendientes -->
            <template x-if="isDirty && !isSaving && !justSaved">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                    <svg class="w-3.5 h-3.5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    <span>Guardar Cambios</span>
                    <span class="hidden md:inline text-[9px] font-mono px-1 py-0.2 rounded bg-black/25 text-indigo-200 font-normal">Ctrl+S</span>
                </span>
            </template>
        </button>
    </div>

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
