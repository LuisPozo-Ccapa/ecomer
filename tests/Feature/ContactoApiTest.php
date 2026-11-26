<?php

namespace Tests\Feature;

use App\Models\Plantilla;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_valida_campos_requeridos_en_contacto(): void
    {
        $response = $this->postJson('/api/contacto', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre', 'telefono', 'mensaje']);
    }

    public function test_envia_correo_de_contacto_exitosamente(): void
    {
        Mail::fake();
        Config::set('mail.from.address', 'admin@magnusonline.com.pe');
        Config::set('mail.from.name', 'Magnus Online');

        $user = User::factory()->create();
        $plantilla = Plantilla::create([
            'slug' => 'ecomer',
            'tipo' => 'ecommerce',
            'nombre' => 'Ecomer',
        ]);

        $site = Site::create([
            'user_id' => $user->id,
            'plantilla_id' => $plantilla->id,
            'nombre' => 'Magnus Online',
            'slug' => 'magnus',
            'estado' => 'publicado',
        ]);

        $payload = [
            'site_id' => $site->id,
            'nombre' => 'Carlos Mendoza',
            'empresa' => 'Distribuidora Lima S.A.C.',
            'telefono' => '998877665',
            'email' => 'carlos@empresa.com',
            'producto' => 'Zapatillas al por mayor',
            'mensaje' => 'Solicito cotización de 50 pares surtidos.',
        ];

        $response = $this->postJson('/api/contacto', $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Cotización enviada exitosamente.',
            ]);
    }
}
