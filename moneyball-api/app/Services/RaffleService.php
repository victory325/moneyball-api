<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\RaffleParticipant;
use App\Notifications\RaffleWinner;
use App\Repositories\RaffleRepository;
use App\Repositories\UserRepository;
use App\Helpers\SettingHelper;
use App\Models\Raffle;
use Illuminate\Support\Collection;

/**
 * Class RaffleService
 * @package App\Services
 */
class RaffleService
{
    protected $raffleRepository;
    protected $userRepository;
    protected $userService;

    /**
     * RaffleService constructor.
     *
     * @param RaffleRepository $raffleRepository
     * @param UserRepository   $userRepository
     * @param UserService      $userService
     */
    public function __construct(
        RaffleRepository $raffleRepository, UserRepository $userRepository, UserService $userService
    )
    {
        $this->raffleRepository = $raffleRepository;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }

    /**
     * Run raffle
     *
     * @return Raffle
     * @throws \Exception
     */
    public function run(): Raffle
    {
        $participants = $this->userRepository->getRaffleParticipants(SettingHelper::raffleMinLevel());
        $winnerId = null;

        $count = $participants->count();
        if ($count) {
            $winnerId = $participants->random()->id;

            // Remove the winner from the list of participants
            $participants = $participants->filter(function ($user, $key) use ($winnerId) {
                return $user->id != $winnerId;
            });
        }

        $raffle = $this->raffleRepository->create([
            'winner_id'          => $winnerId,
            'amount'             => $this->getAmount(),
            'total_participants' => $count,
        ]);

        if ($winnerId) {
            $this->userService->addFunds($raffle->amount, $raffle->winner);

            $this->notifyWinner($raffle);
            $this->notifyParticipants($raffle, $participants);
        }

        return $raffle;
    }

    /**
     * @param Raffle $raffle
     *
     * @return string
     */
    public function getWinnerMessage(Raffle $raffle): string
    {
        $notification = new RaffleWinner($raffle);

        return $notification->getMessage();
    }

    /**
     * @param Raffle $raffle
     *
     * @return string
     */
    public function getParticipantMessage(Raffle $raffle): string
    {
        $notification = new RaffleParticipant($raffle);

        return $notification->getMessage();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return $this->raffleRepository->all();
    }

    /**
     * @return Raffle|\Illuminate\Database\Eloquent\Model
     */
    public function getThisWeekRaffle()
    {
        $thisWeekRaffle = $this->raffleRepository->getThisWeekRaffle();

        if ($thisWeekRaffle) {
            return $thisWeekRaffle;
        } else {
            $participants = $this->userRepository->getRaffleParticipants(SettingHelper::raffleMinLevel());

            return new Raffle([
                'amount'             => $this->getAmount(),
                'total_participants' => $participants->count(),
            ]);
        }
    }

    /**
     * @param Raffle $raffle
     */
    protected function notifyWinner(Raffle $raffle): void
    {
        $raffle->winner->notify(new RaffleWinner($raffle));
    }

    /**
     * @param Raffle     $raffle
     * @param Collection $participants
     */
    protected function notifyParticipants(Raffle $raffle, Collection $participants): void
    {
        /* @var User $row */
        foreach ($participants as $row) {
            $row->notify(new RaffleParticipant($raffle));
        }
    }

    /**
     * @return null|string
     */
    protected function getAmount(): ?string
    {
        return SettingHelper::rafflePrize();
    }
}