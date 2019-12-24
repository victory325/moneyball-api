<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\TransactionRepository;

use App\Http\Resources\UserResource;
use App\Http\Resources\Collections\TransactionListCollection;

/**
 * Class GameService
 * @package App\Services
 */
class GameService
{
    protected $userRepository;
    protected $transactionRepository;

    /**
     * StripeService constructor.
     */
    public function __construct(
        UserRepository $userRepository,
        TransactionRepository $transactionRepository)
    {
        $this->userRepository = $userRepository;
        $this->transactionRepository = $transactionRepository;
    }

    
    /**
     * @param array $data
     *
     */
    public function start_game(array $data): ?UserResource
    {
        if ($data["entry_cash"] == 0 && $data["entry_chip"] == 0) {
            return new UserResource($user);
        }

        $players = $data["players"];

        foreach ($players as $player) {
            $user = $this->userRepository->findById($player);

            // $user->available_amount = $user->available_amount - $data["entry_cash"];
            $user->available_chips = $user->available_chips - $data["entry_chip"];
            $user->save();

            $this->transactionRepository->create([
                'user_id'               => $player,
                'summary'               => "Entry For " . $data["game_name"],
                'amount'                => -1 * $data["entry_chip"],
                'paypal_email'          => "game",
                'paypal_transaction_id' => ""
            ]);

        }

        $user = auth()->user();
        $user->refresh();

        return new UserResource($user);
    }

    /**
     * @param array $data
     *
     */
    public function end_game(array $data): ?UserResource
    {

        if ($data["prize"] > 0) {
            $user = $this->userRepository->findById($data["winner_id"]);

            $user->available_chips = $user->available_chips + $data["prize"];
            $user->total_earned_amount = $user->total_earned_amount + $data["prize"];
            $user->save();

            $this->transactionRepository->create([
                'user_id'               => $data["winner_id"],
                'summary'               => "Prize For " . $data["game_name"],
                'amount'                => $data["prize"],
                'paypal_email'          => "prize",
                'paypal_transaction_id' => ""
            ]);

        }

        $user = auth()->user();
        $user->refresh();

        return new UserResource($user);
    }

}