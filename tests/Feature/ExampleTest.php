<?php

namespace Tests\Feature;

use App\Models\Plantilla;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_retorna_404_cuando_no_hay_sitios_publicados(): void
    {
        $response = $this->get('/');

        $response->assertNotFound();
    }

    public function test_retorna_inicio_cuando_hay_sitio_publicado(): void
    {
        $user = User::factory()->create();

        $plantilla = Plantilla::create([
            'slug' => 'ecomer',
            'tipo' => 'ecommerce',
            'nombre' => 'Ecomer',
        ]);
        $plantilla->secciones()->create(['slug' => 'inicio', 'nombre' => 'Inicio', 'orden' => 1]);

        Site::create([
            'user_id' => $user->id,
            'plantilla_id' => $plantilla->id,
            'nombre' => 'Mi Tienda',
            'slug' => 'mi-tienda',
            'estado' => 'publicado',
        ]);

        $response = $this->get('/');

        $response->assertOk();
    }
}
