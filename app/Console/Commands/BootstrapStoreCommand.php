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
    protected $description = 'Inicializa automáticamente el ecosistema esencial de una tienda en una base de datos vacía (Monedas, Plantillas, Roles, Admin y Sitio)';

    /**
     * Ejecuta el comando.
     */
    public function handle(): int
    {
        // 1. Verificación de Idempotencia: Si ya existe un sitio activo, omitir de forma segura
        if (! $this->option('force') && Site::withoutTrashed()->exists()) {
            $this->info('La base de datos ya cuenta con una tienda configurada. Omitiendo auto-bootstrap de forma segura.');

            return self::SUCCESS;
        }

        $appName = config('app.name', 'Mi Tienda');
        $this->info("Iniciando auto-bootstrap para la tienda: {$appName}...");

        // 2. Monedas Oficiales
        $soles = Moneda::firstOrCreate(
            ['codigo' => 'PEN'],
            ['nombre' => 'Soles', 'simbolo' => 'S/', 'activa' => true]
        );

        $dolares = Moneda::firstOrCreate(
            ['codigo' => 'USD'],
            ['nombre' => 'Dólares', 'simbolo' => '$', 'activa' => true]
        );

        // 3. Plantillas Maestras
        // 3.1 Plantilla Universal: Ecomer
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

        // Secciones base del diseño Ecomer
        $seccionesBase = [
            ['slug' => 'Hero', 'nombre' => 'Inicio', 'orden' => 1, 'activa' => true],
            ['slug' => 'nosotros', 'nombre' => 'Nosotros', 'orden' => 2, 'activa' => true],
            ['slug' => 'productos', 'nombre' => 'Productos', 'orden' => 3, 'activa' => true],
            ['slug' => 'marcas', 'nombre' => 'Marcas', 'orden' => 4, 'activa' => true],
            ['slug' => 'contacto', 'nombre' => 'Contacto', 'orden' => 5, 'activa' => true],
        ];

        foreach ($seccionesBase as $sec) {
            $seccion = Seccion::firstOrCreate(
                ['plantilla_id' => $plantillaEcomer->id, 'slug' => $sec['slug']],
                ['nombre' => $sec['nombre'], 'orden' => $sec['orden'], 'activa' => $sec['activa']]
            );

            if (in_array(strtolower($sec['slug']), ['hero', 'inicio'])) {
                Pregunta::firstOrCreate(
                    ['seccion_id' => $seccion->id, 'label' => 'Titulo'],
                    ['tipo' => 'texto', 'estructura' => 'objeto', 'orden' => 1, 'requerida' => false]
                );
                Pregunta::firstOrCreate(
                    ['seccion_id' => $seccion->id, 'label' => 'Subtitulo'],
                    ['tipo' => 'texto', 'estructura' => 'objeto', 'orden' => 2, 'requerida' => false]
                );
            }
        }

        // 3.2 Plantilla Tortas
        Plantilla::firstOrCreate(
            ['slug' => 'tortas'],
            [
                'tipo'        => 'ecommerce',
                'nombre'      => 'Tortas',
                'descripcion' => 'Catálogo y pedidos para pastelería y repostería.',
                'estilos'     => ['color_primario' => '#f43f5e', 'color_secundario' => '#ffffff'],
                'activa'      => true,
            ]
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

        // 4. Roles Administrativos (Filament Shield)
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web']
        );

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
        $this->info("   • Plantilla: Ecomer (publicada)");

        return self::SUCCESS;
    }
}

