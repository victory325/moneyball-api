<?php

namespace App\Http\Controllers;

use App\Http\Resources\Collections\RaffleListCollection;
use App\Http\Resources\RaffleResource;
use App\Models\Raffle;
use App\Services\RaffleService;

/**
 * Class RaffleController
 * @package App\Http\Controllers
 */
class RaffleController
{
    protected $raffleService;

    /**
     * RaffleController constructor.
     *
     * @param RaffleService $raffleService
     */
    public function __construct(RaffleService $raffleService)
    {
        $this->raffleService = $raffleService;
    }

    /**
     * Raffles List
     *
     * @return mixed
     */
    public function index()
    {
        $raffles = $this->raffleService->all();

        return response()->success(new RaffleListCollection($raffles));
    }

    /**
     * Get this week's raffle details. If there was no raffle yet, the expected details will be displayed
     *
     * @return mixed
     */
    public function current()
    {
        $raffle = $this->raffleService->getThisWeekRaffle();

        return response()->success(new RaffleResource($raffle));
    }

    /**
     * Get raffle
     *
     * @param Raffle $raffle
     *
     * @return mixed
     */
    public function view(Raffle $raffle)
    {
        return response()->success(new RaffleResource($raffle));
    }
}