<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\SmsGatewayInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class SendOtpJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(
        private readonly string $phone,
        private readonly string $plainCode,
    ) {}

    public function handle(SmsGatewayInterface $sms): void
    {
        $message = sprintf(
            'Your Staff Portal verification code is %s. It expires in %d minutes.',
            $this->plainCode,
            (int) (config('otp.ttl') / 60),
        );

        $sms->send($this->phone, $message);
    }
}
