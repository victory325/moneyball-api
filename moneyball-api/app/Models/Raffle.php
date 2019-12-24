<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int    $id
 * @property int    $winner_id
 * @property int    $amount
 * @property int    $total_participants
 * @property string $winnerName
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User   $winner
 */
class Raffle extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'winner_id', 'amount', 'total_participants',
    ];

    /**
     * @return BelongsTo
     */
    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return null|string
     */
    public function getWinnerNameAttribute(): ?string
    {
        return $this->winner ? ($this->winner->name ?? 'anonymous') : null;
    }
}
