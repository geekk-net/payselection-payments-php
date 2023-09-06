<?php

namespace Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData;

use Geekk\PayselectionPaymentsPhp\Paylink\Formatter;

class ItemData
{

    /**
     * @var float
     */
    private $price;

    /**
     * @var string
     */
    private $description;

    /**
     * @var int
     */
    private $quantity = 1;

    /**
     * @var string
     */
    private $paymentMethod = 'full_payment';

    /**
     * @var string
     */
    private $paymentObject = 'service';

    /**
     * @var string
     */
    private $vatType = 'none';

    public function __construct(float $price, string $description, int $quantity = 1)
    {
        $this->quantity = $quantity;
        $this->description = $description;
        $this->price = $price;
    }

    public function setPaymentMethod(string $paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }

    public function setPaymentObject(string $paymentObject): void
    {
        $this->paymentObject = $paymentObject;
    }

    public function setVatType(string $vatType): void
    {
        $this->vatType = $vatType;
    }

    public function getSum(): float
    {
        return round($this->price*$this->quantity, 2);
    }

    /**
     * @return array<mixed>
     */
    public function getBuiltData(): array
    {
        return [
            'name' => $this->description,
            'price' => Formatter::floatToString($this->price),
            'quantity' => $this->quantity,
            'sum' => Formatter::floatToString($this->getSum()),
            'payment_method' => strtolower($this->paymentMethod),
            'payment_object' => strtolower($this->paymentObject),
            'vat' => [
                'type' => strtolower($this->vatType)
            ]
        ];
    }
}
