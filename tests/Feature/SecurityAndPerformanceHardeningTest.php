<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityAndPerformanceHardeningTest extends TestCase
{
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
}

