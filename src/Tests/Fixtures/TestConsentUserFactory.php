<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\Factory;

final class TestConsentUserFactory extends Factory
{
    protected $model = TestConsentUser::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
        ];
    }
}
