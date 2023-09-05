<?php

namespace Geekk\PayselectionPaymentsPhp\Tests;

use Geekk\PayselectionPaymentsPhp\SignatureCreator;
use Geekk\PayselectionPaymentsPhp\WebhookHandler;
use PHPUnit\Framework\TestCase;

class WebhookHandlerTest extends TestCase
{

    public function testSignature(): void
    {
        $secretKey = 'some_key';
        $handler = new WebhookHandler(new SignatureCreator($secretKey));

        $siteId = '20003';
        $requestUrl = 'https://webhook.site/notification/';
        $data = [
            "Event" => "Payment",
            "TransactionId" => "PS00000000000007",
            "OrderId" => "Ilya test",
            "Amount" => "152.12",
            "Currency" => "RUB",
            "DateTime" => "16.09.2019 16.52.41",
            "IsTest" => 1
        ];
        $json = json_encode($data);
        $dataToSign = ["POST", $requestUrl, $siteId, $json];

        // Check signature for correct key
        $headers = [
            'X-SITE-ID' => $siteId,
            'X-WEBHOOK-SIGNATURE' => (new SignatureCreator($secretKey))
                ->makeSignature($dataToSign)
        ];
        $handler->handle($requestUrl, $headers, $json);
        $this->assertTrue($handler->hasCorrectSignature());

        // Check signature for wrong key
        $headers['X-WEBHOOK-SIGNATURE'] = (new SignatureCreator('another-key'))
                ->makeSignature($dataToSign);
        $handler->handle($requestUrl, $headers, $json);
        $this->assertFalse($handler->hasCorrectSignature());
    }

    public function testParameters(): void
    {
        $handler = new WebhookHandler(new SignatureCreator('some_key'));
        $data = [
            "Event" => "Payment",
            "TransactionId" => "PS00000000000007",
            "OrderId" => "H5D6223aA",
            "Amount" => "52.10",
            "Currency" => "RUB",
            "DateTime" => "16.09.2019 16.52.41",
            "CardMasked" => "4539********2773",
            "IsTest" => 1
        ];
        $json = json_encode($data);

        $handler->handle('', [], $json);
        $this->assertEquals("H5D6223aA", $handler->getOrderId());
        $this->assertEquals("52.10", $handler->getAmount());
        $this->assertEquals("payment", $handler->getEventName());
        $this->assertEquals("RUB", $handler->getCurrency());
        $this->assertEquals("PS00000000000007", $handler->getTransactionId());
        $this->assertEquals("4539********2773", $handler->getCardMasked());
        $this->assertEquals("16.09.2019 16.52.41", $handler->getDateTime());
    }
}
