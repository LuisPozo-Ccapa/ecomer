<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TikTokCoverService
{
    /**
     * Directorio relativo dentro del disco 'public' para almacenar las portadas de TikTok.
     */
    protected const STORAGE_DIR = 'tiktok-covers';

    /**
     * Obtiene la URL pública de la portada para un IdVideo dado.
     * Si no existe localmente, la descarga desde TikTok, la optimiza con ImageOptimizerService y la almacena.
     *
     * @param string|int $idVideo
     * @return string|null URL pública local (ej: /storage/tiktok-covers/123456.jpg) o null si falla
     */
    public static function getOrDownloadCover(string|int $idVideo): ?string
    {
        $cleanId = trim((string) $idVideo);
        if ($cleanId === '') {
            return null;
        }

        $disk = Storage::disk('public');
        $fileName = "{$cleanId}.jpg";
        $relativePath = self::STORAGE_DIR . "/{$fileName}";

        // 1. Si ya existe en storage público, retornar URL directa
        if ($disk->exists($relativePath)) {
            return $disk->url($relativePath);
        }

        // 2. Extraer la URL de la portada desde TikTok
        $ogImageUrl = self::fetchOgImageUrl($cleanId);
        if (! $ogImageUrl) {
            return null;
        }

        // 3. Descargar los bytes de la imagen a un archivo temporal
        $tempPath = sys_get_temp_dir() . '/tiktok_raw_' . md5($cleanId . microtime(true)) . '.jpg';

        try {
            $imageResponse = Http::withoutVerifying()
                ->timeout(15)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                    'Referer' => 'https://www.tiktok.com/',
                ])
                ->get($ogImageUrl);

            if (! $imageResponse->successful() || strlen($imageResponse->body()) < 500) {
                Log::warning("TikTokCoverService: No se pudo descargar la imagen para el video {$cleanId} desde {$ogImageUrl}");
                return null;
            }

            file_put_contents($tempPath, $imageResponse->body());

            // 4. Optimizar con ImageOptimizerService (ancho máx 800px, máx 150 KB)
            if (file_exists($tempPath) && filesize($tempPath) > 0) {
                try {
                    ImageOptimizerService::optimizeImage($tempPath, maxWidth: 800, maxSizeKb: 150);
                } catch (\Throwable $optError) {
                    Log::warning("TikTokCoverService: Falló optimización con ImageOptimizerService: " . $optError->getMessage());
                }

                // 5. Guardar en Storage público
                $optimizedBytes = file_get_contents($tempPath);
                $disk->put($relativePath, $optimizedBytes);

                return $disk->url($relativePath);
            }
        } catch (\Throwable $e) {
            Log::error("TikTokCoverService: Error al procesar portada para {$cleanId}: " . $e->getMessage());
        } finally {
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }

        return null;
    }

    /**
     * Elimina del storage cualquier portada perteneciente a un IdVideo.
     *
     * @param string|int $idVideo
     * @return bool
     */
    public static function deleteCover(string|int $idVideo): bool
    {
        $cleanId = trim((string) $idVideo);
        if ($cleanId === '') {
            return false;
        }

        $disk = Storage::disk('public');
        $extensions = ['jpg', 'jpeg', 'webp', 'png'];
        $deleted = false;

        foreach ($extensions as $ext) {
            $path = self::STORAGE_DIR . "/{$cleanId}.{$ext}";
            if ($disk->exists($path)) {
                $disk->delete($path);
                $deleted = true;
            }
        }

        return $deleted;
    }

    /**
     * Sincroniza un conjunto de videos de TikTok:
     * - Identifica videos eliminados o con ID cambiado y elimina sus portadas antiguas del storage.
     * - Para cada video nuevo/actual, descarga y optimiza su portada y le inyecta la propiedad 'imagen'.
     *
     * @param array $newVideos Estructura de videos del formulario/BD (array u objeto indexado)
     * @param array|null $previousVideos Estructura de videos previa para detectar eliminaciones
     * @return array Estructura de videos actualizada con las rutas de imagen locales
     */
    public static function syncVideosCovers(array $newVideos, ?array $previousVideos = null): array
    {
        $oldIds = [];
        if (! empty($previousVideos)) {
            foreach ($previousVideos as $v) {
                $id = self::extractVideoId($v);
                if ($id) {
                    $oldIds[$id] = true;
                }
            }
        }

        $currentIds = [];
        $syncedVideos = [];

        foreach ($newVideos as $key => $videoItem) {
            if (! is_array($videoItem)) {
                $syncedVideos[$key] = $videoItem;
                continue;
            }

            $idVideo = self::extractVideoId($videoItem);
            if ($idVideo) {
                $currentIds[$idVideo] = true;

                // Si no tiene imagen, o es externa, o no coincide con el nuevo idVideo, asegurar copia local correcta
                $currentImg = $videoItem['imagen'] ?? $videoItem['foto'] ?? $videoItem['cover'] ?? null;
                $needsLocalCover = empty($currentImg) 
                    || ! str_contains((string) $currentImg, self::STORAGE_DIR)
                    || ! str_contains((string) $currentImg, (string) $idVideo);

                if ($needsLocalCover) {
                    $localUrl = self::getOrDownloadCover($idVideo);
                    if ($localUrl) {
                        $videoItem['imagen'] = $localUrl;
                    }
                }
            }

            $syncedVideos[$key] = $videoItem;
        }

        // Eliminar portadas antiguas que ya no están presentes en la lista actual
        foreach (array_keys($oldIds) as $oldId) {
            if (! isset($currentIds[$oldId])) {
                self::deleteCover($oldId);
            }
        }

        return $syncedVideos;
    }

    /**
     * Extrae el ID numérico de un video a partir de su objeto o URL.
     */
    public static function extractVideoId(mixed $videoItem): ?string
    {
        if (! is_array($videoItem)) {
            return null;
        }

        $id = $videoItem['IdVideo'] ?? $videoItem['idVideo'] ?? $videoItem['video_id'] ?? null;
        if (! empty($id)) {
            return trim((string) $id);
        }

        $url = $videoItem['url'] ?? $videoItem['link'] ?? $videoItem['urlVideo'] ?? null;
        if (! empty($url) && preg_match('/\/video\/(\d+)/', (string) $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Consulta la página del video en TikTok emulando un crawler/bot para obtener el og:image.
     */
    protected static function fetchOgImageUrl(string $idVideo): ?string
    {
        $url = "https://www.tiktok.com/@tiktok/video/{$idVideo}";

        try {
            $response = Http::withoutVerifying()
                ->timeout(10)
                ->withHeaders([
                    // User-Agent de Facebook external hit / Twitter bot permite recibir el HTML con etiquetas OpenGraph
                    'User-Agent' => 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'es-ES,es;q=0.9,en;q=0.8',
                ])
                ->get($url);

            if ($response->successful()) {
                $html = $response->body();

                // Buscar <meta property="og:image" content="..." />
                if (preg_match('/<meta\s+property=["\']og:image["\']\s+content=["\']([^"\']+)["\']/i', $html, $matches)) {
                    return html_entity_decode($matches[1]);
                }

                // Fallback: buscar "cover":"..." en el JSON embebido
                if (preg_match('/"cover":\s*"(https?:[^"]+)"/i', $html, $matches)) {
                    return stripslashes($matches[1]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning("TikTokCoverService: Error al consultar og:image para {$idVideo}: " . $e->getMessage());
        }

        return null;
    }
}
