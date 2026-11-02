<?php

use App\Http\Controllers\Api\v1\PlantillaApiController;
use App\Http\Controllers\Api\v1\ProductoApiController;
use App\Http\Controllers\Api\v1\SiteApiController;
use App\Http\Controllers\Api\v1\TikTokApiController;
use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

Route::options('{any}', function () {
    return response()->json([], 200, [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers' => '*',
    ]);
})->where('any', '.*');

Route::post('/contacto', function (Request $request) {
    $validated = $request->validate([
        'nombre'       => 'required|string|max:255',
        'empresa'      => 'nullable|string|max:255',
        'telefono'     => 'required|string|max:50',
        'email'        => 'nullable|email|max:255',
        'sector'       => 'nullable|string|max:100',
        'producto'     => 'nullable|string|max:255',
        'mensaje'      => 'required|string|max:5000',
        'site_id'      => 'nullable|integer',
        'sitio_nombre' => 'nullable|string|max:255',
        'sitio_logo'   => 'nullable|string|max:1000',
    ]);

    $site = !empty($validated['site_id']) ? \App\Models\Site::with(['respuestas.pregunta'])->find($validated['site_id']) : null;
    $sitioNombre = $validated['sitio_nombre'] ?? $site?->nombre ?? config('app.name');
    $destinatario = $site?->email ?: (config('mail.from.address') ?: env('MAIL_FROM_ADDRESS'));

    if (!$destinatario) {
        return response()->json(['success' => false, 'error' => 'No se ha configurado la dirección de correo receptora.'], 500);
    }

    // Logo dinámico del CMS según el proyecto
    $sitioLogo = $validated['sitio_logo'] ?? null;
    if (!$sitioLogo && $site) {
        $sitioLogo = $site->imagen;
        if (!$sitioLogo && $site->relationLoaded('respuestas')) {
            $logoRespuesta = $site->respuestas->first(function ($r) {
                $lbl = strtolower($r->pregunta?->label ?? '');
                return $lbl === 'logo' || $lbl === 'logo_nav' || str_contains($lbl, 'logo');
            });
            $sitioLogo = $logoRespuesta?->valor;
        }
        if (!$sitioLogo && !empty($site->estilos['logo'])) {
            $sitioLogo = $site->estilos['logo'];
        }
    }

    // Resolver ruta física local y URL accesible
    $sitioLogoPath = null;
    if ($sitioLogo) {
        $cleanPath = preg_replace('#^https?://[^/]+/#i', '', $sitioLogo);
        $cleanPath = ltrim($cleanPath, '/');
        if (str_starts_with($cleanPath, 'storage/')) {
            $cleanPath = substr($cleanPath, 8);
        }

        $possibleStoragePath = storage_path('app/public/' . $cleanPath);
        $possiblePublicPath = public_path('storage/' . $cleanPath);

        if (file_exists($possibleStoragePath)) {
            $sitioLogoPath = $possibleStoragePath;
        } elseif (file_exists($possiblePublicPath)) {
            $sitioLogoPath = $possiblePublicPath;
        }

        if (!str_starts_with($sitioLogo, 'http://') && !str_starts_with($sitioLogo, 'https://')) {
            $sitioLogo = asset('storage/' . ltrim($sitioLogo, '/'));
        }
    }

    $sitioEstilos = is_array($site?->estilos) ? $site->estilos : (is_string($site?->estilos) ? json_decode($site->estilos, true) : []);
    $sitioDireccion = $sitioEstilos['direccion'] ?? null;

    $folio = 'COT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
    $asunto = '[Cotización Web] ' . (!empty($validated['producto']) ? $validated['producto'] : 'Consulta General') . ' — ' . (!empty($validated['empresa']) ? $validated['empresa'] : $validated['nombre']);

    try {
        Mail::send('emails.contacto', [
            'folio'           => $folio,
            'nombre'          => $validated['nombre'],
            'empresa'         => $validated['empresa'] ?? null,
            'telefono'        => $validated['telefono'],
            'email'           => $validated['email'] ?? null,
            'sector'          => $validated['sector'] ?? null,
            'producto'        => $validated['producto'] ?? null,
            'mensaje'         => $validated['mensaje'],
            'sitio_nombre'    => $sitioNombre,
            'sitio_logo'      => $sitioLogo,
            'sitio_logo_path' => $sitioLogoPath,
            'sitio_direccion' => $sitioDireccion,
            'fecha_envio'     => now()->translatedFormat('d \d\e F \d\e Y, H:i') . ' hrs',
        ], function ($msg) use ($destinatario, $asunto, $validated, $sitioNombre) {
            $msg->from(config('mail.from.address'), $sitioNombre)
                ->to($destinatario)
                ->subject($asunto);

            if (!empty($validated['email'])) {
                $msg->replyTo($validated['email'], $validated['nombre']);
            }
        });

        return response()->json(['success' => true, 'message' => 'Cotización enviada exitosamente.']);
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error("Error enviando correo de contacto: " . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
});

Route::prefix('v1')->group(function () {
    Route::options('{any}', function () {
        return response()->json([], 200, [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => '*',
        ]);
    })->where('any', '.*');

    // Plantillas (Templates)
    Route::get('/plantillas', [PlantillaApiController::class, 'index']);
    Route::get('/plantillas/{plantilla:slug}', [PlantillaApiController::class, 'show']);
    Route::get('/plantillas/{plantilla:slug}/preview/{seccion?}', [PlantillaApiController::class, 'preview']);
    Route::get('/plantillas/{plantilla:slug}/productos', [ProductoApiController::class, 'indexByPlantilla']);
    Route::get('/plantillas/{plantilla:slug}/productos/destacados', [ProductoApiController::class, 'destacadosByPlantilla']);

    // Sitios (Sites)
    Route::get('/sites', [SiteApiController::class, 'index']);
    Route::get('/sites/{dominio}/productos/destacados', [ProductoApiController::class, 'destacadosBySite']);
    Route::get('/sites/{dominio}/productos', [ProductoApiController::class, 'indexBySite']);
    Route::get('/sites/{dominio}/{seccion}', [SiteApiController::class, 'showSection']);
    Route::get('/sites/{dominio}', [SiteApiController::class, 'showSite']);
    Route::get('/sites/{dominio}/{site}/productos/destacados', [ProductoApiController::class, 'destacadosBySite']);
    Route::get('/sites/{dominio}/{site}/productos', [ProductoApiController::class, 'indexBySite']);
    Route::get('/sites/{dominio}/{site}/{seccion}', [SiteApiController::class, 'showSection']);
    Route::get('/sites/{dominio}/{site}', [SiteApiController::class, 'showSite']);

    // Productos / Ecommerce
    Route::get('/productos', [ProductoApiController::class, 'index']);
    Route::get('/productos/destacados', [ProductoApiController::class, 'destacados']);
    Route::get('/productos/{producto:slug}', [ProductoApiController::class, 'show']);
    // TikTok oEmbed Proxy
    Route::get('/tiktok-oembed', [TikTokApiController::class, 'oembed']);
});

Route::get('/tiktok-oembed', [TikTokApiController::class, 'oembed']);

