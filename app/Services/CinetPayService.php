<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CinetPayService
{
    protected string $apiKey;
    protected string $siteId;
    protected string $secretKey;
    protected string $baseUrl = 'https://api-checkout.cinetpay.com/v2';

    public function __construct()
    {
        $this->apiKey = config('services.cinetpay.api_key', '');
        $this->siteId = config('services.cinetpay.site_id', '');
        $this->secretKey = config('services.cinetpay.secret_key', '');
    }

    /**
     * Initier un paiement CinetPay.
     */
    public function initPayment(array $data): array
    {
        $payload = [
            'apikey' => $this->apiKey,
            'site_id' => $this->siteId,
            'transaction_id' => $data['transaction_id'],
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'XOF',
            'description' => $data['description'] ?? 'Paiement CabForm',
            'customer_name' => $data['customer_name'] ?? '',
            'customer_surname' => $data['customer_surname'] ?? '',
            'customer_email' => $data['customer_email'] ?? '',
            'customer_phone_number' => $data['customer_phone'] ?? '',
            'customer_address' => $data['customer_address'] ?? 'Abidjan',
            'customer_city' => $data['customer_city'] ?? 'Abidjan',
            'customer_country' => $data['customer_country'] ?? 'CI',
            'notify_url' => route('payment.notify'),
            'return_url' => route('payment.return'),
            'cancel_url' => route('payment.cancel'),
            'channels' => 'ALL',
            'metadata' => json_encode($data['metadata'] ?? []),
        ];

        try {
            $response = Http::post("{$this->baseUrl}/payment", $payload);
            $result = $response->json();

            if (isset($result['code']) && $result['code'] === '201') {
                return [
                    'success' => true,
                    'payment_url' => $result['data']['payment_url'],
                    'payment_token' => $result['data']['payment_token'] ?? null,
                ];
            }

            Log::error('CinetPay Init Error', $result);
            return ['success' => false, 'error' => $result['message'] ?? 'Erreur inconnue'];

        } catch (\Exception $e) {
            Log::error('CinetPay Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Vérifier le statut d'une transaction.
     */
    public function verifyTransaction(string $transactionId): array
    {
        $payload = [
            'apikey' => $this->apiKey,
            'site_id' => $this->siteId,
            'transaction_id' => $transactionId,
        ];

        try {
            $response = Http::post("{$this->baseUrl}/payment/check", $payload);
            $result = $response->json();

            if (isset($result['code']) && $result['code'] === '00') {
                return [
                    'success' => true,
                    'status' => $result['data']['status'],
                    'amount' => $result['data']['amount'],
                    'currency' => $result['data']['currency'],
                    'payment_method' => $result['data']['payment_method'] ?? 'unknown',
                    'payment_date' => $result['data']['payment_date'] ?? null,
                    'data' => $result['data'],
                ];
            }

            return ['success' => false, 'status' => 'FAILED', 'error' => $result['message'] ?? 'Transaction non trouvée'];

        } catch (\Exception $e) {
            Log::error('CinetPay Verify Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'status' => 'ERROR', 'error' => $e->getMessage()];
        }
    }

    /**
     * Valider la signature CinetPay (x-token).
     */
    public function isValidSignature(\Illuminate\Http\Request $request): bool
    {
        $headerToken = $request->header('x-token');
        if (!$headerToken) {
            return false;
        }

        // Hacher le contenu brut de la requête avec la clé secrète
        $rawBody = $request->getContent();
        $calculatedToken = hash_hmac('sha256', $rawBody, $this->secretKey);

        return hash_equals($calculatedToken, $headerToken);
    }
}
