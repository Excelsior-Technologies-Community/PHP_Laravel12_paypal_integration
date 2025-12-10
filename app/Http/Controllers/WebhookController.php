<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $paypalService;

    public function __construct(PayPalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    /**
     * Handle PayPal webhook events
     */
    public function handleWebhook(Request $request)
    {
        // Get headers
        $headers = [
            'paypal-auth-algo' => $request->header('PAYPAL-AUTH-ALGO'),
            'paypal-cert-url' => $request->header('PAYPAL-CERT-URL'),
            'paypal-transmission-id' => $request->header('PAYPAL-TRANSMISSION-ID'),
            'paypal-transmission-sig' => $request->header('PAYPAL-TRANSMISSION-SIG'),
            'paypal-transmission-time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
        ];

        // Get raw body
        $body = $request->getContent();
        $event = json_decode($body, true);

        Log::info('PayPal Webhook Received:', [
            'event_type' => $event['event_type'] ?? 'unknown',
            'headers' => $headers,
            'body' => $event,
        ]);

        // Verify webhook signature
        $isVerified = $this->paypalService->verifyWebhook($headers, $body);
        
        if (!$isVerified) {
            Log::warning('PayPal Webhook verification failed', $event);
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        }

        // Process the event
        $this->processEvent($event);

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Process webhook events
     */
    private function processEvent($event)
    {
        $eventType = $event['event_type'] ?? '';
        
        switch ($eventType) {
            case 'PAYMENT.CAPTURE.COMPLETED':
                $this->handlePaymentCaptureCompleted($event);
                break;
                
            case 'PAYMENT.CAPTURE.DENIED':
                $this->handlePaymentCaptureDenied($event);
                break;
                
            case 'PAYMENT.CAPTURE.REFUNDED':
                $this->handlePaymentCaptureRefunded($event);
                break;
                
            case 'CHECKOUT.ORDER.APPROVED':
                $this->handleCheckoutOrderApproved($event);
                break;
                
            case 'CHECKOUT.ORDER.COMPLETED':
                $this->handleCheckoutOrderCompleted($event);
                break;
                
            default:
                Log::info("Unhandled PayPal webhook event: {$eventType}", $event);
        }
    }

    /**
     * Handle PAYMENT.CAPTURE.COMPLETED
     */
    private function handlePaymentCaptureCompleted($event)
    {
        $resource = $event['resource'];
        $paymentId = $resource['id'] ?? null;
        
        if ($paymentId) {
            $payment = Payment::where('payment_id', $resource['supplementary_data']['related_ids']['order_id'] ?? $paymentId)
                           ->first();
            
            if ($payment) {
                $payment->update([
                    'payment_status' => 'COMPLETED',
                    'payer_id' => $resource['payer']['payer_id'] ?? $payment->payer_id,
                    'payer_email' => $resource['payer']['email_address'] ?? $payment->payer_email,
                    'payment_details' => array_merge($payment->payment_details ?? [], [
                        'webhook_capture' => $resource,
                        'webhook_received_at' => now()->toISOString(),
                    ]),
                ]);
                
                Log::info("Payment {$paymentId} marked as COMPLETED via webhook");
            }
        }
    }

    /**
     * Handle PAYMENT.CAPTURE.DENIED
     */
    private function handlePaymentCaptureDenied($event)
    {
        $resource = $event['resource'];
        $paymentId = $resource['id'] ?? null;
        
        if ($paymentId) {
            $payment = Payment::where('payment_id', $resource['supplementary_data']['related_ids']['order_id'] ?? $paymentId)
                           ->first();
            
            if ($payment) {
                $payment->update([
                    'payment_status' => 'DENIED',
                    'payment_details' => array_merge($payment->payment_details ?? [], [
                        'webhook_denial' => $resource,
                        'denial_reason' => $resource['status_details']['reason'] ?? 'unknown',
                    ]),
                ]);
            }
        }
    }

    /**
     * Handle PAYMENT.CAPTURE.REFUNDED
     */
    private function handlePaymentCaptureRefunded($event)
    {
        $resource = $event['resource'];
        $paymentId = $resource['id'] ?? null;
        
        if ($paymentId) {
            $payment = Payment::where('payment_id', $resource['supplementary_data']['related_ids']['order_id'] ?? $paymentId)
                           ->first();
            
            if ($payment) {
                $payment->update([
                    'payment_status' => 'REFUNDED',
                    'payment_details' => array_merge($payment->payment_details ?? [], [
                        'refund_details' => $resource,
                        'refunded_at' => now()->toISOString(),
                    ]),
                ]);
            }
        }
    }

    /**
     * Handle CHECKOUT.ORDER.APPROVED
     */
    private function handleCheckoutOrderApproved($event)
    {
        $resource = $event['resource'];
        $orderId = $resource['id'] ?? null;
        
        if ($orderId) {
            $payment = Payment::where('payment_id', $orderId)->first();
            
            if ($payment) {
                $payment->update([
                    'payment_status' => 'APPROVED',
                    'payment_details' => array_merge($payment->payment_details ?? [], [
                        'webhook_approval' => $resource,
                    ]),
                ]);
            }
        }
    }

    /**
     * Handle CHECKOUT.ORDER.COMPLETED
     */
    private function handleCheckoutOrderCompleted($event)
    {
        $resource = $event['resource'];
        $orderId = $resource['id'] ?? null;
        
        if ($orderId) {
            $payment = Payment::where('payment_id', $orderId)->first();
            
            if ($payment) {
                $payment->update([
                    'payment_status' => 'COMPLETED',
                    'payer_id' => $resource['payer']['payer_id'] ?? $payment->payer_id,
                    'payer_email' => $resource['payer']['email_address'] ?? $payment->payer_email,
                    'payment_details' => array_merge($payment->payment_details ?? [], [
                        'webhook_completion' => $resource,
                    ]),
                ]);
            }
        }
    }
}