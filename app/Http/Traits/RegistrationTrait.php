<?php

namespace App\Http\Traits;

use Illuminate\Support\Str;
use App\Models\Registration;

trait RegistrationTrait
{
    public function generateRegistrationCode(int $pad = 4): string
    {
        $prefix = 'REG-' . date('dmY') . '-'; // e.g. REG-10082025-
        // Find last code for today
        $last = Registration::where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        if ($last) {
            // extract trailing count part
            $lastCount = (int) Str::after($last->code, $prefix);
            $next = $lastCount + 1;
        } else {
            $next = 1;
        }

        $countPart = str_pad((string) $next, $pad, '0', STR_PAD_LEFT);
        return $prefix . $countPart; // REG-10082025-0001
    }
}
