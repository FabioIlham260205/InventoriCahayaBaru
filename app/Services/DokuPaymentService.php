<?php

namespace App\Services;

use App\Models\CustomerOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DokuPaymentService
{
    private string $clientId;
    private string $secretKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->clientId = (string) config('services.doku.client_id');
        $this->secretKey = (string) config('services.doku.secret_key');
        $this->baseUrl = (string) config('services.doku.api_base_url');
    }

    public function createPayment(CustomerOrder $order): array
    {
        if ($this->clientId === '' || $this->secretKey === '') {
            return $this->manualPayment($order, ['error' => 'DOKU credentials not configured']);
        }

        $requestId = Str::uuid()->toString();
        // DOKU requires a UTC timestamp within one hour of its current time.
        // `now()` already follows the app timezone, so subtracting seven hours
        // here made the signed request appear stale when the app runs in UTC.
        $requestTimestamp = now('UTC')->format('Y-m-d\TH:i:s') . 'Z';
        $orderNumber = $order->order_number;
        $grossAmount = (int) round((float) $order->total);

        $payload = [
            'order' => [
                'amount' => $grossAmount,
                'invoice_number' => $orderNumber,
                'currency' => 'IDR',
                'callback_url' => route('shop.payment.finish', $order),
                'auto_redirect' => true,
            ],
            'payment' => [
                'payment_due_date' => 60,
            ],
            'customer' => [
                'id' => 'CUST-' . $order->id,
                'name' => $order->customer_name,
                'email' => $order->customer_email ?? '',
                'phone' => $order->customer_phone ?? '',
            ],
        ];

        $path = '/checkout/v1/payment';
        $bodyJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $digest = base64_encode(hash('sha256', $bodyJson, true));
        $signature = $this->generateSignature($requestId, $requestTimestamp, $path, $digest);

        // The digest/signature must be calculated from exactly the same JSON
        // bytes that are sent to DOKU. Do not let the HTTP client encode the
        // array a second time, as it can produce a different representation.
        $response = Http::withHeaders([
            'Client-Id' => $this->clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $requestTimestamp,
            'Signature' => $signature,
        ])->withBody($bodyJson, 'application/json')
            ->send('POST', $this->baseUrl . $path);

        if ($response->failed()) {
            return $this->manualPayment($order, $response->json() ?: ['error' => $response->body()]);
        }

        $body = $response->json();
        $resp = $body['response'] ?? $body;
        $redirectUrl = $resp['payment']['url'] ?? null;

        if (! $redirectUrl) {
            return $this->manualPayment($order, $body);
        }

        return [
            'provider' => 'doku',
            'status' => 'waiting_payment',
            'token' => $resp['payment']['token_id'] ?? null,
            'redirect_url' => $redirectUrl,
            'payload' => $body,
        ];
    }

    public function verifyNotification(array $payload): bool
    {
        $headers = request()->headers->all();
        $headerClientId = $headers['client-id'][0] ?? '';
        $headerRequestId = $headers['request-id'][0] ?? '';
        $headerTimestamp = $headers['request-timestamp'][0] ?? '';
        $headerSignature = $headers['signature'][0] ?? '';

        if ($headerClientId !== $this->clientId) {
            return false;
        }

        $bodyJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $path = request()->path();
        $path = '/' . $path;
        $digest = base64_encode(hash('sha256', $bodyJson, true));
        $expectedSignature = $this->generateSignature($headerRequestId, $headerTimestamp, $path, $digest);

        return hash_equals($headerSignature, $expectedSignature);
    }

    public function getNotificationResult(array $payload): array
    {
        $status = $payload['status'] ?? 'UNPAID';
        $paymentStatus = match ($status) {
            'SUCCESS' => 'paid',
            'EXPIRED', 'FAILED' => 'failed',
            default => 'waiting_payment',
        };

        $paymentType = $payload['payment']['type'] ?? $payload['acquirer']['name'] ?? null;

        return [
            'payment_status' => $paymentStatus,
            'payment_type' => $paymentType,
            'payment_transaction_id' => $payload['acquirer']['transaction_id'] ?? null,
            'paid_at' => $paymentStatus === 'paid' ? now() : null,
            'payload' => $payload,
        ];
    }

    private function generateSignature(
        string $requestId,
        string $requestTimestamp,
        string $path,
        string $digest,
    ): string {
        $component = "Client-Id:{$this->clientId}\n"
            . "Request-Id:{$requestId}\n"
            . "Request-Timestamp:{$requestTimestamp}\n"
            . "Request-Target:{$path}\n"
            . "Digest:{$digest}";

        $hmac = hash_hmac('sha256', $component, $this->secretKey);

        return 'HMACSHA256=' . base64_encode(hex2bin($hmac));
    }

    private function manualPayment(CustomerOrder $order, ?array $payload = null): array
    {
        return [
            'provider' => 'manual',
            'status' => 'waiting_payment',
            'token' => null,
            'redirect_url' => route('shop.payment.show', $order),
            'payload' => $payload,
        ];
    }
}
