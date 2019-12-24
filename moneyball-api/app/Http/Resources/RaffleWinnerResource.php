<?php

namespace App\Http\Resources;

use App\Http\Resources\Collections\PaymentDetailsListCollection;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class RaffleWinnerResource
 *
 * @package App\Http\Resources
 */
class RaffleWinnerResource extends JsonResource
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
        /** @var $this User */
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'level' => $this->level,
        ];
    }
}
