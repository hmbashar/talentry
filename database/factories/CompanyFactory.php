<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'uuid' => fake()->uuid(),
            'name' => $name,
            'slug' => str($name)->slug(),
            'plan' => 'free',
            'is_active' => true,
        ];
    }
}
