# PHP_Laravel12_paypal_integration

A complete Laravel 12 application for PayPal payment processing with webhook integration.

---

## Features

* PayPal payment processing with sandbox support
* Webhook integration for real-time payment updates
* Database storage for payment records
* RESTful API design
* Error handling and logging
* Responsive UI with Tailwind CSS
* Secure payment flow

---

## Requirements

* PHP 8.1 or higher
* Laravel 12
* MySQL 5.7 or higher (or PostgreSQL 9.5+)
* Composer
* PayPal Developer Account

---

## Quick Start / Installation

```bash
# Clone the repository
git clone https://github.com/yourusername/laravel-paypal-webhook.git
cd laravel-paypal-webhook

# Install PHP dependencies
composer install

# Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# Configure database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel_paypal
# DB_USERNAME=root
# DB_PASSWORD=

# Configure PayPal credentials in .env (see section below)

# Run migrations
php artisan migrate

# (Optional) Seed sample data
php artisan db:seed

# Start local server
php artisan serve
```

---

## PayPal Setup (Sandbox)

1. Go to the PayPal Developer Dashboard: `https://developer.paypal.com`
2. Create or log in to your PayPal Developer account.
3. Create a new REST API App (Dashboard → REST API apps → Create App).

   * App name: `Laravel Integration` (or any friendly name)
4. Copy **Client ID** and **Secret** and add them to your `.env` file.
5. Configure Webhooks in the PayPal dashboard:

   * Click **Webhooks** → **Add Webhook**
   * URL: `https://yourdomain.com/webhook/paypal` (for local testing use ngrok)
   * Subscribe to the following event types (recommended):

     * `PAYMENT.CAPTURE.COMPLETED`
     * `PAYMENT.CAPTURE.DENIED`
     * `PAYMENT.CAPTURE.REFUNDED`
     * `CHECKOUT.ORDER.APPROVED`
     * `CHECKOUT.ORDER.COMPLETED`
6. Copy the **Webhook ID** and add to your `.env` as `PAYPAL_WEBHOOK_ID`.

> Use PayPal sandbox business & buyer accounts for testing.

---

## Project Structure (Important files)

```
app/
├── Http/Controllers/
│   ├── PayPalController.php
│   └── WebhookController.php
├── Models/
│   └── Payment.php
└── Services/
    └── PayPalService.php

database/
└── migrations/
    └── create_payments_table.php

resources/views/
├── payments/
│   ├── create.blade.php
│   ├── success.blade.php
│   ├── cancel.blade.php
│   ├── index.blade.php
│   └── show.blade.php
└── layout.blade.php
```

---

## API Endpoints

**Payment Endpoints**

* `GET /payments/create` — Show payment form
* `POST /payments/process` — Process payment (create PayPal order)
* `GET /payments/success` — Payment success callback (capture & record)
* `GET /payments/cancel` — Payment cancellation page
* `GET /payments` — List all payments
* `GET /payments/{id}` — Show payment details

**Webhook Endpoint**

* `POST /webhook/paypal` — Handle PayPal webhook events

---

## Payment Flow Overview

1. User enters payment amount and description on the site.
2. System creates a PayPal order via the PayPal API.
3. User is redirected to PayPal to complete payment.
4. After successful payment, PayPal redirects user back to the `success` route.
5. Application captures the payment and updates the database.
6. PayPal sends webhook events to `/webhook/paypal` to provide real-time updates, which are verified and processed by the app.

---

## Webhook Events Handled

* `PAYMENT.CAPTURE.COMPLETED`
* `PAYMENT.CAPTURE.DENIED`
* `PAYMENT.CAPTURE.REFUNDED`
* `CHECKOUT.ORDER.APPROVED`
* `CHECKOUT.ORDER.COMPLETED`

The WebhookController verifies the PayPal signature, parses the event and updates the local `payments` table accordingly. All webhook verification failures are logged.

---

## Database Schema (payments table)

* `id` — Primary key
* `payment_id` — PayPal order or capture ID (unique)
* `payer_id` — PayPal payer ID
* `payer_email` — Payer email (nullable)
* `amount` — Payment amount (decimal)
* `currency` — Currency code (default: `USD`)
* `payment_status` — Payment status (string)
* `payment_details` — JSON (full details from PayPal)
* `invoice_id` — Invoice ID (nullable)
* `description` — Payment description (nullable)
* `created_at`, `updated_at`

---

## Environment Variables (.env)

Required variables to add to your `.env` file:

```
APP_NAME=Laravel
APP_URL=http://localhost:8000
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_paypal
DB_USERNAME=root
DB_PASSWORD=

PAYPAL_CLIENT_ID=your_client_id
PAYPAL_CLIENT_SECRET=your_client_secret
PAYPAL_WEBHOOK_ID=your_webhook_id
PAYPAL_SANDBOX=true
```

---

## Testing & Local Webhook Debugging

Use `ngrok` to expose your local server so PayPal can reach the webhook endpoint:

```bash
ngrok http 8000
```

Update the webhook URL in the PayPal dashboard to your `ngrok` URL: `https://<ngrok-id>.ngrok.io/webhook/paypal`.

Helpful test routes included in the repo:

* `/test/paypal` — Test PayPal credentials & connection
* `/debug/paypal-setup` — Debug PayPal configuration
* `/debug/payments` — View all recorded payments

---

## Security Considerations

* Verify webhook signatures before accepting events.
* Keep API credentials in environment variables (never commit them).
* CSRF protection must be disabled only for the webhook route(s).
* Input validation for all user-supplied data.
* Use HTTPS in production.
* Use queues for processing webhooks in high-volume environments.

---

## Troubleshooting

**Common Issues**

* `SQL Error 1364: Field doesn't have default value`

  * Run `php artisan migrate:fresh` or make payer_email nullable in migration.

* PayPal Authentication Failed

  * Verify `PAYPAL_CLIENT_ID` and `PAYPAL_CLIENT_SECRET` in `.env`.
  * Ensure `PAYPAL_SANDBOX=true` for sandbox testing.

* Webhook Not Working

  * Ensure webhook URL is publicly accessible (ngrok for local testing).
  * Verify `PAYPAL_WEBHOOK_ID` in `.env` matches PayPal dashboard.

* Redirect Issues

  * Set correct `APP_URL` in `.env` and verify route definitions.

**Debug Commands**

```bash
# Clear Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Reset database
php artisan migrate:fresh

# Test PayPal connection
curl http://localhost:8000/test/paypal
```

---

## Deployment Checklist

* `APP_DEBUG=false` in production
* `PAYPAL_SANDBOX=false` in `.env`
* Use HTTPS for all URLs
* Configure persistent production database
* Configure logging and monitoring
* Use queues for webhook processing
* Apply rate limiting for endpoints
* Configure backups

---

## Server Requirements

* PHP 8.1+ (extensions: OpenSSL, PDO, Mbstring, Tokenizer, XML)
* MySQL 5.7+ or PostgreSQL 9.5+
* Composer
* Nginx or Apache

---


Tell me which of these you'd like me to add next.
