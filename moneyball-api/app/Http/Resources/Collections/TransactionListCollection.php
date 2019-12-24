<?php

namespace App\Http\Resources\Collections;

use App\Models\Transaction;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Helpers\DateHelper;

/**
 * Class TransactionListCollection
 * @package App\Http\Resources\Collections
 */
class TransactionListCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($transaction) {
            /* @var Payout $payout */
            return [
                'id'                    => $transaction->id,
                'amount'                => $transaction->amount,
                'summary'               => $transaction->summary,
                'paypal_email'          => $transaction->paypal_email,
                'paypal_transaction_id' => $transaction->paypal_transaction_id,
                'date'                  => $transaction->created_at ? DateHelper::dt($transaction->created_at) : null
            ];
        })->toArray();
    }
}