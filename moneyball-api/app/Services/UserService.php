<?php

namespace App\Services;

use App\Helpers\SettingHelper;
use App\Repositories\TransferRepository;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Notifications\VerifyCode;
use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Repositories\TransactionRepository;
/**
 * Class UserService
 *
 * @package App\Services
 */
class UserService
{
    protected $userRepository;
    protected $transferRepository;
    protected $transactionRepository;
    protected $twilioService;
    protected $facebookService;
    protected $instagramService;

    /**
     * UserService constructor.
     *
     * @param UserRepository     $userRepository
     * @param TransferRepository $transferRepository
     * @param TwilioService      $twilioService
     * @param FacebookService    $facebookService
     * @param InstagramService   $instagramService
     */
    public function __construct(
        UserRepository $userRepository,
        TransferRepository $transferRepository,
        TwilioService $twilioService,
        FacebookService $facebookService,
        InstagramService $instagramService,
        TransactionRepository $transactionRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->transferRepository = $transferRepository;
        $this->twilioService = $twilioService;
        $this->facebookService = $facebookService;
        $this->instagramService = $instagramService;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * @param array $data
     *
     * @return User|bool
     * @throws \Exception
     */
    public function create(array $data): ?User
    {
        try {
            \DB::beginTransaction();

            if (!empty($data['facebook_id'])) {
                $user = $this->registerWithFacebook($data);
            } elseif (!empty($data['instagram_id'])) {
                $user = $this->registerWithInstagram($data);
            } else {
                $user = $this->userRepository->create($data);
            }

            \DB::commit();

            return $user;
        } catch (\Exception $e) {
            \DB::rollBack();

            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function login(User $user): array
    {
        $accessToken = auth()->login($user);
        $this->userRepository->update($user->id, ['token' => null]);

        return [
            'token' => [
                'access_token' => $accessToken,
                'token_type'   => 'bearer',
                'expires_in'   => auth()->factory()->getTTL() * 60,
            ],
            'user'  => new UserResource($user),
        ];
    }

    /**
     * @param array $data
     *
     * @return User|null
     */
    public function findUser(array $data): ?User
    {
        if (!empty($data['phone'])) {
            return $this->userRepository->findByPhone($data['phone']);
        } elseif (!empty($data['instagram_id'])) {
            return $this->userRepository->findByInstagram($data['instagram_id']);
        } elseif (!empty($data['facebook_id'])) {
            return $this->userRepository->findByFacebook($data['facebook_id']);
        } elseif (!empty($data['user_id'])) {
            $user = $this->userRepository->findByUserId($data['user_id']);
            if ($user == null) {
                $user = $this->userRepository->findByEmail($data['user_id']);
            }
            return $user;
        } else {
            $user = $this->userRepository->findByUserId($data['email']);
            if ($user == null) {
                $user = $this->userRepository->findByEmail($data['email']);
            }
            return $user;
        }
    }

    /**
     * @param User  $user
     * @param array $data
     *
     * @return bool
     * @throws \Exception
     */
    public function sendToken(User $user, array $data): bool
    {
        if (!empty($data['phone'])) {
            return $this->sendTokenByPhone($user);
        } else {
            return $this->sendTokenByEmail($user);
        }
    }

    /**
     * @param User  $user
     * @param array $data
     *
     * @return bool
     * @throws \Exception
     */
    public function loginWithSocialNetworks(User $user, array $data): bool
    {
        if (!empty($data['instagram_id'])) {
            return $this->loginWithInstagram($user, $data['access_token']);
        } elseif (!empty($data['facebook_id'])) {
            return $this->loginWithFacebook($user, $data['access_token']);
        } else {
            throw new \Exception('Invalid parameters');
        }
    }

    /**
     * @param User $user
     *
     * @return bool
     * @throws \Exception
     */
    public function sendTokenByPhone(User $user): bool
    {
        $this->twilioService->sendToken($user);

        return true;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function sendTokenByEmail(User $user): bool
    {
        $token = $this->twilioService->createToken();

        // send email with verification code
        $user->notify(new VerifyCode($token));

        $this->userRepository->update($user->id, ['token' => $token]);

        return true;
    }

    /**
     * @param User   $user
     * @param string $accessToken
     *
     * @return bool
     * @throws \Exception
     */
    public function loginWithInstagram(User $user, string $accessToken): bool
    {
        $result = $this->instagramService->getUser($accessToken);

        if (isset($result['user']) && $user) {
            return true;
        } else {
            throw new \Exception($result['error']);
        }
    }

    /**
     * @param User   $user
     * @param string $accessToken
     *
     * @return bool
     * @throws \Exception
     */
    public function loginWithFacebook(User $user, string $accessToken): bool
    {
        $result = $this->facebookService->getUser($user->facebook_id, $accessToken);

        if (!isset($result['error']) && $user) {
            return true;
        } else {
            throw new \Exception($result['error']);
        }
    }

    /**
     * @param array $data
     *
     * @return User
     */
    public function update(array $data): User
    {
        $user = auth()->user();

        // if (!empty($data['avatar'])) {
        //     $data['avatar'] = $this->saveAvatar($data['avatar']);

        //     if ($user->avatar) {
        //         Storage::disk('public')->delete($user->avatar);
        //     }
        // }

        $raffleMinLevel = SettingHelper::raffleMinLevel();
        if ($raffleMinLevel && !empty($data['level']) && $data['level'] == $raffleMinLevel) {
            $data['raffle_level_reached_at'] = Carbon::now();
        }

        $this->userRepository->update($user->id, $data);

        return $user->fresh();
    }

    /**
     * @param array $data
     *
     * @return User
     */
    public function changeEmail(array $data): ?User
    {
        $user = auth()->user();

        if ($user->token != $data['token']) {
            return null;
        }

        $user->email = $data['email'];
        $user->token = null;
        $user->save();

        return $user->fresh();
    }

    /**
     * @param array $data
     *
     * @return User
     */
    public function completeLevel(array $data): User
    {
        $user = auth()->user();

        if ($data["chip"] > 0) {
            // $user->available_amount = $user->available_amount + $data['cash'];

            $this->transactionRepository->create([
                'summary'               => "Chip Reward For Level " . $user->level,
                'amount'                => $data["chip"],
                'paypal_email'          => "reward",
                'paypal_transaction_id' => ""
            ]);
        }

        $user->available_chips = $user->available_chips + $data["chip"];
        $user->total_earned_amount = $user->total_earned_amount + $data["chip"];
        $user->level = $user->level + 1;
        $user->lives = $user->lives + ($data["lives"] ?? 0);
        $user->save();

        return $user->fresh();
    }

    /**
     * @param array $data
     *
     * @return User
     */
    public function failLevel(): User
    {
        $user = auth()->user();

        if ($user->lives > 0) {
            $user->lives = $user->lives - 1;
        } else {
            $user->level = 1;
        }

        $user->save();

        return $user->fresh();
    }

    /**
     * @param array $params
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->userRepository->list($params);
    }

    /**
     * @return string
     */
    public function getFacebookRedirectUrl(): string
    {
        return $this->facebookService->getRedirectUrl();
    }

    /**
     * @return array
     */
    public function exchangeFacebook(): array
    {
        return $this->facebookService->exchange();
    }

    /**
     * @return string
     */
    public function getInstagramRedirectUrl(): string
    {
        return $this->instagramService->getRedirectUrl();
    }

    /**
     * @param string $code
     *
     * @return array
     */
    public function exchangeInstagram(string $code): array
    {
        return $this->instagramService->exchange($code);
    }

    /**
     * @param string $onesignalId
     */
    public function connectOnesignal(string $onesignalId): void
    {
        $this->userRepository->update(auth()->id(), [
            'onesignal_id' => $onesignalId,
        ]);
    }

    /**
     * @param $data
     *
     * @return User|null
     * @throws \Exception
     */
    protected function registerWithFacebook(array $data): ?User
    {
        $result = $this->facebookService->getUser($data['facebook_id'], $data['access_token']);

        if (!isset($result['error'])) {
            $user = $this->userRepository->create([
                'facebook_id'       => $data['facebook_id'],
                'token'             => $data['access_token'],
                'email'             => $result['email'] ?? null,
                'name'              => $result['name'],
                'email_verified_at' => Carbon::now(),
                'avatar'            => !empty($result['avatar']) ? $this->saveAvatar($result['avatar']) : null,
            ]);

            return $user->fresh();
        } else {
            throw new \Exception($result['error']);
        }
    }

    /**
     * @param $data
     *
     * @return User|null
     * @throws \Exception
     */
    protected function registerWithInstagram(array $data): ?User
    {
        $result = $this->instagramService->getUser($data['access_token']);

        if (isset($result['user'])) {
            return $this->userRepository->create([
                'instagram_id'      => $data['instagram_id'],
                'token'             => $data['access_token'],
                'email'             => $result['user']['email'] ?? null,
                'name'              => $result['user']['name'],
                'email_verified_at' => Carbon::now(),
                'avatar'            => !empty($result['user']['avatar'])
                    ? $this->saveAvatar($result['user']['avatar'])
                    : null,
            ]);
        } else {
            throw new \Exception($result['error']);
        }
    }

    /**
     * @param $avatar
     *
     * @return string
     */
    protected function saveAvatar($avatar): string
    {
        if ($avatar instanceof File) {
            $content = File::get($avatar);
            $extension = $avatar->getClientOriginalExtension();
        } else { // Get avatar from url
            $content = file_get_contents($avatar);
            $extension = 'jpg';
        }

        $filename = 'users' . DIRECTORY_SEPARATOR . str_random(30) . '.' . $extension;

        Storage::disk('public')->put($filename, $content);

        return $filename;
    }
}
