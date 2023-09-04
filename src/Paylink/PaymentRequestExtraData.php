<?php

namespace Geekk\PayselectionPaymentsPhp\Paylink;

class PaymentRequestExtraData
{

    private $successUrl;
    private $declineUrl;
    private $webhookUrl;
    private $returnUrl;

    public function setSuccessUrl(?string $successUrl): void
    {
        $this->successUrl = $successUrl;
    }

    /**
     * @param mixed $declineUrl
     */
    public function setDeclineUrl(?string $declineUrl): void
    {
        $this->declineUrl = $declineUrl;
    }

    public function setWebhookUrl(?string $webhookUrl): void
    {
        $this->webhookUrl = $webhookUrl;
    }

    public function setReturnUrl(?string $returnUrl): void
    {
        $this->returnUrl = $returnUrl;
    }

    public function getBuiltData(): array
    {
        $data = [];

        if (!empty($this->returnUrl)) {
            $data['ReturnUrl'] = $this->returnUrl;
        }

        if (!empty($this->successUrl)) {
            $data['SuccessUrl'] = $this->successUrl;
        }

        if (!empty($this->declineUrl)) {
            $data['DeclineUrl'] = $this->declineUrl;
        }

        if (!empty($this->webhookUrl)) {
            $data['WebhookUrl'] = $this->webhookUrl;
        }

        return $data;
    }
}
