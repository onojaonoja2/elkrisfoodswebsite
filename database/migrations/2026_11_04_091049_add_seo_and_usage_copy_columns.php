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
        Schema::table('meal_types', function (Blueprint $table) {
            $table->text('usage_copy')->nullable()->after('slug');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('nutrition_notes');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('og_image')->nullable()->after('meta_description');
        });

        Schema::table('recipes', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('summary');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('og_image')->nullable()->after('meta_description');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('reading_minutes');
            $table->string('og_image')->nullable()->after('meta_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meal_types', function (Blueprint $table) {
            $table->dropColumn('usage_copy');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'og_image']);
        });

        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'og_image']);
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'og_image']);
        });
    }
};
