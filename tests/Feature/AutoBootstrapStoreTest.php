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

        // 4.1 Verificar plantilla Tortas con sus 9 secciones completas
        $plantillaTortas = Plantilla::where('slug', 'tortas')->first();
        $this->assertNotNull($plantillaTortas, 'Debe existir la plantilla tortas');
        $this->assertEquals(9, $plantillaTortas->secciones()->count(), 'La plantilla Tortas debe tener sus 9 secciones');

        // 5. Verificar rol y usuario super_admin
        $this->assertTrue(Role::where('name', 'super_admin')->exists());
        $user = User::first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('super_admin'));

        // 6. Verificar sitio publicado
        $site = Site::where('estado', 'publicado')->first();
        $this->assertNotNull($site);
        $this->assertEquals($plantilla->id, $site->plantilla_id);
        $this->assertEquals($user->id, $site->user_id);
        $this->assertTrue($site->monedas()->where('codigo', 'PEN')->exists());

        // 7. Verificar que la tienda responde HTTP 200 en la ruta pública principal (sin 404)
        $response = $this->get('/Inicio');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('sites/Ecomer/Index'));
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
}

