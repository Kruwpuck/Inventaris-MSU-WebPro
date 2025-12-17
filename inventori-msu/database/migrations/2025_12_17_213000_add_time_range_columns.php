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
            if (!Schema::hasColumn('loan_requests', 'loan_date_end')) {
                $table->date('loan_date_end')->nullable()->after('loan_date_start');
            }
            if (!Schema::hasColumn('loan_requests', 'start_time')) {
                $table->time('start_time')->nullable()->after('loan_date_end');
            }
            if (!Schema::hasColumn('loan_requests', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            $table->dropColumn(['loan_date_end', 'start_time', 'end_time']);
        });
    }
};
