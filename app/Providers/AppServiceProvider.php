<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\SmsGatewayInterface;
use App\Services\Sms\LogSmsGateway;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind the SMS interface to the log-based implementation.
        // Swap OTP_SMS_DRIVER in .env (or bind a concrete class here) to use a real carrier.
        $this->app->bind(SmsGatewayInterface::class, $this->resolveSmsGateway());
    }

    public function boot(): void
    {
        $this->configureRateLimiters();
    }

    /**
     * Resolve the SMS gateway implementation class from config.
     * Currently only 'log' is shipped; add new cases here as carriers are integrated.
     *
     * @return class-string<SmsGatewayInterface>
     */
    private function resolveSmsGateway(): string
    {
        return match (config('otp.sms_driver')) {
            default => LogSmsGateway::class,
        };
    }

    /**
     * Register named rate limiters for OTP routes.
     * Limits are driven by config/otp.php so they can be tuned per environment.
     */
    private function configureRateLimiters(): void
    {
        // otp-send is keyed by phone + IP so a single phone cannot be
        // hammered from one address, and one IP cannot spray many phones.
        RateLimiter::for('otp-send', function (Request $request): array {
            return [
                Limit::perMinute(config('otp.send_rate_limit_per_minute'))
                    ->by('phone|'.$request->input('phone', '')),
                Limit::perMinute(config('otp.send_rate_limit_per_minute'))
                    ->by('ip|'.$request->ip()),
            ];
        });

        RateLimiter::for('otp-verify', function (Request $request): Limit {
            return Limit::perMinute(config('otp.verify_rate_limit_per_minute'))
                ->by($request->ip());
        });
    }
}
