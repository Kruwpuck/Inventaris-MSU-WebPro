<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('loan_request_id')
                ->constrained('loan_requests')
                ->onDelete('cascade');

            $table->foreignId('inventory_id')
                ->constrained('inventories')
                ->onDelete('cascade');

            $table->integer('quantity')->default(1);

            $table->timestamps();

            // biar item yg sama ga dobel dalam 1 request
            $table->unique(['loan_request_id', 'inventory_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_items');
    }
};
