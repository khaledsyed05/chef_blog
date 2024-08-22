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
        Schema::table('article_tag', function (Blueprint $table) {
            $table->unsignedBiginteger('article_id');
            $table->unsignedBiginteger('tag_id');

            $table->foreign('article_id')->references('id')
                ->on('articles')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')
                ->on('tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipe_tag', function (Blueprint $table) {
            $table->dropColumn('article_id');
            $table->dropColumn('tag_id');
        });
    }
};
