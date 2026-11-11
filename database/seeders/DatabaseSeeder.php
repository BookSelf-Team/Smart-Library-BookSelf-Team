<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Administrator', 'identifier' => 'ADMIN001', 'role' => Role::Admin->value],
            ['name' => 'Pustakawan Contoh', 'identifier' => '198503122009121003', 'role' => Role::Pustakawan->value],
            ['name' => 'Petugas Sirkulasi', 'identifier' => 'P-001', 'role' => Role::Petugas->value],
            ['name' => 'Guru Contoh', 'identifier' => '9283719482910293', 'role' => Role::Guru->value],
            ['name' => 'Siswa Contoh', 'identifier' => '0072938491', 'role' => Role::Siswa->value],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['identifier' => $user['identifier']],
                $user + ['password' => 'password123'],
            );
        }

        $this->call(BookSeeder::class);
    }
}
