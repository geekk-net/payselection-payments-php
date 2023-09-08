<?php

namespace Geekk\PayselectionPaymentsPhp\Tests;

use Geekk\PayselectionPaymentsPhp\Paylink\CustomerInfoData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData\ClientData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData\CompanyData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData\ItemData;
use Geekk\PayselectionPaymentsPhp\PayselectionApi;
use Geekk\PayselectionPaymentsPhp\Paylink\PaymentRequestData;
use Geekk\PayselectionPaymentsPhp\Paylink\PaymentRequestExtraData;
use Geekk\PayselectionPaymentsPhp\SignatureCreator;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class PaylinkCreateTest extends TestCase
{
    public function testCreatePayment(): void
    {
        $secretKey = 'test-secret-key';
        $siteId = "1001";

        $orderId = bin2hex(random_bytes(5));
        $amount = 9.10;
        $currency = 'RUB';
        $description = 'Some goods';
        $email = 'u@mail.com';

        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler([
            new Response(201, [], '"http://test-payment-link"'),
            //new Response(400, [], json_encode(['Code' => 'ERROR_01', 'Description' => 'Unexpected error'])),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client(['handler' => $handlerStack]);

        $paymentRequest = new PaymentRequestData($orderId, $amount, $currency, $description);
        $extraData = new PaymentRequestExtraData();
        $extraData->setWebhookUrl('webhook_url');
        $extraData->setSuccessUrl('success_url');
        $extraData->setDeclineUrl('decline_url');
        $paymentRequest->setExtraData($extraData);

        $items = [new ItemData($amount, 'note')];
        $receipt = new ReceiptData(new CompanyData('0', 'a'), new ClientData($email), $items);

        $customerInfo = new CustomerInfoData($email);

        $paylinkCreator = new PayselectionApi($client, $siteId, new SignatureCreator($secretKey));
        $paylinkResult = $paylinkCreator->createPaylink($paymentRequest, $receipt, $customerInfo);

        $this->assertEquals(1, count($container));
        $transaction = $container[0];
        /**
         * @var Request $request
         */
        $request = $transaction['request'];

        $headersSiteId = $request->getHeader('X-SITE-ID');
        $this->assertCount(1, $headersSiteId);
        $this->assertEquals($siteId, $headersSiteId[0]);
        $this->assertTrue($request->hasHeader('X-REQUEST-ID'));
        $this->assertTrue($request->hasHeader('X-REQUEST-SIGNATURE'));
        $this->assertNotEmpty($request->getHeader('X-REQUEST-ID')[0]);
        $this->assertNotEmpty($request->getHeader('X-REQUEST-SIGNATURE')[0]);

        $this->assertEquals('/webpayments/create', $request->getUri()->getPath());

        $body = $request->getBody()->getContents();
        $requestData = json_decode($body, true);
        $this->assertIsArray($requestData);
        $this->assertArrayHasKey('PaymentRequest', $requestData);
        $this->assertArrayHasKey('ExtraData', $requestData['PaymentRequest']);
        $this->assertArrayHasKey('CustomerInfo', $requestData);
        $extraData = $requestData['PaymentRequest']['ExtraData'];

        $this->assertEquals($orderId, $requestData['PaymentRequest']['OrderId']);
        $this->assertEquals($amount, $requestData['PaymentRequest']['Amount']);
        $this->assertEquals($currency, $requestData['PaymentRequest']['Currency']);

        $this->assertEquals('webhook_url', $extraData['WebhookUrl'] ?? null);
        $this->assertEquals('success_url', $extraData['SuccessUrl'] ?? null);
        $this->assertEquals('decline_url', $extraData['DeclineUrl'] ?? null);

        $this->assertArrayHasKey('ReceiptData', $requestData);
        $this->assertIsArray($requestData['ReceiptData']);
        $this->assertArrayHasKey('timestamp', $requestData['ReceiptData']);
        $this->assertArrayHasKey('receipt', $requestData['ReceiptData']);

        $this->assertArrayHasKey('Email', $requestData['CustomerInfo']);
        $this->assertEquals($email, $requestData['CustomerInfo']['ReceiptEmail']);

        $this->assertTrue($paylinkResult->success());
    }
}
