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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->foreignId('return_id')->nullable()->constrained('returns')->nullOnDelete();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('fine_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2);
            $table->enum('payment_status', ['unpaid', 'paid', 'overdue'])->default('unpaid')->index();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
