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

    public function show($id)
    {
        $sermon = Sermon::findOrFail($id);
        return response()->json(['sermon' => $sermon]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'preacher' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'audio_file' => 'nullable|file|mimes:mp3,wav,ogg|max:20480',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('audio_file')) {
            $data['audio_url'] = 'api/storage/' . $request->file('audio_file')->store('sermons', 'public');
        }
        if ($request->hasFile('thumbnail_file')) {
            $data['thumbnail_url'] = 'api/storage/' . $request->file('thumbnail_file')->store('thumbnails', 'public');
        }

        $sermon = Sermon::create($data);
        return response()->json(['message' => 'Sermon created successfully', 'id' => $sermon->id, 'sermon' => $sermon], 201);
    }
    
    public function update(Request $request, $id)
    {
        $sermon = Sermon::findOrFail($id);
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'preacher' => 'sometimes|string|max:255',
            'date' => 'sometimes|date',
            'description' => 'nullable|string',
            'audio_file' => 'nullable|file|mimes:mp3,wav,ogg|max:20480',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('audio_file')) {
            $data['audio_url'] = 'api/storage/' . $request->file('audio_file')->store('sermons', 'public');
        }
        if ($request->hasFile('thumbnail_file')) {
            $data['thumbnail_url'] = 'api/storage/' . $request->file('thumbnail_file')->store('thumbnails', 'public');
        }

        $sermon->update($data);
        return response()->json(['message' => 'Sermon updated successfully', 'sermon' => $sermon]);
    }

    public function destroy($id)
    {
        Sermon::destroy($id);
        return response()->json(['message' => 'Sermon deleted successfully']);
    }
}
