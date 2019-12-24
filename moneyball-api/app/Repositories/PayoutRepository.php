<?php

namespace App\Repositories;

use App\Models\Payout;

/**
 * Class PayoutRepository
 * @package App\Repositories
 * @method Payout find(int $id, array $relations = [])
 */
class PayoutRepository extends BaseRepository
{
    /**
     * PayoutRepository constructor.
     *
     * @param Payout $payout
     */
    public function __construct(Payout $payout)
    {
        $this->model = $payout;
    }

    /**
     * @param array $data
     *
     * @return Payout|null
     */
    public function create(array $data): ?Payout
    {
        $payout = $this->model->newInstance();
        $payout->user_id = auth()->id();
        $payout->payment_details_id = $data['payment_details_id'];
        $payout->stripe_transfer_id = $data['stripe_transfer_id'] ?? null;
        $payout->stripe_payout_id = $data['stripe_payout_id'] ?? null;
        $payout->paypal_batch_id = $data['paypal_batch_id'] ?? null;
        $payout->amount = $data['amount'];

        return $payout->save() ? $payout : null;
    }
}