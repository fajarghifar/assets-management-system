<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category');
            $table->text('address')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('name');
            $table->index('category');
        });

        DB::statement(
            'ALTER TABLE areas MODIFY code VARCHAR(50) COLLATE utf8mb4_unicode_ci UNIQUE'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
