<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/server-setup', function () {
    try {
        // Generate app key if not set
        if (!env('APP_KEY')) {
            \Illuminate\Support\Facades\Artisan::call('key:generate', ['--force' => true]);
        }
        
        // Clear caches
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        
        // Run migrations and seeders
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
            '--force' => true,
            '--seed' => true
        ]);
        
        return "Setup complete! Migrations and seeders ran successfully.";
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/link-storage', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        return 'Storage link created successfully! Your images should now work.';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/fix', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        return 'Cache cleared and storage link created successfully! Your images should now work.';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/delete-link', function() {
    $storagePath = public_path('storage');
    if (file_exists($storagePath) || is_link($storagePath)) {
        if (PHP_OS_FAMILY === 'Windows') {
            @rmdir($storagePath);
        } else {
            @unlink($storagePath);
            @exec('rm -rf ' . escapeshellarg($storagePath));
        }
        return "Deleted broken public/storage link! Laravel will now serve the images.";
    }
    return "No link found.";
});

// Fallback route to serve images on shared hosting where symlinks are disabled
Route::get('storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        return response("DEBUG ERROR: The file is completely missing from this exact path on your server:\n" . $fullPath . "\n\nPlease check cPanel and ensure the file is physically located exactly in that folder!", 404)
            ->header('Content-Type', 'text/plain');
    }
    return response()->file($fullPath);
})->where('path', '.*');
