<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('phone')->nullable();
            $table->string('opening_hours')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('google_maps_url')->nullable();
            $table->json('products_available')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['state', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
