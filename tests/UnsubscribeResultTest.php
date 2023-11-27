<?php

namespace Geekk\PayselectionPaymentsPhp\Tests;

use Geekk\PayselectionPaymentsPhp\Recurring\UnsubscribeResult;
use PHPUnit\Framework\TestCase;

class UnsubscribeResultTest extends TestCase
{

    /**
     * @dataProvider unsubscribeResultProvider
     */
    public function testSuccessResponse(string $data): void
    {
        $unsubscribeResult = new UnsubscribeResult($data);

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
        $unsubscribeResult = new UnsubscribeResult($payload);

        $this->assertFalse($unsubscribeResult->success());
        $this->assertEquals($failData["Error"]['Code'], $unsubscribeResult->getErrorCode());
        $this->assertEquals($failData["Error"]['Description'], $unsubscribeResult->getErrorDescription());
    }

    /**
     * @return array<array{0: string}>
     */
    public function unsubscribeResultProvider(): array
    {
        return [
            ["{\"TransactionState\": \"true\"}"],
            ["{\"TransactionState\": true}"],
        ];
    }
}
