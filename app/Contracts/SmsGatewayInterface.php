<?php

declare(strict_types=1);

namespace App\Contracts;

interface SmsGatewayInterface
{
    /**
     * Deliver a text message to the given phone number.
     */
    public function send(string $phone, string $message): void;
}
