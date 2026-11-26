<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactoRequest;
use App\Models\Contacto;
use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactoController extends Controller
{
    /**
     * Registra una cotización o consulta web y notifica por correo electrónico.
     */
    public function store(ContactoRequest $request): JsonResponse
    {
        // 1. Protección anti-bot Honeypot: si el campo trampa tiene contenido, descartar silenciosamente
        if (! empty($request->input('_hp_website'))) {
            return response()->json([
                'success' => true,
                'folio'   => Contacto::generarFolio(),
                'message' => 'Cotización enviada exitosamente.',
            ]);
        }

        $validated = $request->validated();

        $site = ! empty($validated['site_id'])
            ? Site::with(['respuestas.pregunta'])->find($validated['site_id'])
            : null;

        $sitioNombre = $validated['sitio_nombre'] ?? $site?->nombre ?? config('app.name');
        $destinatario = $site?->email ?: (config('mail.from.address') ?: env('MAIL_FROM_ADDRESS'));

        $folio = Contacto::generarFolio();

        // 2. Blindaje de datos: Persistencia garantizada en base de datos antes de cualquier llamada externa
        $contacto = Contacto::create([
            'folio'          => $folio,
            'site_id'        => $site?->id,
            'nombre'         => $validated['nombre'],
            'empresa'        => $validated['empresa'] ?? null,
            'telefono'       => $validated['telefono'],
            'email'          => $validated['email'] ?? null,
            'sector'         => $validated['sector'] ?? null,
            'producto'       => $validated['producto'] ?? null,
            'mensaje'        => $validated['mensaje'],
            'sitio_nombre'   => $sitioNombre,
            'estado'         => 'nuevo',
            'correo_enviado' => false,
        ]);

        if (! $destinatario) {
            $contacto->update(['error_envio' => 'No se ha configurado la dirección de correo receptora.']);

            return response()->json([
                'success' => true,
                'folio'   => $folio,
                'message' => 'Cotización registrada exitosamente. Nos pondremos en contacto contigo a la brevedad.',
            ]);
        }

        // Logo dinámico del CMS según el proyecto
        $sitioLogo = $validated['sitio_logo'] ?? null;
        if (! $sitioLogo && $site) {
            $sitioLogo = $site->imagen;
            if (! $sitioLogo && $site->relationLoaded('respuestas')) {
                $logoRespuesta = $site->respuestas->first(function ($r) {
                    $lbl = strtolower($r->pregunta?->label ?? '');

                    return $lbl === 'logo' || $lbl === 'logo_nav' || str_contains($lbl, 'logo');
                });
                $sitioLogo = $logoRespuesta?->valor;
            }
            if (! $sitioLogo && ! empty($site->estilos['logo'])) {
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

            if (! str_starts_with($sitioLogo, 'http://') && ! str_starts_with($sitioLogo, 'https://')) {
                $sitioLogo = asset('storage/' . ltrim($sitioLogo, '/'));
            }
        }

        $sitioEstilos = is_array($site?->estilos) ? $site->estilos : (is_string($site?->estilos) ? json_decode($site->estilos, true) : []);
        $sitioDireccion = $sitioEstilos['direccion'] ?? null;

        $asunto = '[Cotización Web] ' . (! empty($validated['producto']) ? $validated['producto'] : 'Consulta General') . ' — ' . (! empty($validated['empresa']) ? $validated['empresa'] : $validated['nombre']);

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

                if (! empty($validated['email'])) {
                    $msg->replyTo($validated['email'], $validated['nombre']);
                }
            });

            $contacto->update(['correo_enviado' => true]);

            return response()->json([
                'success' => true,
                'folio'   => $folio,
                'message' => 'Cotización enviada exitosamente.',
            ]);
        } catch (Throwable $e) {
            Log::error("Error enviando correo de contacto (Folio: {$folio}): " . $e->getMessage());
            $contacto->update(['error_envio' => $e->getMessage()]);

            return response()->json([
                'success' => true,
                'folio'   => $folio,
                'message' => 'Cotización registrada exitosamente. Nos pondremos en contacto contigo a la brevedad.',
            ]);
        }
    }
}
