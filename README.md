# Payselection payments

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
$paylinkResult = $paylinkCreator->createPaylink($paymentRequest, null);

if ($paylinkResult->success()) {
    header('Location: ' . $paylinkResult->getPaymentUrl());
    exit;
}

echo $paylinkResult->getErrorCode().' '.$paylinkResult->getErrorDescription()
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

if ( ! $webhook->isCorrect()) {
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