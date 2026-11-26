<?php

namespace Tests\Feature;

use App\Services\TikTokCoverService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TikTokSecurityTest extends TestCase
{
    public function test_rechaza_id_de_video_invalido_con_error_422(): void
    {
        $response = $this->getJson('/api/v1/tiktok-oembed?id=../../traversal-test');

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'El identificador de vídeo no es válido.'
            ]);
    }

    public function test_rechaza_id_con_caracteres_no_numericos(): void
    {
        $response = $this->getJson('/api/v1/tiktok-oembed?id=malicious_payload');

        $response->assertStatus(422);
    }

    public function test_get_or_download_cover_retorna_null_con_path_traversal(): void
    {
        Storage::fake('public');

        $result = TikTokCoverService::getOrDownloadCover('../../secret_file');

        $this->assertNull($result);
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_delete_cover_retorna_false_con_path_traversal(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('secret.txt', 'sensible');

        $result = TikTokCoverService::deleteCover('../../secret');

        $this->assertFalse($result);
        Storage::disk('public')->assertExists('secret.txt');
    }
}
