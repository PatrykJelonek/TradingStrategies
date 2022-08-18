<?php

namespace TradingStrategies;

use TradingStrategies\Strategies\Stuckey\StuckeyStrategy;
use TradingStrategies\Structures\Item;
use TradingStrategies\Structures\OHLC;

class SA
{
    public function __invoke()
    {
        $data = json_decode(file_get_contents("./Data/FW20WS210120.json"), true);
        $parsedData = [];

        foreach ($data as $item) {
            $parsedData[] = (new Item())
                ->setDate($item['date'])
                ->setLop($item['lop'])
                ->setVolumen($item['volumen'])
                ->setOhlc(
                    (new OHLC())
                        ->setHigh($item['high'])
                        ->setLow($item['low'])
                        ->setOpen($item['open'])
                        ->setClose($item['close'])
                );
        }

        if (!empty($parsedData)) {
            (new StuckeyStrategy($parsedData))();
        }
    }
}