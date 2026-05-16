<?php

declare(strict_types=1);

namespace Strongmb\Resources;

use Strongmb\Response;

class Account extends Resource
{
    /**
     * Get the authenticated user's profile.
     */
    public function user(): Response
    {
        return $this->client->get('user');
    }

    /**
     * Get all wallets and their balances.
     */
    public function wallets(): Response
    {
        return $this->client->get('wallets');
    }

    /**
     * Get recent transaction history.
     */
    public function transactions(): Response
    {
        return $this->client->get('transactions');
    }

    /**
     * Get a single transaction by its reference.
     */
    public function transaction(string $reference): Response
    {

        return $this->client->get('transactions/' . $reference);
    }
}
