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
        Schema::table('loans', function (Blueprint $table) {
            $table->index('book_id', 'loans_book_id_index');
            $table->index('user_id', 'loans_user_id_index');
            $table->index(['user_id', 'status'], 'loans_user_id_status_index');
            $table->index(['status', 'batas_kembali'], 'loans_status_batas_kembali_index');
            $table->index('tanggal_dikembalikan', 'loans_tanggal_dikembalikan_index');
        });

        Schema::table('fines', function (Blueprint $table) {
            $table->index('status_pembayaran', 'fines_status_pembayaran_index');
        });

        Schema::table('books', function (Blueprint $table) {
            $table->index('judul', 'books_judul_index');
            $table->index('pengarang', 'books_pengarang_index');
            $table->index('jumlah_stok', 'books_jumlah_stok_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('role', 'users_role_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropIndex('loans_book_id_index');
            $table->dropIndex('loans_user_id_index');
            $table->dropIndex('loans_user_id_status_index');
            $table->dropIndex('loans_status_batas_kembali_index');
            $table->dropIndex('loans_tanggal_dikembalikan_index');
        });

        Schema::table('fines', function (Blueprint $table) {
            $table->dropIndex('fines_status_pembayaran_index');
        });

        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex('books_judul_index');
            $table->dropIndex('books_pengarang_index');
            $table->dropIndex('books_jumlah_stok_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_index');
        });
    }
};
