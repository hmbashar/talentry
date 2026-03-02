<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicationNote>
 */
class ApplicationNoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'application_id' => Application::factory(),
            'user_id' => User::factory(),
            'note' => fake()->paragraph(),
        ];
    }
}
