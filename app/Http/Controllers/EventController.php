<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Http\Resources\EventResource;

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

    public function create(EventResource $request){
        $body = $request->validated();
        DB::beginTransaction();
        try {
            $event = new Event();
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

            return $this->successResponse(new EventResource($event), 'Event created successfully', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    public function update(EventResource $request, $id){
        $body = $request->validated();
        DB::beginTransaction();
        try {
            $event = Event::where('id', $id)->where('deleted_at', null)->first();
            if (!$event) {
                return $this->errorResponse('Event not found', 404);
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

    public function remove(EventResource $request, $id){
        DB::beginTransaction();
        try {
            $event = Event::where('id', $id)->where('deleted_at', null)->first();
            if (!$event) {
                return $this->errorResponse('Event not found', 404);
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
