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
        return new PaylinkCreatorResult();
    }
}