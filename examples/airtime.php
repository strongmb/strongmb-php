<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Strongmb\Strongmb;
use Strongmb\Exceptions\ApiException;
use Strongmb\Exceptions\AuthException;
use Strongmb\Exceptions\StrongmbException;

$strongmb = new Strongmb('your_api_key_here');

// ── Get airtime plans / product codes ─────────────────────────────────────────
try {
    $response = $strongmb->airtime->plans();

    foreach ($response->data()['billers'] as $biller) {
        echo $biller['name'] . PHP_EOL;

        foreach ($biller['products'] as $productType => $product) {
            foreach ($product['plans'] as $plan) {
                echo '  [' . $plan['product_code'] . '] '
                    . $plan['name']
                    . ' — ' . $plan['discount'] . '% discount'
                    . PHP_EOL;
            }
        }

        echo PHP_EOL;
    }
} catch (ApiException $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}


// ── Purchase airtime ──────────────────────────────────────────────────────────
try {
    $response = $strongmb->airtime->purchase(
        phone: '08012345678',
        productCode: 'smb_mtn_vtu',
        amount: '100',
        reference: 'MYAPP' . strtoupper(uniqid()),
    );

    if ($response->successful()) {
        $data = $response->data();
        echo 'Airtime sent!' . PHP_EOL;
        echo 'Provider:  ' . $data['provider'] . PHP_EOL;
        echo 'Recipient: ' . $data['recipient'] . PHP_EOL;
        echo 'Amount:    ₦' . $data['airtime_amount'] . PHP_EOL;
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
