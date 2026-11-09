<?php
/**
 * Webhook Post-Deploy Automatizado para cPanel
 * Ejecuta migraciones, cachés y valida storage sin alterar datos existentes.
 */

// 1. Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    die(json_encode(['status' => 'error', 'message' => 'Método no permitido. Use POST.']));
}

// 2. Extraer cabecera X-Deploy-Token
$headers = function_exists('getallheaders') ? getallheaders() : [];
$receivedToken = $headers['X-Deploy-Token'] 
    ?? $headers['x-deploy-token'] 
    ?? ($_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? null);

$basePath = dirname(__DIR__);

// Cargar token desde .env local del servidor si no está en $_ENV
if (file_exists($basePath . '/.env')) {
    $lines = file($basePath . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (str_starts_with($trimmed, '#')) continue;
        if (str_contains($trimmed, '=')) {
            [$k, $v] = explode('=', $trimmed, 2);
            $k = trim($k);
            $v = trim(trim($v), '"\'');
            if (!isset($_ENV[$k])) $_ENV[$k] = $v;
        }
    }
}

$expectedToken = $_ENV['DEPLOY_TOKEN'] ?? getenv('DEPLOY_TOKEN');

if (empty($expectedToken) || empty($receivedToken) || !hash_equals($expectedToken, $receivedToken)) {
    http_response_code(403);
    header('Content-Type: application/json');
    die(json_encode(['status' => 'error', 'message' => 'Acceso denegado: Token inválido o no configurado']));
}

// 3. Inicializar Laravel de forma nativa en PHP (sin terminal)
header('Content-Type: application/json');

try {
    require_once $basePath . '/vendor/autoload.php';
    $app = require_once $basePath . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    use Illuminate\Support\Facades\Artisan;

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
        $results[$command] = [
            'exit_code' => $exitCode,
            'output' => trim(Artisan::output())
        ];

        // Si una migración falla, abortar con error 500 para alertar a GitHub Actions
        if ($exitCode !== 0 && $command === 'migrate') {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'failed_step' => 'migrate',
                'details' => $results
            ], JSON_PRETTY_PRINT);
            exit;
        }
    }

    // Validar enlace simbólico de imágenes (respetando las fotos existentes)
    $publicStorage = __DIR__ . '/storage';
    $targetStorage = $basePath . '/storage/app/public';
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

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'timestamp' => date('Y-m-d H:i:s'),
        'results' => $results
    ], JSON_PRETTY_PRINT);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'fatal_error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
}

