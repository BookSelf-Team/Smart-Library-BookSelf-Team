<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            ['judul' => 'Bumi Manusia (Tetralogi Buru)', 'pengarang' => 'Pramoedya Ananta Toer', 'tahun_terbit' => 1980, 'jumlah_stok' => 6],
            ['judul' => 'Clean Architecture: Craftsman Guide', 'pengarang' => 'Robert C. Martin (Uncle Bob)', 'tahun_terbit' => 2017, 'jumlah_stok' => 3],
            ['judul' => 'Kalkulus Lanjut dan Geometri', 'pengarang' => 'Edwin J. Purcell & Dale Varberg', 'tahun_terbit' => 2003, 'jumlah_stok' => 4],
        ];

        foreach ($books as $index => $book) {
            Book::updateOrCreate(
                ['kode_buku' => 'BKS-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT)],
                $book,
            );
        }
    }
}
