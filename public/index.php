<?php

@ini_set('memory_limit', '512M');

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Auto-creación resiliente de estructura storage si no existe (primer despliegue en cPanel)
$storageFramework = __DIR__.'/../storage/framework';
if (!is_dir($storageFramework.'/sessions')) {
    foreach ([
        __DIR__.'/../storage/app/public',
        $storageFramework.'/cache/data',
        $storageFramework.'/sessions',
        $storageFramework.'/views',
        __DIR__.'/../storage/logs',
    ] as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
