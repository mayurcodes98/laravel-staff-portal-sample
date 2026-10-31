<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<OtpCode>
 */
final class OtpCodeFactory extends Factory
{
    protected $model = OtpCode::class;

    /** @return array<string,mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addSeconds(600),
            'attempts' => 0,
            'used_at' => null,
        ];
    }

    public function expired(): static
    {
        return $this->state(['expires_at' => now()->subMinutes(10)]);
    }

    public function maxedOut(): static
    {
        return $this->state(['attempts' => 5]);
    }
}
