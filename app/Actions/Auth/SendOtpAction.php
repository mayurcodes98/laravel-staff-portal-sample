<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Jobs\SendOtpJob;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class SendOtpAction
{
    /**
     * Generate a fresh OTP, persist a hashed copy, and queue delivery.
     * Any previous unused codes for this user are invalidated first.
     */
    public function execute(User $user): void
    {
        // Invalidate outstanding codes so only one is valid at a time.
        $user->otpCodes()->active()->update(['used_at' => now()]);

        $plainCode = (string) random_int(
            (int) str_repeat('1', config('otp.digits')),
            (int) str_repeat('9', config('otp.digits')),
        );

        OtpCode::create([
            'user_id' => $user->id,
            'code_hash' => Hash::make($plainCode),
            'expires_at' => now()->addSeconds(config('otp.ttl')),
            'attempts' => 0,
        ]);

        SendOtpJob::dispatch($user->phone, $plainCode);
    }
}
