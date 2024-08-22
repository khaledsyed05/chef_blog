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
        Schema::create('seos', function (Blueprint $table) {
            $table->id();
            $table->morphs('seoable');
            $table->timestamps();
        });

        Schema::create('seo_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seo_id')->constrained()->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->unique(['seo_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_translations');
        Schema::dropIfExists('seos');
    }
};
