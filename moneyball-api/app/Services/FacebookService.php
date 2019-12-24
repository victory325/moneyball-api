<?php

namespace App\Services;

use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;

/**
 * Class FacebookService
 * @package App\Services
 */
class FacebookService
{
    protected $client;
    protected $redirectUrl;

    /**
     * FacebookService constructor.
     *
     * @param LaravelFacebookSdk $fb
     */
    public function __construct(LaravelFacebookSdk $fb)
    {
        $this->client = $fb;

        if (\Route::has('facebook.exchange')) {
            $this->redirectUrl = route('facebook.exchange');
        }
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        $url = $this->client->getRedirectLoginHelper()
            ->getLoginUrl($this->redirectUrl);

        return $url;
    }

    /**
     * @return array
     */
    public function exchange(): array
    {
        try {
            $token = $this->client->getAccessTokenFromRedirect($this->redirectUrl);
            $this->client->setDefaultAccessToken($token);

            $response = $this->client->get('/me');
            $data = $response->getDecodedBody();

            return [
                'user'         => $data,
                'access_token' => $token->getValue(),
            ];

        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * @param string $id
     * @param string $token
     *
     * @return array
     */
    public function getUser(string $id, string $token): array
    {
        $this->client->setDefaultAccessToken($token);
        $response = $this->client->get('/' . $id . '?fields=email,name');

        $data = $response->getDecodedBody();

        $response = $this->client->get('/' . $id . '/picture?redirect=false&width=150&height=150');
        $imageData = $response->getDecodedBody();

        if (isset($imageData['data']['url'])) {
            $data['avatar'] = $imageData['data']['url'];
        }

        return $data;
    }
}