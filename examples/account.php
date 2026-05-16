<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Strongmb\Strongmb;
use Strongmb\Exceptions\ApiException;
use Strongmb\Exceptions\AuthException;
use Strongmb\Exceptions\StrongmbException;

$strongmb = new Strongmb('your_api_key_here');


// ── User profile ──────────────────────────────────────────────────────────────
try {
    $response = $strongmb->account->user();

    $account = $response->data()['account'];
    echo 'Name:   ' . $account['name'] . PHP_EOL;
    echo 'Phone:  ' . $account['phone'] . PHP_EOL;
    echo 'Status: ' . $account['account_status'] . PHP_EOL;
    echo 'KYC:    ' . $account['kyc_status'] . PHP_EOL;
} catch (AuthException $e) {
    echo 'Invalid API key.' . PHP_EOL;
} catch (ApiException $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}


echo PHP_EOL;

// ── Wallets ───────────────────────────────────────────────────────────────────
try {
    $response = $strongmb->account->wallets();
    var_dump($response->data());

    foreach ($response->data()['wallets'] as $wallet) {
        echo 'Wallet:  ' . $wallet['type'] . PHP_EOL;
        echo 'Balance: ₦' . number_format($wallet['balance'], 2) . PHP_EOL;
        echo 'Status:  ' . $wallet['status'] . PHP_EOL;
        echo PHP_EOL;
    }
} catch (ApiException $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}


// ── Transaction history ───────────────────────────────────────────────────────
try {
    $response = $strongmb->account->transactions();

    $data = $response->data();
    echo 'Total transactions: ' . $data['count'] . PHP_EOL . PHP_EOL;

    foreach ($data['transactions'] as $txn) {
        echo '[' . $txn['status'] . '] ' . strtoupper($txn['type'])
            . ' — ₦' . number_format($txn['amount'], 2)
            . ' (' . $txn['reference'] . ')' . PHP_EOL;
    }
} catch (ApiException $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}


// ── Single transaction by reference ──────────────────────────────────────────
try {
    $response = $strongmb->account->transaction('SMB-D-2605151149272145-E4B08DDB');
    $txn = $response->data()['transaction'];
    echo 'Reference: ' . $txn['reference'] . PHP_EOL;
    echo 'Type:      ' . $txn['type'] . PHP_EOL;
    echo 'Amount:    ₦' . number_format($txn['amount'], 2) . PHP_EOL;
    echo 'Status:    ' . $txn['status'] . PHP_EOL;
} catch (ApiException $e) {
    echo 'Error [' . $e->getApiCode() . ']: ' . $e->getMessage() . PHP_EOL;
} catch (StrongmbException $e) {
    echo 'Network error: ' . $e->getMessage() . PHP_EOL;
}
