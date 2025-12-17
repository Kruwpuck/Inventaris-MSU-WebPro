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
            // Add new columns
            if (!Schema::hasColumn('loan_requests', 'ktp_path')) {
                $table->string('ktp_path')->nullable()->after('proposal_path');
            }
            if (!Schema::hasColumn('loan_requests', 'activity_location')) {
                $table->string('activity_location')->default('Telkom University')->after('ktp_path');
            }
            if (!Schema::hasColumn('loan_requests', 'activity_description')) {
                $table->text('activity_description')->nullable()->after('activity_location');
            }

            // Drop duration if exists
            if (Schema::hasColumn('loan_requests', 'duration')) {
                $table->dropColumn('duration');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            $table->dropColumn(['ktp_path', 'activity_location', 'activity_description']);
            $table->string('duration')->nullable();
        });
    }
};
