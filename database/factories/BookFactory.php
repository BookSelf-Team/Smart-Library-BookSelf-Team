<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_buku' => 'BKS-'.fake()->unique()->numerify('####'),
            'judul' => fake()->sentence(3),
            'pengarang' => fake()->name(),
            'tahun_terbit' => fake()->numberBetween(1980, 2026),
            'jumlah_stok' => fake()->numberBetween(1, 10),
            'sinopsis' => fake()->optional()->paragraph(),
        ];
    }
}
