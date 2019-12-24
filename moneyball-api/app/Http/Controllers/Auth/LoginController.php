<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\ValidateRegisterUserRequest;
use App\Http\Requests\ExistUsernameRequest;
use App\Repositories\UserRepository;
use App\Http\Requests\LoginUserRequest;
use App\Services\TwilioService;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;

/**
 * Class LoginController
 *
 * @package App\Http\Controllers\Auth
 */
class LoginController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;
    /**
     * @var TwilioService
     */
    protected $twilioService;
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * LoginController constructor.
     *
     * @param UserRepository $userRepository
     * @param UserService    $userService
     * @param TwilioService  $twilioService
     */
    public function __construct(UserRepository $userRepository, UserService $userService, TwilioService $twilioService)
    {
        $this->userRepository = $userRepository;
        $this->twilioService = $twilioService;
        $this->userService = $userService;
    }

    /**
     * Register user with a phone number or email (sends token to phone number/email)
     *
     * @param RegisterUserRequest $request
     *
     * @return mixed
     */
    public function register(RegisterUserRequest $request)
    {
        try {
            $data = $request->validated();
            $user = $this->userService->create($data);
//            return $user;
            if (!$user) {
                return response()->error(['success' => false], 'user.login.failed');
            }

            $this->userService->sendToken($user, $data);

            return $user
                ? response()->success(['success' => true], 'user.login.success')
                : response()->error(['success' => false], 'user.login.failed');
        } catch (\Exception $e) {
            return response()->error(['success' => false, 'error' => $e->getMessage()], 'user.create.failed');
        }
    }

    /**
     * Login user with Facebook, Instagram, phone, email or user id. Requires password if phone, email or user_id is used.
     *
     * @param LoginUserRequest $request
     *
     * @return mixed
     * @throws \Exception
     */
    public function login(LoginUserRequest $request)
    {
        $data = $request->validated();
        $user = $this->userService->findUser($data);

        // For social networks validation is not needed, so we will immediately log the user
        if ($request->input('facebook_id') || $request->input('instagram_id')) {
            try {
                if (!$user) {
                    $user = $this->userService->create($data);
                }

                if (!$user) {
                    return response()->error(['success' => false], 'user.login.failed');
                }

                $this->userService->loginWithSocialNetworks($user, $data);

                return response()->success($this->userService->login($user));
            } catch (\Exception $e) {
                return response()->error(['success' => false, 'error' => $e->getMessage()], 'user.create.failed');
            }
        }

        if (!$user) {
            return response()->error(['success' => false], 'user.login.failed');
        }

        if (!Hash::check($request->input('password'), $user->password)) {
            return response()->error(['success' => false], 'user.login.failed');
        }

        if (!$user->hasVerifiedEmail()) {
            $this->userService->sendToken($user, ["email" => $user->email]);
            return response()->error(['success' => false], 'user.verify.needed');
        }

        return response()->success($this->userService->login($user));
    }

    /**
     * Verification for user registration by phone number or email address(token validation)
     *
     * @param ValidateRegisterUserRequest $request
     *
     * @return mixed
     */
    public function validateRegister(ValidateRegisterUserRequest $request)
    {
        $user = $this->userService->findUser($request->validated());

        if ($user && $user->token === $request->input('token')) {
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            return response()->success($this->userService->login($user));
        } else {
            return response()->error(['success' => false], 'user.validate.failed');
        }
    }

    /**
     * Validate user name
     *
     * @param ValidateRegisterUserRequest $request
     *
     * @return mixed
     */
    public function existUsername(ExistUsernameRequest $request)
    {
        $user = $this->userService->findUser($request->validated());

        if ($user) {
            return response()->error(['success' => false], 'username already exists');
        } else {
            return response()->success(['success' => true]);
        }
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return response()->success([
            'access_token' => auth()->refresh(),
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60,
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->success([], 'Successfully logged out');
    }
}
