<?php

namespace Geekk\PayselectionPaymentsPhp\Tests;

use Geekk\PayselectionPaymentsPhp\PayselectionApi;
use Geekk\PayselectionPaymentsPhp\Paylink\PaymentRequestData;
use Geekk\PayselectionPaymentsPhp\Paylink\PaymentRequestExtraData;
use Geekk\PayselectionPaymentsPhp\SignatureCreator;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use PHPUnit\Framework\TestCase;
class PaylinkCreateTest extends TestCase
{
    public function testCreatePayment()
    {
        $secretKey = 'test-secret-key';
        $siteId = 1001;

        $orderId = bin2hex(random_bytes(5));
        $amount = 9.10;
        $currency = 'RUB';
        $description = 'Some goods';

        $container = [];
        $history = Middleware::history($container);
        $handlerStack = HandlerStack::create();
        $handlerStack->push($history);
        $client = new Client(['handler' => $handlerStack]);

        $paymentRequest = new PaymentRequestData($orderId, $amount, $currency, $description);
        $extraData = new PaymentRequestExtraData();
        $extraData->setWebhookUrl('https://...');
        $extraData->setSuccessUrl('https://...');
        $extraData->setDeclineUrl('https://...');
        $paymentRequest->setExtraData($extraData);

        $paylinkCreator = new PayselectionApi($client, $siteId, new SignatureCreator($secretKey));
        $paylinkResult = $paylinkCreator->createPaylink($paymentRequest);

        $this->assertTrue($paylinkResult->success());
    }
}