<?php

namespace TradingStrategies\Strategies\Stuckey;

use JetBrains\PhpStorm\Pure;
use TradingStrategies\Strategies\TradingStrategy;
use TradingStrategies\Structures\Candlestick;
use TradingStrategies\Structures\MarketAnalysisResult;

class StuckeyStrategy extends TradingStrategy
{
    public const ORIGINAL_STUCKEY_FACTOR = 0.5;

    protected float $rec = -1111;
    protected ?float $stopLimit = 50;

    public function analyzeMarketByStrategy(): MarketAnalysisResult
    {
        $marketAnalysisResult = new MarketAnalysisResult();
        $this->numberOfIteration = 0;

        $highLowDifferencesByIteration = [];
        $meanOfHighLowDifferencesByIteration = [];

        $biggestLongPositionProfit = 0;
        $biggestShortPositionProfit = 0;
        $biggestLongPositionLoss = 0;
        $biggestShortPositionLoss = 0;

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
                        $this->longPositionsPivotPoints[$i]
                    );
                }

                if (isset($this->longPositionsProfits[$i]) && $this->longPositionsProfits[$i] < -$this->stopLimit) {
                    $this->longPositionsProfits[$i] = -$this->stopLimit - $this->exchangeConfig->getSpread();
                    $this->numberOfStopsOrders++;
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

                if (isset($this->shortPositionsProfits[$i]) && $this->shortPositionsProfits[$i] < -$this->stopLimit) {
                    $this->shortPositionsProfits[$i] = -$this->stopLimit - $this->exchangeConfig->getSpread();
                    $this->numberOfStopsOrders++;
                }

                if (!empty($this->longPositionsProfits[$i]) && $this->longPositionsProfits[$i] > $biggestLongPositionProfit) {
                    $biggestLongPositionProfit = $this->longPositionsProfits[$i];
                }

                if (!empty($this->shortPositionsProfits[$i]) && $this->shortPositionsProfits[$i] > $biggestShortPositionProfit) {
                    $biggestShortPositionProfit = $this->shortPositionsProfits[$i];
                }

                if (!empty($this->longPositionsProfits[$i]) && $this->longPositionsProfits[$i] < $biggestLongPositionLoss) {
                    $biggestLongPositionLoss = $this->longPositionsProfits[$i];
                }

                if (!empty($this->shortPositionsProfits[$i]) && $this->shortPositionsProfits[$i] < $biggestShortPositionLoss) {
                    $biggestShortPositionLoss = $this->shortPositionsProfits[$i];
                }
            }
        }

        $marketAnalysisResult
            ->setFactor($this->factor)
            ->setNumberOfIterations($this->numberOfIteration)
            ->setNumberOfLongPositions($this->numberOfLongPositions)
            ->setNumberOfShortPositions($this->numberOfShortPositions)
            ->setNumberOfStopLossOrders($this->numberOfStopsOrders)
            ->setBiggestLongPositionProfit($biggestLongPositionProfit)
            ->setBiggestLongPositionLoss($biggestLongPositionLoss)
            ->setBiggestShortPositionProfit($biggestShortPositionProfit)
            ->setBiggestShortPositionLoss($biggestShortPositionLoss);

        return $marketAnalysisResult;
    }

    #[Pure] private function getLongPositionPivotPoint(Candlestick $candlestick, float $highLowDifferencesMean): float
    {
        return $candlestick->getOpen() + $this->factor * $highLowDifferencesMean;
    }

    #[Pure] private function getLongPositionProfit(Candlestick $candlestick, float $pivotPoint): float
    {
        return $candlestick->getClose() - $pivotPoint - $this->exchangeConfig->getSpread();
    }

    #[Pure] private function getShortPositionPivotPoint(Candlestick $candlestick, float $highLowDifferencesMean): float
    {
        return $candlestick->getOpen() - $this->factor * $highLowDifferencesMean;
    }

    #[Pure] private function getShortPositionProfit(Candlestick $candlestick, float $pivotPoint): float
    {
        return $pivotPoint - $candlestick->getClose() - $this->exchangeConfig->getSpread();
    }
}