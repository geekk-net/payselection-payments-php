<?php

namespace Geekk\PayselectionPaymentsPhp\Paylink\RecurringData;

use Geekk\PayselectionPaymentsPhp\Paylink\Formatter;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData;

class RecurringData
{
    const PERIOD_DAY = 'day';
    const PERIOD_WEEK = 'week';
    const PERIOD_MONTH = 'month';

    /**
     * @var float
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var string|null
     */
    private $webhookUrl;

    /**
     * @var string
     */
    private $accountId;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var string
     */
    private $startDate;

    /**
     * @var string
     */
    private $interval;

    /**
     * @var string
     */
    private $period;

    /**
     * @var string|null
     */
    private $maxPeriods;

    /**
     * @var ReceiptData|null
     */
    private $receiptData;

    public function __construct(
        float        $amount,
        string       $currency,
        string       $accountId,
        string       $startDate,
        string       $interval,
        string       $period,
        ?string      $description = null,
        ?string      $webhookUrl = null,
        ?string      $email = null,
        ?string      $maxPeriods = null,
        ?ReceiptData $receiptData = null
    ) {
        $this->amount = $amount;
        $this->currency = strtoupper($currency);
        $this->accountId = $accountId;
        $this->startDate = $startDate;
        $this->interval = $interval;
        $this->period = $period;
        $this->description = $description;
        $this->webhookUrl = $webhookUrl;
        $this->email = $email;
        $this->maxPeriods = $maxPeriods;
        $this->receiptData = $receiptData;
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
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function getWebhookUrl(): ?string
    {
        return $this->webhookUrl;
    }

    /**
     * @return string
     */
    public function getAccountId(): string
    {
        return $this->accountId;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getStartDate(): string
    {
        return $this->startDate;
    }

    /**
     * @return string
     */
    public function getInterval(): string
    {
        return $this->interval;
    }

    /**
     * @return string
     */
    public function getPeriod(): string
    {
        return $this->period;
    }

    /**
     * @return string
     */
    public function getMaxPeriods(): ?string
    {
        return $this->maxPeriods;
    }

    /**
     * @return array<string, array<string, string>|string>
     */
    public function getBuiltData(): array
    {
        $data = [
            "Amount" => Formatter::floatToString($this->getAmount()),
            "Currency" => $this->getCurrency(),
            "Description" => $this->getDescription(),
            "WebhookUrl" => $this->getWebhookUrl(),
            "AccountId" => $this->getAccountId(),
            "Email" => $this->getEmail(),
            "StartDate" => $this->getStartDate(),
            "Interval" => $this->getInterval(),
            "Period" => $this->getPeriod(),
            "ReceiptData" => $this->receiptData->getBuiltData(),
        ];

        if (!empty($this->getMaxPeriods())) {
            $data["MaxPeriods"] = $this->getMaxPeriods();
        }

        return $data;
    }
}
