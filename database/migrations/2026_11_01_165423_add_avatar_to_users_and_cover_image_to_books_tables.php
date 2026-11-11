<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('email');
            }
        });

        Schema::table('books', function (Blueprint $table) {
            if (! Schema::hasColumn('books', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('sinopsis');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'avatar')) {
                $table->dropColumn('avatar');
            }
        });

        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'cover_image')) {
                $table->dropColumn('cover_image');
            }
        });
    }
};
