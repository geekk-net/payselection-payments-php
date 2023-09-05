<?php

namespace Geekk\PayselectionPaymentsPhp\Tests;

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

        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler([
            new Response(201, [], 'http://test-payment-link'),
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

        $paylinkCreator = new PayselectionApi($client, $siteId, new SignatureCreator($secretKey));
        $paylinkResult = $paylinkCreator->createPaylink($paymentRequest);

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
        $extraData = $requestData['PaymentRequest']['ExtraData'];

        $this->assertEquals('webhook_url', $extraData['WebhookUrl'] ?? null);
        $this->assertEquals('success_url', $extraData['SuccessUrl'] ?? null);
        $this->assertEquals('decline_url', $extraData['DeclineUrl'] ?? null);
        // {"PaymentRequest":{"OrderId":"14710ea1df","Amount":"9.10","Currency":"RUB","Description":"Some goods"}}.

        $this->assertTrue($paylinkResult->success());
    }
}
