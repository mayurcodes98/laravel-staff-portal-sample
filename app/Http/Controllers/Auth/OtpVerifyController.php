<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\VerifyOtpAction;
use App\Exceptions\OtpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\OtpVerifyRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class OtpVerifyController extends Controller
{
    public function show(): View
    {
        return view('auth.otp-verify');
    }

    public function store(OtpVerifyRequest $request, VerifyOtpAction $action): RedirectResponse
    {
        $user = User::where('phone', $request->validated('phone'))->first();

        if ($user === null) {
            return back()->withErrors(['phone' => 'No account found for that number.']);
        }

        try {
            $action->execute($user, $request->validated('code'));
        } catch (OtpException $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        return redirect()->intended(route('dashboard'));
    }
}
