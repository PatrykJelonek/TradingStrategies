<?php

namespace TradingStrategies\Strategies\Stuckey;

use JetBrains\PhpStorm\Pure;
use TradingStrategies\Strategies\TradingStrategy;
use TradingStrategies\Structures\Candlestick;
use TradingStrategies\Structures\MarketAnalisysResult;

class StuckeyStrategyNew extends TradingStrategy
{
    public const ORIGINAL_STUCKEY_FACTOR = 0.5;

    private float $stuckeyFactor = self::ORIGINAL_STUCKEY_FACTOR;

    public function analyzeMarketByStrategy(): MarketAnalisysResult
    {
        $this->numberOfIteration = 0;

        $highLowDifferencesByIteration = [];
        $meanOfHighLowDifferencesByIteration = [];

        for ($i = $this->calculationConfig->getOffset(); $i < $this->marketData->getSize(); $i++) {
            $this->numberOfIteration++;

            $this->longPositionsProfits[$i] = 0;
            $this->shortPositionsProfits[$i] = 0;

            $currentCandlestick = $this->marketData->getData()[$i];
            $highLowDifferencesByIteration[$i] = $this->marketData->getData()[$i - 1]->getHighLowDifference();

            if ($this->calculationConfig->isBufferReached($i)) {
                $meanOfHighLowDifferencesByIteration[$i] = $this->mean(
                    array_slice($highLowDifferencesByIteration, -6, 6)
                );

                $this->longPositionsPivotPoints[$i] = $this->getLongPositionPivotPoint(
                    $currentCandlestick,
                    $meanOfHighLowDifferencesByIteration[$i]
                );

                if ($currentCandlestick->getHigh() > $this->longPositionsPivotPoints[$i]) {
                    $this->numberOfLongPositions++;
                    $this->longPositionsProfits[$i] = $this->getLongPositionProfit(
                        $currentCandlestick,
                        $meanOfHighLowDifferencesByIteration[$i]
                    );
                }

                if (isset($this->longPositionsPivotPoints[$i]) && $this->longPositionsProfits[$i] < -$this->stopLossLimit) {
                    $this->numberOfStopLossOrders++;
                    $this->longPositionsProfits[$i] = -$this->stopLossLimit - $this->exchangeConfig->getSpread();
                }

                $this->shortPositionsPivotPoints[$i] = $this->getShortPositionPivotPoint(
                    $currentCandlestick,
                    $meanOfHighLowDifferencesByIteration[$i]
                );

                if ($currentCandlestick->getLow() < $this->shortPositionsPivotPoints[$i]) {
                    $this->numberOfShortPositions++;
                    $this->shortPositionsProfits[$i] = $this->getShortPositionProfit(
                        $currentCandlestick,
                        $this->shortPositionsPivotPoints[$i]
                    );
                }

                if (isset($this->shortPositionsProfits[$i]) && $this->shortPositionsProfits[$i] < -$this->stopLossLimit) {
                    $this->shortPositionsProfits[$i] = -$this->stopLossLimit - $this->exchangeConfig->getSpread();
                    $this->numberOfStopLossOrders++;
                }
            }
        }

        return new MarketAnalisysResult();
    }

    #[Pure] private function getLongPositionPivotPoint(Candlestick $candlestick, float $highLowDifferencesMean): float
    {
        return $candlestick->getOpen() + ($this->stuckeyFactor * $highLowDifferencesMean);
    }

    #[Pure] private function getLongPositionProfit(Candlestick $candlestick, float $pivotPoint): float
    {
        return $candlestick->getClose() - $pivotPoint - $this->exchangeConfig->getSpread();
    }

    #[Pure] private function getShortPositionPivotPoint(Candlestick $candlestick, float $highLowDifferencesMean): float
    {
        return $candlestick->getOpen() - ($this->stuckeyFactor * $highLowDifferencesMean);
    }

    #[Pure] private function getShortPositionProfit(Candlestick $candlestick, float $pivotPoint): float
    {
        return $pivotPoint - $candlestick->getClose() - $this->exchangeConfig->getSpread();
    }

    public function getStuckeyFactor(): float
    {
        return $this->stuckeyFactor;
    }

    public function setStuckeyFactor(float $stuckeyFactor): StuckeyStrategyNew
    {
        $this->stuckeyFactor = $stuckeyFactor;
        return $this;
    }
}