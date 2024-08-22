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
        Schema::create('nutrition_facts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recipe_id');
            $table->timestamps();

            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
        });

        Schema::create('nutrition_fact_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nutrition_fact_id');
            $table->string('locale')->index();

            $table->string('calories')->nullable();
            $table->string('protein')->nullable();
            $table->string('fat')->nullable();

            $table->unique(['nutrition_fact_id', 'locale']);
            $table->foreign('nutrition_fact_id')->references('id')->on('nutrition_facts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrition_fact_translations');
        Schema::dropIfExists('nutrition_facts');
    }
};
