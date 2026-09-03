<?php

declare(strict_types=1);

putenv('APP_BASE_PATH=src');
$_ENV['APP_BASE_PATH'] = 'src';
$_SERVER['APP_BASE_PATH'] = 'src';

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/src/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

if (! defined('LARAVEL_VERSION')) {
    define('LARAVEL_VERSION', \Illuminate\Foundation\Application::VERSION);
}
