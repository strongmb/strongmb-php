<?php

declare(strict_types=1);

namespace Strongmb\Resources;

use Strongmb\Client;

abstract class Resource
{
    public function __construct(protected readonly Client $client)
    {
    }
}
