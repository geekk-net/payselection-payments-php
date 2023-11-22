<?php

namespace Geekk\PayselectionPaymentsPhp\Paylink;

class PaymentRequestData
{

    /**
     * @var string
     */
    private $orderId;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $description;

    /**
     * @var bool
     */
    private $rebillFlag;

    /**
     * @var ?PaymentRequestExtraData $extraData
     */
    private $extraData;

    public function __construct(
        string $orderId,
        float  $amount,
        string $currency,
        string $description,
        bool   $rebillFlag = false
    ) {
        $this->description = $description;
        $this->currency = strtoupper($currency);
        $this->amount = $amount;
        $this->orderId = $orderId;
        $this->rebillFlag = $rebillFlag;
    }

    public function setExtraData(PaymentRequestExtraData $extraData): void
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

    /**
     * @return bool
     */
    public function getRebillFlag(): bool
    {
        return $this->rebillFlag;
    }

    /**
     * @return array<string, array<string, string>|string>
     */
    public function getBuiltData(): array
    {
        $data = [
            "OrderId" => $this->getOrderId(),
            "Amount" => Formatter::floatToString($this->getAmount()),
            "Currency" => $this->getCurrency(),
            "Description" => $this->getDescription(),
            "RebillFlag" => $this->getRebillFlag()
        ];

        if (!empty($this->extraData)) {
            $data['ExtraData'] = $this->extraData->getBuiltData();
        }

        return $data;
    }
}
