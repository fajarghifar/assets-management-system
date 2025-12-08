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
        Schema::create('installed_item_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installed_item_id')
                ->constrained('installed_items')
                ->cascadeOnDelete();
            $table->foreignId('location_id')
                ->constrained('locations')
                ->cascadeOnDelete();
            $table->date('installed_at');
            $table->date('removed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['installed_item_id', 'installed_at']);
            $table->index(['location_id', 'removed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installed_item_location_histories');
    }
};
