<?php

namespace Geekk\PayselectionPaymentsPhp\Recurring;

class UnsubscribeResult
{
    /**
     * @var string|null
     */
    private $payload;

    /**
     * @var bool
     */
    private $success;

    /**
     * @var string|null
     */
    private $errorCode = null;

    /**
     * @var string|null
     */
    private $errorDescription = null;

    /**
     * @var array<string, string>
     */
    private $addDetails;


    /**
     * @param int $httpCode
     * @param ?string $payload
     */
    public function __construct(int $httpCode, ?string $payload)
    {
        $this->payload = $payload;

        $data = json_decode($payload, true);

        if ($httpCode == 201) {
            $this->success = $data['TransactionState'] === true || $data['TransactionState'] === "true";

            if (!empty($data['Error'])) {
                $this->errorDescription = 'Unknown error';
                $this->errorCode = $data['Error']['Code'] ?? null;
                if (!empty($data['Error']['Description'])) {
                    $this->errorDescription = $data['Error']['Description'];
                }
            }
        } else {
            $this->success = false;
            $this->errorCode = $data['Code'] ?? null;
            $this->errorDescription = $data['Description'] ?? null;
            $this->addDetails = $data['AddDetails'] ?? [];
        }
    }

    public function success(): bool
    {
        return $this->success;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getErrorDescription(): ?string
    {
        return $this->errorDescription;
    }

    public function getAddDetailsStatus(): ?string
    {
        return $this->addDetails['Status'] ?? null;
    }

    public function isAlreadyCanceled(): bool
    {
        return $this->getErrorCode() == 'RecurrentStatusError' && $this->getAddDetailsStatus() == 'canceled';
    }

    public function getPayload(): ?string
    {
        return $this->payload;
    }
}
