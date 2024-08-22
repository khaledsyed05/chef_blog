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
        Schema::create('recipe_translations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->json('ingredients');
            $table->json('instructions');
            $table->integer('total_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_translations');
    }
};
