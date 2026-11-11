<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'identifier')) {
                $table->string('identifier')->nullable()->unique()->after('name');
            }

            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role', 20)->default('siswa')->after('identifier');
            }
        });
    }

    public function down(): void
    {
        // Kolom identifier & role adalah bagian inti skema, jangan dihapus.
    }
};
