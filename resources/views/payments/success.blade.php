@extends('layout')

@section('title', 'Payment Successful')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-8 py-6">
            <div class="flex items-center">
                <div class="bg-white p-3 rounded-full mr-4">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Payment Successful!</h1>
                    <p class="text-green-100">Thank you for your payment.</p>
                </div>
            </div>
        </div>

        <div class="p-8">
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Payment Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600 text-sm">Payment ID</p>
                        <p class="font-medium">{{ $payment->payment_id }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Amount</p>
                        <p class="font-medium">${{ number_format($payment->amount, 2) }} {{ $payment->currency }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Status</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check mr-1"></i> {{ $payment->payment_status }}
                        </span>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Date</p>
                        <p class="font-medium">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @if($payment->description)
                    <div class="md:col-span-2">
                        <p class="text-gray-600 text-sm">Description</p>
                        <p class="font-medium">{{ $payment->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            @if(isset($details) && is_array($details))
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Transaction Information</h3>
                <div class="bg-gray-900 text-gray-100 rounded-lg p-4 font-mono text-sm overflow-x-auto">
                    <pre>{{ json_encode($details, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            @endif

            <div class="flex flex-col sm:flex-row gap-4">
                <a 
                    href="{{ route('payments.create') }}" 
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center font-semibold py-3 px-4 rounded-lg transition"
                >
                    <i class="fas fa-plus-circle mr-2"></i> New Payment
                </a>
                <a 
                    href="{{ route('payments.index') }}" 
                    class="flex-1 bg-gray-600 hover:bg-gray-700 text-white text-center font-semibold py-3 px-4 rounded-lg transition"
                >
                    <i class="fas fa-history mr-2"></i> View History
                </a>
                <button 
                    onclick="window.print()" 
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center font-semibold py-3 px-4 rounded-lg transition"
                >
                    <i class="fas fa-print mr-2"></i> Print Receipt
                </button>
            </div>

            <div class="mt-8 text-center">
                <p class="text-gray-600">
                    A confirmation email has been sent to 
                    <span class="font-medium">{{ $payment->payer_email ?? 'your PayPal email' }}</span>
                </p>
                <p class="text-gray-500 text-sm mt-2">
                    Transaction ID: {{ $payment->id }} | 
                    Reference: {{ $payment->payment_id }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection