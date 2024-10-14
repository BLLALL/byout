<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FatoraService
{
    protected $apiUrl;
    protected $username;
    protected $password;

    public function __construct()
    {
        $config = config('fatora');
        $environment = $config['environment'];

        $this->apiUrl = $config[$environment]['api_url'];
        $this->username = $config[$environment]['username'];
        $this->password = $config[$environment]['password'];
    }

    public function createPayment($data)
    {
        $response = Http::withBasicAuth($this->username, $this->password)
            ->post($this->apiUrl . 'create-payment', $data);
        return $response->json();
    }

    public function getPaymentStatus($paymentId)
    {
        $response = Http::withBasicAuth($this->username, $this->password)
            ->get($this->apiUrl . 'get-payment-status/' . $paymentId);

        return $response->json();
    }

    public function cancelPayment(array $data)
    {
        $response = Http::withBasicAuth($this->username, $this->password)
            ->post($this->apiUrl . 'cancel-payment', $data);
        return $response->json();
    }
}
