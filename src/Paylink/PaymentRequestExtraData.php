<?php

namespace Geekk\PayselectionPaymentsPhp\Paylink;

class PaymentRequestExtraData
{

    private $successUrl;
    private $declineUrl;
    private $webhookUrl;
    private $resultUrl;

    public function getSuccessUrl(): ?string
    {
        return $this->successUrl;
    }

    public function setSuccessUrl(?string $successUrl): void
    {
        $this->successUrl = $successUrl;
    }

    public function getDeclineUrl(): ?string
    {
        return $this->declineUrl;
    }

    /**
     * @param mixed $declineUrl
     */
    public function setDeclineUrl(?string $declineUrl): void
    {
        $this->declineUrl = $declineUrl;
    }

    public function getWebhookUrl(): ?string
    {
        return $this->webhookUrl;
    }

    public function setWebhookUrl(?string $webhookUrl): void
    {
        $this->webhookUrl = $webhookUrl;
    }

    public function getResultUrl(): ?string
    {
        return $this->resultUrl;
    }

    public function setResultUrl(?string $resultUrl): void
    {
        $this->resultUrl = $resultUrl;
    }




}