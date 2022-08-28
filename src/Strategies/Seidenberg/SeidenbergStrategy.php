<?php

namespace TradingStrategies\Strategies\Seidenberg;

use JetBrains\PhpStorm\Pure;
use TradingStrategies\Strategies\TradingStrategy;
use TradingStrategies\Structures\Candlestick;
use TradingStrategies\Structures\MarketAnalysisResult;

class SeidenbergStrategy extends TradingStrategy
{
    public const DEFAULT_SEIDENBERG_FACTOR = 0.65;

    protected int $numberOfOpeningBelowYesterdayStop = 0;
    protected float $seidenbergFactor = self::DEFAULT_SEIDENBERG_FACTOR;
    protected float $rec = -111111;
    protected ?float $stopLossLimit = 16.0;

    public function analyzeMarketByStrategy(): MarketAnalysisResult
    {
        $marketAnalysisResult = new MarketAnalysisResult();

        for ($i = $this->calculationConfig->getOffset(); $i < $this->marketData->getSize() - 1; $i++) {
            $this->numberOfIteration++;

            $this->longPositionsProfits[$i] = 0;
            $this->shortPositionsProfits[$i] = 0;

            $marketData = $this->marketData->getData();
            $currentCandlestick = $marketData[$i];

            $highLowDifferencesByIteration[$i] = $marketData[$i - 1]->getHighLowDifference();

            $this->longPositionsPivotPoints[$i] = $this->getLongPositionPivotPoint(
                $marketData[$i - 1],
                $highLowDifferencesByIteration[$i]
            );

            if (
                $currentCandlestick->getOpen() < $this->longPositionsPivotPoints[$i] &&
                $currentCandlestick->getHigh() > $this->longPositionsPivotPoints[$i]
            ) {
                $this->numberOfLongPositions++;
                $this->longPositionsProfits[$i] = $this->getLongPositionProfit(
                    $currentCandlestick,
                    $this->longPositionsPivotPoints[$i]
                );
            }

            if ($currentCandlestick->getOpen() > $this->longPositionsPivotPoints[$i]) {
                $this->numberOfOpeningBelowYesterdayStop++;
                $this->longPositionsProfits[$i] = $this->getLongPositionProfit(
                    $currentCandlestick,
                    $currentCandlestick->getOpen()
                );

                if (($currentCandlestick->getOpen() - $currentCandlestick->getLow()) > $this->stopLossLimit) {
                    $this->numberOfStopLossOrders++;
                    $this->longPositionsProfits[$i] = -$this->stopLossLimit - $this->exchangeConfig->getSpread();
                }
            }

            $this->shortPositionsPivotPoints[$i] = $this->getShortPositionPivotPoint(
                $marketData[$i - 1],
                $highLowDifferencesByIteration[$i]
            );

            if (
                $currentCandlestick->getLow() < $this->shortPositionsPivotPoints[$i] &&
                $currentCandlestick->getOpen() > $this->shortPositionsPivotPoints[$i]
            ) {
                $this->numberOfShortPositions++;
                $this->shortPositionsProfits[$i] = $this->getShortPositionProfit(
                    $currentCandlestick,
                    $this->shortPositionsPivotPoints[$i]
                );
            }

            if ($currentCandlestick->getOpen() < $this->shortPositionsPivotPoints[$i]) {
                $this->numberOfOpeningBelowYesterdayStop++;
                $this->shortPositionsProfits[$i] = $this->getShortPositionProfit(
                    $currentCandlestick,
                    $currentCandlestick->getOpen()
                );

                if (($currentCandlestick->getHigh() - $currentCandlestick->getOpen()) > $this->stopLossLimit) {
                    $this->numberOfStopLossOrders++;
                    $this->shortPositionsProfits[$i] = -$this->stopLossLimit - $this->exchangeConfig->getSpread();
                }
            }
        }

        $marketAnalysisResult
            ->setFactor($this->seidenbergFactor)
            ->setNumberOfIterations($this->numberOfIteration)
            ->setNumberOfLongPositions($this->numberOfLongPositions)
            ->setNumberOfShortPositions($this->numberOfShortPositions)
            ->setNumberOfStopLossOrders($this->numberOfStopLossOrders)
            ->setNumberOfOpeningBelowYesterdayStop($this->numberOfOpeningBelowYesterdayStop);

        return $marketAnalysisResult;
    }

    #[Pure] private function getLongPositionPivotPoint(Candlestick $candlestick, float $highLowDifferences): float
    {
        return $candlestick->getHigh() + $this->seidenbergFactor * $highLowDifferences;
    }

    #[Pure] private function getLongPositionProfit(Candlestick $candlestick, float $pivotPoint): float
    {
        return $candlestick->getClose() - $pivotPoint - $this->exchangeConfig->getSpread();
    }

    #[Pure] private function getShortPositionPivotPoint(Candlestick $candlestick, float $highLowDifferences): float
    {
        return $candlestick->getLow() - $this->seidenbergFactor * $highLowDifferences;
    }

    #[Pure] private function getShortPositionProfit(Candlestick $candlestick, float $pivotPoint): float
    {
        return $pivotPoint - $candlestick->getClose() - $this->exchangeConfig->getSpread();
    }
}
