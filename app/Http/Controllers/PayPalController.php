<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayPalController extends Controller
{
    protected $paypalService;

    public function __construct(PayPalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    /**
     * Show payment form
     */
    public function create()
    {
        return view('payments.create');
    }

    /**
     * Process payment
     */
    public function process(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $order = $this->paypalService->createOrder(
                $request->amount,
                'USD',
                $request->description
            );

            // Store initial payment record with nullable fields
            $payment = Payment::create([
                'payment_id' => $order['id'],
                'amount' => $request->amount,
                'currency' => 'USD',
                'payment_status' => 'CREATED',
                'description' => $request->description,
                'payment_details' => $order,
                // payer_email is nullable, so we don't need to set it here
                // It will be updated after payment completion
            ]);

            // Find approval URL
            $approveUrl = '';
            foreach ($order['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    $approveUrl = $link['href'];
                    break;
                }
            }

            if ($approveUrl) {
                return redirect()->away($approveUrl);
            }

            return back()->with('error', 'Could not create PayPal payment.');
        } catch (\Exception $e) {
            Log::error('Payment Processing Error: ' . $e->getMessage());
            return back()->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful payment
     */
    public function success(Request $request)
    {
        try {
            $token = $request->query('token');
            
            if (!$token) {
                return redirect()->route('payments.create')->with('error', 'Payment token not found.');
            }

            // Capture the payment
            $capture = $this->paypalService->captureOrder($token);
            
            // Update payment record
            $payment = Payment::where('payment_id', $token)->firstOrFail();
            
            // Extract payer email from capture response
            $payerEmail = null;
            if (isset($capture['payer']['email_address'])) {
                $payerEmail = $capture['payer']['email_address'];
            } elseif (isset($capture['payment_source']['paypal']['email_address'])) {
                $payerEmail = $capture['payment_source']['paypal']['email_address'];
            }

            $payment->update([
                'payment_status' => strtoupper($capture['status'] ?? 'COMPLETED'),
                'payer_id' => $capture['payer']['payer_id'] ?? null,
                'payer_email' => $payerEmail,
                'payment_details' => $capture,
            ]);

            return view('payments.success', [
                'payment' => $payment,
                'details' => $capture,
            ]);
        } catch (\Exception $e) {
            Log::error('Payment Success Error: ' . $e->getMessage());
            return redirect()->route('payments.create')->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle cancelled payment
     */
    public function cancel()
    {
        return view('payments.cancel');
    }

    /**
     * List all payments
     */
    public function index()
    {
        $payments = Payment::orderBy('created_at', 'desc')->paginate(10);
        return view('payments.index', compact('payments'));
    }

    /**
     * Show payment details
     */
    public function show($id)
    {
        $payment = Payment::findOrFail($id);
        return view('payments.show', compact('payment'));
    }
}