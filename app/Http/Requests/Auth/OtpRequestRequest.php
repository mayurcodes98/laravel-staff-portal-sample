<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

final class OtpRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // open endpoint — any visitor may request an OTP
    }

    /** @return array<string,mixed> */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'regex:/^\+[1-9]\d{6,14}$/'],
        ];
    }

    /** @return array<string,string> */
    public function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid phone number in E.164 format (e.g. +12125550100).',
        ];
    }
}
