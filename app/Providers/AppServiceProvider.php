<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\SmsGatewayInterface;
use App\Services\Sms\LogSmsGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind the SMS interface to the log-based implementation.
        // Swap this binding (via env check or a dedicated provider) to use a real carrier.
        $this->app->bind(SmsGatewayInterface::class, LogSmsGateway::class);
    }

    public function boot(): void
    {
        //
    }
}
