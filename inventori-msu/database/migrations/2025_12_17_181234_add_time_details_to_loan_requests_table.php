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
            if (!Schema::hasColumn('loan_requests', 'start_time')) {
                $table->time('start_time')->nullable()->after('loan_date_end');
            }
            if (!Schema::hasColumn('loan_requests', 'duration')) {
                $table->integer('duration')->nullable()->after('start_time'); // dalam jam
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'duration']);
        });
    }
};
