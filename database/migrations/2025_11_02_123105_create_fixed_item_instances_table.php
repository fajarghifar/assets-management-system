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
        Schema::create('fixed_item_instances', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->foreignId('item_id')->constrained()->restrictOnDelete();
            $table->string('serial_number')->nullable()->unique();
            $table->string('status')->default('available');
            $table->foreignId('location_id')
                ->nullable()
                ->constrained('locations')
                ->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index('item_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_item_instances');
    }
};
