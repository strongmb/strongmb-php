<?php

declare(strict_types=1);

namespace Strongmb\Exceptions;

class AuthException extends ApiException
{
    public function __construct(string $message = 'Unauthorized. Invalid or missing API key.', array $body = [])
    {
        parent::__construct($message, 'ERR_UNAUTHENTICATED', 401, $body);
    }
}
