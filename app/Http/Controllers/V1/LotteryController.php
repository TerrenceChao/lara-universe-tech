<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Domain\Lottery\Games\Lottery;
use App\Http\Controllers\Controller;
use App\Domain\Lottery\GameService;

/**
 * Class LotteryController
 * @package App\Http\Controllers\V1
 */
class LotteryController extends Controller
{
    /**
     * @param Request $request
     * @return string
     */
    public function update(Request $request)
    {
        // TODO: 需做參數檢查，在此省略...
        $params = [
            'game_id' => intval($request->query('game_id')),
            'issue' => $request->query('issue'),
        ];

        $target = GameService::instance();
        return $target->getWinningNumber(new Lottery($params));
    }
}
