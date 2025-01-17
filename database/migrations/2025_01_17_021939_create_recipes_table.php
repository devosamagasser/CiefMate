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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('cover');
            $table->string('preparation_time');
            $table->tinyInteger('calories')->default(0);
            $table->tinyInteger('protein')->default(0);
            $table->tinyInteger('fats')->default(0);
            $table->tinyInteger('carbs')->default(0);
            $table->enum('status', ['favourite', 'completed', 'draft'])->default('draft');
            $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
