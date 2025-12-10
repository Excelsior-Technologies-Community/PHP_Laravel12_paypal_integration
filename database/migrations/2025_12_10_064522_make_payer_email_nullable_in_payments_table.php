<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Make payer_email nullable
            $table->string('payer_email')->nullable()->change();
            
            // Also make payment_status nullable with default
            $table->string('payment_status')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payer_email')->nullable(false)->change();
            $table->string('payment_status')->nullable(false)->change();
        });
    }
};