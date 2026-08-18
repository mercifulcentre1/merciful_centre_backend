<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Manually delete cache files just to be 100% sure
@unlink(__DIR__.'/../bootstrap/cache/routes-v7.php');
@unlink(__DIR__.'/../bootstrap/cache/routes-v7.php.php'); // Sometimes it has an extra .php
@unlink(__DIR__.'/../bootstrap/cache/config.php');
@unlink(__DIR__.'/../bootstrap/cache/events.php');
@unlink(__DIR__.'/../bootstrap/cache/services.php');
@unlink(__DIR__.'/../bootstrap/cache/packages.php');

\Illuminate\Support\Facades\Artisan::call('optimize:clear');
echo "Cache completely cleared successfully (and files deleted manually)!";
