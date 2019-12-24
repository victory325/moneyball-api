<?php

namespace App\Repositories;

use App\Models\Purchase;

/**
 * Class PurchaseRepository
 * @package App\Repositories
 * @method Purchase find(int $id, array $relations = [])
 */
class PurchaseRepository extends BaseRepository
{
    /**
     * PurchaseRepository constructor.
     *
     * @param Purchase $purchase
     */
    public function __construct(Purchase $purchase)
    {
        $this->model = $purchase;
    }

    /**
     * @param array $data
     *
     * @return Purchase|null
     */
    public function create(array $data): ?Purchase
    {
        $purchase = $this->model->newInstance();
        $purchase->user_id = auth()->id();
        $purchase->transaction_id = $data['transaction_id'];
        $purchase->product_id = $data['product_id'];
        $purchase->item_id = $data['item_id'];
        $purchase->purchase_date = $data['purchase_date'];

        return $purchase->save() ? $purchase : null;
    }
}