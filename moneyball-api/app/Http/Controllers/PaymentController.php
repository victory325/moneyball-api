<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentSubscribeRequest;
use App\Http\Requests\PaymentDepositRequest;
use App\Http\Requests\PaymentChipsRequest;
use App\Http\Requests\PaymentWithdrawRequest;
use App\Http\Requests\PaymentPackageRequest;
use App\Http\Requests\PaymentRedeemRequest;
use App\Http\Requests\TransactionsRequest;

use App\Http\Requests\DefaultListRequest;

use App\Http\Resources\Collections\PayoutListCollection;
use App\Http\Resources\PaymentDetailsResource;
use App\Http\Resources\PayoutResource;
use App\Services\PaymentService;

/**
 * Class PaymentController
 * @package App\Http\Controllers
 */
class PaymentController
{
    protected $paymentService;

    /**
     * PayoutController constructor.
     *
     * @param PaymentService $paymentService
     */
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * User List
     *
     * @param TransactionsRequest $request
     *
     * @return mixed
     */
    public function index(TransactionsRequest $request)
    {
        $transactions = $this->paymentService->transactions($request->validated());

        return response()->success($transactions);
    }

    /**
     * Purchase Subscription
     *
     * @param PaymentSubscribeRequest $request
     *
     * @return mixed
     */
    public function unlock(PaymentSubscribeRequest $request)
    {
        try {
            $userResource = $this->paymentService->unlock($request->authorization_code);

            return $userResource
                ? response()->success($userResource)
                : response()->error(['payment.unlock.failed'], 'payment.unlock.failed');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 'payment.unlock.failed');
        }
    }

    /**
     * Purchase Subscription
     *
     * @param PaymentSubscribeRequest $request
     *
     * @return mixed
     */
    public function unlockinapp(PaymentSubscribeRequest $request)
    {
        try {
            $userResource = $this->paymentService->unlockinapp($request->authorization_code);

            return $userResource
                ? response()->success($userResource)
                : response()->error(['payment.unlock.failed'], 'payment.unlock.failed');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 'payment.unlock.failed');
        }
    }

    /**
     * Make a Deposit
     *
     * @param PaymentDepositRequest $request
     *
     * @return mixed
     */
    public function deposit(PaymentDepositRequest $request)
    {
        try {
            $userResource = $this->paymentService->deposit($request->validated());

            return $userResource
                ? response()->success($userResource)
                : response()->error(['payment.deposit.failed'], 'payment.deposit.failed');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 'payment.deposit.failed');
        }
    }

    /**
     * Make a Deposit
     *
     * @param PaymentDepositRequest $request
     *
     * @return mixed
     */
    public function chips(PaymentChipsRequest $request)
    {
        try {
            $userResource = $this->paymentService->purchaseChips($request->validated());

            return $userResource
                ? response()->success($userResource)
                : response()->error(['payment.purchase.failed'], 'payment.purchase.failed');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 'payment.purchase.failed');
        }
    }

    /**
     * Purchase Life Package
     *
     * @param PaymentPackageRequest $request
     *
     * @return mixed
     */
    public function package(PaymentPackageRequest $request)
    {
        try {
            $userResource = $this->paymentService->purchasePackage($request->validated());

            return $userResource
                ? response()->success($userResource)
                : response()->error(['payment.purchase.failed'], 'payment.purchase.failed');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 'payment.purchase.failed');
        }
    }

    /**
     * Redeem Chips
     *
     * @param PaymentRedeemRequest $request
     *
     * @return mixed
     */
    public function redeem(PaymentRedeemRequest $request)
    {
        try {
            $userResource = $this->paymentService->redeem($request->validated());

            return $userResource
                ? response()->success($userResource)
                : response()->error(['payment.redeem.failed'], 'payment.redeem.failed');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 'payment.redeem.failed');
        }
    }

    /**
     * Withdraw funds
     *
     * @param PaymentWithdrawRequest $request
     *
     * @return mixed
     */
    public function withdraw(PaymentWithdrawRequest $request)
    {
        try {
            $userResource = $this->paymentService->createPayout($request->validated());

            return $userResource
                ? response()->success($userResource)
                : response()->error(['payment.withdraw.failed'], 'payment.withdraw.failed');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 'payment.withdraw.failed');
        }
    }

}
