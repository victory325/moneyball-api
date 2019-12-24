<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserRepository
 *
 * @package App\Repositories
 * @method User find(int $id, array $relations = [])
 */
class UserRepository extends BaseRepository
{
    /** @var array */
    protected $filterWhereColumns = [
        'name'  => 'user_id',
        'phone' => 'phone',
        'email' => 'email',
    ];

    /**
     * UserRepository constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * @param array $data
     *
     * @return User|null
     */
    public function create(array $data): ?User
    {
        $user = $this->model->newInstance();
        $user->name = $data['name'] ?? null;
        $user->phone = $data['phone'] ?? null;
        $user->user_id = $data['user_id'] ?? uniqid();
        $user->password = Hash::make($data['password'] ?? '');
        $user->email = $data['email'] ?? null;
        $user->facebook_id = $data['facebook_id'] ?? null;
        $user->instagram_id = $data['instagram_id'] ?? null;
        $user->token = $data['token'] ?? null;
        $user->avatar = $data['avatar'] ?? null;

        return $user->save() ? $user : null;
    }

    /**
     * @param $userId
     *
     * @return User|null
     */
    public function findById($userId): ?User
    {
        return $this->findWhere(['id' => $userId])->first();
    }

    /**
     * @param string $phone
     *
     * @return User|null
     */
    public function findByPhone(string $phone): ?User
    {
        return $this->findWhere(['phone' => $phone])->first();
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->findWhere(['email' => $email])->first();
    }

    /**
     * @param string $userId
     *
     * @return User|null
     */
    public function findByUserId(string $userId): ?User
    {
        return $this->findWhere(['user_id' => $userId])->first();
    }

    /**
     * @param string $id
     *
     * @return User|null
     */
    public function findByFacebook(string $id): ?User
    {
        return $this->findWhere(['facebook_id' => $id])->first();
    }

    /**
     * @param string $id
     *
     * @return User|null
     */
    public function findByInstagram(string $id): ?User
    {
        return $this->findWhere(['instagram_id' => $id])->first();
    }

    /**
     * @return Collection
     */
    public function getLeaderBoard(): Collection
    {
        return $this->newQuery()
            ->orderBy('total_earned_amount', 'desc')
            ->limit(30)
            ->get();
    }

    /**
     * @param int $level
     *
     * @return Collection
     */
    public function getRaffleParticipants(int $level): Collection
    {
        return $this->newQuery()
            ->where('level', '>=', $level)
            ->where('premium', 1)
            ->whereRaw('YEARWEEK(raffle_level_reached_at, 1) = YEARWEEK(CURDATE(), 1)')
            ->get();
    }
}