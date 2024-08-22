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
        Schema::table('recipe_translations', function (Blueprint $table) {
            $table->unsignedBigInteger('recipe_id')->after('id');
            $table->string('locale')->index()->after('recipe_id');

            $table->unique(['recipe_id', 'locale']);
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipe_translations', function (Blueprint $table) {
            //
        });
    }
};
