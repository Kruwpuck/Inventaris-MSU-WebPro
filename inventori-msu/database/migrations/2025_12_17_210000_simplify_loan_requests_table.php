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
            // Drop columns as requested to simplify
            if (Schema::hasColumn('loan_requests', 'loan_date_end')) {
                $table->dropColumn('loan_date_end');
            }
            if (Schema::hasColumn('loan_requests', 'duration')) {
                $table->dropColumn('duration');
            }
            if (Schema::hasColumn('loan_requests', 'start_time')) {
                $table->dropColumn('start_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            $table->dateTime('loan_date_end')->nullable();
            $table->string('duration')->nullable();
            $table->time('start_time')->nullable();
        });
    }
};
