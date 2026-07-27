<?php

namespace App\Services;

use App\Models\CustomerOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentGateway
{
    private DokuPaymentService $dokuService;

    public function __construct()
    {
        $this->dokuService = new DokuPaymentService();
    }

    public function createPayment(CustomerOrder $order, ?string $gateway = null): array
    {
        if ($gateway === 'doku') {
            return $this->dokuService->createPayment($order);
        }

        if (! config('services.midtrans.server_key')) {
            return $this->manualPayment($order);
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) round((float) $order->total),
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
            ],
            'item_details' => $order->items->map(fn ($item) => [
                'id' => (string) ($item->fruit_id ?? $item->id),
                'price' => (int) round((float) $item->line_total),
                'quantity' => 1,
                'name' => Str::limit($item->fruit_name . ' ' . $item->quantity . ' ' . $item->unit, 50, ''),
            ])->values()->all(),
            'callbacks' => [
                'finish' => route('shop.payment.finish', $order),
            ],
        ];

        $response = Http::withBasicAuth((string) config('services.midtrans.server_key'), '')
            ->acceptJson()
            ->post(config('services.midtrans.snap_base_url') . '/snap/v1/transactions', $payload);

        if ($response->failed()) {
            return $this->manualPayment($order, $response->json() ?: ['error' => $response->body()]);
        }

        $body = $response->json();
        $token = $body['token'] ?? null;
        $redirectUrl = $body['redirect_url'] ?? ($token ? config('services.midtrans.snap_base_url') . "/snap/v2/vtweb/{$token}" : null);

        if (! $redirectUrl) {
            return $this->manualPayment($order, $body);
        }

        return [
            'provider' => 'midtrans',
            'status' => 'waiting_payment',
            'token' => $token,
            'redirect_url' => $redirectUrl,
            'payload' => $body,
        ];
    }

    public function notificationIsValid(array $payload, string $provider = 'midtrans'): bool
    {
        if ($provider === 'doku') {
            return $this->dokuService->verifyNotification($payload);
        }

        $serverKey = (string) config('services.midtrans.server_key');

        if ($serverKey === '') {
            return false;
        }

        $signature = $payload['signature_key'] ?? '';
        $source = ($payload['order_id'] ?? '')
            . ($payload['status_code'] ?? '')
            . ($payload['gross_amount'] ?? '')
            . $serverKey;

        return hash_equals($signature, hash('sha512', $source));
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
