<?php

namespace Geekk\PayselectionPaymentsPhp\Paylink;

use DateTimeImmutable;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData\ClientData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData\CompanyData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData\ItemData;

class ReceiptData
{
    /**
     * @var CompanyData
     */
    private $company;

    /**
     * @var ClientData
     */
    private $client;

    /**
     * @var array<int, ItemData>
     */
    private $items;

    /**
     * @var int
     */
    private $paymentsType = 1;

    /**
     * @param CompanyData $company
     * @param ClientData $client
     * @param array<int, ItemData> $items
     */
    public function __construct(CompanyData $company, ClientData $client, array $items)
    {
        $this->items = $items;
        $this->client = $client;
        $this->company = $company;
    }

    public function setPaymentsType(int $paymentsType): void
    {
        $this->paymentsType = $paymentsType;
    }

    /**
     * @return array<mixed>
     */
    private function getItemsData(): array
    {
        /**
         * @param ItemData $value
         * @return array<mixed>
         */
        $func = function (ItemData $value): array {
            return $value->getBuiltData();
        };

        return array_map($func, $this->items);
    }

    private function getPaymentSum(): float
    {
        /**
         * @param ItemData $value
         * @return float
         */
        $func = function (float $total, ItemData $value): float {
            $total += $value->getSum();
            return $total;
        };

        return array_reduce($this->items, $func, 0);
    }

    /**
     * @return array<mixed>
     */
    public function getBuiltData(): array
    {
        $paymentsSum = Formatter::floatToString($this->getPaymentSum());

        return [
            'timestamp' => (new DateTimeImmutable())->format('d.m.Y H:i'),
            'receipt' => [
                'client' => $this->client->getBuiltData(),
                'company' => $this->company->getBuiltData(),
                'items' => $this->getItemsData(),
                'payments' => [
                    [
                        'type' => $this->paymentsType,
                        'sum' => floatval($paymentsSum)
                    ]
                ],
                'total' => floatval($paymentsSum)
            ]
        ];
    }
}
