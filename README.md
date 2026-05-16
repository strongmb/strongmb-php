# Strongmb PHP SDK

Official PHP SDK for the [Strongmb API](https://developers.strongmb.ng) — buy airtime, data, and more with a few lines of PHP.

> For the full API reference and endpoint details, visit **[developers.strongmb.ng](https://developers.strongmb.ng)**.

---

## Requirements

- PHP 8.1+
- Composer

## Installation

```bash
composer require strongmb/strongmb-php
```

---

## Quick Start

```php
use Strongmb\Strongmb;

$strongmb = new Strongmb('your-api-key');

$response = $strongmb->data->purchase(
    phone:       '08012345678',
    productCode: 'smb_mtn_sme_1gb_30days',
    reference:   'MYAPP' . strtoupper(uniqid()),
);

if ($response->successful()) {
    echo 'Data sent! Reference: ' . $response->data()['reference'];
} elseif ($response->processing()) {
    echo 'Processing — poll by reference to check status.';
} elseif ($response->failed()) {
    echo 'Transaction failed. Contact support if wallet was debited.';
}
```

---

## Authentication

Get your API key from the **Developer** tab in your [Strongmb dashboard](https://dashboard.strongmb.ng).

```php
// Live mode
$strongmb = new Strongmb(apiKey: 'your-api-key');

// Sandbox / test mode
$strongmb = new Strongmb(apiKey: 'your-api-key', sandbox: true);
```

---

## Data

```php
// List all available data plans and product codes
$response = $strongmb->data->plans();
$billers  = $response->data()['billers'];

// Purchase a data plan
$response = $strongmb->data->purchase(
    phone:       '08012345678',
    productCode: 'smb_mtn_sme_1gb_30days',
    reference:   'MYAPP_UNIQUE_REF_001',
);
```

## Airtime

```php
// List all airtime providers and product codes
$response = $strongmb->airtime->plans();

// Top up airtime
$response = $strongmb->airtime->purchase(
    phone:       '08012345678',
    productCode: 'smb_mtn_vtu',
    amount:      '100',
    reference:   'MYAPP_UNIQUE_REF_002',
);
```

## Account

```php
// Authenticated user profile
$response = $strongmb->account->user();

// Wallet balances
$response = $strongmb->account->wallets();

// Transaction history
$response = $strongmb->account->transactions();

// Single transaction by reference
$response = $strongmb->account->transaction('MYAPP_UNIQUE_REF_001');
```

---

## Response

Every method returns a `Response` object:

```php
$response->ok();          // bool   — true when the API status is successful
$response->message();     // string — human-readable message from the API
$response->data();        // array  — the response payload
$response->code();        // string — machine-readable code (e.g. TRANSACTION_SUCCESSFUL)
$response->traceId();     // string — use this when contacting support
$response->httpStatus();  // int    — HTTP status code
$response->toArray();     // array  — full raw response

// Purchase transaction helpers
$response->successful();  // true when code === TRANSACTION_SUCCESSFUL
$response->processing();  // true when still being processed — poll by reference
$response->failed();      // true when transaction failed — wallet may have been debited
```

### Handling all purchase outcomes

```php
if ($response->successful()) {
    $data = $response->data();
    echo 'Done! Reference: ' . $data['reference'];

} elseif ($response->processing()) {
    // Request accepted but not yet fulfilled.
    // Poll account->transaction($reference) until it resolves.
    echo 'Processing — reference: ' . $response->data()['reference'];

} elseif ($response->failed()) {
    // Transaction was attempted but failed.
    // Provide the trace ID to support if the wallet was debited.
    echo 'Failed. Trace ID: ' . $response->traceId();
}
```

---

## Error Handling

```php
use Strongmb\Exceptions\ApiException;
use Strongmb\Exceptions\AuthException;
use Strongmb\Exceptions\StrongmbException;

try {
    $response = $strongmb->data->purchase(...);

} catch (AuthException $e) {
    // HTTP 401 — invalid or missing API key
    echo 'Invalid API key.';

} catch (ApiException $e) {
    // HTTP 4xx / 5xx — the API returned an error
    echo $e->getMessage();     // Human-readable message
    echo $e->getApiCode();     // e.g. ERR_INSUFFICIENT_BALANCE
    echo $e->getHttpStatus();  // e.g. 402
    echo $e->getTraceId();     // Send this to developers@strongmb.ng

} catch (StrongmbException $e) {
    // Network error or unexpected failure
    echo 'Network error: ' . $e->getMessage();
}
```

### Exception hierarchy

```
StrongmbException        — base: network errors, unexpected failures
  └── ApiException       — HTTP 4xx/5xx returned by the API
        └── AuthException — HTTP 401: invalid or missing API key
```

---

## Notes

- Always use a **unique `reference`** per transaction — duplicate references will be rejected.
- References must contain only `a-zA-Z0-9` characters. No spaces, dashes, or special characters.
- Call `$strongmb->data->plans()` or `$strongmb->airtime->plans()` to discover valid `productCode` values before purchasing.

---

## Links

- [Full API Documentation](https://developers.strongmb.ng)
- [Dashboard](https://dashboard.strongmb.ng)
- [Packagist](https://packagist.org/packages/strongmb/strongmb-php)
- [Support](mailto:developers@strongmb.ng)

## License

MIT
