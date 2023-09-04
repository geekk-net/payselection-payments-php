<?php
namespace Geekk\PayselectionPaymentsPhp;

class SignatureCreator
{

    private $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function makeSignature(array $data): string
    {
        $string = implode("\n", $data);

        return hash_hmac('sha256', $string, $this->secretKey, false);
    }
}
