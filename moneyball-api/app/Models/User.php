<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Auth\MustVerifyEmail;

/**
 * @property int            $id
 * @property string         $name
 * @property string         $email
 * @property string         $phone
 * @property string         $user_id
 * @property string         $password
 * @property string         $instagram_id
 * @property string         $facebook_id
 * @property string         $token
 * @property string         $avatar
 * @property Carbon         $email_verified_at
 * @property Carbon         $created_at
 * @property Carbon         $updated_at
 * @property Carbon         $raffle_level_reached_at
 * @property int            $level
 * @property int            $lives
 * @property float          $available_amount
 * @property float          $total_earned_amount
 * @property bool           $premium
 * @property bool           $is_seconds_added
 * @property string         $onesignal_id
 * @property Purchase       $purchases
 * @property PaymentDetails $paymentDetails
 * @property Payout         $payouts
 * @property Transfer       $transfers
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable, MustVerifyEmail;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'user_id',
        'password',
        'instagram_id',
        'facebook_id',
        'token',
        'email_verified_at',
        'level',
        'lives',
        'avatar',
        'premium',
        'available_amount',
        'total_earned_amount',
        'is_seconds_added',
        'onesignal_id',
        'raffle_level_reached_at',
        'paypal_code',
        'paypal_email',
        'available_chips'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @return HasMany
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * @return HasMany
     */
    public function paymentDetails(): HasMany
    {
        return $this->hasMany(PaymentDetails::class);
    }

    /**
     * @return HasMany
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    /**
     * @return HasMany
     */
    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class);
    }

    /**
     * @return null|string
     */
    public function routeNotificationForOneSignal(): ?string
    {
        return $this->onesignal_id;
    }

    public function password(): string {
        return $this->password;
    }

    public static function getRanking($total_earned_amount) {
        $query = "SELECT SUM(IF(total_earned_amount>'$total_earned_amount', 1, 0)) AS ranking, SUM(1) as total FROM users";
        $result = \DB::select($query);
        return $result[0];
    }
}
