<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('loans')) {
            return;
        }

        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->date('tanggal_pinjam')->useCurrent();
            $table->date('batas_kembali');
            $table->date('tanggal_dikembalikan')->nullable();
            $table->string('status', 30)->default('ACTIVE');
            $table->timestamp('created_at')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
