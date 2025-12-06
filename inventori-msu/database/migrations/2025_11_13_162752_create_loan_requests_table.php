<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_requests', function (Blueprint $table) {
            $table->id();

            // data peminjam (guest)
            $table->string('borrower_name');
            $table->string('borrower_email');
            $table->string('borrower_phone');
            $table->text('borrower_reason');

            // tanggal pinjam
            $table->date('loan_date_start');
            $table->date('loan_date_end');

            // status workflow pengelola
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'handed_over',
                'returned'
            ])->default('pending');

            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_requests');
    }
};
