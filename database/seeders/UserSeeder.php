<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    public function run(): void
    {
        $eng = Department::where('code', 'ENG')->firstOrFail();
        $fin = Department::where('code', 'FIN')->firstOrFail();
        $hr = Department::where('code', 'HR')->firstOrFail();

        // Admin user
        $admin = User::firstOrCreate(
            ['phone' => '+12025550001'],
            ['name' => 'Alex Admin', 'role' => 'admin'],
        );
        $admin->departments()->sync([$eng->id, $fin->id, $hr->id]);

        // Employee in Engineering
        $emp1 = User::firstOrCreate(
            ['phone' => '+12025550002'],
            ['name' => 'Sam Engineer', 'role' => 'employee'],
        );
        $emp1->departments()->sync([$eng->id]);

        // Employee in Finance
        $emp2 = User::firstOrCreate(
            ['phone' => '+12025550003'],
            ['name' => 'Jordan Finance', 'role' => 'employee'],
        );
        $emp2->departments()->sync([$fin->id]);

        // Employee in HR
        $emp3 = User::firstOrCreate(
            ['phone' => '+12025550004'],
            ['name' => 'Riley HR', 'role' => 'employee'],
        );
        $emp3->departments()->sync([$hr->id]);
    }
}
