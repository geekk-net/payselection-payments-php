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

    public function post(string $action, ?array $data)
    {
        //$this->client->request('POST', self::BASE_URL.$action, [ 'json' => $data ]);
    }

    public function createPaylink(
        PaymentRequestData $paymentRequestData,
        ?ReceiptData $receiptData = null
    ): PaylinkResult
    {
        $data = [
            'PaymentRequest' => $paymentRequestData->getBuiltData()
        ];
        $this->post('/webpayments/create', $data);

        return new PaylinkResult(200, 'http://test-payment-link');
    }
}