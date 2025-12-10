@extends('layout')

@section('title', 'Payment Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-blue-600 px-8 py-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-white">
                    <i class="fas fa-receipt mr-2"></i> Payment Details
                </h1>
                <a href="{{ route('payments.index') }}" class="text-white hover:text-blue-200">
                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                </a>
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Payment Information -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Payment Information</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-gray-600 text-sm">Payment ID</p>
                            <p class="font-medium text-gray-800">{{ $payment->payment_id }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Internal ID</p>
                            <p class="font-medium text-gray-800">#{{ $payment->id }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Amount</p>
                            <p class="text-2xl font-bold text-blue-600">
                                ${{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Status</p>
                            @php
                                $statusColors = [
                                    'COMPLETED' => 'bg-green-100 text-green-800',
                                    'CREATED' => 'bg-blue-100 text-blue-800',
                                    'APPROVED' => 'bg-yellow-100 text-yellow-800',
                                    'DENIED' => 'bg-red-100 text-red-800',
                                    'REFUNDED' => 'bg-purple-100 text-purple-800',
                                ];
                                $color = $statusColors[$payment->payment_status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $color }}">
                                @if($payment->payment_status == 'COMPLETED')
                                    <i class="fas fa-check-circle mr-2"></i>
                                @elseif($payment->payment_status == 'CREATED')
                                    <i class="fas fa-clock mr-2"></i>
                                @elseif($payment->payment_status == 'APPROVED')
                                    <i class="fas fa-thumbs-up mr-2"></i>
                                @elseif($payment->payment_status == 'DENIED')
                                    <i class="fas fa-times-circle mr-2"></i>
                                @elseif($payment->payment_status == 'REFUNDED')
                                    <i class="fas fa-undo mr-2"></i>
                                @endif
                                {{ $payment->payment_status }}
                            </span>
                        </div>
                        @if($payment->description)
                        <div>
                            <p class="text-gray-600 text-sm">Description</p>
                            <p class="font-medium text-gray-800">{{ $payment->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Payer Information -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Payer Information</h2>
                    <div class="space-y-3">
                        @if($payment->payer_email)
                        <div>
                            <p class="text-gray-600 text-sm">Email</p>
                            <p class="font-medium text-gray-800">{{ $payment->payer_email }}</p>
                        </div>
                        @endif
                        
                        @if($payment->payer_id)
                        <div>
                            <p class="text-gray-600 text-sm">Payer ID</p>
                            <p class="font-medium text-gray-800">{{ $payment->payer_id }}</p>
                        </div>
                        @endif
                        
                        <div>
                            <p class="text-gray-600 text-sm">Created</p>
                            <p class="font-medium text-gray-800">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        
                        <div>
                            <p class="text-gray-600 text-sm">Last Updated</p>
                            <p class="font-medium text-gray-800">{{ $payment->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                        
                        @if($payment->invoice_id)
                        <div>
                            <p class="text-gray-600 text-sm">Invoice ID</p>
                            <p class="font-medium text-gray-800">{{ $payment->invoice_id }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Payment Details JSON -->
            @if($payment->payment_details)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Raw Payment Details</h3>
                <div class="relative">
                    <button 
                        onclick="copyToClipboard()" 
                        class="absolute right-4 top-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition z-10"
                    >
                        <i class="fas fa-copy mr-1"></i> Copy JSON
                    </button>
                    <div id="json-content" class="bg-gray-900 text-gray-100 rounded-lg p-4 font-mono text-sm overflow-x-auto">
                        <pre>{{ json_encode($payment->payment_details, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <a 
                    href="{{ route('payments.create') }}" 
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center font-semibold py-3 px-4 rounded-lg transition"
                >
                    <i class="fas fa-plus-circle mr-2"></i> New Payment
                </a>
                
                @if($payment->payment_status == 'COMPLETED')
                <button 
                    onclick="window.print()" 
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center font-semibold py-3 px-4 rounded-lg transition"
                >
                    <i class="fas fa-print mr-2"></i> Print Receipt
                </button>
                @endif
                
                @if($payment->payment_status == 'COMPLETED')
                <a 
                    href="#" 
                    class="flex-1 bg-purple-600 hover:bg-purple-700 text-white text-center font-semibold py-3 px-4 rounded-lg transition"
                >
                    <i class="fas fa-redo mr-2"></i> Refund
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard() {
    const jsonContent = document.getElementById('json-content').textContent;
    navigator.clipboard.writeText(jsonContent).then(() => {
        alert('JSON copied to clipboard!');
    });
}
</script>
@endsection