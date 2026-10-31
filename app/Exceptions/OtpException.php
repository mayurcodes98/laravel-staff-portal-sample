<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class OtpException extends RuntimeException
{
    public static function expired(): self
    {
        return new self('The one-time code has expired. Please request a new one.');
    }

    public static function invalid(): self
    {
        return new self('The code you entered is incorrect.');
    }

    public static function tooManyAttempts(): self
    {
        return new self('Too many incorrect attempts. Please request a new code.');
    }
}
