<?php

namespace Geekk\PayselectionPaymentsPhp\Tests;

use Geekk\PayselectionPaymentsPhp\Recurring\UnsubscribeResult;
use PHPUnit\Framework\TestCase;

class UnsubscribeResultTest extends TestCase
{
    /**
     * @dataProvider unsubscribeResultProvider
     */
    public function testErrorResponse(
        bool    $isSuccess,
        int     $httpCode,
        string  $payload,
        ?string $code,
        ?string $description,
        bool    $isCanceled
    ): void {
        $unsubscribeResult = new UnsubscribeResult($httpCode, $payload);
        $this->assertEquals($isSuccess, $unsubscribeResult->success());
        $this->assertEquals($code, $unsubscribeResult->getErrorCode());
        $this->assertEquals($description, $unsubscribeResult->getErrorDescription());
        $this->assertEquals($isCanceled, $unsubscribeResult->isAlreadyCanceled());
    }

    /**
     * @return array<array{0: bool, 1: int, 2: string, 3: string|null, 4: string|null, 5: bool}>
     */
    public function unsubscribeResultProvider(): array
    {
        return [
            [
                true,
                201,
                '{"TransactionState": "true"}',
                null,
                null,
                false
            ],
            [
                true,
                201,
                '{"TransactionState": true}',
                null,
                null,
                false
            ],
            [
                false,
                201,
                '{"TransactionState": "false"}',
                null,
                null,
                false
            ],
            [
                false,
                201,
                '{"TransactionState": false}',
                null,
                null,
                false
            ],
            [
                false,
                201,
                '{"TransactionState": false, "Error": {"Code": "ERROR_CODE_01", "Description": "Error description"}}',
                'ERROR_CODE_01',
                'Error description',
                false
            ],
            [
                false,
                400,
                '{"Code": "ERROR_CODE_01", "Description": "Error description"}',
                'ERROR_CODE_01',
                'Error description',
                false
            ],
            [
                false,
                409,
                '{"Code": "RecurrentStatusError", "Description": "Operation does not change status", "AddDetails": 
                    {"RebillId": "PS00000341193131", "Operation": "Unsubscribe", "Status": "canceled"}}',
                'RecurrentStatusError',
                'Operation does not change status',
                true
            ],
            [
                false,
                409,
                '{"Code": "RecurrentStatusError", "Description": "Operation does not change status", 
                    "AddDetails": {"RebillId": "PS00000341193131"}}',
                'RecurrentStatusError',
                'Operation does not change status',
                false
            ],
        ];
    }
}
