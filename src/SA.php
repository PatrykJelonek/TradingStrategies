<?php

namespace TradingStrategies;

use TradingStrategies\Strategies\Stuckey\StuckeyStrategy;
use TradingStrategies\Structures\CalculationParams;
use TradingStrategies\Structures\Item;
use TradingStrategies\Structures\OHLC;

class SA
{
    /**
     * @throws Strategies\StrategyException
     */
    public function __invoke(): array
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
            $stuckeyStrategy = new StuckeyStrategy($parsedData);

            $calculationParams = new CalculationParams();
            $calculationParams
                ->setMarketData($parsedData)
                ->setMarketDataSize(count($parsedData))
                ->setCalculationOffset(3900)
                ->setCalculationBuffer(100)
                ->setFactor(0.5);

            $calculationOutput = $stuckeyStrategy->calculatePivots($calculationParams);
            $sumResult = $stuckeyStrategy->sumResult($calculationOutput);
            $recordResult = $stuckeyStrategy->calculateRecordResult($sumResult);

            $x = [];
            foreach ($sumResult->getZr() as $index => $value) {
                $x[] = $index;
            }

            //$zlr = $sumResult->getZlr();
            //$y = $zlr[count($parsedData) - count($x) + count($sumResult->getZr())];

            $result = [];

            foreach ($sumResult->getZr() as $index => $value) {
                $result['zr'][] = [
                    'y' => $value,
                    'label' => $index + 3900,
                ];
            }

            foreach ($sumResult->getZlr() as $index => $value) {
                $result['zlr'][] = [
                    'y' => $value,
                    'label' => $index + 3900,
                ];
            }

            foreach ($sumResult->getZsr() as $index => $value) {
                $result['zsr'][] = [
                    'y' => $value,
                    'label' => $index + 3900,
                ];
            }

            return $result;
        }
    }
}