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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('license_plate')->unique();
            $table->string('brand');
            $table->string('model');
            $table->integer('year');
            $table->enum('transmission', ['manual', 'automatic']);
            $table->decimal('rental_rate_per_day', 12, 2);
            $table->enum('status', ['available', 'rented', 'maintenance', 'out_of_service'])->default('available')->index();
            $table->string('image_url')->nullable();
            $table->integer('capacity')->default(5);
            $table->timestamps();

            $table->index(['brand', 'model']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
