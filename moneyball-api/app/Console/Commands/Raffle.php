<?php

namespace App\Console\Commands;

use App\Services\RaffleService;
use Illuminate\Console\Command;

/**
 * Class Raffle
 * @package App\Console\Commands
 */
class Raffle extends Command
{
    protected $signature = 'raffle';
    protected $description = 'Run ruffle';
    protected $errors = [];
    protected $raffleService;

    /**
     * Raffle constructor.
     *
     * @param RaffleService $raffleService
     */
    public function __construct(RaffleService $raffleService)
    {
        parent::__construct();

        $this->raffleService = $raffleService;
    }

    /**
     * Handle console command
     */
    public function handle(): void
    {
        try {
            $raffle = $this->raffleService->run();
        } catch (\Exception $e) {
            exit('ERROR: ' . $e->getMessage());
        }

        $messages = [
            'Raffle id: ' . $raffle->id,
            'Winner id: ' . $raffle->winner_id,
            'Winner name: ' . $raffle->winnerName,
            'Amount: ' . $raffle->amount,
            'Total participants: ' . $raffle->total_participants,
            'Message to the winner: ' . $this->raffleService->getWinnerMessage($raffle),
            'Message to participants: ' . $this->raffleService->getParticipantMessage($raffle),
        ];

        foreach ($messages as $message) {
            echo $message . PHP_EOL;
        }
    }
}