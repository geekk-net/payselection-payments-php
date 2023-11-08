<?php

namespace Geekk\PayselectionPaymentsPhp;

use Geekk\PayselectionPaymentsPhp\Paylink\CustomerInfoData;
use Geekk\PayselectionPaymentsPhp\Paylink\PaylinkResult;
use Geekk\PayselectionPaymentsPhp\Paylink\PaymentRequestData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData;
use Geekk\PayselectionPaymentsPhp\Paylink\RecurringData;
use Geekk\PayselectionPaymentsPhp\Recurring\UnsubscribeResult;
use GuzzleHttp\Client;

class PayselectionApi
{
    const PAY_LINK_URL = 'https://webform.payselection.com';
    const API_URL = 'https://gw.payselection.com';

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
        ?CustomerInfoData $customerInfoData = null,
        ?RecurringData $recurringData = null
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

        if (!empty($recurringData)) {
            $data['RecurringData'] = $recurringData->getBuiltData();
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

        $response = $this->client->request('POST', self::PAY_LINK_URL.$action, $options);

        return new PaylinkResult($response->getStatusCode(), $response->getBody());
    }

    public function recurringUnsubscribe(string $rebillId): UnsubscribeResult
    {
        $action = '/payments/unsubscribe';
        $data = [
            'RebillId' => $rebillId,
        ];

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

        $response = $this->client->request('POST', self::API_URL.$action, $options);

        return new UnsubscribeResult($response->getBody());
    }
}
