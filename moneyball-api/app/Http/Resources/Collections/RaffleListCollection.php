<?php

namespace App\Http\Resources\Collections;

use App\Http\Resources\RaffleWinnerResource;
use App\Models\Raffle;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Helpers\DateHelper;

/**
 * Class RaffleListCollection
 * @package App\Http\Resources\Collections
 */
class RaffleListCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($raffle) {
            /* @var Raffle $raffle */

            return [
                'id'                 => $raffle->id,
                'amount'             => $raffle->amount,
                'total_participants' => $raffle->total_participants,
                'created_at'         => $raffle->created_at ? DateHelper::dt($raffle->created_at) : null,
                'winner'             => $raffle->winner_id ? new RaffleWinnerResource($raffle->winner) : null,
            ];
        })->toArray();
    }
}