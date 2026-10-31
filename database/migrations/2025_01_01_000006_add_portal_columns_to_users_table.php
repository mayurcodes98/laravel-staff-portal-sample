<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('phone', 20)->nullable()->unique()->after('name');
            $table->string('role', 20)->default('employee')->after('phone');
            $table->timestamp('phone_verified_at')->nullable()->after('role');
            $table->dropUnique('users_email_unique');
            $table->dropColumn(['email', 'email_verified_at', 'password']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['phone', 'role', 'phone_verified_at']);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
        });
    }
};
