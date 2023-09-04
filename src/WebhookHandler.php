<?php

namespace Geekk\PayselectionPaymentsPhp;

class WebhookHandler
{

    /**
     * @var SignatureCreator
     */
    private $signatureCreator;

    /**
     * @var ?string
     */
    private $requestUri;

    /**
     * @var ?array<string, string>
     */
    private $headers;

    /**
     * @var ?string
     */
    private $body;

    /**
     * @var ?array<string, string>
     */
    private $data;

    public function __construct(SignatureCreator $signature)
    {
        $this->signatureCreator = $signature;
    }

    /**
     * @param string $requestUri
     * @param array<string, string>|null $headers
     * @param string|null $body
     * @return void
     */
    public function handle(string $requestUri, ?array $headers, ?string $body): void
    {
        $this->requestUri = $requestUri;
        $this->headers = $headers ?? [];
        $data = json_decode($body, true);
        $this->body = $body;
        $this->data = empty($data) ? null : $data;
    }

    /**
     * @return array<string, string>|null
     */
    public function getAllHeaders(): ?array
    {
        return $this->headers;
    }

    /**
     * @return array<string, string>|null
     */
    public function getAllData(): ?array
    {
        return $this->data;
    }

    public function getSignature(): ?string
    {
        return $this->getAllHeaders()['X-REQUEST-SIGNATURE'] ?? null;
    }

    public function getSiteId(): ?string
    {
        return $this->getAllHeaders()['X-SITE-ID'] ?? null;
    }

    public function isCorrect(): bool
    {
        $dataToSign = ['POST', $this->requestUri, $this->getSiteId(), $this->body];

        return $this->signatureCreator->makeSignature($dataToSign) == $this->getSignature();
    }

    public function getEventName(): ?string
    {
        return $this->data['Event'] ?? null;
    }

    public function getOrderId(): ?string
    {
        return $this->data['OrderId'] ?? null;
    }

    public function getAmount(): ?string
    {
        return $this->data['Amount'] ?? null;
    }

    public function getCurrency(): ?string
    {
        return $this->data['Currency'] ?? null;
    }

    public function getTransactionId(): ?string
    {
        return $this->data['TransactionId'] ?? null;
    }

    public function getDateTime(): ?string
    {
        return $this->data['DateTime'] ?? null;
    }
}
