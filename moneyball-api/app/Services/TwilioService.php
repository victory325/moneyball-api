<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Twilio\Rest\Client;
use App\Models\User;

/**
 * Class TwilioService
 * @package App\Services
 */
class TwilioService
{
    protected $client;
    protected $senderNumber;
    protected $userRepository;
    protected $tokenMessage = 'Your auth token is %s';

    /**
     * TwilioService constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->client = new Client(config('services.twilio.account_sid'), config('services.twilio.token'));
        $this->senderNumber = str_start(config('services.twilio.phone_number'), '+');
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $phoneNumber
     * @param string $message
     */
    public function sendMessage(string $phoneNumber, string $message)
    {
        $phoneNumber = str_start($phoneNumber, '+');

        $this->client->messages->create($phoneNumber,
            [
                'from' => $this->senderNumber,
                'body' => $message,
            ]
        );
    }

    /**
     * @param User $user
     *
     * @throws \Exception
     */
    public function sendToken(User $user)
    {
        try {
            $token = $this->createToken();

            $this->sendMessage($user->phone, $this->getTokenMessage($token));

            $this->userRepository->update($user->id, ['token' => $token]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @return int
     */
    public function createToken(): int
    {
        return mt_rand(1000, 9999);
    }

    /**
     * @param string $token
     *
     * @return string
     */
    public function getTokenMessage(string $token): string
    {
        return sprintf($this->tokenMessage, $token);
    }
}