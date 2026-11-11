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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->integer('queue_position')->default(1);
            $table->string('status', 20)->default('WAITING'); // WAITING, READY, FULFILLED, CANCELLED
            $table->timestamp('reserved_at')->useCurrent();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['book_id', 'status'], 'reservations_book_id_status_index');
            $table->index(['user_id', 'status'], 'reservations_user_id_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
