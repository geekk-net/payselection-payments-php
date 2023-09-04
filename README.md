# Payselection payments


```php

$paymentRequest = new PaymentRequestData($orderId, $amount, $currency, $description);
$extraData = new PaymentRequestExtraData();
$extraData->setWebhookUrl('https://...');
$extraData->setSuccessUrl('https://...');
$extraData->setDeclineUrl('https://...');
$paymentRequest->setExtraData($extraData);

$paylinkCreator = new PayselectionApi(new Client(), $siteId, new SignatureCreator($secretKey));
$paylinkResult = $paylinkCreator->createPayment($paymentRequest, null);

if ($paylinkResult->success()) {
    header('Location: ' . $paylinkResult->getPaymentUrl());
    exit;
}

echo $paylinkResult->getErrorCode().' '.$paylinkResult->getErrorDescription()
```