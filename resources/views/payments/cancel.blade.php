@extends('layout')

@section('title', 'Payment Cancelled')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-8 py-6">
            <div class="flex items-center">
                <div class="bg-white p-3 rounded-full mr-4">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Payment Cancelled</h1>
                    <p class="text-yellow-100">Your payment was not completed.</p>
                </div>
            </div>
        </div>

        <div class="p-8 text-center">
            <div class="mb-8">
                <i class="fas fa-shopping-cart text-gray-300 text-6xl mb-4"></i>
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Payment Not Processed</h2>
                <p class="text-gray-600">
                    You have cancelled the payment process. No charges were made to your account.
                </p>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
                <h3 class="font-semibold text-yellow-800 mb-2">Why was my payment cancelled?</h3>
                <ul class="text-yellow-700 text-left list-disc pl-5 space-y-1">
                    <li>You clicked "Cancel" on the PayPal page</li>
                    <li>You closed the payment window</li>
                    <li>Payment session expired (timeout)</li>
                    <li>You decided not to proceed with the payment</li>
                </ul>
            </div>

            <div class="space-y-4">
                <a 
                    href="{{ route('payments.create') }}" 
                    class="inline-block w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition"
                >
                    <i class="fas fa-redo mr-2"></i> Try Again
                </a>
                
                <p class="text-gray-500 text-sm">
                    Need help? <a href="#" class="text-blue-600 hover:text-blue-800">Contact Support</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection