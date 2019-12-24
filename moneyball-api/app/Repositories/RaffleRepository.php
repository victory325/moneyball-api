<?php

namespace App\Repositories;

use App\Models\Raffle;

/**
 * Class RaffleRepository
 * @package App\Repositories
 * @method Raffle find(int $id, array $relations = [])
 */
class RaffleRepository extends BaseRepository
{
    /**
     * RaffleRepository constructor.
     *
     * @param Raffle $raffle
     */
    public function __construct(Raffle $raffle)
    {
        $this->model = $raffle;
    }

    /**
     * @param array $data
     *
     * @return Raffle|null
     */
    public function create(array $data): ?Raffle
    {
        $raffle = $this->model->newInstance();
        $raffle->winner_id = $data['winner_id'];
        $raffle->amount = $data['amount'];
        $raffle->total_participants = $data['total_participants'];

        return $raffle->save() ? $raffle : null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getThisWeekRaffle()
    {
         return $this->newQuery()
            ->whereRaw('YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)')
            ->first();
    }
}