<?php

namespace Geekk\PayselectionPaymentsPhp\Recurring;

class UnsubscribeResult
{

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
     * @param ?string $payload
     */
    public function __construct(?string $payload)
    {
        $data = json_decode($payload, true);

        $this->success = $data['TransactionState'] === "true";

        if (!empty($data['Error'])) {
            $this->errorDescription = 'Unknown error';
            $this->errorCode = $data['Error']['Code'] ?? null;
            if (!empty($data['Error']['Description'])) {
                $this->errorDescription = $data['Error']['Description'];
            }
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
}
