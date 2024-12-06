<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\FatoraService;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FatoraController extends Controller
{
    protected FatoraService $fatoraService;
    protected PaymentService $paymentService;
    public function __construct(FatoraService $fatoraService, PaymentService $paymentService)
    {
        $this->fatoraService = $fatoraService;
        $this->paymentService = $paymentService;
    }

    public function handleCallback(Request $request)
    {
        Log::info('Fatora Callback Received:', $request->all());

        try {
            // Extract paymentId from the callback request
            $paymentId = $request->input('paymentId'); // Adjust based on Fatora's actual payload

            if (!$paymentId) {
                Log::error('Fatora Callback Missing paymentId');
                return response()->json(['message' => 'Invalid callback data'], 400);
            }

            // Retrieve the corresponding payment
            $payment = Payment::where('payment_id', $paymentId)->first();

            if (!$payment) {
                Log::error("Payment not found for paymentId: $paymentId");
                return response()->json(['message' => 'Payment not found'], 404);
            }

            // Fetch payment status from Fatora
            $statusResponse = $this->fatoraService->getPaymentStatus($paymentId);

            if ($statusResponse['ErrorCode'] !== 0) {
                Log::error('Failed to get payment status from Fatora:', $statusResponse);
                return response()->json(['message' => 'Failed to retrieve payment status'], 400);
            }

            $paymentStatus = $statusResponse['Data']['status']; // 'A', 'F', 'C', 'P'

            // Map Fatora status to your payment status؛
            $statusMap = [
                'A' => 'completed',
                'F' => 'failed',
                'C' => 'canceled',
                'P' => 'pending',
            ];

            if (!array_key_exists($paymentStatus, $statusMap)) {
                Log::warning("Unknown payment status: $paymentStatus for paymentId: $paymentId");
                return response()->json(['message' => 'Unknown payment status'], 400);
            }

            $mappedStatus = $statusMap[$paymentStatus];

            // Update payment status
            $payment->payment_status = $mappedStatus;
            $payment->save();

            Log::info("Payment status updated to '$mappedStatus' for paymentId: $paymentId");

            return response()->json(['message' => 'Callback processed successfully'], 200);
        } catch (Exception $e) {
            Log::error('Error processing Fatora callback: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    public function handleTrigger(Request $request)
    {
        Log::info('Fatora Trigger Data:', $request->all());

        $paymentId = $request->input('paymentId');
        if (!$paymentId) {
            Log::error('Payment ID not found in trigger data.');
            return response()->json(['status' => 'error', 'message' => 'Payment ID not found in trigger data.'], 400);
        }
        $payment = Payment::where('payment_id', $paymentId)->first();
        if (!$payment) {
            Log::error('Payment not found for ID: ' . $paymentId);
            return response()->json(['status' => 'error', 'message' => 'Payment not found for ID: ' . $paymentId], 404);
        }

        $statusResponse = $this->fatoraService->getPaymentStatus($paymentId);
        if ($statusResponse['ErrorCode'] !== 0) {
            Log::error('Error getting payment status from fatora: ' . $statusResponse['ErrorMessage']);
            return response()->json(['status' => 'error', 'message' => 'Error getting payment status from fatora'], 500);
        }
        $paymentStatus = $statusResponse['Data']['status']; // 'A', 'F', 'C', 'P'

        $statusMap = [
            'A' => 'completed',
            'F' => 'failed',
            'c' => 'cancelled',
            'P' => 'pending',
        ];

        if (!array_key_exists($paymentStatus, $statusMap)) {
            Log::warning("Unknown payment status: $paymentStatus for paymentId: $paymentId");
            return response()->json(['message' => 'Unknown payment status'], 400);
        }

        $mappedStatus = $statusMap[$paymentStatus];
        $payment->payment_status = $mappedStatus;
        $payment->save();

        Log::info("Payment status updated to '$mappedStatus' for paymentId: $paymentId");

        return response()->json(['message' => 'Trigger processed successfully'], 200);
    }
    public function getPaymentStatus($paymentId)
    {
        $response = $this->fatoraService->getPaymentStatus($paymentId);
        return response()->json($response);
    }

    public function cancelPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lang' => 'required|in:en,ar',
            'payment_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ErrorCode' => 400,
                'ErrorMessage' => 'Validation Failed',
                'Errors' => $validator->errors(),
            ], 400);
        }

        $data = $validator->validated();

        $response = $this->fatoraService->cancelPayment($data);

        return response()->json($response);
    }
}
