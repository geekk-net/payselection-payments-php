<?php

namespace Geekk\PayselectionPaymentsPhp\Paylink;

use Geekk\PayselectionPaymentsPhp\ApiConnection;

class PaylinkCreator
{
    private $apiConnection;

    public function __construct(ApiConnection $apiConnection)
    {
        $this->apiConnection = $apiConnection;
    }

    public function createPaymentLink(
        PaymentRequestData $paymentRequestData,
        ?ReceiptData $receiptData = null
    ): PaylinkCreatorResult
    {
        $data = [
            'PaymentRequest' => $paymentRequestData->getBuiltData()
        ];
        $this->apiConnection->post('/webpayments/create', $data);

        return new PaylinkCreatorResult(200, 'http://test-payment-link');
    }
}