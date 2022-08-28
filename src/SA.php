<?php

namespace TradingStrategies;

use JetBrains\PhpStorm\Pure;
use TradingStrategies\Strategies\Seidenberg\SeidenbergStrategy;
use TradingStrategies\Strategies\Stuckey\StuckeyStrategy;
use TradingStrategies\Strategies\TradingStrategy;
use TradingStrategies\Structures\CalculationConfig;
use TradingStrategies\Structures\ExchangeConfig;
use TradingStrategies\Structures\MarketData;

class SA
{
    public function __invoke(): array
    {
        //$marketData = (new MarketData())->readMarketDataFromJson("./Data/FW20WS210120.json");
        $marketData = (new MarketData())->readMarketDataForBitfinex("./Data/bitfinex_btc_usd.json");

        if (!empty($marketData->getData())) {
            $strategy = $this->getStuckeyStrategy($marketData);
            $result = $strategy->analyzeMarketByStrategy();
            $cumulativeProfits = $strategy->calculateCumulativeProfits();

           dump($result);

            $result = [];

            foreach ($cumulativeProfits->getCumulativeLongAndShortPositionsProfits() as $index => $value) {
                $result['zr'][] = [
                    'y' => $value,
                    'label' => $index + 5,
                ];
            }

            foreach ($cumulativeProfits->getCumulativeLongPositionsProfits() as $index => $value) {
                $result['zlr'][] = [
                    'y' => $value,
                    'label' => $index + 5,
                ];
            }

            foreach ($cumulativeProfits->getCumulativeShortPositionsProfits() as $index => $value) {
                $result['zsr'][] = [
                    'y' => $value,
                    'label' => $index + 5,
                ];
            }

            return $result;
        }

        return [];
    }

    #[Pure] private function getStuckeyStrategy(MarketData $marketData): TradingStrategy
    {
        $exchangeConfig = new ExchangeConfig(1);
        $calculationConfig = new CalculationConfig(10, 2);

        return new StuckeyStrategy($exchangeConfig, $calculationConfig, $marketData);
    }

    #[Pure] private function getSeidenbergStrategy(MarketData $marketData): TradingStrategy
    {
        $exchangeConfig = new ExchangeConfig(1);
        $calculationConfig = new CalculationConfig(5, 0);

        return new SeidenbergStrategy($exchangeConfig, $calculationConfig, $marketData);
    }
}