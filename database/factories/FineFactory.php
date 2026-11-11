<?php

namespace Database\Factories;

use App\Models\Fine;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fine>
 */
class FineFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'loan_id' => Loan::factory(),
            'nominal' => fake()->numberBetween(1000, 50000),
            'status_pembayaran' => 'BELUM',
        ];
    }
}
