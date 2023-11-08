<?php

namespace Geekk\PayselectionPaymentsPhp\Tests;

use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData\ClientData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData\CompanyData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData\ItemData;
use Geekk\PayselectionPaymentsPhp\Paylink\RecurringData\RecurringData;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;
use DateInterval;

class RecurringDataTest extends TestCase
{
    public function testBaseStructure(): void
    {
        $inn = '7707414777';
        $shopUrl = 'https://shop-site.net';
        $webhookUrl = 'https://shop-site.net/webhook';
        $clientEmail = 'user@mail.com';
        $paymentAmount = 7.95;
        $currency = 'RUB';
        $paymentDescription = 'Some digital goods';
        $startDate = (new DateTimeImmutable())->add(new DateInterval('P30D'))->format('d.m.Y H:i:s');
        $interval = '1';
        $period = RecurringData::PERIOD_MONTH;
        $maxPeriods = '12';

        $company = new CompanyData($inn, $shopUrl);
        $client = new ClientData($clientEmail);
        $items = [new ItemData($paymentAmount, $paymentDescription)];
        $receipt = new ReceiptData($company, $client, $items);

        $recurring = new RecurringData(
            $paymentAmount,
            $currency,
            $clientEmail,
            $startDate,
            $interval,
            $period,
            $paymentDescription,
            $webhookUrl,
            $clientEmail,
            $maxPeriods,
            $receipt
        );

        $recurringData = $recurring->getBuiltData();

        $this->assertIsArray($recurringData);
        $this->assertArrayHasKey('Amount', $recurringData);
        $this->assertArrayHasKey('Currency', $recurringData);
        $this->assertArrayHasKey('Description', $recurringData);
        $this->assertArrayHasKey('WebhookUrl', $recurringData);
        $this->assertArrayHasKey('AccountId', $recurringData);
        $this->assertArrayHasKey('Email', $recurringData);
        $this->assertArrayHasKey('StartDate', $recurringData);
        $this->assertArrayHasKey('Interval', $recurringData);
        $this->assertArrayHasKey('Period', $recurringData);
        $this->assertArrayHasKey('MaxPeriods', $recurringData);
        $this->assertIsArray($recurringData['ReceiptData']);

        $this->assertEquals($paymentAmount, $recurringData['Amount'] ?? null);
        $this->assertEquals($currency, $recurringData['Currency'] ?? null);
        $this->assertEquals($paymentDescription, $recurringData['Description'] ?? null);
        $this->assertEquals($webhookUrl, $recurringData['WebhookUrl'] ?? null);
        $this->assertEquals($clientEmail, $recurringData['AccountId'] ?? null);
        $this->assertEquals($clientEmail, $recurringData['Email'] ?? null);
        $this->assertEquals($interval, $recurringData['Interval'] ?? null);
        $this->assertEquals($period, $recurringData['Period'] ?? null);
        $this->assertEquals($maxPeriods, $recurringData['MaxPeriods'] ?? null);
        $this->assertEquals($receipt->getBuiltData(), $recurringData['ReceiptData']);
    }
}
