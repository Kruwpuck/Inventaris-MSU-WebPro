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
        Schema::create('loan_requests', function (Blueprint $table) {
            $table->id();
            $table->string('borrower_name');
            $table->string('borrower_email');
            $table->string('borrower_phone');
            $table->text('borrower_reason');
            $table->date('loan_date_start');
            $table->date('loan_date_end');
            $table->enum('status', ['pending', 'approved', 'rejected', 'handed_over', 'returned'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_requests');
    }
};
