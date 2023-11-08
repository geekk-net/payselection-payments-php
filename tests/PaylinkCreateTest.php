<?php

namespace Geekk\PayselectionPaymentsPhp\Tests;

use Geekk\PayselectionPaymentsPhp\Paylink\CustomerInfoData;
use Geekk\PayselectionPaymentsPhp\Paylink\PaymentRequestData;
use Geekk\PayselectionPaymentsPhp\Paylink\PaymentRequestExtraData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData\ClientData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData\CompanyData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData\ItemData;
use Geekk\PayselectionPaymentsPhp\Paylink\RecurringData;
use Geekk\PayselectionPaymentsPhp\PayselectionApi;
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
    private $orderId = 'order-id';
    /**
     * @var float
     */
    private $amount = 9.10;
    /**
     * @var string
     */
    private $currency = 'RUB';
    /**
     * @var string
     */
    private $description = 'Some goods';
    /**
     * @var string
     */
    private $email = 'u@mail.com';

    /**
     * @dataProvider createPaymentProvider
     */
    public function testCreatePayment(
        PaymentRequestData $paymentRequest,
        ReceiptData        $receipt,
        CustomerInfoData   $customerInfo,
        ?RecurringData     $recurringData
    ): void {
        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler([
            new Response(201, [], '"http://test-payment-link"'),
            //new Response(400, [], json_encode(['Code' => 'ERROR_01', 'Description' => 'Unexpected error'])),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client(['handler' => $handlerStack]);

        $paylinkCreator = new PayselectionApi($client, $this->siteId, new SignatureCreator($this->secretKey));
        $paylinkResult = $paylinkCreator->createPaylink($paymentRequest, $receipt, $customerInfo, $recurringData);

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

        $this->assertEquals('/webpayments/create', $request->getUri()->getPath());

        $body = $request->getBody()->getContents();
        $requestData = json_decode($body, true);
        $this->assertIsArray($requestData);
        $this->assertArrayHasKey('PaymentRequest', $requestData);
        $this->assertArrayHasKey('ExtraData', $requestData['PaymentRequest']);
        $this->assertArrayHasKey('CustomerInfo', $requestData);
        $extraData = $requestData['PaymentRequest']['ExtraData'];

        $this->assertEquals($this->orderId, $requestData['PaymentRequest']['OrderId']);
        $this->assertEquals($this->amount, $requestData['PaymentRequest']['Amount']);
        $this->assertEquals($this->currency, $requestData['PaymentRequest']['Currency']);

        $this->assertEquals('webhook_url', $extraData['WebhookUrl'] ?? null);
        $this->assertEquals('success_url', $extraData['SuccessUrl'] ?? null);
        $this->assertEquals('decline_url', $extraData['DeclineUrl'] ?? null);

        $this->assertArrayHasKey('ReceiptData', $requestData);
        $this->assertIsArray($requestData['ReceiptData']);
        $this->assertArrayHasKey('timestamp', $requestData['ReceiptData']);
        $this->assertArrayHasKey('receipt', $requestData['ReceiptData']);

        $this->assertArrayHasKey('Email', $requestData['CustomerInfo']);
        $this->assertEquals($this->email, $requestData['CustomerInfo']['ReceiptEmail']);

        if (!empty($recurringData)) {
            $this->assertArrayHasKey('RecurringData', $requestData);
            $this->assertIsArray($requestData['RecurringData']);

            $this->assertEquals($this->amount, $requestData['RecurringData']['Amount']);
            $this->assertEquals($this->currency, $requestData['RecurringData']['Currency']);
            $this->assertEquals($this->description, $requestData['RecurringData']['Description']);
            $this->assertEquals('recurring_url', $requestData['RecurringData']['WebhookUrl']);
            $this->assertEquals($this->email, $requestData['RecurringData']['AccountId']);
            $this->assertArrayHasKey('StartDate', $requestData['RecurringData']);
            $this->assertEquals('1', $requestData['RecurringData']['Interval']);
            $this->assertEquals('month', $requestData['RecurringData']['Period']);
            $this->assertEquals($requestData['ReceiptData'], $requestData['RecurringData']['ReceiptData']);
        } else {
            $this->assertArrayNotHasKey('RecurringData', $requestData);
        }

        $this->assertTrue($paylinkResult->success());
    }

    /**
     * @return array<array{0: PaymentRequestData, 1: ReceiptData, 2:CustomerInfoData, 3?: RecurringData}>
     */
    public function createPaymentProvider(): array
    {
        $paymentRequest = new PaymentRequestData($this->orderId, $this->amount, $this->currency, $this->description);
        $extraData = new PaymentRequestExtraData();
        $extraData->setWebhookUrl('webhook_url');
        $extraData->setSuccessUrl('success_url');
        $extraData->setDeclineUrl('decline_url');
        $paymentRequest->setExtraData($extraData);

        $items = [new ItemData($this->amount, 'note')];
        $receipt = new ReceiptData(new CompanyData('0', 'a'), new ClientData($this->email), $items);

        $customerInfo = new CustomerInfoData($this->email);

        $startTime = (new \DateTimeImmutable())
            ->add(new \DateInterval('P30D'))
            ->format(\DateTimeInterface::RFC3339_EXTENDED);
        $recurringData = new RecurringData(
            $this->amount,
            $this->currency,
            $this->email,
            $startTime,
            '1',
            RecurringData::PERIOD_MONTH,
            $this->description,
            'recurring_url',
            $this->email,
            null,
            $receipt
        );

        return [
            [$paymentRequest, $receipt, $customerInfo, null],
            [$paymentRequest, $receipt, $customerInfo, $recurringData],
        ];
    }
}
