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

    public function show($id)
    {
        $event = Event::findOrFail($id);
        return response()->json(['event' => $event]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('image_file')) {
            $data['image_url'] = 'storage/' . $request->file('image_file')->store('events', 'public');
        }

        $event = Event::create($data);
        return response()->json(['message' => 'Event created successfully', 'id' => $event->id, 'event' => $event], 201);
    }
    
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'event_date' => 'sometimes|date',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('image_file')) {
            $data['image_url'] = 'storage/' . $request->file('image_file')->store('events', 'public');
        }

        $event->update($data);
        return response()->json(['message' => 'Event updated successfully', 'event' => $event]);
    }

    public function destroy($id)
    {
        Event::destroy($id);
        return response()->json(['message' => 'Event deleted successfully']);
    }
}
