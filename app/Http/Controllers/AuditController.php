<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audit;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $audits = Audit::where('user_id', $user->id)->get();
        return $this->successResponse($audits, 'Audits retrieved successfully', 200);
    }

    public function show($id)
    {
        $audit = Audit::where('entity_id', $id)->first();
        return $this->successResponse($audit, 'Audit retrieved successfully', 200);
    }
}
