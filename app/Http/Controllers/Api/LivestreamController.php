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
        $settings = LivestreamSetting::first();
        return response()->json(['settings' => $settings]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'platform' => 'required|in:youtube,facebook',
            'channel_url' => 'required|url',
            'stream_title' => 'nullable|string|max:255',
            'stream_description' => 'nullable|string',
            'is_live' => 'boolean',
            'next_service_date' => 'nullable|date',
            'next_service_title' => 'nullable|string|max:255',
            'next_service_description' => 'nullable|string',
        ]);

        $settings = LivestreamSetting::first();
        if ($settings) {
            $settings->update($data);
        } else {
            $settings = LivestreamSetting::create($data);
        }
        
        return response()->json(['message' => 'Livestream settings updated', 'settings' => $settings]);
    }
    
    public function archives()
    {
        return response()->json(['archives' => LivestreamArchive::orderBy('stream_date', 'desc')->get()]);
    }

    public function storeArchive(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'platform' => 'required|in:youtube,facebook',
            'video_id' => 'required|string',
            'thumbnail_url' => 'nullable|url',
            'stream_date' => 'required|date',
        ]);

        $archive = LivestreamArchive::create($data);
        return response()->json(['message' => 'Archive added successfully', 'archive' => $archive]);
    }

    public function destroyArchive($id)
    {
        $archive = LivestreamArchive::find($id);
        if (!$archive) {
            return response()->json(['message' => 'Archive not found'], 404);
        }
        $archive->delete();
        return response()->json(['message' => 'Archive deleted successfully']);
    }
}
