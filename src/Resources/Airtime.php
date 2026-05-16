<?php

declare(strict_types=1);

namespace Strongmb\Resources;

use Strongmb\Response;

class Airtime extends Resource
{
    /**
     * Get available airtime providers and their product codes.
     */
    public function plans(): Response
    {
        return $this->client->get('products/airtime');
    }

    /**
     * Purchase airtime for any Nigerian network.
     *
     * @param string $phone       Recipient phone number (e.g. 0812345678)
     * @param string $productCode Product code from plans() (e.g. smb_mtn_vtu)
     * @param string $amount      Airtime face value in Naira as string (e.g. "100")
     * @param string $reference   Your unique reference. a-zA-Z0-9 only, no spaces or dashes.
     */
    public function purchase(string $phone, string $productCode, string $amount, string $reference): Response
    {
        return $this->client->post('purchases/airtime', [
            'phone'        => $phone,
            'product_code' => $productCode,
            'amount'       => $amount,
            'reference'    => $reference,
        ]);
    }
}
