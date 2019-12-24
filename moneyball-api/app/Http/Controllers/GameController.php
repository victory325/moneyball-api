<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameStartRequest;
use App\Http\Requests\GameEndRequest;

use App\Http\Resources\UserResource;
use App\Services\GameService;

/**
 * Class GameController
 * @package App\Http\Controllers
 */
class GameController
{
    protected $gameService;

    /**
     * PayoutController constructor.
     *
     * @param GameService $gameService
     */
    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * User List
     *
     * @param GameStartRequest $request
     *
     * @return mixed
     */
    public function start(GameStartRequest $request)
    {
        try {
            $userResource = $this->gameService->start_game($request->validated());

            return $userResource
                ? response()->success($userResource)
                : response()->error(['game.start.failed'], 'game.start.failed');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 'game.start.failed');
        }
    }

    /**
     * Purchase Subscription
     *
     * @param GameEndRequest $request
     *
     * @return mixed
     */
    public function end(GameEndRequest $request)
    {
        try {
            $userResource = $this->gameService->end_game($request->validated());

            return $userResource
                ? response()->success($userResource)
                : response()->error(['game.finish.failed'], 'game.finish.failed');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 'game.finish.failed');
        }
    }

}
