<?php

namespace App\Http\Resources\Collections;

use App\Models\PaymentDetails;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Helpers\DateHelper;

/**
 * Class PaymentDetailsListCollection
 * @package App\Http\Resources\Collections
 */
class PaymentDetailsListCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($payout) {
            /* @var PaymentDetails $payout */
            return [
                'id'                => $payout->id,
                'stripe_account_id' => $payout->stripe_account_id,
                'stripe_card_id'    => $payout->stripe_card_id,
                'card_last4'        => $payout->card_last4,
                'card_brand'        => $payout->card_brand,
                'paypal_account_id' => $payout->paypal_account_id,
                'created_at'        => DateHelper::dt($payout->created_at),
                'updated_at'        => DateHelper::dt($payout->updated_at),
            ];
        })->toArray();
    }
}