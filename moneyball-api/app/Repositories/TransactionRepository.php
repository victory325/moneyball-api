<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Support\Collection;

/**
 * Class TransactionRepository
 * @package App\Repositories
 * @method Transaction find(int $id, array $relations = [])
 */
class TransactionRepository extends BaseRepository
{
    /**
     * TransactionRepository constructor.
     *
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->model = $transaction;
    }

    /**
     * @param array $data
     *
     * @return Transaction|null
     */
    public function create(array $data, $user_id = null): ?Transaction
    {
        $item = $this->model->newInstance();
        $item->user_id = $data['user_id'] ?? auth()->id();
        $item->amount = $data['amount'] ?? null;
        $item->summary = $data['summary'] ?? '';
        $item->paypal_email = $data['paypal_email'] ?? '';
        $item->paypal_transaction_id = $data['paypal_transaction_id'] ?? '';

        return $item->save() ? $item : null;
    }

    public function myTransactions(array $params): Collection
    {
        $query = $this->newQuery()
            ->where('user_id', auth()->user()->id);

        if (isset($params['fromDate'])) {
            $query = $query->where('created_at', '>=', $params['fromDate']);
        }

        if (isset($params['toDate'])) {
            $query = $query->where('created_at', '<=', $params['toDate'] . " 23:59:59");
        }

        if (isset($params['filter'])) {
            if ($params['filter'] == 1) {
                $query = $query->whereRaw("summary LIKE '%in-app%'");
            } else if ($params['filter'] == 2) {
                $query = $query->whereRaw("summary LIKE '%reward%'");
            } else if ($params['filter'] == 3) {
                $query = $query->whereRaw("summary LIKE '%prize%'");
            } else if ($params['filter'] == 4) {
                $query = $query->whereRaw("summary LIKE '%redeem%'");
            }
        }

        return $query->orderBy('created_at', 'desc')
                    ->get();
    }
}