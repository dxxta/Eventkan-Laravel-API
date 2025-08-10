<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use App\Http\Resources\RegistrationResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Http\Requests\RegistrationRequest;
use App\Http\Traits\RegistrationTrait;

class RegistrationController extends Controller
{
    use RegistrationTrait;

    public function index(){
        try {
            $user = Auth::user();
            $registrations = Registration::where('user_id', $user->id)->with(['event', 'user'])->get();
            return $this->successResponse($registrations, 'Registrations retrieved successfully', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    public function create(RegistrationRequest $request)
    {
        $body = $request->validated();
        DB::beginTransaction();
        try {
            $event = Event::where('id', $body['event_id'])->where('deleted_at', null)->first();
            if (!$event) {
                return $this->errorResponse('Event not found', 400);
            }

            $findRegistration = Registration::where('event_id', $event->id)->where('user_id', Auth::user()->id)->where('created_at', '>=', now()->subHours(24))->first();
            if ($findRegistration) {
                return $this->errorResponse('You have already registered for this event', 400);
            }

            $user = Auth::user();
            $code = $this->generateRegistrationCode();
            $registration = Registration::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'code' => $code,
                'status' => 'process',
            ]);
            DB::commit();

            $registration->load(['event', 'user']);
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
