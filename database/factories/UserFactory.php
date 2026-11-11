<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'identifier' => fake()->unique()->numerify('##########'),
            'role' => Role::Siswa->value,
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes): array => ['role' => Role::Admin->value]);
    }

    public function pustakawan(): static
    {
        return $this->state(fn (array $attributes): array => ['role' => Role::Pustakawan->value]);
    }

    public function petugas(): static
    {
        return $this->state(fn (array $attributes): array => ['role' => Role::Petugas->value]);
    }

    public function guru(): static
    {
        return $this->state(fn (array $attributes): array => ['role' => Role::Guru->value]);
    }

    public function siswa(): static
    {
        return $this->state(fn (array $attributes): array => ['role' => Role::Siswa->value]);
    }

    public function pengguna(): static
    {
        return $this->state(fn (array $attributes): array => ['role' => Role::Pengguna->value]);
    }
}
