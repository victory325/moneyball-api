<?php

namespace App\Http\Resources;

use App\Models\Raffle;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\DateHelper;

/**
 * Class RaffleResource
 * @package App\Http\Resources
 */
class RaffleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        /* @var Raffle $this */

        return [
            'id'                 => $this->id,
            'amount'             => $this->amount,
            'total_participants' => $this->total_participants,
            'created_at'         => $this->created_at ? DateHelper::dt($this->created_at) : null,
            'winner'             => $this->winner_id ? new RaffleWinnerResource($this->winner) : null,
        ];
    }
}
