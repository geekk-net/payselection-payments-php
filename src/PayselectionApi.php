<?php

namespace Geekk\PayselectionPaymentsPhp;

use Geekk\PayselectionPaymentsPhp\Paylink\PaylinkResult;
use Geekk\PayselectionPaymentsPhp\Paylink\PaymentRequestData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData;
use GuzzleHttp\Client;

class PayselectionApi
{
    const BASE_URL = 'https://webform.payselection.com';
    private $siteId;
    private $signatureCreator;
    private $client;

    public function __construct(Client $client, string $siteId, SignatureCreator $signatureCreator)
    {
        $this->client = $client;
        $this->siteId = $siteId;
        $this->signatureCreator = $signatureCreator;
    }

    private function makeRequestId(): string
    {
        return bin2hex(random_bytes(16));
    }

    private function post(string $action, ?array $data)
    {
        $requestId = $this->makeRequestId();
        $json = json_encode($data);
        $paramsToSign = ['POST', $action, $this->siteId, $requestId, $json];

        $options = [
            'http_errors' => false,
            'json' => $data,
            'headers' => [
                'X-SITE-ID' => $this->siteId,
                'X-REQUEST-ID' => $requestId,
                'X-REQUEST-SIGNATURE' => $this->signatureCreator->makeSignature($paramsToSign)
            ]
        ];

        return $this->client->request('POST', self::BASE_URL.$action, $options);
    }

    public function createPaylink(
        PaymentRequestData $paymentRequestData,
        ?ReceiptData $receiptData = null
    ): PaylinkResult {
        $data = [
            'PaymentRequest' => $paymentRequestData->getBuiltData()
        ];
        $response = $this->post('/webpayments/create', $data);

        return new PaylinkResult($response->getStatusCode(), $response->getBody());
    }
}
