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
            // Re-add duration column if it doesn't exist
            if (!Schema::hasColumn('loan_requests', 'duration')) {
                $table->string('duration')->nullable()->after('loan_date_start');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            if (Schema::hasColumn('loan_requests', 'duration')) {
                $table->dropColumn('duration');
            }
        });
    }
};
