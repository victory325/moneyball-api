<?php

namespace App\Services;

use Stripe\ApiResource;
use Stripe\Account;
use Stripe\Stripe;
use Stripe\Transfer;
use Stripe\Payout;

/**
 * Class StripeApiService
 * @package App\Services
 */
class StripeApiService
{
    const CURRENCY = 'usd';
    const COUNTRY = 'US';

    /**
     * StripeService constructor.
     */
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * @param array       $data
     * @param string|null $email
     *
     * @return ApiResource
     */
    public function createStripeAccount(array $data, string $email = null): ApiResource
    {
        $birthday = strtotime($data['birthday']);

        $account = Account::create([
            'country'          => self::COUNTRY,
            'type'             => 'custom',
            'email'            => $email,
            'external_account' => $data['stripe_token'],
            'legal_entity'     => [
                'type'       => 'individual',
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'ssn_last_4' => $data['ssn_last_4'],
                'dob'        => [
                    'day'   => date('d', $birthday),
                    'month' => date('m', $birthday),
                    'year'  => date('Y', $birthday),
                ],
                'address'    => [
                    'city'        => $data['city'],
                    'line1'       => $data['address'],
                    'postal_code' => $data['postal_code'],
                    'state'       => $data['state'],
                ],
            ],
            'tos_acceptance'   => [
                'date' => $data['tos_acceptance_timestamp'],
                'ip'   => $data['tos_acceptance_ip'],
            ],
        ]);

        return $account;
    }

    /**
     * @param string $connectedAccountId
     * @param float  $amount
     *
     * @return ApiResource
     */
    public function createTransfer(string $connectedAccountId, float $amount): ApiResource
    {
        return Transfer::create([
            'amount'      => $amount,
            'currency'    => self::CURRENCY,
            'destination' => $connectedAccountId,
        ]);
    }

    /**
     * @param string $stripeCardId
     * @param string $connectedAccountId
     * @param float  $amount
     *
     * @return ApiResource
     */
    public function createPayout(string $stripeCardId, string $connectedAccountId, float $amount): ApiResource
    {
        return Payout::create([
            'amount'      => $amount,
            'currency'    => self::CURRENCY,
            'source_type' => 'card',
            'destination' => $stripeCardId,
        ], ['stripe_account' => $connectedAccountId]);
    }
}