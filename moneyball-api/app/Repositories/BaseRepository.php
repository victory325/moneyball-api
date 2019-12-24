<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class BaseRepository
 *
 * @package App\Repositories
 */
abstract class BaseRepository
{
    /** @var int */
    protected $perPage = 20;
    /** @var Model */
    protected $model;
    /** @var array */
    protected $filterWhereColumns = [];

    /**
     * @param string $orderBy
     *
     * @param string $direction
     *
     * @return Collection|static[]
     */
    public function all(string $orderBy = null, string $direction = 'asc'): Collection
    {
        return $orderBy
            ? $this->model->orderBy($orderBy, $direction)->get()
            : $this->model->all();
    }

    /**
     * Return paginated results of the given model from the database
     *
     * @param array $params
     * @param array $relationships
     *
     * @return LengthAwarePaginator
     */
    public function list(array $params, array $relationships = [])
    {
        $query = $this->model->newQuery();

        return $this->processList($query, $params, $relationships);
    }

    /**
     * Return paginated results of the given model from the database for current user
     *
     * @param array $params
     * @param array $relationships
     *
     * @return LengthAwarePaginator
     */
    public function userRelatedList(array $params, array $relationships = [])
    {
        $query = $this->model->newQuery();
        $query->where('user_id', auth()->id());

        return $this->processList($query, $params, $relationships);
    }

    /**
     * @param Builder $query
     * @param array   $params
     * @param array   $relationships
     *
     * @return LengthAwarePaginator
     * @throws \Exception
     */
    protected function processList(Builder $query, array $params = [], array $relationships = [])
    {
        if ($relationships) {
            $query->with($relationships);
        }

        $query = $this->applyFilters($query, $params);

        $orderBy = $params['order_by'] ?? 'id';
        $order = $params['order'] ?? 'asc';
        $perPage = $params['per_page'] ?? $this->perPage;

        return $query
            ->orderBy($orderBy, $order)
            ->paginate($perPage);
    }

    /**
     * Return a model by ID from the database. If relationships are provided, eager load those relationships.
     *
     * @param int   $id
     * @param array $relationships
     *
     * @return Model|null
     */
    public function find(int $id, array $relationships = [])
    {
        return $this->model
            ->with($relationships)
            ->find($id);
    }

    /**
     * @param string $column
     * @param string $value
     * @param array  $relationships
     *
     * @return Builder[]|Collection|Model[]|\Illuminate\Support\Collection
     */
    public function findBy(string $column, string $value, array $relationships = [])
    {
        return $this->model
            ->with($relationships)
            ->where($column, $value)
            ->get();
    }

    /**
     * @param array $where
     * @param array $relationships
     *
     * @return Builder[]|Collection|Model[]|\Illuminate\Support\Collection
     */
    public function findWhere(array $where, array $relationships = [])
    {
        return $this->model
            ->with($relationships)
            ->where($where)
            ->get();
    }

    /**
     * Create a new Eloquent Query Builder instance
     *
     * @return Builder
     */
    public function newQuery(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Update model
     *
     * @param int   $id
     * @param array $data
     *
     * @return bool
     */
    public function update(int $id, array $data)
    {
        return $this->find($id)->update($data);
    }

    /**
     * @param array $ids
     *
     * @return bool|null
     * @throws \Exception
     */
    public function batchDelete(array $ids)
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    /**
     * @param int $id
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete(int $id)
    {
        return $this->find($id)->delete();
    }

    /**
     * @param Model $model
     *
     * @return bool|null
     * @throws \Exception
     */
    public function deleteModel(Model $model)
    {
        return $model->delete();
    }

    /**
     * Delete all models
     *
     * @param array $where
     *
     * @return bool|null
     *
     */
    public function deleteAll(array $where = []): ?bool
    {
        $query = $this->newQuery();

        if ($where) {
            $query->where($where);
        }

        return $query
            ->delete();
    }

    /**
     * @param Builder $query
     * @param array   $params
     *
     * @return Builder
     * @throws \Exception
     */
    protected function applyFilters(Builder $query, array $params): Builder
    {
        if (!isset($params['filter']) || !$params['filter']['value']) {
            return $query;
        }

        $column = $params['filter']['column'];
        $value = $params['filter']['value'];

        if (!isset($this->filterWhereColumns[$column])) {
            throw new BadRequestHttpException($this->getFilterErrorMessage());
        }

        $columnConditions = $this->filterWhereColumns[$column] ?? null;

        if ($columnConditions) {
            $query->where(function ($query) use ($columnConditions, $value) {
                /* @var Builder $query */
                $query->where($columnConditions, 'like', '%' . $value . '%');
            });
        }

        return $query;
    }

    /**
     * @return string
     */
    protected function getFilterErrorMessage(): string
    {
        $format = 'Invalid parameter. Accepted parameters: %s';
        $params = implode(', ', array_keys($this->filterWhereColumns));

        return sprintf($format, $params);
    }
}