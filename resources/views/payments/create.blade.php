@extends('layout')

@section('title', 'Create Payment')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-credit-card mr-2 text-blue-600"></i> Create New Payment
        </h1>

        <form action="{{ route('payments.process') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label for="amount" class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-dollar-sign mr-1"></i> Amount (USD)
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500">$</span>
                    </div>
                    <input 
                        type="number" 
                        id="amount" 
                        name="amount" 
                        min="0.01" 
                        step="0.01" 
                        value="{{ old('amount', '10.00') }}"
                        class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="0.00"
                        required
                    >
                </div>
                <p class="text-gray-500 text-sm mt-1">Minimum amount: $0.01 USD</p>
            </div>

            <div>
                <label for="description" class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-file-alt mr-1"></i> Description (Optional)
                </label>
                <input 
                    type="text" 
                    id="description" 
                    name="description" 
                    value="{{ old('description') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="Payment for services"
                >
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fab fa-paypal text-blue-600 text-xl mt-1 mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-blue-800">Secure PayPal Payment</h3>
                        <p class="text-blue-700 text-sm mt-1">
                            You will be redirected to PayPal's secure payment page to complete your transaction.
                        </p>
                    </div>
                </div>
            </div>

            <button 
                type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-300 transform hover:-translate-y-1"
            >
                <i class="fab fa-paypal mr-2"></i> Proceed to PayPal
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Test Credentials (Sandbox)</h3>
            <div class="bg-gray-50 rounded-lg p-4 text-sm">
                <p class="text-gray-600 mb-2"><strong>Buyer Account:</strong></p>
                <p class="text-gray-700">Email: sb-43v5ch27593741@personal.example.com</p>
                <p class="text-gray-700">Password: )S0&I5s-</p>
                <p class="text-gray-600 mt-3"><strong>Seller Account:</strong></p>
                <p class="text-gray-700">Email: sb-43v5ch27593741@business.example.com</p>
                <p class="text-gray-700">Password: )S0&I5s-</p>
            </div>
        </div>
    </div>
</div>
@endsection