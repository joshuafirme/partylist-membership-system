<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the events.
     */
    public function index(Request $request)
    {
        // Eager load the creator and count the attendances
        $query = Event::with('creator')->withCount('attendances');

        // Apply Search Filter (Title or Venue)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('venue', 'like', "%{$search}%");
            });
        }

        // Apply Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Fetch paginated results, sorting by the upcoming event dates first
        // Or you can use latest() if you prefer sorting by when they were created
        $events = $query->orderBy('event_date', 'desc')->paginate(10);

        return view('core.events.list', compact('events'));
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'venue' => 'nullable|string|max:255',
            'event_date' => 'required|date',
            'status' => 'required|boolean',
        ]);

        // Auto-assign the creator
        $validated['created_by'] = auth()->id();

        Event::create($validated);

        return redirect()->route('events.index')
            ->with('success', 'Event created successfully.');
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'venue' => 'nullable|string|max:255',
            'event_date' => 'required|date',
            'status' => 'required|boolean',
        ]);

        $event->update($validated);

        return redirect()->route('events.index')
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Event deleted successfully.');
    }
}