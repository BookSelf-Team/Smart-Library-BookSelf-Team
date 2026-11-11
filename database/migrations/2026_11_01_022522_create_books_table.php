<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('books')) {
            return;
        }

        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('kode_buku', 15)->unique();
            $table->string('judul', 150);
            $table->string('pengarang', 100);
            $table->integer('tahun_terbit');
            $table->integer('jumlah_stok')->default(1);
            $table->text('sinopsis')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
