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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained()->onDelete('restrict');
            // Kode Otomatis: Fixed 9 Karakter (Cth: BKS01-A9X)
            $table->char('code', 9)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('name');
            $table->index(['area_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
