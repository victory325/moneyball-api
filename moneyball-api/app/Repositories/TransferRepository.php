<?php

namespace App\Repositories;

use App\Models\Transfer;

/**
 * Class TransferRepository
 * @package App\Repositories
 * @method Transfer find(int $id, array $relations = [])
 */
class TransferRepository extends BaseRepository
{
    /**
     * TransferRepository constructor.
     *
     * @param Transfer $transfer
     */
    public function __construct(Transfer $transfer)
    {
        $this->model = $transfer;
    }

    /**
     * @param array $data
     * @param null  $userId
     *
     * @return Transfer|null
     */
    public function create(array $data, $userId = null): ?Transfer
    {
        if (!$userId) {
            $userId = auth()->id();
        }

        $transfer = $this->model->newInstance();
        $transfer->user_id = $userId;
        $transfer->amount = $data['amount'];

        return $transfer->save() ? $transfer : null;
    }
}