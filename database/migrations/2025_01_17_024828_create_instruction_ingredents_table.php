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
        Schema::create('instruction_ingredents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instruction_id')->constrained('instructions')->cascadeOnDelete();
            $table->foreignId('ingredent_id')->constrained('ingredents')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instruction_ingredents');
    }
};
