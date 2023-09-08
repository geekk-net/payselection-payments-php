<?php

namespace Geekk\PayselectionPaymentsPhp\Paylink;

class CustomerInfoData
{

    /**
     * @var string
     */
    private $email;

    /**
     * @var string|null
     */
    private $phone;

    /**
     * @var string|null
     */
    private $language;

    public function __construct(string $email, ?string $phone = null, ?string $language = null)
    {
        $this->language = $language;
        $this->phone = $phone;
        $this->email = $email;
    }

    /**
     * @return array<string, string>
     */
    public function getBuiltData(): array
    {
        $data = [];

        if (!empty($this->email)) {
            $data['Email'] = $this->email;
            $data['ReceiptEmail'] = $this->email;
        }

        if (!empty($this->phone)) {
            $data['Phone'] = $this->phone;
        }

        if (!empty($this->language)) {
            $data['Language'] = $this->language;
        }

        return $data;
    }
}
