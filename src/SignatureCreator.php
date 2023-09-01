<?php
namespace Geekk\PayselectionPaymentsPhp;

class SignatureCreator
{

    private $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function makeSignature(string $data): string
    {
        return '';
    }
}