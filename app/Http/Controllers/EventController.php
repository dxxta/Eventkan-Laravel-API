<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Category;
use App\Http\Resources\EventResource;
use App\Http\Requests\EventRequest;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index()
    {
        try {
            $events = Event::where('is_published', true)->where('deleted_at', null)->get();

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

    public function update(EventRequest $request, $id){
        $body = $request->validated();
        DB::beginTransaction();
        try {
            $event = Event::where('id', $id)->where('deleted_at', null)->first();
            if (!$event) {
                return $this->errorResponse('Event not found', 400);
            }
            $event->name = $body['name'];
            $event->description = $body['description'];
            $event->content = $body['content'];
            $event->start_date = $body['start_date'];
            $event->end_date = $body['end_date'];
            $event->location = $body['location'];
            $event->max_participants = $body['max_participants'];
            $event->is_published = $body['is_published'];
            $event->save();
            DB::commit();

            return $this->successResponse(new EventResource($event), 'Event updated successfully', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    public function remove(EventRequest $request, $id){
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
}
