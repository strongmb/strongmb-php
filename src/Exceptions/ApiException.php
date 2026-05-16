<?php

declare(strict_types=1);

namespace Strongmb\Exceptions;

class ApiException extends StrongmbException
{
    public function __construct(
        string $message,
        private readonly string $apiCode,
        private readonly int $httpStatus,
        private readonly array $body = [],
    ) {
        parent::__construct($message, $httpStatus);
    }

    public function getApiCode(): string
    {
        return $this->apiCode;
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getTraceId(): string
    {
        return $this->body['metadata']['trace_id'] ?? '';
    }
}
