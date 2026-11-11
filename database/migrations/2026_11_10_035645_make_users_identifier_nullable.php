<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Identifier kini dibuat otomatis oleh sistem (mis. SIS-000001),
     * sehingga kolom boleh kosong sementara bagi pengguna lama.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('identifier')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('identifier')->nullable(false)->change();
        });
    }
};
