<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Jobs\SendOtpJob;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

final class OtpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        RateLimiter::clear('otp-request-'.request()->ip());
    }

    // ── 1. Request dispatches a job and redirects ────────────────────────────
    public function test_valid_phone_dispatches_otp_job(): void
    {
        $user = User::factory()->create(['phone' => '+12025550099']);

        $this->post('/login', ['phone' => '+12025550099'])
            ->assertRedirect(route('otp.verify.show'));

        Queue::assertPushed(SendOtpJob::class);
    }

    // ── 2. Unknown phone returns same redirect (enumeration guard) ───────────
    public function test_unknown_phone_does_not_reveal_existence(): void
    {
        $this->post('/login', ['phone' => '+19995550000'])
            ->assertRedirect(route('otp.verify.show'));

        Queue::assertNotPushed(SendOtpJob::class);
    }

    // ── 3. Correct code logs user in ─────────────────────────────────────────
    public function test_correct_code_authenticates_user(): void
    {
        $user = User::factory()->create(['phone' => '+12025550010', 'role' => 'employee']);

        OtpCode::factory()->for($user)->create([
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
        ]);

        $this->post('/verify', ['phone' => '+12025550010', 'code' => '123456'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    // ── 4. Expired code returns validation error ──────────────────────────────
    public function test_expired_code_returns_error(): void
    {
        $user = User::factory()->create(['phone' => '+12025550011']);

        OtpCode::factory()->for($user)->expired()->create([
            'code_hash' => Hash::make('123456'),
        ]);

        $this->post('/verify', ['phone' => '+12025550011', 'code' => '123456'])
            ->assertRedirect()
            ->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    // ── 5. Wrong code increments attempts and returns error ───────────────────
    public function test_wrong_code_returns_error_and_increments_attempts(): void
    {
        $user = User::factory()->create(['phone' => '+12025550012']);

        $otp = OtpCode::factory()->for($user)->create([
            'code_hash' => Hash::make('999999'),
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
        ]);

        $this->post('/verify', ['phone' => '+12025550012', 'code' => '111111'])
            ->assertSessionHasErrors('code');

        $this->assertEquals(1, $otp->fresh()->attempts);
    }

    // ── 6. Maxed-out attempts return lock error ───────────────────────────────
    public function test_too_many_attempts_returns_error(): void
    {
        $user = User::factory()->create(['phone' => '+12025550013']);

        OtpCode::factory()->for($user)->maxedOut()->create([
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->post('/verify', ['phone' => '+12025550013', 'code' => '123456'])
            ->assertSessionHasErrors('code');
    }
}
