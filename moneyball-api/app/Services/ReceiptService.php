<?php

namespace App\Services;

use ReceiptValidator\iTunes\Validator as iTunes;

/**
 * Class ReceiptService
 * @package App\Services
 */
class ReceiptService
{
    protected $client;

    /**
     * ReceiptService constructor.
     */
    public function __construct()
    {
        $endpoint = config('services.itunes.mode') == 'live'
            ? iTunes::ENDPOINT_PRODUCTION
            : iTunes::ENDPOINT_SANDBOX;

        $this->client = new iTunes($endpoint);
    }

    /**
     * @param string $receiptBase64Data
     *
     * @return array
     * @throws \Exception
     */
    public function getData(string $receiptBase64Data): array
    {
        $response = $this->client->setReceiptData($receiptBase64Data)->validate();

        if ($response->isValid()) {
            return $response->getReceipt();
        } else {
            throw new \Exception('Receipt is not valid. Result code: ' . $response->getResultCode());
        }
    }
}