<?php

namespace App\Services;

use MetzWeb\Instagram\Instagram;

/**
 * Class InstagramService
 * @package App\Services
 */
class InstagramService
{
    protected $client;

    /**
     * InstagramService constructor.
     */
    public function __construct()
    {
        $credentials = [
            'apiKey'      => config('services.instagram.client_id'),
            'apiSecret'   => config('services.instagram.client_secret'),
            'apiCallback' => '',
        ];

        if (\Route::has('instagram.exchange')) {
            $credentials['apiCallback'] = route('instagram.exchange');
        }

        $this->client = new Instagram($credentials);
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->client->getLoginUrl();
    }

    /**
     * @param string $code
     *
     * @return mixed
     */
    public function exchange(string $code)
    {
        $data = $this->client->getOAuthToken($code);

        return $data;
    }

    /**
     * @param string $token
     *
     * @return array
     */
    public function getUser(string $token): array
    {
        $this->client->setAccessToken($token);
        $data = $this->client->getUser();

        if ($data->meta->code == 200) {
            return ['user' => [
                'name'   => $data->data->username,
                'avatar' => $data->data->profile_picture,
            ]];
        } else {
            return ['error' => $data->meta->error_message];
        }
    }
}