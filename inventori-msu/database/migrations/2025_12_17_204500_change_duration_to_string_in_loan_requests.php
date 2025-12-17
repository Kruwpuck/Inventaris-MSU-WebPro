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
            // Drop description as it is replaced/merged into duration
            if (Schema::hasColumn('loan_requests', 'description')) {
                $table->dropColumn('description');
            }
            
            // Change duration to string to hold text like "Pagi (06.00 - 12.00)"
            if (Schema::hasColumn('loan_requests', 'duration')) {
                $table->string('duration')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->integer('duration')->change();
        });
    }
};
