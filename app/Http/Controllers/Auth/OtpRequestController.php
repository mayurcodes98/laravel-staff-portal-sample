<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\SendOtpAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\OtpRequestRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class OtpRequestController extends Controller
{
    public function show(): View
    {
        return view('auth.otp-request');
    }

    public function store(OtpRequestRequest $request, SendOtpAction $action): RedirectResponse
    {
        $user = User::where('phone', $request->validated('phone'))->first();

        // Return the same message whether the phone exists or not (enumeration guard).
        if ($user !== null) {
            $action->execute($user);
        }

        return redirect()->route('otp.verify.show')
            ->with('phone', $request->validated('phone'))
            ->with('status', 'If that number is registered, a code has been sent.');
    }
}
