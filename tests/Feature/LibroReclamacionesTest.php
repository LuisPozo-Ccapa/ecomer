<?php

namespace Tests\Feature;

use App\Mail\ReclamacionRegistradaMailable;
use App\Models\Plantilla;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LibroReclamacionesTest extends TestCase
{
    use RefreshDatabase;

    private function crearSitio(): Site
    {
        $user = User::factory()->create();
        $plantilla = Plantilla::create([
            'slug' => 'ecomer',
            'tipo' => 'ecommerce',
            'nombre' => 'Ecomer',
        ]);

        return Site::create([
            'user_id' => $user->id,
            'plantilla_id' => $plantilla->id,
            'nombre' => 'Mi Tienda',
            'slug' => 'mi-tienda',
            'estado' => 'publicado',
        ]);
    }

    public function test_valida_campos_requeridos(): void
    {
        $response = $this->postJson('/libro-de-reclamaciones', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonValidationErrors([
                'site_id',
                'tipo',
                'tipo_documento',
                'numero_documento',
                'nombres',
                'email',
                'telefono',
                'direccion',
                'descripcion_bien',
                'detalle',
                'pedido',
            ]);
    }

    public function test_registra_reclamacion_con_exito_y_genera_codigo(): void
    {
        Mail::fake();

        $site = $this->crearSitio();

        $payload = [
            'site_id' => $site->id,
            'tipo' => 'reclamo',
            'tipo_documento' => 'DNI',
            'numero_documento' => '12345678',
            'nombres' => 'Juan Carlos',
            'apellidos' => 'Pérez Quispe',
            'email' => 'juan.perez@example.com',
            'telefono' => '987654321',
            'direccion' => 'Av. Principal 123, Lima',
            'tipo_bien' => 'producto',
            'monto_reclamado' => 150.50,
            'descripcion_bien' => 'Zapatillas deportivas talla 41',
            'detalle' => 'El producto llegó con la suela despegada.',
            'pedido' => 'Cambio de producto o reembolso del dinero.',
        ];

        $response = $this->postJson('/libro-de-reclamaciones', $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Su reclamación ha sido registrada correctamente.',
            ])
            ->assertJsonPath('data.tipo', 'reclamo');

        $this->assertDatabaseHas('libro_reclamaciones', [
            'site_id' => $site->id,
            'tipo' => 'reclamo',
            'numero_documento' => '12345678',
            'email' => 'juan.perez@example.com',
            'estado' => 'pendiente',
        ]);

        Mail::assertSent(ReclamacionRegistradaMailable::class);
    }
}

