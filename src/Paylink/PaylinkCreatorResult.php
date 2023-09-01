<?php

namespace Geekk\PayselectionPaymentsPhp\Paylink;

class PaylinkCreatorResult
{
    public function success(): bool
    {
        return false;
    }

    public function getPaymentUrl(): ?string
    {
        return null;
    }

    public function getErrorCode(): ?int
    {
        return null;
    }

    public function getErrorDescription(): ?string
    {
        return null;
    }
}