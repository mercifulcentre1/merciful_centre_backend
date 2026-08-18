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

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:50',
            'image_file' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);
        
        $data['uploaded_by'] = $request->user()->id;

        if ($request->hasFile('image_file')) {
            $data['image_url'] = 'api/storage/' . $request->file('image_file')->store('gallery', 'public');
        }

        $gallery = Gallery::create($data);
        return response()->json(['message' => 'Gallery item created successfully', 'id' => $gallery->id, 'gallery' => $gallery], 201);
    }
    
    public function update(Request $request, $id)
    {
        $gallery = Gallery::findOrFail($id);
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:50',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        if ($request->hasFile('image_file')) {
            $data['image_url'] = 'api/storage/' . $request->file('image_file')->store('gallery', 'public');
        }

        $gallery->update($data);
        return response()->json(['message' => 'Gallery item updated successfully', 'gallery' => $gallery]);
    }
    
    public function destroy($id)
    {
        Gallery::destroy($id);
        return response()->json(['message' => 'Gallery item deleted successfully']);
    }
}
