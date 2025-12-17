<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            // Kolom untuk Program Studi / Unit
            if (!Schema::hasColumn('loan_requests', 'department')) {
                $table->string('department')->nullable()->after('borrower_phone');
            }
            // Kolom untuk NIM/NIP
            if (!Schema::hasColumn('loan_requests', 'nim_nip')) {
                $table->string('nim_nip')->nullable()->after('department');
            }
            // Kolom untuk Deskripsi Keperluan (Detailed)
            if (!Schema::hasColumn('loan_requests', 'description')) {
                $table->text('description')->nullable()->after('borrower_reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            $table->dropColumn(['department', 'nim_nip', 'description']);
        });
    }
};
