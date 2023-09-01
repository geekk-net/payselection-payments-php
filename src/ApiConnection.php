<?php

namespace Geekk\PayselectionPaymentsPhp;

class ApiConnection
{

    private $siteId;
    private $signatureCreator;

    public function __construct(string $siteId, SignatureCreator $signatureCreator)
    {
        $this->siteId = $siteId;
        $this->signatureCreator = $signatureCreator;
    }
}