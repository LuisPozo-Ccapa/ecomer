<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Plantilla;
use App\Models\Producto;
use App\Models\Site;
use App\Models\User;
use App\Observers\ProductoObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityAndPerformanceHardeningTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Fase 1: Verifica que cualquier intento de escape de directorio con '..' sea bloqueado con 404.
     */
    public function test_storage_bloquea_path_traversal_con_puntos_consecutivos(): void
    {
        $response1 = $this->get('/storage/../../.env');
        $response1->assertStatus(404);

        $response2 = $this->get('/storage/productos/../../../.env');
        $response2->assertStatus(404);

        $response3 = $this->get('/storage/..%2F..%2F.env');
        $response3->assertStatus(404);
    }

    /**
     * Fase 1: Verifica que extensiones no autorizadas (.env, .php, .sql, .log) sean bloqueadas con 404.
     */
    public function test_storage_bloquea_extensiones_no_autorizadas(): void
    {
        $response1 = $this->get('/storage/config.env');
        $response1->assertStatus(404);

        $response2 = $this->get('/storage/shell.php');
        $response2->assertStatus(404);

        $response3 = $this->get('/storage/dump.sql');
        $response3->assertStatus(404);
    }

    /**
     * Fase 1: Verifica que una imagen autorizada dentro de storage/app/public sea servida correctamente con 200 OK.
     */
    public function test_storage_sirve_archivo_valido_autorizado(): void
    {
        $filename = 'test_security_fase1.webp';
        Storage::disk('public')->put($filename, 'dummy-image-binary-data');

        try {
            $response = $this->get('/storage/' . $filename);
            $response->assertStatus(200);
            $response->assertHeader('Cache-Control');
        } finally {
            Storage::disk('public')->delete($filename);
        }
    }

    /**
     * Fase 1: Verifica que una imagen inexistente sirva el placeholder SVG seguro sin romper la página.
     */
    public function test_storage_sirve_placeholder_para_imagen_inexistente(): void
    {
        $response = $this->get('/storage/productos/imagen_fantasma_segura.png');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $this->assertStringContainsString('Imagen no encontrada', $response->getContent());
    }

    /**
     * Fase 2: Verifica que el rate limiting en /api/contacto bloquee ráfagas excesivas (máximo 10 por minuto).
     */
    public function test_rate_limiting_en_api_contacto_bloquea_rafagas_excesivas(): void
    {
        $payload = [
            'nombre'   => 'Cliente Normal',
            'telefono' => '999888777',
            'mensaje'  => 'Consulta de prueba de frecuencia.',
        ];

        // 10 peticiones deben ser procesadas
        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/api/contacto', $payload);
            $this->assertNotEquals(429, $response->getStatusCode(), "La petición {$i} no debió ser bloqueada.");
        }

        // La petición número 11 debe ser rechazada con 429 Too Many Requests
        $response11 = $this->postJson('/api/contacto', $payload);
        $response11->assertStatus(429);
    }

    /**
     * Fase 2: Verifica que el honeypot en /api/contacto descarte bots silenciosamente sin escribir en BD.
     */
    public function test_honeypot_en_contacto_descarta_bot_silenciosamente_sin_guardar_en_bd(): void
    {
        $response = $this->postJson('/api/contacto', [
            '_hp_website' => 'http://spam-promoter-bot.com/buy-now',
            'nombre'      => 'Spam Bot Automático',
            'telefono'    => '123456789',
            'mensaje'     => 'Texto basura publicitario masivo.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // La base de datos NO debe contener el registro basura del bot
        $this->assertDatabaseMissing('contactos', [
            'nombre' => 'Spam Bot Automático',
        ]);
    }

    /**
     * Fase 2: Verifica que el rate limiting en /libro-de-reclamaciones limite a máximo 5 quejas por minuto.
     */
    public function test_rate_limiting_en_libro_de_reclamaciones_bloquea_rafagas_excesivas(): void
    {
        // 5 peticiones permitidas
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/libro-de-reclamaciones', []);
            $this->assertNotEquals(429, $response->getStatusCode(), "La queja {$i} no debió dar 429.");
        }

        // La sexta petición debe dar 429
        $response6 = $this->postJson('/libro-de-reclamaciones', []);
        $response6->assertStatus(429);
    }

    /**
     * Fase 3: Verifica que el catálogo PDF se sirva desde la caché estática si existe en disco.
     */
    public function test_catalogo_pdf_se_sirve_desde_cache_estatica(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $plantilla = Plantilla::create([
            'nombre' => 'Plantilla Base',
            'slug'   => 'plantilla-base',
            'activo' => true,
        ]);
        $site = Site::create([
            'user_id'      => $user->id,
            'plantilla_id' => $plantilla->id,
            'nombre'       => 'Magnus Store',
            'slug'         => 'magnus-store',
            'estado'       => 'publicado',
        ]);

        $cacheRelPath = 'catalogos/catalogo_site_' . $site->id . '.pdf';
        $dummyContent = '%PDF-1.4 dummy pre-rendered catalog binary content';
        Storage::disk('public')->put($cacheRelPath, $dummyContent);

        $response = $this->get('/catalogo/descargar-pdf');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Fase 3: Verifica que al modificar o eliminar un producto, la caché del catálogo PDF se invalida automáticamente.
     */
    public function test_modificar_o_eliminar_producto_invalida_cache_del_catalogo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $plantilla = Plantilla::create([
            'nombre' => 'Plantilla Base',
            'slug'   => 'plantilla-base',
            'activo' => true,
        ]);
        $site = Site::create([
            'user_id'      => $user->id,
            'plantilla_id' => $plantilla->id,
            'nombre'       => 'Magnus Store',
            'slug'         => 'magnus-store',
            'estado'       => 'publicado',
        ]);
        $categoria = Categoria::create(['nombre' => 'General', 'slug' => 'general', 'activo' => true]);

        // Simular que el catálogo está en caché
        $cacheRelPath = 'catalogos/catalogo_site_' . $site->id . '.pdf';
        Storage::disk('public')->put($cacheRelPath, 'dummy content');
        $this->assertTrue(Storage::disk('public')->exists($cacheRelPath));

        // 1. Guardar un producto nuevo debe invalidar la caché
        $producto = Producto::create([
            'site_id'      => $site->id,
            'categoria_id' => $categoria->id,
            'nombre'       => 'Audífonos Bluetooth',
            'slug'         => 'audifonos-bluetooth',
            'sku'          => 'AUD-001',
            'precio'       => 99.90,
            'activo'       => true,
        ]);
        $this->assertFalse(Storage::disk('public')->exists($cacheRelPath), 'Guardar un producto debió purgar la caché.');

        // Re-crear la caché
        Storage::disk('public')->put($cacheRelPath, 'dummy content 2');
        $this->assertTrue(Storage::disk('public')->exists($cacheRelPath));

        // 2. Eliminar (soft delete) el producto debe invalidar la caché
        $producto->delete();
        $this->assertFalse(Storage::disk('public')->exists($cacheRelPath), 'Eliminar un producto debió purgar la caché.');
    }

    /**
     * Fase 3: Verifica que al actualizar el sitio (estilos o configuración), la caché del catálogo PDF se purga automáticamente.
     */
    public function test_actualizar_sitio_invalida_cache_del_catalogo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $plantilla = Plantilla::create([
            'nombre' => 'Plantilla Base',
            'slug'   => 'plantilla-base',
            'activo' => true,
        ]);
        $site = Site::create([
            'user_id'      => $user->id,
            'plantilla_id' => $plantilla->id,
            'nombre'       => 'Magnus Store',
            'slug'         => 'magnus-store',
            'estado'       => 'publicado',
        ]);

        $cacheRelPath = 'catalogos/catalogo_site_' . $site->id . '.pdf';
        Storage::disk('public')->put($cacheRelPath, 'dummy content');
        $this->assertTrue(Storage::disk('public')->exists($cacheRelPath));

        // Al guardar cambios en el sitio, se debe disparar el evento booted y purgar la caché
        $site->update(['nombre' => 'Magnus Store Updated']);
        $this->assertFalse(Storage::disk('public')->exists($cacheRelPath), 'Actualizar el sitio debió purgar la caché.');
    }

    /**
     * Fase 3: Verifica que el endpoint /catalogo/descargar-pdf tenga rate limiting (máximo 6 peticiones por minuto).
     */
    public function test_rate_limiting_en_catalogo_descargar_pdf_bloquea_rafagas_excesivas(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $plantilla = Plantilla::create([
            'nombre' => 'Plantilla Base',
            'slug'   => 'plantilla-base',
            'activo' => true,
        ]);
        $site = Site::create([
            'user_id'      => $user->id,
            'plantilla_id' => $plantilla->id,
            'nombre'       => 'Magnus Store',
            'slug'         => 'magnus-store',
            'estado'       => 'publicado',
        ]);

        $cacheRelPath = 'catalogos/catalogo_site_' . $site->id . '.pdf';
        Storage::disk('public')->put($cacheRelPath, 'dummy-pdf');

        // 6 peticiones permitidas
        for ($i = 0; $i < 6; $i++) {
            $response = $this->get('/catalogo/descargar-pdf');
            $this->assertNotEquals(429, $response->getStatusCode(), "La petición de catálogo {$i} no debió dar 429.");
        }

        // La séptima petición debe dar 429 Too Many Requests
        $response7 = $this->get('/catalogo/descargar-pdf');
        $response7->assertStatus(429);
    }
}

