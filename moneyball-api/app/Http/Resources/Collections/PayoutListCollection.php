<?php

namespace App\Http\Resources\Collections;

use App\Models\Payout;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Helpers\DateHelper;

/**
 * Class PayoutListCollection
 * @package App\Http\Resources\Collections
 */
class PayoutListCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($payout) {
            /* @var Payout $payout */
            return [
                'id'         => $payout->id,
                'amount'     => $payout->amount,
                'card_last4' => $payout->paymentDetails->card_last4,
                'card_brand' => $payout->paymentDetails->card_brand,
                'created_at' => DateHelper::dt($payout->created_at),
            ];
        })->toArray();
    }
}