<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Strongmb\Strongmb;
use Strongmb\Exceptions\ApiException;
use Strongmb\Exceptions\AuthException;
use Strongmb\Exceptions\StrongmbException;

$strongmb = new Strongmb('your_api_key_here');


// ── Get data plans / product codes ────────────────────────────────────────────
try {
    $response = $strongmb->data->plans();

    foreach ($response->data()['billers'] as $biller) {
        echo $biller['name'] . PHP_EOL;

        foreach ($biller['products'] as $productType => $product) {
            echo '  ' . strtoupper($productType) . PHP_EOL;

            foreach ($product['plans'] as $plan) {
                $size = $plan['bundle_size_mb'] >= 1024
                    ? round($plan['bundle_size_mb'] / 1024, 1) . 'GB'
                    : $plan['bundle_size_mb'] . 'MB';

                echo '    [' . $plan['product_code'] . '] '
                    . $size
                    . ' — ₦' . number_format($plan['amount'])
                    . ' / ' . $plan['validity']
                    . PHP_EOL;
            }
        }

        echo PHP_EOL;
    }
} catch (ApiException $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}


// ── Purchase a data plan ──────────────────────────────────────────────────────
try {
    $response = $strongmb->data->purchase(
        phone: '0812345678',
        productCode: 'smb_air_sme_ec9e4',
        reference: 'MYAPP' . strtoupper(uniqid()),
    );

    if ($response->successful()) {
        $data = $response->data();
        echo 'Data sent!' . PHP_EOL;
        echo 'Provider:  ' . $data['provider'] . PHP_EOL;
        echo 'Recipient: ' . $data['recipient'] . PHP_EOL;
        echo 'Plan:      ' . $data['plan'] . ' (' . $data['data_type'] . ')' . PHP_EOL;
        echo 'Validity:  ' . $data['validity'] . PHP_EOL;
        echo 'Amount:    ₦' . number_format($data['amount']) . PHP_EOL;
        echo 'Reference: ' . $data['reference'] . PHP_EOL;
        echo 'Balance:   ₦' . $data['balance_after'] . PHP_EOL;
    } elseif ($response->processing()) {
        $data = $response->data();
        echo 'Processing — poll by reference to check status.' . PHP_EOL;
        echo 'Reference: ' . $data['reference'] . PHP_EOL;
    } elseif ($response->failed()) {
        $data = $response->data();
        echo 'Transaction failed. Check status and contact support if wallet was debited.' . PHP_EOL;
        echo 'Reference: ' . $data['reference'] . PHP_EOL;
    }
} catch (AuthException $e) {
    echo 'Invalid API key.' . PHP_EOL;
} catch (ApiException $e) {
    echo 'Error [' . $e->getApiCode() . ']: ' . $e->getMessage() . PHP_EOL;
    echo 'Trace ID: ' . $e->getTraceId() . PHP_EOL;
} catch (StrongmbException $e) {
    echo 'Network error: ' . $e->getMessage() . PHP_EOL;
}
