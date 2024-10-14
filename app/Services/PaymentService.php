<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Rental;
use Exception;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected FatoraService $fatoraService;

    public function __construct(FatoraService $fatoraService)
    {
        $this->fatoraService = $fatoraService;
    }
    public function processPayment(Rental $rental, array $paymentData)
    {
        try {

            $fatoraData = [
                'lang' => 'en',
                'terminalId' => config('fatora.test.terminal_id'),
                'amount' => $paymentData['amount'],
                'callbackURL' => route('fatora.callback'), // Ensure this route is defined
                'triggerURL' => route('fatora.trigger'), // Ensure this route is defined
                // assuming no saved cards
            ];

            $fatoraResponse = $this->fatoraService->createPayment($fatoraData);
            
            if ($fatoraResponse['ErrorCode'] !== 0) {
                throw new Exception('Fatora Payment Creation Failed: ' . $fatoraResponse['ErrorMessage']);
            }

            $paymentId = $fatoraResponse['Data']['paymentId'];
            
            $paymentUrl = $fatoraResponse['Data']['url'];

            Log::info("Payment created with payment_id: $paymentId and url: $paymentUrl for rental_id: {$rental->id}");
            $payment = Payment::create([
                'rental_id' => $rental->id,
                'payment_id' => $paymentId,
                'payment_url' => $paymentUrl,
                'amount' => $paymentData['amount'],
                'currency' => $paymentData['currency'],
                'payment_method' => $paymentData['payment_method'],
                'payment_status' => 'pending',
            ]);

            return $payment;

        } catch (Exception $e) {
            // Log the error and rethrow
            Log::error('Payment processing error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updatePaymentStatus(string $paymentId, string $status)
    {
        $payment = Payment::where('payment_id', $paymentId)->firstOrFail();

        if(!$payment) {
            Log::error("Payment not fount for payment_id: $paymentId");
        }

        $payment->payment_status = $status;
        $payment->save();

        Log::info("Payment status updated to '$status' for payment_id: $paymentId");

    }
}
