<?php

namespace App\Services;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

use PayPal\Api\Payout;
use PayPal\Api\PayoutSenderBatchHeader;
use PayPal\Api\Currency;
use PayPal\Api\PayoutItem;
use PayPal\Api\PayoutBatch;
use PayPal\Api\Amount;
use PayPal\Api\FuturePayment;
use PayPal\Api\Payer;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Authorization;
use PayPal\Api\Capture;

use App\Repositories\UserRepository;
use App\Repositories\TransactionRepository;

use App\Http\Resources\UserResource;
use App\Http\Resources\Collections\TransactionListCollection;

/**
 * Class PaymentService
 * @package App\Services
 */
class PaymentService
{
    const CURRENCY = 'USD';
    const STATUS_PENDING = 'PENDING';

    protected $client;
    protected $userRepository;
    protected $transactionRepository;

    /**
     * StripeService constructor.
     */
    public function __construct(
        UserRepository $userRepository,
        TransactionRepository $transactionRepository)
    {
        $this->client = new ApiContext(
            new OAuthTokenCredential(
                config('services.paypal.client_id'),
                config('services.paypal.secret')
            )
        );

        $this->client->setConfig(
            array(
                'log.LogEnabled' => true,
                'log.FileName' => 'PayPal.log',
                'log.LogLevel' => 'FINE',
                'mode' => config('services.paypal.mode')
              )
        );

        $this->userRepository = $userRepository;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     *
     */
    public function transactions(array $params): TransactionListCollection
    {
        $collection = $this->transactionRepository->myTransactions($params);
        return new TransactionListCollection($collection);
    }

    /**
     * @param string $authorizationCode
     *
     */
    public function unlock(string $authorizationCode): UserResource
    {
        $authorization = Authorization::get($authorizationCode, $this->client);
        $amt = new Amount();
        $amt->setCurrency("USD")
            ->setTotal(9.99);
        ### Capture
        $capture = new Capture();
        $capture->setAmount($amt);
        // Perform a capture
        $getCapture = $authorization->capture($capture, $this->client);

        // Success
        $user = auth()->user();
        $user->premium = true;
        // $user->paypal_email = $payment->payer->payer_info->email;
        // $user->paypal_code = $refreshToken;
        $user->save();

        // $this->transactionRepository->create([
        //     'summary'               => 'Unlock',
        //     'amount'                => 9.99,
        //     'paypal_email'          => 'premium',
        //     'paypal_transaction_id' => $getCapture->getParentPayment()
        // ]);

        return new UserResource($user);
    }

    /**
     * @param string $authorizationCode
     *
     */
    public function unlockinapp(string $authorizationCode): UserResource
    {
        // Success
        $user = auth()->user();
        $user->premium = true;
        $user->save();

        // $this->transactionRepository->create([
        //     'summary'               => 'Unlock',
        //     'amount'                => 9.99,
        //     'paypal_email'          => 'premium',
        //     'paypal_transaction_id' => $authorizationCode
        // ]);

        return new UserResource($user);
    }

    /**
     * @param array $data
     *
     */
    public function deposit(array $data): UserResource
    {
        $authorization = Authorization::get($data["authorization_code"], $this->client);
        $amt = new Amount();
        $amt->setCurrency("USD")
            ->setTotal($data["amount"]);
        ### Capture
        $capture = new Capture();
        $capture->setAmount($amt);
        // Perform a capture
        $getCapture = $authorization->capture($capture, $this->client);

        // Success
        $user = auth()->user();
        $user->available_amount = $user->available_amount + $data["amount"];
        if (!empty($data["bonus"])) {
            $user->available_amount = $user->available_amount + $data["bonus"];
        }

        $user->available_amount = number_format($user->available_amount, 2);

        $user->save();

        $this->transactionRepository->create([
            'summary'               => $data["summary"],
            'amount'                => $data["amount"],
            'paypal_email'          => 'balance',
            'paypal_transaction_id' => $getCapture->getParentPayment()
        ]);

        return new UserResource($user);
    }

    /**
     * @param array $data
     *
     */
    public function purchasePackage(array $data): ?UserResource
    {
        $user = auth()->user();

        if (!empty($data["lives"])) {
            $user->lives = $user->lives + $data["lives"];
            $user->save();

            // $this->transactionRepository->create([
            //     'summary'               => $data["lives"] . " LIVES for MoneyBall",
            //     'amount'                => -1 * $data["amount"],
            //     'paypal_email'          => "in-app",
            //     'paypal_transaction_id' => $data["transaction_id"]
            // ]);
        } else {
            $user->is_seconds_added = true;
            $user->save();

            // $this->transactionRepository->create([
            //     'summary'               => "Additional Seconds Added",
            //     'amount'                => -1 * $data["amount"],
            //     'paypal_email'          => "balance",
            //     'paypal_transaction_id' => ""
            // ]);
        }

        $user->available_amount = number_format($user->available_amount, 2);

        return new UserResource($user);
    }
    
    /**
     * @param array $data
     *
     */
    public function redeem(array $data): ?UserResource
    {
        $user = auth()->user();

        if ($user->available_chips < $data['chips']) {
            return null;
        }

        $user->lives = $user->lives + $data["amount"];
        $user->available_chips = $user->available_chips - $data["chips"];
        $user->save();

        $this->transactionRepository->create([
            'summary'               => "Redeem " . $data["chips"] . ' $Chips',
            'amount'                => -1 * $data["amount"],
            'paypal_email'          => "redeem",
            'paypal_transaction_id' => ""
        ]);

        // $user->available_amount = number_format($user->available_amount, 2);

        return new UserResource($user);
    }

    /**
     * @param array $data
     *
     */
    public function purchaseChips(array $data): ?UserResource
    {
        $user = auth()->user();

        // if ($user->available_amount < $data['amount']) {
        //     return null;
        // }

        // $user->available_amount = $user->available_amount - $data["amount"];
        $user->available_chips = $user->available_chips + $data["chips"];
        $user->save();

        $this->transactionRepository->create([
            'summary'               => "Purchase " . $data["chips"] . " CHIPS",
            'amount'                => $data["chips"],
            'paypal_email'          => "in-app",
            'paypal_transaction_id' => $data["transaction_id"]
        ]);

        $user->available_amount = number_format($user->available_amount, 2);

        return new UserResource($user);
    }

    /**
     * @param array $data
     *
     * @return UserResource
     */
    public function createPayout(array $data): ?UserResource
    {
        $user = auth()->user();

        if ($user->available_amount < $data['amount']) {
            return null;
        }

        $amount = new Currency(json_encode([
            'value'    => $data['amount'],
            'currency' => self::CURRENCY,
        ]));

        $senderItem = new PayoutItem();
        $senderItem->setRecipientType('Email')
            ->setReceiver($user->email)
            ->setAmount($amount);

        $senderBatchHeader = new PayoutSenderBatchHeader();
        $senderBatchHeader->setSenderBatchId(uniqid());

        $payouts = new Payout();
        $payouts->setSenderBatchHeader($senderBatchHeader)
            ->addItem($senderItem);

        // Create Payout
        $batch = $payouts->create([], $this->client);

        $user->available_amount = $user->available_amount - $data["amount"];
        $user->save();

        $status = $this->getPayoutStatus($batch->getBatchHeader()->getPayoutBatchId());
        $header = $status->getBatchHeader();
        $this->transactionRepository->create([
            'summary'               => "Withdraw",
            'amount'                => -1 * $data["amount"],
            'paypal_email'          => $user->email,
            'paypal_transaction_id' => $header->getPayoutBatchId()
        ]);

        $user->available_amount = number_format($user->available_amount, 2);
        return new UserResource($user);
    }

    /**
     * @param string $status
     *
     * @return bool
     */
    public function checkBatchStatus(string $status): bool
    {
        return $status == self::STATUS_PENDING;
    }

    /**
     * @param string $payoutBatchId
     *
     * @return PayoutBatch
     */
    public function getPayoutStatus(string $payoutBatchId): PayoutBatch
    {
        return Payout::get($payoutBatchId, $this->client);
    }
}