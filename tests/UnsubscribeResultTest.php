<?php

namespace Geekk\PayselectionPaymentsPhp\Tests;

use Geekk\PayselectionPaymentsPhp\Recurring\UnsubscribeResult;
use PHPUnit\Framework\TestCase;

class UnsubscribeResultTest extends TestCase
{

    public function testSuccessResponse(): void
    {
        $unsubscribeResult = new UnsubscribeResult(201, "{\"TransactionState\": \"true\"}");

        $this->assertTrue($unsubscribeResult->success());
    }

    public function testErrorResponse(): void
    {
        $failData = [
            "TransactionState" => "false",
            "Error" => [
                "Code" => "ERROR_CODE_01",
                "Description" => "Error description"
            ]
        ];
        $payload = json_encode($failData);
        $unsubscribeResult = new UnsubscribeResult(201, $payload);

        $this->assertFalse($unsubscribeResult->success());
        $this->assertEquals($failData["Error"]['Code'], $unsubscribeResult->getErrorCode());
        $this->assertEquals($failData["Error"]['Description'], $unsubscribeResult->getErrorDescription());
    }
}
