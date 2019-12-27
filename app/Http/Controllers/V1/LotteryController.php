<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Lottery\Games\Lottery;
use App\Http\Controllers\Controller;
use Psr\Container\ContainerInterface;

/**
 * Class LotteryController
 * @package App\Http\Controllers\V1
 */
class LotteryController extends Controller
{
    /**
     * @param ContainerInterface $container
     * @return mixed
     */
    public function update(ContainerInterface $container)
    {
        $target = $container->get('App\Lottery\GameService');
        return $target->getWinningNumber(new Lottery(['game_id' => 2, 'issue' => '20190903001']));
    }
}
