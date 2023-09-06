<?php

namespace Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData;

class CompanyData
{
    /**
     * @var string
     */
    private $inn;

    /**
     * @var string
     */
    private $shopUrl;

    public function __construct(string $inn, string $shopUrl)
    {
        $this->shopUrl = $shopUrl;
        $this->inn = $inn;
    }

    /**
     * @return array<string, string>
     */
    public function getBuiltData(): array
    {
        return [
            'inn' => $this->inn,
            'payment_address' => $this->shopUrl
        ];
    }
}
