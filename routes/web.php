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
