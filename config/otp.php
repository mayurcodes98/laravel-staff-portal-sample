<?php

declare(strict_types=1);

return [
    /*
    |----------------------------------------------------------------------
    | OTP Time-to-Live (seconds)
    |----------------------------------------------------------------------
    */
    'ttl' => (int) env('OTP_TTL', 600),

    /*
    |----------------------------------------------------------------------
    | Code length (number of digits)
    |----------------------------------------------------------------------
    */
    'digits' => (int) env('OTP_DIGITS', 6),

    /*
    |----------------------------------------------------------------------
    | Maximum verification attempts before a code is invalidated
    |----------------------------------------------------------------------
    */
    'max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),

    /*
    |----------------------------------------------------------------------
    | Rate-limit: max OTP send requests per phone per minute
    |----------------------------------------------------------------------
    */
    'send_rate_limit_per_minute' => (int) env('OTP_SEND_RATE_LIMIT_PER_MINUTE', 3),

    /*
    |----------------------------------------------------------------------
    | Rate-limit: max OTP verify requests per IP per minute
    |----------------------------------------------------------------------
    */
    'verify_rate_limit_per_minute' => (int) env('OTP_VERIFY_RATE_LIMIT_PER_MINUTE', 5),

    /*
    |----------------------------------------------------------------------
    | SMS driver — controls which SmsGatewayInterface implementation is
    | bound in AppServiceProvider. 'log' (default) writes to the log file.
    | Add new driver names as real carriers are integrated.
    |----------------------------------------------------------------------
    */
    'sms_driver' => env('OTP_SMS_DRIVER', 'log'),
];
