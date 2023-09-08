<?php

namespace Geekk\PayselectionPaymentsPhp;

use Geekk\PayselectionPaymentsPhp\Paylink\CustomerInfoData;
use Geekk\PayselectionPaymentsPhp\Paylink\PaylinkResult;
use Geekk\PayselectionPaymentsPhp\Paylink\PaymentRequestData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData;
use GuzzleHttp\Client;

class PayselectionApi
{
    const BASE_URL = 'https://webform.payselection.com';

    /**
     * @var string
     */
    private $siteId;

    /**
     * @var SignatureCreator
     */
    private $signatureCreator;

    /**
     * @var Client
     */
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

    public function createPaylink(
        PaymentRequestData $paymentRequestData,
        ?ReceiptData $receiptData = null,
        ?CustomerInfoData $customerInfoData = null
    ): PaylinkResult {

        $action = '/webpayments/create';
        $data = [
            'PaymentRequest' => $paymentRequestData->getBuiltData(),
        ];

        if (!empty($receiptData)) {
            $data['ReceiptData'] = $receiptData->getBuiltData();
        }

        if (!empty($customerInfoData)) {
            $data['CustomerInfo'] = $customerInfoData->getBuiltData();
        }

        $requestId = $this->makeRequestId();
        /**
         * @var string $json
         */
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

        $response = $this->client->request('POST', self::BASE_URL.$action, $options);

        return new PaylinkResult($response->getStatusCode(), $response->getBody());
    }
}
