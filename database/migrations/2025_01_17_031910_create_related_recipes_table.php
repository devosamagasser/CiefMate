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
        Schema::create('related_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('primary_recipe_id')->constrained('recipes')->cascadeOnDelete();
            $table->foreignId('related_recipe_id')->constrained('recipes')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('related_recipes');
    }
};
