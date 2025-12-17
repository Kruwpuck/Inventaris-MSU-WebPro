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
        Schema::table('loan_requests', function (Blueprint $table) {
            
            // 1. Department (Program Studi / Unit)
            if (!Schema::hasColumn('loan_requests', 'department')) {
                $table->string('department')->nullable();
            }

            // 2. NIM/NIP
            if (!Schema::hasColumn('loan_requests', 'nim_nip')) {
                $table->string('nim_nip')->nullable();
            }

            // 3. Activity Description (Deskripsi Kegiatan)
            if (!Schema::hasColumn('loan_requests', 'activity_description')) {
                $table->text('activity_description')->nullable();
            }

            // 4. Activity Location (Lokasi Kegiatan)
            if (!Schema::hasColumn('loan_requests', 'activity_location')) {
                $table->string('activity_location')->nullable()->default('Telkom University');
            }

            // 5. Proposal Path
            if (!Schema::hasColumn('loan_requests', 'proposal_path')) {
                $table->string('proposal_path')->nullable();
            }

            // 6. KTP Path
            if (!Schema::hasColumn('loan_requests', 'ktp_path')) {
                $table->string('ktp_path')->nullable();
            }

            // 7. Time Details
            if (!Schema::hasColumn('loan_requests', 'start_time')) {
                $table->time('start_time')->nullable();
            }
            if (!Schema::hasColumn('loan_requests', 'end_time')) {
                $table->time('end_time')->nullable();
            }
            
            // 8. Donation Amount
            if (!Schema::hasColumn('loan_requests', 'donation_amount')) {
                $table->decimal('donation_amount', 15, 2)->default(0);
            }
            
            // 9. Duration (some migrations dropped it, controller uses it)
             if (!Schema::hasColumn('loan_requests', 'duration')) {
                $table->string('duration')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We generally don't want to drop these in a fix migration as they might have been added by others.
    }
};
