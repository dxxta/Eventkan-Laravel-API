<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Category;
use App\Http\Resources\EventResource;
use App\Http\Requests\EventRequest;
use App\Http\Requests\EventUpdateRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\EventCategoriesResource;

class EventController extends Controller
{
    public function index()
    {
        try {
            $events = Event::where('is_published', true)->where('deleted_at', null)->with('categories')->get();

            return $this->successResponse(EventResource::collection($events), 'Events fetched successfully', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    public function create(EventRequest $request){
        $body = $request->validated();
        DB::beginTransaction();
        try {
            $categories = Category::whereIn('id', $body['category_ids'])
            ->whereNull('deleted_at')
            ->get();
            if ($categories->count() !== count($body['category_ids'])) {
                return $this->errorResponse('One or more categories not found', 400);
            }

            $event = Event::create([
                'name' => $body['name'],
                'description' => $body['description'],
                'content' => $body['content'],
                'start_date' => $body['start_date'],
                'end_date' => $body['end_date'],
                'location' => $body['location'],
                'max_participants' => $body['max_participants'],
                'is_published' => $body['is_published'],
            ]);
            $event->categories()->attach($categories->pluck('id'));
            DB::commit();

            return $this->successResponse(new EventResource($event), 'Event created successfully', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    public function update($id, EventUpdateRequest $request){
        $body = $request->validated();
        DB::beginTransaction();
        try {
            $event = Event::where('id', $id)->where('deleted_at', null)->first();
            if (!$event) {
                return $this->errorResponse('Event not found', 400);
            }
            // Update only fields present in $body
            foreach ($body as $key => $value) {
                if ($key !== 'category_ids') {
                    $event->$key = $value;
                }
            }
            if (isset($body['category_ids'])) {
                $event->categories()->sync($body['category_ids']);
            }
            $event->save();
            DB::commit();

            return $this->successResponse(new EventResource($event), 'Event updated successfully', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    public function remove($id){
        DB::beginTransaction();
        try {
            $event = Event::where('id', $id)->where('deleted_at', null)->first();
            if (!$event) {
                return $this->errorResponse('Event not found', 400);
            }
            $event->deleted_at = now();
            $event->save();
            DB::commit();

            return $this->successResponse(new EventResource($event), 'Event removed successfully', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    public function show($id){
        try {
            $event = Event::where('id', $id)->where('deleted_at', null)->first();
            if (!$event) {
                return $this->errorResponse('Event not found', 400);
            }
            return $this->successResponse(new EventResource($event), 'Event fetched successfully', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    public function eventCategories($id){
        try {
            $event = Event::where('id', $id)->where('deleted_at', null)->first();
            if (!$event) {
                return $this->errorResponse('Event not found', 400);
            }

            $categories = Category::where('deleted_at', null)->whereHas('events', function ($query) use ($event) {
                $query->where('event_id', $event->id);
            })->get();
            // Log::info($categories);
            // collection of category
            return $this->successResponse(EventCategoriesResource::collection($categories), 'Event categories fetched successfully', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }
}
