<?php

namespace Geekk\PayselectionPaymentsPhp\Tests;

use Geekk\PayselectionPaymentsPhp\Paylink\PaylinkCreatorResult;
use PHPUnit\Framework\TestCase;
class PaylinkCreatorResultTest extends TestCase
{

    public function testSuccessResponse()
    {
        $paymentUrl = 'http://somepath';
        $paylinkResult = new PaylinkCreatorResult(200, $paymentUrl);

        $this->assertTrue($paylinkResult->success());
        $this->assertEquals($paymentUrl, $paylinkResult->getPaymentUrl());
    }

    public function testErrorResponse()
    {
        $errorData = [
            "Code" => "ERROR_CODE_01",
            "Description" => "Error description"
        ];
        $payload = json_encode($errorData);
        $paylinkResult = new PaylinkCreatorResult(400, $payload);

        $this->assertFalse($paylinkResult->success());
        $this->assertEquals($errorData['Code'], $paylinkResult->getErrorCode());
        $this->assertEquals($errorData['Description'], $paylinkResult->getErrorDescription());
    }

    public function testEmptyErrorData()
    {
        $paylinkResult = new PaylinkCreatorResult(400, '');

        $this->assertFalse($paylinkResult->success());
        $this->assertNull($paylinkResult->getErrorCode());
        $this->assertEquals('Unknown error', $paylinkResult->getErrorDescription());

        $paylinkResult = new PaylinkCreatorResult(400, json_encode([]));

        $this->assertFalse($paylinkResult->success());
        $this->assertNull($paylinkResult->getErrorCode());
        $this->assertEquals('Unknown error', $paylinkResult->getErrorDescription());
    }
}