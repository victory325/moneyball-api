<?php

namespace App\Repositories;

use App\Models\PaymentDetails;

/**
 * Class PaymentDetailsRepository
 * @package App\Repositories
 * @method PaymentDetails find(int $id, array $relations = [])
 */
class PaymentDetailsRepository extends BaseRepository
{
    /**
     * PaymentDetailsRepository constructor.
     *
     * @param PaymentDetails $paymentDetails
     */
    public function __construct(PaymentDetails $paymentDetails)
    {
        $this->model = $paymentDetails;
    }

    /**
     * @param array $data
     *
     * @return PaymentDetails|null
     */
    public function create(array $data): ?PaymentDetails
    {
        $paymentDetails = $this->model->newInstance();
        $paymentDetails->user_id = auth()->id();
        $paymentDetails->stripe_account_id = $data['stripe_account_id'] ?? null;
        $paymentDetails->stripe_card_id = $data['stripe_card_id'] ?? null;
        $paymentDetails->card_last4 = $data['card_last4'] ?? null;
        $paymentDetails->card_brand = $data['card_brand'] ?? null;
        $paymentDetails->paypal_account_id = $data['paypal_account_id'] ?? null;

        return $paymentDetails->save() ? $paymentDetails : null;
    }
}