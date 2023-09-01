# Payselection payments


```php

$signature = new SignatureCreator($secretKey);

$apiConnection = new ApiConnection($siteId, $signature);

$paymentRequest = new PaymentRequestData($orderId, $amount, $currecny, $description);
$extraData = new PaymentRequestExtraData();
$extradata->webhookUrl = 'https://...';
$extradata->successUrl = 'https://...';
$extradata->declineUrl = 'https://...';
$paymentRequest->extraData = $extraData;

$receiptData = new ReceiptData();
// ...

$paylinkCreator = new PaylinkCreator($apiConnection);
$paymentlinkResult = $paylinkCreator->createPayment($paymentRequest, $receiptData);

if ($paymentlinkResult->success()) {
    header('Location: '.$paymentlinkResult->getPaymentUrl());
    exit;
}

echo $paymentlinkResult->getErrorCode().' '.$paymentlinkResult->getErrorDescription()
```