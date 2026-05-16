<?php

declare(strict_types=1);

namespace Strongmb;

class Response
{
    public function __construct(
        private readonly array $raw,
        private readonly int $httpStatus,
    ) {
    }

    /**
     * Whether the API returned status: true.
     */
    public function ok(): bool
    {
        return $this->raw['status'] ?? false;
    }

    /**
     * Human-readable message from the API.
     */
    public function message(): string
    {
        return $this->raw['message'] ?? '';
    }

    /**
     * The data payload. Key varies per endpoint.
     */
    public function data(): array|null
    {
        return $this->raw['data'] ?? null;
    }

    /**
     * Machine-readable code from metadata (e.g. TRANSACTION_SUCCESSFUL).
     */
    public function code(): string
    {
        return $this->raw['metadata']['code'] ?? '';
    }

    /**
     * Trace ID for support queries.
     */
    public function traceId(): string
    {
        return $this->raw['metadata']['trace_id'] ?? '';
    }

    /**
     * True when a purchase was accepted and is still being processed.
     * Poll transaction by reference to get the final status.
     */
    public function processing(): bool
    {
        return $this->code() === 'ERR_TRANSACTION_PROCESSING';
    }

    /**
     * True when a purchase completed successfully.
     */
    public function successful(): bool
    {
        return $this->code() === 'TRANSACTION_SUCCESSFUL';
    }

    /**
     * True when a purchase was attempted but failed.
     * Wallet may have been debited — check transaction status and contact support if needed.
     */
    public function failed(): bool
    {
        return $this->code() === 'ERR_TRANSACTION_FAILED';
    }

    public function httpStatus(): int
    {
        return $this->httpStatus;
    }

    public function toArray(): array
    {
        return $this->raw;
    }
}
