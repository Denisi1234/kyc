<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Find the Laravel application. This supports both a standard layout
// (<app>/public) and a copied public folder inside the domain document root.
$candidates = [
    dirname(__DIR__),
    dirname(__DIR__, 2).'/kyc_portal',
    dirname(__DIR__, 2).'/kyc-portal',
];

$appBase = null;

foreach ($candidates as $candidate) {
    if (is_file($candidate.'/vendor/autoload.php') && is_file($candidate.'/bootstrap/app.php')) {
        $appBase = $candidate;
        break;
    }
}

if ($appBase === null) {
    http_response_code(500);
    exit('The portal application could not be found. Check the deployment path.');
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $appBase.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $appBase.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $appBase.'/bootstrap/app.php';

// The public files live in this directory.
$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
