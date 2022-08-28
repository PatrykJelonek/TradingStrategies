<?php

namespace TradingStrategies;

use JetBrains\PhpStorm\Pure;
use TradingStrategies\Exchanges\Poloniex\PoloniexMarketData;
use TradingStrategies\Strategies\Seidenberg\SeidenbergStrategy;
use TradingStrategies\Strategies\Stuckey\StuckeyStrategy;
use TradingStrategies\Strategies\TradingStrategy;
use TradingStrategies\Structures\CalculationConfig;
use TradingStrategies\Structures\CumulativeProfitsCalculationResult;
use TradingStrategies\Structures\ExchangeConfig;
use TradingStrategies\Exchanges\Bitfinex\BitfinexMarketData;
use TradingStrategies\Structures\Exchanges\CryptoCompare\CryptoCompareMarketData;
use TradingStrategies\Structures\Exchanges\Custom\CustomMarketData;
use TradingStrategies\Structures\MarketData;

class GetStrategyResult
{
    public function __invoke(
        string $strategy = StuckeyStrategy::class,
        string $exchange = CustomMarketData::class
    ): array {
        $marketData = match ($exchange) {
            CryptoCompareMarketData::class => (new CryptoCompareMarketData())
                ->readMarketDataFromJson("./Data/hour_CryptoCompare_Index_BTC_USDT_872_31661724111871.json"),
            BitfinexMarketData::class => (new BitfinexMarketData())
                ->readMarketDataFromJson("./Data/bitfinex_btc_usd.json"),
            PoloniexMarketData::class => (new PoloniexMarketData())
                ->readMarketDataFromJson("./Data/poloniex_btc_usdt.json"),
            default => (new CustomMarketData())
                ->readMarketDataFromJson("./Data/FW20WS210120.json"),
        };

        if ($marketData !== null && !empty($marketData->getData())) {
            switch ($strategy) {
                case StuckeyStrategy::class:
                    $strategy = $this->getStuckeyStrategy($marketData);
                    break;
                case SeidenbergStrategy::class:
                    $strategy = $this->getSeidenbergStrategy($marketData);
                    break;
                default:
                    return [];
            }

            $result = $strategy->analyzeMarketByStrategy();
            $cumulativeProfits = $strategy->calculateCumulativeProfits();

            dump($result);

            return $this->getResult($cumulativeProfits);
        }

        return [];
    }

    #[Pure] private function getStuckeyStrategy(MarketData $marketData): TradingStrategy
    {
        $exchangeConfig = new ExchangeConfig(0.05);
        $calculationConfig = new CalculationConfig(7, 2);

        return new StuckeyStrategy($exchangeConfig, $calculationConfig, $marketData);
    }

    #[Pure] private function getSeidenbergStrategy(MarketData $marketData): TradingStrategy
    {
        $exchangeConfig = new ExchangeConfig(0.05);
        $calculationConfig = new CalculationConfig(5, 0);

        return new SeidenbergStrategy($exchangeConfig, $calculationConfig, $marketData);
    }

    #[Pure] private function getResult(CumulativeProfitsCalculationResult $cumulativeProfits): array
    {
        $result = [];

        foreach ($cumulativeProfits->getCumulativeLongAndShortPositionsProfits() as $index => $value) {
            $result['cumulativeLongAndShortPositionsProfits'][] = [
                'y' => $value,
                'label' => $index + 5,
            ];
        }

        foreach ($cumulativeProfits->getCumulativeLongPositionsProfits() as $index => $value) {
            $result['cumulativeLongPositionsProfits'][] = [
                'y' => $value,
                'label' => $index + 5,
            ];
        }

        foreach ($cumulativeProfits->getCumulativeShortPositionsProfits() as $index => $value) {
            $result['cumulativeShortPositionsProfits'][] = [
                'y' => $value,
                'label' => $index + 5,
            ];
        }

        return $result;
    }
}