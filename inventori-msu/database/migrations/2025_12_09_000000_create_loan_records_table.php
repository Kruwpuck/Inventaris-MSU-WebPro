<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_records', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('loan_request_id')
                  ->constrained('loan_requests')
                  ->onDelete('cascade');

            // Waktu realisasi
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('returned_at')->nullable();

            // Status submit ke pengelola (untuk pelaporan)
            $table->boolean('is_submitted')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_records');
    }
};
