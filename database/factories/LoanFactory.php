<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->siswa(),
            'book_id' => Book::factory(),
            'tanggal_pinjam' => now()->toDateString(),
            'batas_kembali' => now()->addDays(7)->toDateString(),
            'tanggal_dikembalikan' => null,
            'status' => 'ACTIVE',
        ];
    }

    public function returned(): static
    {
        return $this->state(fn (array $attributes): array => [
            'tanggal_dikembalikan' => now()->toDateString(),
            'status' => 'COMPLETED',
        ]);
    }
}
