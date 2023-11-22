<?php

namespace Geekk\PayselectionPaymentsPhp\Tests;

use Geekk\PayselectionPaymentsPhp\PayselectionApi;
use Geekk\PayselectionPaymentsPhp\SignatureCreator;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class UnsubscribeTest extends TestCase
{
    /**
     * @var string
     */
    private $secretKey = 'test-secret-key';
    /**
     * @var string
     */
    private $siteId = "1001";
    /**
     * @var string
     */
    private $rebillId = 'PS00000000000001';

    public function testUnsubscribe(): void
    {
        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler([
            new Response(201, [], "{\"TransactionState\": \"true\"}"),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client(['handler' => $handlerStack]);

        $payselectionApi = new PayselectionApi($client, $this->siteId, new SignatureCreator($this->secretKey));
        $unsubscribeResult = $payselectionApi->recurringUnsubscribe($this->rebillId);

        $this->assertEquals(1, count($container));
        $transaction = $container[0];
        /**
         * @var Request $request
         */
        $request = $transaction['request'];

        $headersSiteId = $request->getHeader('X-SITE-ID');
        $this->assertCount(1, $headersSiteId);
        $this->assertEquals($this->siteId, $headersSiteId[0]);
        $this->assertTrue($request->hasHeader('X-REQUEST-ID'));
        $this->assertTrue($request->hasHeader('X-REQUEST-SIGNATURE'));
        $this->assertNotEmpty($request->getHeader('X-REQUEST-ID')[0]);
        $this->assertNotEmpty($request->getHeader('X-REQUEST-SIGNATURE')[0]);

        $this->assertEquals('/payments/unsubscribe', $request->getUri()->getPath());

        $body = $request->getBody()->getContents();
        $requestData = json_decode($body, true);
        $this->assertIsArray($requestData);
        $this->assertArrayHasKey('RebillId', $requestData);

        $this->assertEquals($this->rebillId, $requestData['RebillId']);

        $this->assertTrue($unsubscribeResult->success());
    }
}
