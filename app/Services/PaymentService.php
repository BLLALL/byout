<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Rental;
use Exception;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function processPayment(Rental $rental, array $paymentData)
    {
        // Here you would integrate with a payment gateway (e.g., Stripe, PayPal)
        // For this example, we'll simulate a successful payment

        try {
            // Simulate payment processing
            $paymentSuccessful = true;

            if ($paymentSuccessful) {
                $payment = Payment::create([
                    'rental_id' => $rental->id,
                    'amount' => $paymentData['amount'],
                    'currency' => $paymentData['currency'],
                    'payment_method' => $paymentData['payment_method'],
                    'payment_status' => 'completed',
                ]);

                return $payment;
            } else {
                throw new Exception('Payment processing failed');
            }
        } catch (Exception $e) {
            // Log the error and rethrow
            Log::error('Payment processing error: ' . $e->getMessage());
            throw $e;
        }
    }
}