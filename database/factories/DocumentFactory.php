<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Department;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
final class DocumentFactory extends Factory
{
    protected $model = Document::class;

    /** @return array<string,mixed> */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'file_path' => 'documents/'.$this->faker->uuid().'.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => $this->faker->numberBetween(1024, 5 * 1024 * 1024),
            'department_id' => Department::factory(),
            'uploaded_by' => User::factory()->admin(),
        ];
    }
}
