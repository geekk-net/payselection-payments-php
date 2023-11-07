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
     * @param int $httpCode
     * @param ?string $payload
     */
    public function __construct(int $httpCode, ?string $payload)
    {
        $data = json_decode($payload, true);

        $this->success = $data['TransactionState'] === "true";
        
        $this->errorDescription = 'Unknown error';
        if (!empty($data['Error'])) {
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
