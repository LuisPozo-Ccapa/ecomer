<?php

namespace App\Console\Commands;

use App\Models\Moneda;
use App\Models\Plantilla;
use App\Models\Pregunta;
use App\Models\Seccion;
use App\Models\Site;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class BootstrapStoreCommand extends Command
{
    /**
     * El nombre y firma del comando de consola.
     */
    protected $signature = 'store:bootstrap {--force : Forzar inicialización incluso si ya existe un sitio}';

    /**
     * La descripción del comando.
     */
    protected $description = 'Inicializa automáticamente el ecosistema esencial de una tienda en una base de datos vacía (Monedas, Plantillas con 17 secciones completas, Roles, Admin y Sitio)';

    /**
     * Ejecuta el comando.
     */
    public function handle(): int
    {
        $appName = config('app.name', 'Mi Tienda');
        $this->info("Iniciando auto-bootstrap para la tienda: {$appName}...");

        // 1. Monedas Oficiales
        $soles = Moneda::firstOrCreate(
            ['codigo' => 'PEN'],
            ['nombre' => 'Soles', 'simbolo' => 'S/', 'activa' => true]
        );

        $dolares = Moneda::firstOrCreate(
            ['codigo' => 'USD'],
            ['nombre' => 'Dólares', 'simbolo' => '$', 'activa' => true]
        );

        // 2. Roles Administrativos (Filament Shield)
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web']
        );

        // 3. Plantillas Maestras
        // 3.1 Plantilla Universal: Ecomer (17 Secciones)
        $plantillaEcomer = Plantilla::firstOrCreate(
            ['slug' => 'ecomer'],
            [
                'tipo'        => 'ecommerce',
                'nombre'      => 'Ecomer',
                'descripcion' => 'Tienda virtual completa con navegación, catálogo, filtros, sliders y carrito.',
                'estilos'     => [
                    'color_primario'     => '#1854a4',
                    'color_secundario'   => '#ffffff',
                    'tipografia_titulos' => 'Montserrat',
                    'tipografia_texto'   => 'Montserrat',
                    'radio_bordes'       => '0.5rem',
                    'espaciado'          => '1rem',
                ],
                'activa'      => true,
            ]
        );

        $this->seedSeccionesDesdeJson(
            $plantillaEcomer,
            database_path('data/plantilla_ecomer_secciones.json')
        );

        // 3.2 Plantilla Tortas (9 Secciones)
        $plantillaTortas = Plantilla::firstOrCreate(
            ['slug' => 'tortas'],
            [
                'tipo'        => 'ecommerce',
                'nombre'      => 'Tortas',
                'descripcion' => 'Catálogo y pedidos para pastelería y repostería.',
                'estilos'     => ['color_primario' => '#f43f5e', 'color_secundario' => '#ffffff'],
                'activa'      => true,
            ]
        );

        $this->seedSeccionesDesdeJson(
            $plantillaTortas,
            database_path('data/plantilla_tortas_secciones.json')
        );

        // 3.3 Plantilla Corporativa
        Plantilla::firstOrCreate(
            ['slug' => 'corporativa'],
            [
                'tipo'        => 'landing_page',
                'nombre'      => 'Corporativa',
                'descripcion' => 'Sitio institucional y servicios empresariales.',
                'estilos'     => ['color_primario' => '#2563eb', 'color_secundario' => '#ffffff'],
                'activa'      => true,
            ]
        );

        // 4. Verificación y Estandarización de Navegación para Tienda y Sitio
        $existingSite = Site::withoutTrashed()->first();
        if ($existingSite) {
            $siteEstilos = $existingSite->estilos ?? [];
            $needsUpdate = false;

            if (! isset($siteEstilos['seccion_productos']['orden']) || $siteEstilos['seccion_productos']['orden'] === null || $siteEstilos['seccion_productos']['orden'] === '') {
                $siteEstilos['seccion_productos']['orden'] = 3;
                $needsUpdate = true;
            }
            if (! isset($siteEstilos['seccion_marcas']['orden']) || $siteEstilos['seccion_marcas']['orden'] === null || $siteEstilos['seccion_marcas']['orden'] === '') {
                $siteEstilos['seccion_marcas']['orden'] = 4;
                $needsUpdate = true;
            }
            if (! isset($siteEstilos['seccion_servicios']['orden']) || $siteEstilos['seccion_servicios']['orden'] === null || $siteEstilos['seccion_servicios']['orden'] === '') {
                $siteEstilos['seccion_servicios']['orden'] = 6;
                $needsUpdate = true;
            }

            if ($needsUpdate) {
                $existingSite->update(['estilos' => $siteEstilos]);
                $this->info("   • Estilos de orden de navegación estandarizados en el sitio existente: {$existingSite->nombre}");
            }

            // Asegurar que la sección servicios de la plantilla ecomer esté activa con orden oficial
            Seccion::whereHas('plantilla', fn ($q) => $q->where('slug', 'ecomer'))
                ->whereIn('slug', ['servicios', 'servicio'])
                ->update(['activa' => true, 'orden' => 6]);
        }

        // Si ya existe un sitio activo y no se usa --force, omitir la creación de tienda/admin
        if (! $this->option('force') && $existingSite) {
            $this->info("✅ Plantillas y secciones maestras sincronizadas. Tienda existente protegida sin sobreescrituras.");

            return self::SUCCESS;
        }

        // 5. Usuario Administrador Inicial
        $adminName = env('ADMIN_NAME', 'Administrador');
        $host = parse_url(config('app.url', 'http://localhost'), PHP_URL_HOST) ?: 'tienda.com';
        $adminEmail = env('ADMIN_EMAIL', "admin@{$host}");
        $adminPassword = env('ADMIN_PASSWORD', 'Admin2026!');

        $user = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name'              => $adminName,
                'password'          => Hash::make($adminPassword),
                'email_verified_at' => now(),
            ]
        );

        if (! $user->hasRole('super_admin')) {
            $user->assignRole($superAdminRole);
        }

        // 6. Tienda Base (Inventario y Almacén)
        $tiendaSlug = Str::slug($appName) ?: 'tienda-principal';
        $tienda = Tienda::firstOrCreate(
            ['slug' => $tiendaSlug],
            [
                'nombre'      => $appName,
                'descripcion' => 'Tienda y almacén principal de ' . $appName,
                'estado'      => 'activo',
                'moneda_id'   => $soles->id,
            ]
        );

        // 7. Sitio Oficial (Site)
        $siteSlug = Str::slug($appName) ?: 'tienda-principal';
        $site = Site::firstOrCreate(
            ['slug' => $siteSlug],
            [
                'user_id'      => $user->id,
                'plantilla_id' => $plantillaEcomer->id,
                'tienda_id'    => $tienda->id,
                'moneda_id'    => $soles->id,
                'nombre'       => $appName,
                'estado'       => 'publicado',
                'estilos'      => [
                    'color_primario'     => '#1854a4',
                    'color_secundario'   => '#ffffff',
                    'tipografia_titulos' => 'Montserrat',
                    'tipografia_texto'   => 'Montserrat',
                    'catalogo'           => ['activo' => true, 'titulo' => 'Catálogo', 'tipo_filtro' => 'todos'],
                    'redes_sociales'     => ['whatsapp' => '', 'facebook' => '', 'instagram' => ''],
                    'seccion_productos'  => ['orden' => 3],
                    'seccion_marcas'     => ['orden' => 4],
                    'seccion_servicios'  => ['orden' => 6],
                ],
            ]
        );

        // Vincular monedas al sitio
        if (! $site->monedas()->where('monedas.id', $soles->id)->exists()) {
            $site->monedas()->attach($soles->id);
        }
        if (! $site->monedas()->where('monedas.id', $dolares->id)->exists()) {
            $site->monedas()->attach($dolares->id);
        }

        $this->info("✅ Auto-bootstrap completado exitosamente:");
        $this->info("   • Tienda: {$appName} (Slug: {$site->slug})");
        $this->info("   • Admin:  {$adminEmail}");
        $this->info("   • Plantilla: Ecomer (con 17 secciones completas)");

        return self::SUCCESS;
    }

    /**
     * Siembra o sincroniza las secciones y preguntas de una plantilla desde un archivo JSON.
     */
    protected function seedSeccionesDesdeJson(Plantilla $plantilla, string $jsonPath): void
    {
        if (! file_exists($jsonPath)) {
            $this->warn("Archivo no encontrado: {$jsonPath}. Omitiendo siembra de secciones.");
            return;
        }

        $secciones = json_decode(file_get_contents($jsonPath), true);
        if (! is_array($secciones)) {
            return;
        }

        foreach ($secciones as $sec) {
            $seccion = Seccion::firstOrNew(
                [
                    'plantilla_id' => $plantilla->id,
                    'slug'         => $sec['slug'],
                ]
            );

            $seccion->nombre = $sec['nombre'];
            $seccion->orden  = $sec['orden'] ?? 1;
            if (! $seccion->exists) {
                $seccion->activa = $sec['activa'] ?? false;
            } elseif (! empty($sec['activa']) && ! $seccion->activa) {
                $seccion->activa = true;
            }
            $seccion->save();

            if (! empty($sec['preguntas']) && is_array($sec['preguntas'])) {
                foreach ($sec['preguntas'] as $p) {
                    $pregunta = Pregunta::firstOrCreate(
                        [
                            'seccion_id' => $seccion->id,
                            'label'      => $p['label'],
                            'parent_id'  => null,
                        ],
                        [
                            'tipo'       => $p['tipo'] ?? 'texto',
                            'estructura' => $p['estructura'] ?? 'objeto',
                            'orden'      => $p['orden'] ?? 0,
                            'requerida'  => $p['requerida'] ?? false,
                            'ayuda'      => $p['ayuda'] ?? null,
                            'opciones'   => $p['opciones'] ?? null,
                        ]
                    );

                    if (! empty($p['hijos']) && is_array($p['hijos'])) {
                        foreach ($p['hijos'] as $h) {
                            Pregunta::firstOrCreate(
                                [
                                    'seccion_id' => $seccion->id,
                                    'label'      => $h['label'],
                                    'parent_id'  => $pregunta->id,
                                ],
                                [
                                    'tipo'       => $h['tipo'] ?? 'texto',
                                    'estructura' => $h['estructura'] ?? 'objeto',
                                    'orden'      => $h['orden'] ?? 0,
                                    'requerida'  => $h['requerida'] ?? false,
                                    'ayuda'      => $h['ayuda'] ?? null,
                                    'opciones'   => $h['opciones'] ?? null,
                                ]
                            );
                        }
                    }
                }
            }
        }
    }
}
