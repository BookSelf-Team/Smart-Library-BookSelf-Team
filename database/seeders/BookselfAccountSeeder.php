<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookselfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Administrator Bookself',
                'identifier' => 'ADM-000001',
                'email' => 'admin@bookself.com',
                'role' => Role::Admin,
                'password' => 'ADMIN123',
            ],
            [
                'name' => 'Petugas Bookself',
                'identifier' => 'P-001',
                'email' => 'petugas@bookself.com',
                'role' => Role::Petugas,
                'password' => 'PETUGAS123',
            ],
            [
                'name' => 'Siswa Bookself',
                'identifier' => '0072938491',
                'email' => 'siswa@bookself.com',
                'role' => Role::Siswa,
                'password' => 'SISWA123',
            ],
        ];

        foreach ($accounts as $account) {
            $user = User::where('email', $account['email'])->first()
                ?? User::where('identifier', $account['identifier'])->first();

            $attributes = [
                'name' => $account['name'],
                'identifier' => $account['identifier'],
                'email' => $account['email'],
                'role' => $account['role']->value,
                'password' => $account['password'],
            ];

            if ($user !== null) {
                $user->update($attributes);
            } else {
                User::create($attributes);
            }
        }
    }
}
