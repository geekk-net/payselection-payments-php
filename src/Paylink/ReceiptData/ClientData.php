<?php

namespace Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData;

class ClientData
{

    /**
     * @var ?string
     */
    private $email;

    /**
     * @var ?string
     */
    private $phone;

    public function __construct(?string $email = null, ?string $phone = null)
    {
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
            $data['email'] = $this->email;
        }

        if (!empty($this->phone)) {
            $data['phone'] = $this->phone;
        }

        return $data;
    }
}
