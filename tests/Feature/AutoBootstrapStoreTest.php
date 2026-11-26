<?php

namespace Tests\Feature;

use App\Models\Moneda;
use App\Models\Plantilla;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AutoBootstrapStoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba que el comando store:bootstrap configure todo el ecosistema en una BD limpia.
     */
    public function test_bootstrap_inicializa_tienda_completa_en_base_de_datos_limpia(): void
    {
        // 1. Asegurar que la BD empieza completamente limpia
        $this->assertEquals(0, Site::count());
        $this->assertEquals(0, User::count());

        // 2. Ejecutar auto-bootstrap
        $exitCode = Artisan::call('store:bootstrap');
        $this->assertEquals(0, $exitCode);

        // 3. Verificar monedas creadas
        $this->assertTrue(Moneda::where('codigo', 'PEN')->exists(), 'Debe existir la moneda Soles (PEN)');
        $this->assertTrue(Moneda::where('codigo', 'USD')->exists(), 'Debe existir la moneda Dólares (USD)');

        // 4. Verificar plantilla Ecomer con sus 17 secciones completas
        $plantilla = Plantilla::where('slug', 'ecomer')->first();
        $this->assertNotNull($plantilla, 'Debe existir la plantilla ecomer');
        $this->assertEquals('ecommerce', $plantilla->tipo);
        $this->assertEquals(17, $plantilla->secciones()->count(), 'La plantilla Ecomer debe tener sus 17 secciones');
        $this->assertTrue($plantilla->secciones()->where('slug', 'Hero')->exists());
        $this->assertTrue($plantilla->secciones()->where('slug', 'sectores')->exists());
        $this->assertTrue($plantilla->secciones()->where('slug', 'faq')->exists());

        // 4.0.1 Verificar que Servicios -> Hero -> Imagenes cuenta con sus 3 subcampos (Imagen_pc, Imagen_tablet, Imagen_cel)
        $seccionServicios = $plantilla->secciones()->where('slug', 'servicios')->first();
        $this->assertNotNull($seccionServicios, 'La plantilla Ecomer debe tener la sección servicios');
        $heroPregunta = $seccionServicios->preguntas()->where('label', 'Hero')->whereNull('parent_id')->first();
        $this->assertNotNull($heroPregunta, 'Servicios debe tener la pregunta raíz Hero');
        $imagenesPregunta = \App\Models\Pregunta::where('parent_id', $heroPregunta->id)->where('label', 'Imagenes')->first();
        $this->assertNotNull($imagenesPregunta, 'Hero debe tener la pregunta Imagenes');
        $this->assertTrue(
            \App\Models\Pregunta::where('parent_id', $imagenesPregunta->id)->where('label', 'Imagen_pc')->exists(),
            'Imagenes debe contener el campo Imagen_pc'
        );
        $this->assertTrue(
            \App\Models\Pregunta::where('parent_id', $imagenesPregunta->id)->where('label', 'Imagen_tablet')->exists(),
            'Imagenes debe contener el campo Imagen_tablet'
        );
        $this->assertTrue(
            \App\Models\Pregunta::where('parent_id', $imagenesPregunta->id)->where('label', 'Imagen_cel')->exists(),
            'Imagenes debe contener el campo Imagen_cel'
        );

        // 4.1 Verificar plantilla Tortas con sus 9 secciones completas
        $plantillaTortas = Plantilla::where('slug', 'tortas')->first();
        $this->assertNotNull($plantillaTortas, 'Debe existir la plantilla tortas');
        $this->assertEquals(9, $plantillaTortas->secciones()->count(), 'La plantilla Tortas debe tener sus 9 secciones');

        // 5. Verificar rol y usuario super_admin
        $this->assertTrue(Role::where('name', 'super_admin')->exists());
        $user = User::first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('super_admin'));

        // 6. Verificar sitio publicado y estilos de orden estándar
        $site = Site::where('estado', 'publicado')->first();
        $this->assertNotNull($site);
        $this->assertEquals($plantilla->id, $site->plantilla_id);
        $this->assertEquals($user->id, $site->user_id);
        $this->assertTrue($site->monedas()->where('codigo', 'PEN')->exists());
        $this->assertEquals(3, $site->estilos['seccion_productos']['orden'] ?? null);
        $this->assertEquals(4, $site->estilos['seccion_marcas']['orden'] ?? null);
        $this->assertEquals(6, $site->estilos['seccion_servicios']['orden'] ?? null);

        // 7. Verificar que la tienda responde HTTP 200 en la ruta pública principal con el orden oficial
        $response = $this->get('/Inicio');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('sites/Ecomer/Index')
            ->has('secciones')
            ->where('secciones.0.slug', 'Hero')
            ->where('secciones.1.slug', 'nosotros')
            ->where('secciones.2.slug', 'productos')
        );
    }

    /**
     * Prueba que el comando sea estrictamente idempotente y no altere datos si la tienda ya existe.
     */
    public function test_bootstrap_es_idempotente_y_no_altera_tienda_existente(): void
    {
        // Primera ejecución: inicializar
        Artisan::call('store:bootstrap');
        $site = Site::first();
        $siteCountInitial = Site::count();
        $userCountInitial = User::count();

        // Modificar deliberadamente el nombre de la tienda para simular personalización del cliente
        $site->update(['nombre' => 'Nombre Personalizado Por El Cliente']);

        // Segunda ejecución: debe omitir y no tocar nada
        $exitCode = Artisan::call('store:bootstrap');
        $this->assertEquals(0, $exitCode);

        // Los conteos deben ser idénticos
        $this->assertEquals($siteCountInitial, Site::count());
        $this->assertEquals($userCountInitial, User::count());

        // El nombre personalizado no debe haber sido sobrescrito
        $this->assertEquals('Nombre Personalizado Por El Cliente', Site::first()->nombre);
    }

    /**
     * Prueba que las secciones del nav se entreguen en el orden estándar oficial.
     */
    public function test_secciones_nav_siguen_orden_estandar_oficial(): void
    {
        Artisan::call('store:bootstrap');

        $site = Site::first();
        // Crear una marca activa vinculada a la plantilla
        $marca = \App\Models\Marca::create([
            'titulo' => 'Marca Demo',
            'slug'   => 'marca-demo',
            'activa' => true,
        ]);
        $site->marcas()->attach($marca->id);

        $response = $this->get('/Inicio');
        $response->assertStatus(200);

        $response->assertInertia(function ($page) {
            $secciones = $page->toArray()['props']['secciones'] ?? [];
            $slugs = array_map(fn ($s) => $s['slug'], $secciones);

            // Filtrar solo las secciones que van al nav (excluyendo catalogo que es modal/flotante)
            $navSlugs = array_values(array_filter($slugs, fn ($s) => $s !== 'catalogo'));

            // El orden estándar debe ser: Hero (Inicio) -> nosotros -> productos -> marcas -> liquidación -> servicios -> contacto
            $this->assertEquals(['Hero', 'nosotros', 'productos', 'marcas', 'liquidación', 'servicios', 'contacto'], $navSlugs);
        });
    }
}

