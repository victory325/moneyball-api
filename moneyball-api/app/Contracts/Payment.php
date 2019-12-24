<?php

namespace App\Contracts;

use App\Models\PaymentDetails;
use App\Models\Payout;

/**
 * Interface Payment
 * @package App\Services
 */
interface Payment
{
    /**
     * @param array $data
     *
     * @return PaymentDetails|null
     */
    public function addPaymentDetails(array $data): ?PaymentDetails;

    /**
     * @param array $data
     *
     * @return PaymentDetails|null
     */
    public function getPaymentDetails(array $data): ?PaymentDetails;

    /**
     * @param PaymentDetails $paymentDetails
     * @param float          $amount
     *
     * @return Payout|null
     */
    public function payout(PaymentDetails $paymentDetails, float $amount): ?Payout;
}