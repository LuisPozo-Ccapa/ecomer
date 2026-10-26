<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\TikTokCoverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TikTokApiController extends Controller
{
    /**
     * Proxy request to TikTok oEmbed endpoint with local storage cover caching.
     */
    public function oembed(Request $request): JsonResponse
    {
        $id = $request->query('id');
        $url = $request->query('url');

        if (! $id && $url && preg_match('/\/video\/(\d+)/', $url, $matches)) {
            $id = $matches[1];
        }

        if (! $url && $id) {
            $url = "https://www.tiktok.com/@tiktok/video/{$id}";
        }

        if (! $url && ! $id) {
            return response()->json([
                'success' => false,
                'message' => 'El parámetro URL o ID de vídeo es requerido.'
            ], 400);
        }

        // 1. Obtener o descargar la portada local optimizada con TikTokCoverService
        $localCoverUrl = null;
        if ($id) {
            $localCoverUrl = TikTokCoverService::getOrDownloadCover($id);
        }

        // 2. Consultar oEmbed para obtener título y autor si están disponibles
        $cacheKey = 'tiktok_oembed_meta_' . md5($url);
        $meta = Cache::remember($cacheKey, 86400, function () use ($url) {
            try {
                $response = Http::withoutVerifying()
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                        'Accept' => 'application/json',
                        'Accept-Language' => 'es-ES,es;q=0.9,en;q=0.8',
                    ])
                    ->timeout(6)
                    ->get('https://www.tiktok.com/oembed', [
                        'url' => $url,
                    ]);

                if ($response->successful()) {
                    $json = $response->json();
                    if (is_array($json)) {
                        return $json;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("TikTokApiController: Error consultando oEmbed: " . $e->getMessage());
            }

            return null;
        });

        $finalThumbnail = $localCoverUrl ?: ($meta['thumbnail_url'] ?? null);

        if (! $finalThumbnail && ! $meta) {
            return response()->json([
                'success' => false,
                'thumbnail_url' => null,
                'author_name' => null,
                'author_unique_id' => null,
                'title' => null,
            ], 200, [
                'Access-Control-Allow-Origin' => '*',
            ]);
        }

        $responseData = array_merge($meta ?: [], [
            'success' => true,
            'thumbnail_url' => $finalThumbnail,
        ]);

        return response()->json($responseData, 200, [
            'Access-Control-Allow-Origin' => '*',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
