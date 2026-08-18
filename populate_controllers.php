<?php
$dir = __DIR__;
$apiDir = $dir . '/app/Http/Controllers/Api';
if (!is_dir($apiDir)) {
    mkdir($apiDir, 0755, true);
}

$controllers = [
    'AuthController' => <<<EOT
<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request \$request)
    {
        \$request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        \$user = AdminUser::where('username', \$request->username)->first();

        if (!\$user || !Hash::check(\$request->password, \$user->password_hash)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        \$user->last_login = now();
        \$user->save();

        return response()->json([
            'token' => \$user->createToken('admin_token')->plainTextToken,
            'user' => \$user
        ]);
    }
    
    public function logout(Request \$request)
    {
        \$request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request \$request)
    {
        return response()->json(\$request->user());
    }
}
EOT,

    'SermonsController' => <<<EOT
<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Sermon;
use Illuminate\Http\Request;

class SermonsController extends Controller
{
    public function index()
    {
        return response()->json(['sermons' => Sermon::orderBy('date', 'desc')->get()]);
    }

    public function show(\$id)
    {
        \$sermon = Sermon::findOrFail(\$id);
        return response()->json(['sermon' => \$sermon]);
    }

    public function store(Request \$request)
    {
        \$data = \$request->validate([
            'title' => 'required|string|max:255',
            'preacher' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'audio_url' => 'nullable|string',
            'thumbnail_url' => 'nullable|string',
        ]);

        \$sermon = Sermon::create(\$data);
        return response()->json(['message' => 'Sermon created successfully', 'id' => \$sermon->id, 'sermon' => \$sermon], 201);
    }
    
    public function update(Request \$request, \$id)
    {
        \$sermon = Sermon::findOrFail(\$id);
        \$data = \$request->validate([
            'title' => 'sometimes|string|max:255',
            'preacher' => 'sometimes|string|max:255',
            'date' => 'sometimes|date',
            'description' => 'nullable|string',
            'audio_url' => 'nullable|string',
            'thumbnail_url' => 'nullable|string',
        ]);
        \$sermon->update(\$data);
        return response()->json(['message' => 'Sermon updated successfully', 'sermon' => \$sermon]);
    }

    public function destroy(\$id)
    {
        Sermon::destroy(\$id);
        return response()->json(['message' => 'Sermon deleted successfully']);
    }
}
EOT,

    'EventsController' => <<<EOT
<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    public function index()
    {
        return response()->json(['events' => Event::orderBy('event_date', 'asc')->get()]);
    }

    public function show(\$id)
    {
        \$event = Event::findOrFail(\$id);
        return response()->json(['event' => \$event]);
    }

    public function store(Request \$request)
    {
        \$data = \$request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
        ]);

        \$event = Event::create(\$data);
        return response()->json(['message' => 'Event created successfully', 'id' => \$event->id, 'event' => \$event], 201);
    }
    
    public function update(Request \$request, \$id)
    {
        \$event = Event::findOrFail(\$id);
        \$data = \$request->validate([
            'title' => 'sometimes|string|max:255',
            'event_date' => 'sometimes|date',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
        ]);
        \$event->update(\$data);
        return response()->json(['message' => 'Event updated successfully', 'event' => \$event]);
    }

    public function destroy(\$id)
    {
        Event::destroy(\$id);
        return response()->json(['message' => 'Event deleted successfully']);
    }
}
EOT,

    'GalleryController' => <<<EOT
<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        return response()->json(['gallery' => Gallery::orderBy('created_at', 'desc')->get()]);
    }

    public function store(Request \$request)
    {
        \$data = \$request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'required|string',
            'category' => 'nullable|string|max:50',
        ]);
        
        \$data['uploaded_by'] = \$request->user()->id;

        \$gallery = Gallery::create(\$data);
        return response()->json(['message' => 'Gallery item created successfully', 'id' => \$gallery->id, 'gallery' => \$gallery], 201);
    }
    
    public function destroy(\$id)
    {
        Gallery::destroy(\$id);
        return response()->json(['message' => 'Gallery item deleted successfully']);
    }
}
EOT,

    'LivestreamController' => <<<EOT
<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\LivestreamSetting;
use App\Models\LivestreamArchive;
use Illuminate\Http\Request;

class LivestreamController extends Controller
{
    public function getSettings()
    {
        \$settings = LivestreamSetting::first();
        return response()->json(['settings' => \$settings]);
    }

    public function updateSettings(Request \$request)
    {
        \$data = \$request->validate([
            'platform' => 'required|in:youtube,facebook',
            'channel_url' => 'required|url',
            'stream_title' => 'nullable|string|max:255',
            'stream_description' => 'nullable|string',
            'is_live' => 'boolean',
            'next_service_date' => 'nullable|date',
            'next_service_title' => 'nullable|string|max:255',
            'next_service_description' => 'nullable|string',
        ]);

        \$settings = LivestreamSetting::first();
        if (\$settings) {
            \$settings->update(\$data);
        } else {
            \$settings = LivestreamSetting::create(\$data);
        }
        
        return response()->json(['message' => 'Livestream settings updated', 'settings' => \$settings]);
    }
    
    public function archives()
    {
        return response()->json(['archives' => LivestreamArchive::orderBy('stream_date', 'desc')->get()]);
    }
}
EOT,

    'SettingController' => <<<EOT
<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        \$settings = Setting::all()->pluck('setting_value', 'setting_key');
        return response()->json(['settings' => \$settings]);
    }

    public function update(Request \$request)
    {
        \$data = \$request->all();
        foreach (\$data as \$key => \$value) {
            Setting::updateOrCreate(['setting_key' => \$key], ['setting_value' => \$value]);
        }
        return response()->json(['message' => 'Settings updated successfully']);
    }
}
EOT
];

foreach (\$controllers as \$name => \$content) {
    file_put_contents(\$apiDir . '/' . \$name . '.php', \$content);
}

// Generate routes/api.php
\$routes = <<<EOT
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SermonsController;
use App\Http\Controllers\Api\EventsController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\LivestreamController;
use App\Http\Controllers\Api\SettingController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

Route::get('/sermons', [SermonsController::class, 'index']);
Route::get('/sermons/{id}', [SermonsController::class, 'show']);

Route::get('/events', [EventsController::class, 'index']);
Route::get('/events/{id}', [EventsController::class, 'show']);

Route::get('/gallery', [GalleryController::class, 'index']);

Route::get('/livestream/settings', [LivestreamController::class, 'getSettings']);
Route::get('/livestream/archives', [LivestreamController::class, 'archives']);

Route::get('/settings', [SettingController::class, 'index']);


// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Sermons
    Route::post('/sermons', [SermonsController::class, 'store']);
    Route::put('/sermons/{id}', [SermonsController::class, 'update']);
    Route::delete('/sermons/{id}', [SermonsController::class, 'destroy']);
    
    // Events
    Route::post('/events', [EventsController::class, 'store']);
    Route::put('/events/{id}', [EventsController::class, 'update']);
    Route::delete('/events/{id}', [EventsController::class, 'destroy']);
    
    // Gallery
    Route::post('/gallery', [GalleryController::class, 'store']);
    Route::delete('/gallery/{id}', [GalleryController::class, 'destroy']);
    
    // Livestream
    Route::post('/livestream/settings', [LivestreamController::class, 'updateSettings']);
    
    // Settings
    Route::post('/settings', [SettingController::class, 'update']);
});
EOT;

file_put_contents(\$dir . '/routes/api.php', \$routes);

echo "Controllers and Routes generated successfully!\n";
