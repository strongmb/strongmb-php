<?php

declare(strict_types=1);

namespace Strongmb;

use Strongmb\Resources\Account;
use Strongmb\Resources\Airtime;
use Strongmb\Resources\Data;

class Strongmb
{
    public readonly Account $account;
    public readonly Airtime $airtime;
    public readonly Data $data;

    public function __construct(string $apiKey, bool $sandbox = false)
    {
        $client = new Client($apiKey, $sandbox);

        $this->account = new Account($client);
        $this->airtime = new Airtime($client);
        $this->data    = new Data($client);
    }
}
