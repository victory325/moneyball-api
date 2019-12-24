<?php

namespace App\Services;

use App\Repositories\PaymentDetailsRepository;
use App\Repositories\PayoutRepository;
use App\Models\PaymentDetails;
use App\Models\Payout;
use App\Contracts\Payment;

/**
 * Class StripeService
 * @package App\Services
 */
class StripeService implements Payment
{
    protected $payoutRepository;
    protected $paymentDetailsRepository;
    protected $service;

    /**
     * StripeService constructor.
     *
     * @param PayoutRepository         $payoutRepository
     * @param PaymentDetailsRepository $paymentDetailsRepository
     * @param StripeApiService         $service
     */
    public function __construct(
        PayoutRepository $payoutRepository,
        PaymentDetailsRepository $paymentDetailsRepository,
        StripeApiService $service
    )
    {
        $this->payoutRepository = $payoutRepository;
        $this->paymentDetailsRepository = $paymentDetailsRepository;
        $this->service = $service;
    }


    /**
     * @param PaymentDetails $paymentDetails
     * @param float          $amount
     *
     * @return Payout|null
     * @throws \Exception
     */
    public function payout(PaymentDetails $paymentDetails, float $amount): ?Payout
    {
        if (!$paymentDetails->stripe_account_id) {
            throw new \Exception('Invalid Payment Details');
        }

        // Stripe accepts payments in cents
        $cents = bcmul($amount, 100);

        // Send funds to user's Stripe account
        $transfer = $this->service->createTransfer($paymentDetails->stripe_account_id, $cents);

        // Send funds from user's Stripe account to his debit card
        $payout = $this->service->createPayout(
            $paymentDetails->stripe_card_id, $paymentDetails->stripe_account_id, $cents
        );

        return $this->payoutRepository->create([
            'user_id'            => auth()->id(),
            'amount'             => $amount,
            'payment_details_id' => $paymentDetails->id,
            'stripe_transfer_id' => $transfer->id,
            'stripe_payout_id'   => $payout->id,
        ]);
    }

    /**
     * @param array $data
     *
     * @return PaymentDetails|null
     */
    public function addPaymentDetails(array $data): ?PaymentDetails
    {
        /* @var \Stripe\Account $account */
        $account = $this->service->createStripeAccount($data, auth()->user()->email);
        /* @var \Stripe\Card $card */
        $card = $account->external_accounts->data[0];

        return $this->paymentDetailsRepository->create([
            'stripe_account_id' => $account->id,
            'stripe_card_id'    => $card->id,
            'card_last4'        => $card->last4,
            'card_brand'        => $card->brand,
        ]);
    }

    /**
     * @param array $data
     *
     * @return PaymentDetails|null
     */
    public function getPaymentDetails(array $data): ?PaymentDetails
    {
        return $this->paymentDetailsRepository->find($data['payment_details_id']);
    }
}