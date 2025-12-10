<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample payments for testing
        Payment::create([
            'payment_id' => 'PAYID-' . strtoupper(bin2hex(random_bytes(8))),
            'payer_email' => 'buyer@example.com',
            'amount' => 25.00,
            'currency' => 'USD',
            'payment_status' => 'COMPLETED',
            'description' => 'Sample completed payment',
        ]);

        Payment::create([
            'payment_id' => 'PAYID-' . strtoupper(bin2hex(random_bytes(8))),
            'payer_email' => 'customer@example.com',
            'amount' => 50.00,
            'currency' => 'USD',
            'payment_status' => 'CREATED',
            'description' => 'Sample pending payment',
        ]);
    }
}