<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id')->unique();
            $table->string('payer_id')->nullable();
            $table->string('payer_email')->nullable(); // Changed to nullable
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('payment_status')->nullable(); // Added nullable
            $table->json('payment_details')->nullable();
            $table->string('invoice_id')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
            
            $table->index(['payment_id', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};