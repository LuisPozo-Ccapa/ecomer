<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Throwable;

class DeployWebhookController extends Controller
{
    /**
     * Webhook seguro de post-despliegue para cPanel (sin terminal SSH).
     * Ejecuta migraciones aditivas, optimizaciones de cache y valida storage sin tocar datos existentes.
     */
    public function handle(Request $request): JsonResponse
    {
        // 1. Extraer el token recibido desde el encabezado X-Deploy-Token
        $receivedToken = $request->header('X-Deploy-Token');

        // 2. Extraer el token esperado desde config o fallback env
        $expectedToken = config('app.deploy_token') ?? env('DEPLOY_TOKEN');

        if (empty($expectedToken) || empty($receivedToken) || !hash_equals($expectedToken, $receivedToken)) {
            Log::warning('Intento no autorizado en webhook de deploy', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Acceso denegado: Token inválido o no configurado.'
            ], 403);
        }

        try {
            $tasks = [
                // SOLO migraciones aditivas (NUNCA fresh, NUNCA drop, NUNCA seed)
                'migrate' => ['--force' => true],
                'optimize:clear' => [],
                'config:cache' => [],
                'route:cache' => [],
                'view:cache' => [],
            ];

            $results = [];

            foreach ($tasks as $command => $params) {
                $exitCode = Artisan::call($command, $params);
                $output = trim(Artisan::output());

                $results[$command] = [
                    'exit_code' => $exitCode,
                    'output' => $output
                ];

                // Si una migración falla, abortar con HTTP 500 para alertar a GitHub Actions
                if ($exitCode !== 0 && $command === 'migrate') {
                    Log::error('Fallo en post-deploy al ejecutar migrate', ['details' => $results]);

                    return response()->json([
                        'status' => 'error',
                        'failed_step' => 'migrate',
                        'details' => $results
                    ], 500);
                }
            }

            // Validar enlace simbólico de imágenes (respetando las fotos existentes)
            $publicStorage = public_path('storage');
            $targetStorage = storage_path('app/public');
            $symlinkStatus = 'preserved_existing';

            if (!file_exists($publicStorage)) {
                if (function_exists('symlink')) {
                    $created = @symlink($targetStorage, $publicStorage);
                    $symlinkStatus = $created ? 'created_symlink' : 'failed';
                } else {
                    Artisan::call('storage:link');
                    $symlinkStatus = 'created_via_artisan';
                }
            }
            $results['storage:link'] = ['status' => $symlinkStatus];

            Log::info('Post-deploy ejecutado exitosamente', ['ip' => $request->ip(), 'results' => $results]);

            return response()->json([
                'status' => 'success',
                'timestamp' => now()->toIso8601String(),
                'results' => $results
            ], 200);

        } catch (Throwable $e) {
            Log::critical('Error fatal en webhook de post-deploy: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return response()->json([
                'status' => 'fatal_error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
