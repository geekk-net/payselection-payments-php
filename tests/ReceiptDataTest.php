<?php

namespace Geekk\PayselectionPaymentsPhp\Tests;

use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData\ClientData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData\CompanyData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData\ItemData;
use PHPUnit\Framework\TestCase;

class ReceiptDataTest extends TestCase
{
    public function testBaseStructure(): void
    {
        $inn = '7707414777';
        $shopUrl = 'https://shop-site.net';
        $clientEmail = 'user@mail.com';
        $paymentAmount = 7.95;
        $paymentDescription = 'Some digital goods';
        $company = new CompanyData($inn, $shopUrl);
        $client = new ClientData($clientEmail);
        $items = [new ItemData($paymentAmount, $paymentDescription)];
        $receipt = new ReceiptData($company, $client, $items);

        $receiptData = $receipt->getBuiltData();
        $this->assertIsArray($receiptData);
        $this->assertArrayHasKey('timestamp', $receiptData);
        $this->assertArrayHasKey('receipt', $receiptData);
        $this->assertIsArray($receiptData['receipt']);

        $receiptDetails = $receiptData['receipt'];
        $this->assertArrayHasKey('client', $receiptDetails);
        $this->assertArrayHasKey('company', $receiptDetails);
        $this->assertArrayHasKey('items', $receiptDetails);
        $this->assertArrayHasKey('payments', $receiptDetails);
        $this->assertArrayHasKey('total', $receiptDetails);

        $this->assertEquals($clientEmail, $receiptDetails['client']['email'] ?? null);

        $this->assertEquals($inn, $receiptDetails['company']['inn'] ?? null);
        $this->assertEquals($shopUrl, $receiptDetails['company']['payment_address'] ?? null);

        $this->assertIsArray($receiptDetails['items']);
        $this->assertEquals(1, count($receiptDetails['items']));
        $item = $receiptDetails['items'][0];
        $this->assertEquals($paymentDescription, $item['name'] ?? null);
        $this->assertEquals($paymentAmount, $item['price'] ?? null);
        $this->assertIsFloat($item['price'] ?? null);
        $this->assertEquals(1, $item['quantity'] ?? null);
        $this->assertEquals($paymentAmount, $item['sum'] ?? null);
        $this->assertIsFloat($item['sum'] ?? null);

        $this->assertIsArray($receiptDetails['payments']);
        $this->assertEquals(1, count($receiptDetails['payments']));
        $payment = $receiptDetails['payments'][0];
        $this->assertEquals(1, $payment['type'] ?? null);
        $this->assertEquals($paymentAmount, $payment['sum'] ?? null);

        $this->assertEquals($paymentAmount, $receiptDetails['total']);
        $this->assertIsFloat($receiptDetails['total']);
    }
}
