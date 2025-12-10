<?php

use App\Http\Controllers\PayPalController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('payments.create');
});

// Payment Routes
Route::controller(PayPalController::class)->group(function () {
    Route::get('/payments/create', 'create')->name('payments.create');
    Route::post('/payments/process', 'process')->name('payments.process');
    Route::get('/payments/success', 'success')->name('paypal.success');
    Route::get('/payments/cancel', 'cancel')->name('paypal.cancel');
    Route::get('/payments', 'index')->name('payments.index');
    Route::get('/payments/{id}', 'show')->name('payments.show'); // Added show route
});

// Webhook Route (without CSRF protection)
Route::post('/webhook/paypal', [WebhookController::class, 'handleWebhook'])
    ->name('webhook.paypal')
    ->withoutMiddleware(['web']);

// Debug route for testing
Route::get('/debug/payments', function () {
    $payments = \App\Models\Payment::all();
    return response()->json($payments);
});