@extends('layout')

@section('title', 'Payment History')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">
                <i class="fas fa-history mr-2 text-blue-600"></i> Payment History
            </h1>

            @if($payments->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-receipt text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No Payments Yet</h3>
                    <p class="text-gray-500 mb-6">You haven't made any payments yet.</p>
                    <a href="{{ route('payments.create') }}"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                        <i class="fas fa-plus-circle mr-2"></i> Make First Payment
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payer
                                    Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($payments as $payment)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ substr($payment->payment_id, 0, 12) }}...
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $payment->id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">${{ number_format($payment->amount, 2) }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $payment->currency }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
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
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                            @if($payment->payment_status == 'COMPLETED')
                                                <i class="fas fa-check-circle mr-1"></i>
                                            @elseif($payment->payment_status == 'CREATED')
                                                <i class="fas fa-clock mr-1"></i>
                                            @elseif($payment->payment_status == 'APPROVED')
                                                <i class="fas fa-thumbs-up mr-1"></i>
                                            @elseif($payment->payment_status == 'DENIED')
                                                <i class="fas fa-times-circle mr-1"></i>
                                            @elseif($payment->payment_status == 'REFUNDED')
                                                <i class="fas fa-undo mr-1"></i>
                                            @endif
                                            {{ $payment->payment_status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $payment->payer_email ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $payment->created_at->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $payment->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('payments.show', $payment->id) }}"
                                            class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if($payment->payment_status == 'COMPLETED')
                                            <button onclick="window.print()" class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-print"></i> Print
                                            </button>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Details Row (hidden by default) -->
                                <tr id="details-{{ $payment->id }}" class="hidden">
                                    <td colspan="6" class="px-6 py-4 bg-gray-50">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <h4 class="font-semibold text-gray-700 mb-2">Payment Information</h4>
                                                <div class="space-y-1">
                                                    <p><span class="text-gray-600">Payment ID:</span> {{ $payment->payment_id }}</p>
                                                    <p><span class="text-gray-600">Description:</span>
                                                        {{ $payment->description ?? 'N/A' }}</p>
                                                    <p><span class="text-gray-600">Payer ID:</span>
                                                        {{ $payment->payer_id ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-700 mb-2">Transaction Details</h4>
                                                <div class="space-y-1">
                                                    <p><span class="text-gray-600">Created:</span>
                                                        {{ $payment->created_at->format('Y-m-d H:i:s') }}</p>
                                                    <p><span class="text-gray-600">Updated:</span>
                                                        {{ $payment->updated_at->format('Y-m-d H:i:s') }}</p>
                                                    <p><span class="text-gray-600">Invoice ID:</span>
                                                        {{ $payment->invoice_id ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        @if($payment->payment_details)
                                            <div class="mt-4">
                                                <button onclick="toggleRawData('{{ $payment->id }}')"
                                                    class="text-sm text-blue-600 hover:text-blue-800 mb-2">
                                                    <i class="fas fa-code mr-1"></i> Toggle Raw Data
                                                </button>
                                                <div id="raw-data-{{ $payment->id }}" class="hidden">
                                                    <pre
                                                        class="bg-gray-900 text-gray-100 p-4 rounded text-xs overflow-x-auto">{{ json_encode($payment->payment_details, JSON_PRETTY_PRINT) }}</pre>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function showDetails(paymentId) {
            const detailsRow = document.getElementById(`details-${paymentId}`);
            detailsRow.classList.toggle('hidden');
        }

        function toggleRawData(paymentId) {
            const rawData = document.getElementById(`raw-data-${paymentId}`);
            rawData.classList.toggle('hidden');
        }
    </script>
@endsection