<?php

declare(strict_types=1);

namespace Strongmb\Resources;

use Strongmb\Response;

class Data extends Resource
{
    /**
     * Get available data plans and their product codes.
     */
    public function plans(): Response
    {
        return $this->client->get('products/internet');
    }

    /**
     * Purchase a data plan.
     *
     * @param string $phone       Recipient phone number (e.g. 0812345678)
     * @param string $productCode Product code from plans() (e.g. smb_mtn_sme_1gb_30days)
     * @param string $reference   Your unique reference. a-zA-Z0-9 only, no spaces or dashes.
     */
    public function purchase(string $phone, string $productCode, string $reference): Response
    {
        return $this->client->post('purchases/data', [
            'phone'        => $phone,
            'product_code' => $productCode,
            'reference'    => $reference,
        ]);
    }
}
