<?php

namespace Geekk\PayselectionPaymentsPhp\Paylink;

class PaymentRequestData
{

    private $orderId;
    private $amount;
    private $currency;
    private $description;

    /**
     * @var PaymentRequestExtraData $extraData
     */
    private $extraData;

    public function __construct(string $orderId, float $amount, string $currency, string $description)
    {
        $this->description = $description;
        $this->currency = strtoupper($currency);
        $this->amount = $amount;
        $this->orderId = $orderId;
    }

    public function setExtraData(PaymentRequestExtraData $extraData)
    {
        $this->extraData = $extraData;
    }

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    public function getBuiltData(): array
    {
        $data = [
            "OrderId" => $this->getOrderId(),
            "Amount" => number_format($this->getAmount(), 2, '.', ''),
            "Currency" => $this->getCurrency(),
            "Description" => $this->getDescription()
        ];

        if (!empty($this->extraData)) {
            $data['ExtraData'] = $this->extraData->getBuiltData();
        }

        return $data;
    }
}