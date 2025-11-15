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
        Schema::create('borrowing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->restrictOnDelete();
            // Untuk `fixed`: merujuk ke instance spesifik
            $table->foreignId('fixed_instance_id')->nullable()
                ->constrained('fixed_item_instances')
                ->nullOnDelete();
            // Untuk `consumable`: lokasi asal stok
            $table->foreignId('location_id')->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->integer('quantity')->default(1); // 1 untuk fixed, >1 untuk consumable
            $table->integer('returned_quantity')->default(0);
            $table->dateTime('returned_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('item_id');
            $table->index('fixed_instance_id');
            $table->index('location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowing_items');
    }
};
