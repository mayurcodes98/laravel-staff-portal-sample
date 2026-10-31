<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Exceptions\OtpException;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

final class VerifyOtpAction
{
    /**
     * Validate the submitted code and, on success, authenticate the user.
     *
     * @throws OtpException
     */
    public function execute(User $user, string $plainCode): void
    {
        /** @var OtpCode|null $otpCode */
        $otpCode = $user->otpCodes()->active()->latest()->first();

        if ($otpCode === null || $otpCode->isExpired()) {
            throw OtpException::expired();
        }

        if ($otpCode->attempts >= config('otp.max_attempts')) {
            throw OtpException::tooManyAttempts();
        }

        $otpCode->increment('attempts');

        if (! Hash::check($plainCode, $otpCode->code_hash)) {
            throw OtpException::invalid();
        }

        $otpCode->update(['used_at' => now()]);

        Auth::login($user, remember: false);
    }
}
