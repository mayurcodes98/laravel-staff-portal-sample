<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
final class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    /** @return array<string,mixed> */
    public function definition(): array
    {
        $names = ['Engineering', 'Finance', 'Operations', 'Human Resources', 'Marketing'];
        $name = $this->faker->unique()->randomElement($names);

        return [
            'name' => $name,
            'code' => strtoupper(substr(str_replace(' ', '', $name), 0, 6)),
        ];
    }
}
