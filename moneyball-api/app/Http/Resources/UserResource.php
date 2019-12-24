<?php

namespace App\Http\Resources;

use App\Http\Resources\Collections\PaymentDetailsListCollection;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\DateHelper;

/**
 * Class UserResource
 *
 * @package App\Http\Resources
 */
class UserResource extends JsonResource
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
        $rank_info = User::getRanking($this->total_earned_amount);
        /** @var $this User */
        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'email'               => $this->email,
            'user_id'             => $this->user_id,
            'phone'               => $this->phone,
            'level'               => $this->level,
            'lives'               => $this->lives,
            'premium'             => $this->premium,
            'avatar'              => $this->avatar, // ? url('storage' . DIRECTORY_SEPARATOR . $this->avatar) : null,
            'available_amount'    => $this->available_amount,
            'available_chips'     => $this->available_chips,
            'total_earned_amount' => $this->total_earned_amount,
            'is_seconds_added'    => $this->is_seconds_added,
            'onesignal_id'        => $this->onesignal_id,
            'paypal_email'        => $this->paypal_email,
            'ranking'             => $rank_info->ranking + 1,
            'total_users'         => $rank_info->total * 1,
            'created_at'          => $this->created_at ? DateHelper::dt($this->created_at) : null,
            'updated_at'          => $this->updated_at ? DateHelper::dt($this->updated_at) : null
        ];
    }
}
