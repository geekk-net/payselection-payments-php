# Payselection payments


```php

$signature = new SignatureCreator($secretKey);

$apiConnection = new ApiConnection(new Client(), $siteId, $signature);

$paymentRequest = new PaymentRequestData($orderId, $amount, $currency, $description);
$extraData = new PaymentRequestExtraData();
$extraData->setWebhookUrl('https://...');
$extraData->setSuccessUrl('https://...');
$extraData->setDeclineUrl('https://...');
$paymentRequest->setExtraData($extraData);

$paylinkCreator = new PaylinkCreator($apiConnection);
$paylinkResult = $paylinkCreator->createPayment($paymentRequest, null);

if ($paylinkResult->success()) {
    header('Location: ' . $paylinkResult->getPaymentUrl());
    exit;
}

echo $paylinkResult->getErrorCode().' '.$paylinkResult->getErrorDescription()
```