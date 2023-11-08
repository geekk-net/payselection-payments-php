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

    /**
     * @param array<string> $data
     * @dataProvider parametersProvider
     */
    public function testParameters(array $data, bool $isRebill): void
    {
        $handler = new WebhookHandler(new SignatureCreator('some_key'));

        $json = json_encode($data);

        $handler->handle('', [], $json);
        $this->assertEquals("H5D6223aA", $handler->getOrderId());
        $this->assertEquals("52.10", $handler->getAmount());
        $this->assertEquals("payment", $handler->getEventName());
        $this->assertEquals("RUB", $handler->getCurrency());
        $this->assertEquals("PS00000000000007", $handler->getTransactionId());
        $this->assertEquals("4539********2773", $handler->getCardMasked());
        $this->assertEquals("16.09.2019 16.52.41", $handler->getDateTime());
        $this->assertEquals($isRebill ? "1173" : null, $handler->getRecurringId());
        $this->assertEquals($isRebill ? "PS00000000000001" : null, $handler->getRebillId());
        $this->assertEquals($isRebill, $handler->isRebill());
    }

    /**
     * @return array<array<string|bool>>
     */
    public function parametersProvider(): array
    {
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
        $rebillData = [
            "Event" => "Payment",
            "TransactionId" => "PS00000000000007",
            "OrderId" => "H5D6223aA",
            "Amount" => "52.10",
            "Currency" => "RUB",
            "DateTime" => "16.09.2019 16.52.41",
            "CardMasked" => "4539********2773",
            "IsTest" => 1,
            "RebillId" => "PS00000000000001",
            "RecurringId" => "1173",
        ];

        return [
            [$data, false,],
            [$rebillData, true,],
        ];
    }
}
