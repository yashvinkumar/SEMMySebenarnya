<?php

namespace Database\Factories;

use App\Models\UserRecord;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserRecord>
 */
class UserRecordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = UserRecord::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'contact_number' => fake()->phoneNumber(),
            'user_type' => 'public',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a public user.
     */
    public function publicUser(): static
    {
        return $this->state(fn(array $attributes) => [
            'user_type' => 'public',
        ]);
    }

    /**
     * Create an MCMC staff user.
     */
    public function mcmcStaff(): static
    {
        return $this->state(fn(array $attributes) => [
            'user_type' => 'mcmc',
            'email' => fake()->unique()->safeEmail() . '@mcmc.gov.my',
        ]);
    }

    /**
     * Create an agency user.
     */
    public function agency(): static
    {
        return $this->state(fn(array $attributes) => [
            'user_type' => 'agency',
            'email' => fake()->unique()->safeEmail() . '@gov.my',
            'temporary_password' => Hash::make('temp123'),
            'force_password_reset' => true,
        ]);
    }
}
