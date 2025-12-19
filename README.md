# PHP_Laravel12_paypal_integration

A complete **Laravel 12** application demonstrating **PayPal payment gateway integration** with **webhook support**, database persistence, and a secure end-to-end payment flow.

This project is designed for **learning**, **real-world implementation**, and **interview demonstration** purposes.

---

## Project Overview

This application shows how to integrate PayPal Checkout in a Laravel 12 application using PayPal REST APIs. It covers:

* Creating PayPal orders
* Redirecting users to PayPal for approval
* Capturing payments after approval
* Storing payment details in the database
* Handling PayPal webhooks for real-time updates
* Secure configuration using environment variables

---

## Features

* PayPal payment processing (Sandbox & Live)
* Webhook integration for real-time payment status updates
* Database storage for all payment records
* RESTful controller design
* Error handling and logging
* Responsive UI using Tailwind CSS
* Secure and scalable payment flow

---

## Requirements

* PHP 8.1 or higher
* Laravel 12
* MySQL 5.7+ or PostgreSQL 9.5+
* Composer
* PayPal Developer Account

---

## Quick Start / Installation

```bash
# Clone the repository
git clone https://github.com/yourusername/laravel-paypal-webhook.git
cd laravel-paypal-webhook

# Install dependencies
composer install

# Environment setup
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# (Optional) Seed sample data
php artisan db:seed

# Start server
php artisan serve
```

---

## PayPal Sandbox Setup

1. Visit PayPal Developer Dashboard
2. Create a REST API App
3. Copy **Client ID** and **Secret**
4. Add credentials to `.env`
5. Configure webhooks:

Recommended events:

* PAYMENT.CAPTURE.COMPLETED
* PAYMENT.CAPTURE.DENIED
* PAYMENT.CAPTURE.REFUNDED
* CHECKOUT.ORDER.APPROVED
* CHECKOUT.ORDER.COMPLETED

Add webhook URL:

```
https://yourdomain.com/webhook/paypal
```

(Use ngrok for local testing)

---

## Environment Variables

```env
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

## Project Structure

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

## Routes & Endpoints

### Payment Routes

* GET /payments/create
* POST /payments/process
* GET /payments/success
* GET /payments/cancel
* GET /payments
* GET /payments/{id}

### Webhook Route

* POST /webhook/paypal

---

## Payment Flow

1. User enters amount and description
2. Application creates PayPal order
3. User is redirected to PayPal
4. Payment is approved
5. User returns to success route
6. Payment is captured and stored
7. Webhook updates payment status

---

## Webhook Handling

The webhook controller:

* Verifies PayPal webhook signature
* Parses event payload
* Updates payment records
* Logs failures securely

---

## Database Schema (payments)

* id
* payment_id
* payer_id
* payer_email (nullable)
* amount
* currency
* payment_status
* payment_details (JSON)
* invoice_id (nullable)
* description (nullable)
* timestamps

---

## Local Webhook Testing

```bash
ngrok http 8000
```

Update PayPal webhook URL with ngrok domain.

---

## Security Best Practices

* Verify webhook signatures
* Never commit API credentials
* Disable CSRF only for webhook route
* Use HTTPS in production
* Validate all inputs
* Use queues for webhook processing

---

## Troubleshooting

**PayPal Auth Error**

* Check client ID and secret

**Webhook Not Triggering**

* Ensure public webhook URL
* Verify webhook ID

**Database Errors**

* Run migrate:fresh

---

## Deployment Checklist

* APP_DEBUG=false
* PAYPAL_SANDBOX=false
* HTTPS enabled
* Queues configured
* Logging enabled

---

## Server Requirements

* PHP 8.1+
* OpenSSL, PDO, Mbstring, XML
* MySQL or PostgreSQL
* Apache / Nginx

---

## Screenshots

<img width="1903" height="969" alt="image" src="https://github.com/user-attachments/assets/0eee381d-a3f9-4a58-b5e9-97256f8b7701" />
<img width="1919" height="793" alt="image" src="https://github.com/user-attachments/assets/5a994922-45fa-4961-9879-e857973ab374" /> 
<img width="889" height="848" alt="image" src="https://github.com/user-attachments/assets/a6b533ad-c51e-4293-ac30-3f95f7922e8f" />
<img width="1247" height="974" alt="image" src="https://github.com/user-attachments/assets/9692ae81-4f51-43e1-b078-e0c94b423695" />

---

