<?php

namespace Geekk\PayselectionPaymentsPhp;

use GuzzleHttp\Client;

class ApiConnection
{

    const BASE_URL = 'https://webform.payselection.com';
    private $siteId;
    private $signatureCreator;

    private $client;

    public function __construct(Client $client, string $siteId, SignatureCreator $signatureCreator)
    {
        $this->client = $client;
        $this->siteId = $siteId;
        $this->signatureCreator = $signatureCreator;
    }

    public function post(string $action, ?array $data)
    {
        //$this->client->request('POST', self::BASE_URL.$action, [ 'json' => $data ]);
    }
}