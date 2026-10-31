<?php

declare(strict_types=1);

namespace App\Services\Sms;

use App\Contracts\SmsGatewayInterface;
use Illuminate\Support\Facades\Log;

/**
 * Development / testing gateway that writes outbound SMS to the log.
 * Swap this binding in AppServiceProvider for a real carrier in production.
 */
final class LogSmsGateway implements SmsGatewayInterface
{
    public function send(string $phone, string $message): void
    {
        Log::channel('stack')->info('[SMS] Outbound message', [
            'to' => $phone,
            'message' => $message,
        ]);
    }
}
