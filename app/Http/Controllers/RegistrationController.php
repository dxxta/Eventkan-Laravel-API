<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use App\Http\Resources\RegistrationResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Http\Requests\RegistrationRequest;

class RegistrationController extends Controller
{
    public function create(RegistrationRequest $request)
    {
        $body = $request->validated();
        DB::beginTransaction();
        try {
            $event = Event::where('id', $body['event_id'])->where('deleted_at', null)->first();
            if (!$event) {
                return $this->errorResponse('Event not found', 400);
            }
            $user = Auth::user();
            $registration = Registration::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'status' => 'process',
            ]);
            DB::commit();

            return $this->successResponse(new RegistrationResource($registration), 'Registration created successfully', 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {

    }
}
