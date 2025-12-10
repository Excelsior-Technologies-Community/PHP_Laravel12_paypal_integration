<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    protected $client;
    protected $clientId;
    protected $clientSecret;
    protected $apiUrl;
    protected $webhookId;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
        $this->webhookId = config('services.paypal.webhook_id');
        
        $this->apiUrl = config('services.paypal.sandbox', true) 
            ? 'https://api.sandbox.paypal.com' 
            : 'https://api.paypal.com';
        
        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'timeout' => 30,
            'verify' => false, // For development only, remove in production
        ]);
    }

    /**
     * Get Access Token
     */
    private function getAccessToken()
    {
        try {
            $response = $this->client->post('/v1/oauth2/token', [
                'auth' => [$this->clientId, $this->clientSecret],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['access_token'];
        } catch (RequestException $e) {
            $error = $this->getErrorMessage($e);
            Log::error('PayPal Access Token Error: ' . $error);
            throw new \Exception('PayPal authentication failed: ' . $error);
        }
    }

    /**
     * Create PayPal Order
     */
    public function createOrder($amount, $currency = 'USD', $description = '')
    {
        try {
            $accessToken = $this->getAccessToken();
            
            $response = $this->client->post('/v2/checkout/orders', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'PayPal-Request-Id' => uniqid(),
                ],
                'json' => [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'amount' => [
                                'currency_code' => strtoupper($currency),
                                'value' => number_format($amount, 2, '.', ''),
                            ],
                            'description' => $description,
                        ]
                    ],
                    'application_context' => [
                        'return_url' => route('paypal.success'),
                        'cancel_url' => route('paypal.cancel'),
                        'brand_name' => config('app.name', 'Laravel PayPal'),
                        'user_action' => 'PAY_NOW',
                        'shipping_preference' => 'NO_SHIPPING',
                    ]
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            
            if (!isset($data['id'])) {
                throw new \Exception('Invalid response from PayPal');
            }
            
            return $data;
        } catch (RequestException $e) {
            $error = $this->getErrorMessage($e);
            Log::error('PayPal Create Order Error: ' . $error);
            throw new \Exception('Failed to create PayPal order: ' . $error);
        }
    }

    /**
     * Capture PayPal Order
     */
    public function captureOrder($orderId)
    {
        try {
            $accessToken = $this->getAccessToken();
            
            Log::info('Capturing PayPal order: ' . $orderId);
            
            $response = $this->client->post("/v2/checkout/orders/{$orderId}/capture", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'PayPal-Request-Id' => uniqid(),
                    'Prefer' => 'return=representation',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            
            Log::info('PayPal capture response:', $data);
            
            if (!isset($data['status'])) {
                throw new \Exception('Invalid capture response from PayPal');
            }
            
            return $data;
        } catch (RequestException $e) {
            $error = $this->getErrorMessage($e);
            Log::error('PayPal Capture Order Error for ' . $orderId . ': ' . $error);
            throw new \Exception('Failed to capture PayPal order: ' . $error);
        }
    }

    /**
     * Verify Webhook Signature
     */
    public function verifyWebhook($headers, $body)
    {
        try {
            $accessToken = $this->getAccessToken();
            
            $response = $this->client->post('/v1/notifications/verify-webhook-signature', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'auth_algo' => $headers['paypal-auth-algo'] ?? '',
                    'cert_url' => $headers['paypal-cert-url'] ?? '',
                    'transmission_id' => $headers['paypal-transmission-id'] ?? '',
                    'transmission_sig' => $headers['paypal-transmission-sig'] ?? '',
                    'transmission_time' => $headers['paypal-transmission-time'] ?? '',
                    'webhook_id' => $this->webhookId,
                    'webhook_event' => json_decode($body, true),
                ],
            ]);

            $result = json_decode($response->getBody(), true);
            return $result['verification_status'] === 'SUCCESS';
        } catch (\Exception $e) {
            Log::error('PayPal Webhook Verification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get Order Details
     */
    public function getOrder($orderId)
    {
        try {
            $accessToken = $this->getAccessToken();
            
            $response = $this->client->get("/v2/checkout/orders/{$orderId}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('PayPal Get Order Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get error message from exception
     */
    private function getErrorMessage(RequestException $e)
    {
        if ($e->hasResponse()) {
            $response = $e->getResponse();
            $body = json_decode($response->getBody(), true);
            
            if (isset($body['error_description'])) {
                return $body['error_description'];
            }
            
            if (isset($body['message'])) {
                return $body['message'];
            }
            
            if (isset($body['name'])) {
                return $body['name'] . ': ' . ($body['details'][0]['description'] ?? 'Unknown error');
            }
            
            return 'HTTP ' . $response->getStatusCode() . ': ' . $response->getReasonPhrase();
        }
        
        return $e->getMessage();
    }
}