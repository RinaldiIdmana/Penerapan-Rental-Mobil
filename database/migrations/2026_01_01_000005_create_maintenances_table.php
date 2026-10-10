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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->date('service_date');
            $table->string('service_type');
            $table->decimal('cost', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->date('next_service_due_date')->index();
            $table->enum('status', ['scheduled', 'in_progress', 'completed'])->default('scheduled')->index();
            $table->timestamps();

            $table->index(['vehicle_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
