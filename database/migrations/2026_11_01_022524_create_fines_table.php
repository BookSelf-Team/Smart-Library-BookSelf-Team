<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fines')) {
            return;
        }

        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->unique()->constrained('loans')->cascadeOnDelete();
            $table->integer('nominal');
            $table->string('status_pembayaran', 20)->default('BELUM');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
