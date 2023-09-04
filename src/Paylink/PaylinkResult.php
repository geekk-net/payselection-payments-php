<?php

namespace Geekk\PayselectionPaymentsPhp\Paylink;

class PaylinkResult
{

    /**
     * @var bool
     */
    private $success;

    /**
     * @var string|null
     */
    private $paymentUrl = null;

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
        $this->success = $httpCode == 200;

        if ($this->success) {
            $this->paymentUrl = $payload;
        } else {
            $errorData = json_decode($payload, true);
            $this->errorDescription = 'Unknown error';
            if (!empty($errorData)) {
                $this->errorCode = $errorData['Code'] ?? null;
                if (!empty($errorData['Description'])) {
                    $this->errorDescription = $errorData['Description'];
                }
            }
        }
    }

    public function success(): bool
    {
        return $this->success and !empty($this->paymentUrl);
    }

    public function getPaymentUrl(): ?string
    {
        return $this->paymentUrl;
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
