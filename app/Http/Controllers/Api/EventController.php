<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    use CanLoadRelationships;

    private $relations = ['user', 'attendees', 'attendees.user'];

    public function __construct() {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->middleware('throttle:60,1')->only(['store', 'update', 'destroy']);
        $this->authorizeResource(Event::class, 'event');
    }

    public function index()
    {   
        $query = $this->loadRelationships(Event::query());

        return EventResource::collection($query->latest()->paginate());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
    
        // Add user_id to the validated data
        $validatedData['user_id'] = $request->user()->id;
    
        $event = Event::create($validatedData);
    
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        // Check if the user is authorized to update the event
/*         if(Gate::denies('update-event', $event)){
            abort(403, 'You are not authorised to update this event');
        } */

        //$this->authorize('update-event', $event);

        $event->update($request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
        ]));

        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(event $event)
    {
        $event->delete();

        return response()->json(['message' => 'Event deleted']);
    }
}
