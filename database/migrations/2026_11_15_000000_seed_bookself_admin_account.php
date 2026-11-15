<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Seed the administrator account used by the Bookself deployment.
     *
     * The EthioDeploy project runs on an ephemeral SQLite database, so this
     * migration makes sure the account exists after every deployment.
     */
    public function up(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        if (User::query()->where('email', 'admin@bookself.com')->exists()) {
            return;
        }

        User::create([
            'name' => 'Administrator Bookself',
            'identifier' => User::generateUniqueIdentifier(Role::Admin),
            'email' => 'admin@bookself.com',
            'role' => Role::Admin,
            'password' => 'ADMIN123',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
