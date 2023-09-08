# Payselection payments

![CI workflow](https://github.com/geekk-net/payselection-payments-php/actions/workflows/ci.yml/badge.svg)

## Redirect to payment

```php

use Geekk\PayselectionPaymentsPhp\Paylink\PaymentRequestData;
use Geekk\PayselectionPaymentsPhp\Paylink\PaymentRequestExtraData;
use Geekk\PayselectionPaymentsPhp\PayselectionApi;
use Geekk\PayselectionPaymentsPhp\SignatureCreator;
use GuzzleHttp\Client;

//...

$paymentRequest = new PaymentRequestData($orderId, $amount, $currency, $description);
$extraData = new PaymentRequestExtraData();
$extraData->setWebhookUrl('https://...');
$extraData->setSuccessUrl('https://...');
$extraData->setDeclineUrl('https://...');
$paymentRequest->setExtraData($extraData);

$paylinkCreator = new PayselectionApi(new Client(), $siteId, new SignatureCreator($secretKey));
$paylinkResult = $paylinkCreator->createPaylink($paymentRequest);

if ($paylinkResult->success()) {
    header('Location: ' . $paylinkResult->getPaymentUrl());
    exit;
}

echo $paylinkResult->getErrorCode().' '.$paylinkResult->getErrorDescription()
```

## Add receipt for to payment

```php
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData\ClientData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData\CompanyData;
use Geekk\PayselectionPaymentsPhp\Paylink\ReceiptData\ItemData;

// ...

$company = new CompanyData('YOUR_INN', 'https://shop-site.net');
$client = new ClientData('user@mail.com');
$items = [new ItemData(7.95, 'Some digital goods')];
$receipt = new ReceiptData($company, $client, $items);

$paylinkResult = $paylinkCreator->createPaylink($paymentRequest, $receipt);
```

## Process a webhook

```php
use Geekk\PayselectionPaymentsPhp\WebhookHandler;

// ...

$data = file_get_contents("php://input");
$headers = getallheaders();
$requestUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']
    === 'on' ? "https" : "http") .
    "://" . $_SERVER['HTTP_HOST'] .
    $_SERVER['REQUEST_URI'];

$webhook = new WebhookHandler(new SignatureCreator($secretKey));

$webhook->handle($requestUrl, $headers, $data);

if ( ! $webhook->hasCorrectSignature()) {
    log("Signature error");
    return;
}

$eventType = $webhook->getEventName();

if (! in_array($eventType, $webhook->getOneStepEventTypes())) {
    log("Incorrect event type");
    return;
}

if ($eventType == $webhook::EVENT_PAYMENT) {
    log(sprintf("Successful payment #%s: %s %s",
        $webhook->getOrderId(),
        $webhook->getAmount(),
        $webhook->getCurrency()
    ));
}
```