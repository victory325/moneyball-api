<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteLevelRequest;
use App\Http\Requests\ConnectOnesignalRequest;
use App\Http\Requests\DefaultListRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\PasswordChangeRequest;
use App\Http\Requests\SendTokenRequest;
use App\Http\Requests\EmailChangeRequest;

use App\Http\Resources\Collections\UserListCollection;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserController
 *
 * @package App\Http\Controllers
 */
class UserController
{
    /**
     * @var UserRepository
     */
    protected $userRepository;
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * UserController constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService, UserRepository $userRepository)
    {
        $this->userService = $userService;
        $this->userRepository = $userRepository;
    }

    /**
     * User List
     *
     * @param DefaultListRequest $request
     *
     * @return mixed
     */
    public function index(DefaultListRequest $request)
    {
        $users = $this->userService->list($request->validated());

        return response()->success(new UserListCollection($users));
    }

    /**
     * Update an user
     *
     * @param UpdateUserRequest $request
     *
     * @return mixed
     */
    public function update(UpdateUserRequest $request)
    {
        $user = $this->userService->update($request->validated());

        return $user
            ? response()->success(new UserResource($user), 'user.update.success')
            : response()->error(['user.update.failed'], 'user.update.failed');
    }

    /**
     * Change Password
     *
     * @param PasswordChangeRequest $request
     *
     * @return mixed
     */
    public function changePassword(PasswordChangeRequest $request)
    {
        $data = $request->validated();
        if (auth()->user()) {
            $user = $this->userService->findUser(['user_id' => auth()->user()->user_id]);

            if(!Hash::check($data['cur_password'], $user->password())) {
                return response()->error(['success' => false], 'Password is not correct.');
            }

            $this->userRepository->update($user->id, ['password' => Hash::make($data['new_password'])]);

            return $user
                ? response()->success(new UserResource($user), 'user.update.success')
                : response()->error(['user.update.failed'], 'user.update.failed');
        } else {
            $user = $this->userService->findUser(['email' => $data['email']]);
            if ($user) {
                if ($user->token != $data['token']) {
                    return response()->error(['user.update.failed'], 'Verification code is not correct.');
                } else {
                    $this->userRepository->update($user->id, [
                        'password' => Hash::make($data['new_password']),
                        'token' => ''
                    ]);
                    return response()->success(new UserResource($user), 'user.update.success');
                }
            } else {
                return response()->error(['user.update.failed'], 'No email address found.');
            }
        }
    }

    /**
     * Send Token
     *
     * @param SendTokenRequest $request
     *
     * @return mixed
     */
    public function sendToken(SendTokenRequest $request)
    {
        $data = $request->validated();
        $user = $this->userService->findUser(['email' => $data['email']]);
        
        if (auth()->user()) {
            if($user && $user->id != auth()->user()->id) {
                return response()->error(['success' => false], 'Email is already exist.');
            }

            $user = auth()->user();
            $user->email = $data['email'];
            $this->userService->sendTokenByEmail($user);

            return $user
                ? response()->success(new UserResource($user), 'user.update.success')
                : response()->error(['user.update.failed'], 'user.update.failed');
        } else {
            if ($user) {
                $this->userService->sendTokenByEmail($user);
                return response()->success(new UserResource($user), 'user.update.success');
            } else {
                return response()->error(['user.update.failed'], 'No email address found.');
            }
        }
    }

    
    /**
     * Change Email
     *
     * @param EmailChangeRequest $request
     *
     * @return mixed
     */
    public function changeEmail(EmailChangeRequest $request)
    {
        $data = $request->validated();

        if (auth()->user()) {
            $user = $this->userService->changeEmail($data);

            return $user
                ? response()->success(new UserResource($user), 'user.update.success')
                : response()->error(['user.update.failed'], 'Verification code is not correct.');
        } else {
            $user = $this->userService->findUser(['email' => $data['email']]);
            if ($user) {
                if ($user->token != $data['token']) {
                    return response()->error(['user.update.failed'], 'Verification code is not correct.');
                } else {
                    return response()->success(new UserResource($user), 'user.update.success');
                }
            } else {
                return response()->error(['user.update.failed'], 'No email address found.');
            }
        }
    }

    /**
     * Add funds to available amount
     *
     * @param completeLevel $request
     *
     * @return mixed
     */
    public function completeLevel(CompleteLevelRequest $request)
    {
        $user = $this->userService->completeLevel($request->validated());

        return $user
            ? response()->success(new UserResource($user), 'user.add-funds.success')
            : response()->error(['user.add-funds.failed'], 'user.add-funds.failed');
    }

    /**
     * Add funds to available amount
     *
     * @param completeLevel $request
     *
     * @return mixed
     */
    public function failLevel()
    {
        $user = $this->userService->failLevel();

        return $user
            ? response()->success(new UserResource($user), 'user.add-funds.success')
            : response()->error(['user.add-funds.failed'], 'user.add-funds.failed');
    }

    /**
     * View user
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function view()
    {
        return response()->success(new UserResource(auth()->user()));
    }

    /**
     * @return mixed
     */
    public function connectFacebook()
    {
        $url = $this->userService->getFacebookRedirectUrl();

        return response()->success(['redirect_url' => $url]);
    }

    /**
     * @return mixed
     */
    public function connectInstagram()
    {
        $url = $this->userService->getInstagramRedirectUrl();

        return response()->success(['redirect_url' => $url]);
    }

    /**
     * @return mixed
     */
    public function exchangeFacebook()
    {
        $result = $this->userService->exchangeFacebook();

        return isset($result['user'])
            ? response()->success($result, 'facebook.exchange.success')
            : response()->error($result, 'facebook.exchange.failed');
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function exchangeInstagram(Request $request)
    {
        $result = $this->userService->exchangeInstagram($request->code);

        return isset($result['user'])
            ? response()->success($result, 'instagram.exchange.success')
            : response()->error($result, 'instagram.exchange.failed');
    }

    /**
     * Leader Board
     *
     * @return mixed
     */
    public function leaderBoard()
    {
        $result = $this->userRepository->getLeaderBoard();

        return response()->success(UserResource::collection($result));
    }

    /**
     * @param ConnectOnesignalRequest $request
     */
    public function connectOnesignal(ConnectOnesignalRequest $request)
    {
        $this->userService->connectOnesignal($request->onesignal_id);
    }
}