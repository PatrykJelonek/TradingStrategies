<?php

namespace TradingStrategies\Strategies\SeidenbergStuckey;

use TradingStrategies\Strategies\Seidenberg\SeidenbergStrategy;
use TradingStrategies\Strategies\Stuckey\StuckeyStrategy;
use TradingStrategies\Strategies\TradingStrategy;
use TradingStrategies\Structures\MarketAnalysisResult;

class SeidenbergStuckeyStrategy extends TradingStrategy
{
    /* Variables for SeidenbergStuckey strategy part */
    protected bool $hasStuckeyLongPosition = false;
    protected bool $hasSeidenbergLongPosition = false;
    protected bool $hasStuckeyShortPosition = false;
    protected bool $hasSeidenbergShortPosition = false;

    private array $highLowDifferencesByIteration = [];

    /* Variables for Stuckey strategy part */
    protected float $stuckeyFactor = StuckeyStrategy::ORIGINAL_STUCKEY_FACTOR;

    protected int $numberOfStuckeyLongPosition = 0;
    protected int $numberOfStuckeyShortPosition = 0;

    protected array $stuckeyLongPositionsPivotPoints = [];
    protected array $stuckeyShortPositionsPivotPoints = [];

    protected array $stuckeyLongPositionsProfits = [];
    protected array $stuckeyShortPositionsProfits = [];

    private array $stuckeyMeanHighLowDifferencesByIteration = [];

    /* Variables for Seidenberg strategy part */
    protected float $seidenbergFactor = SeidenbergStrategy::ORIGINAL_SEIDENBERG_FACTOR;

    protected int $numberOfSeidenbergLongPosition = 0;
    protected int $numberOfSeidenbergShortPosition = 0;
    protected int $numberOfSeidenbergOpeningBelowYesterdayStop = 0;

    protected array $seidenbergLongPositionsPivotPoints = [];
    protected array $seidenbergShortPositionsPivotPoints = [];

    protected array $seidenbergLongPositionsProfits = [];
    protected array $seidenbergShortPositionsProfits = [];

    public function analyzeMarketByStrategy(): MarketAnalysisResult
    {
        $marketData = $this->marketData->getData();
        $this->highLowDifferencesByIteration = [];
        $this->stuckeyMeanHighLowDifferencesByIteration = [];

        for ($i = $this->calculationConfig->getOffset(); $i < $this->marketData->getSize(); $i++) {
            $this->numberOfIteration++;
            $currentCandlestick = $this->marketData->getData()[$i];
            $this->highLowDifferencesByIteration[$i] = $marketData[$i - 1]->getHighLowDifference();

            $this->initializeIterationData($i);
            $this->analyzeMarketByStuckeyStrategy($i);
            $this->analyzeMarketBySeidenbergStrategy($i);

            if ($this->hasStuckeyLongPosition && $this->hasSeidenbergLongPosition) {
                $this->numberOfLongPositions++;

                $this->longPositionsProfits[$i] = $currentCandlestick->getClose();
                $this->longPositionsProfits[$i] -= $this->stuckeyLongPositionsPivotPoints[$i];
                $this->longPositionsProfits[$i] -= $this->exchangeConfig->getSpread();

                $this->shortPositionsProfits[$i] = $currentCandlestick->getClose();
                $this->shortPositionsProfits[$i] -= $this->seidenbergLongPositionsPivotPoints[$i];
                $this->shortPositionsProfits[$i] -= $this->exchangeConfig->getSpread();
            }
        }

        return new MarketAnalysisResult();
    }

    private function initializeIterationData(int $iteration): void
    {
        $this->numberOfIteration++;
        $this->hasStuckeyLongPosition = false;
        $this->hasSeidenbergLongPosition = false;
        $this->hasStuckeyShortPosition = false;
        $this->hasSeidenbergShortPosition = false;

        /* Variables for SeidenbergStuckey strategy part */
        $this->longPositionsProfits[$iteration] = 0;
        $this->shortPositionsProfits[$iteration] = 0;

        /* Variables for Stuckey strategy part */
        $this->stuckeyLongPositionsProfits[$iteration] = 0;
        $this->stuckeyShortPositionsProfits[$iteration] = 0;

        /* Variables for Seidenberg strategy part */
        $this->seidenbergLongPositionsProfits[$iteration] = 0;
        $this->seidenbergShortPositionsProfits[$iteration] = 0;
    }

    private function analyzeMarketByStuckeyStrategy(int $iteration): void
    {
        $candlestick = $this->marketData->getData()[$iteration];

        if ($this->calculationConfig->isBufferReached($iteration)) {
            $this->stuckeyMeanHighLowDifferencesByIteration[$iteration] = $this->mean(
                array_slice($this->highLowDifferencesByIteration, -6, 6)
            );

            $this->stuckeyLongPositionsPivotPoints[$iteration] = $candlestick->getOpen();
            $this->stuckeyLongPositionsPivotPoints[$iteration] += $this->stuckeyFactor * $this->stuckeyMeanHighLowDifferencesByIteration[$iteration];

            if ($candlestick->getHigh() > $this->stuckeyLongPositionsPivotPoints[$iteration]) {
                $this->numberOfStuckeyLongPosition++;
                $this->hasStuckeyLongPosition = true;

                $this->stuckeyLongPositionsProfits[$iteration] = $candlestick->getClose();
                $this->stuckeyLongPositionsProfits[$iteration] -= $this->stuckeyLongPositionsPivotPoints[$iteration];
                $this->stuckeyLongPositionsProfits[$iteration] -= $this->exchangeConfig->getSpread();
            }

            if (isset($this->stuckeyLongPositionsProfits[$iteration]) && $this->stuckeyLongPositionsProfits[$iteration] < -$this->stopLimit) {
                $this->numberOfStopsOrders++;

                $this->stuckeyLongPositionsProfits[$iteration] = -$this->stopLimit;
                $this->stuckeyLongPositionsProfits[$iteration] -= $this->exchangeConfig->getSpread();
            }

            $this->stuckeyShortPositionsPivotPoints[$iteration] = $candlestick->getOpen();
            $this->stuckeyShortPositionsPivotPoints[$iteration] -= $this->stuckeyFactor * $this->stuckeyMeanHighLowDifferencesByIteration[$iteration];

            if ($candlestick->getLow() < $this->stuckeyShortPositionsPivotPoints[$iteration]) {
                $this->numberOfStuckeyShortPosition++;
                $this->hasStuckeyShortPosition = true;

                $this->stuckeyShortPositionsProfits[$iteration] = $this->stuckeyShortPositionsPivotPoints[$iteration];
                $this->stuckeyShortPositionsProfits[$iteration] -= $candlestick->getClose();
                $this->stuckeyShortPositionsProfits[$iteration] -= $this->exchangeConfig->getSpread();
            }

            if (isset($this->stuckeyShortPositionsProfits[$iteration]) && $this->stuckeyShortPositionsProfits[$iteration] < -$this->stopLimit) {
                $this->numberOfStopsOrders++;
                $this->stuckeyShortPositionsProfits[$iteration] = -$this->stopLimit;
                $this->stuckeyShortPositionsProfits[$iteration] -= $this->exchangeConfig->getSpread();
            }
        }
    }

    private function analyzeMarketBySeidenbergStrategy(int $iteration): void
    {
        $marketData = $this->marketData->getData();
        $candlestick = $this->marketData->getData()[$iteration];

        $this->seidenbergLongPositionsPivotPoints[$iteration] = $marketData[$iteration - 1]->getHigh();
        $this->seidenbergLongPositionsPivotPoints[$iteration] += $this->seidenbergFactor * $this->highLowDifferencesByIteration[$iteration];

        if ($candlestick->getHigh() > $this->seidenbergLongPositionsPivotPoints[$iteration]) {
            $this->numberOfSeidenbergLongPosition++;
            $this->hasSeidenbergLongPosition = true;

            $this->seidenbergLongPositionsProfits[$iteration] = $candlestick->getClose();
            $this->seidenbergLongPositionsProfits[$iteration] -= $this->seidenbergLongPositionsPivotPoints[$iteration];
            $this->seidenbergLongPositionsProfits[$iteration] -= $this->exchangeConfig->getSpread();
        }

        if ($candlestick->getOpen() > $this->seidenbergLongPositionsPivotPoints[$iteration]) {
            $this->numberOfSeidenbergOpeningBelowYesterdayStop++;

            $this->seidenbergLongPositionsProfits[$iteration] = $candlestick->getClose();
            $this->seidenbergLongPositionsProfits[$iteration] -= $candlestick->getOpen();
            $this->seidenbergLongPositionsProfits[$iteration] -= $this->exchangeConfig->getSpread();
        }

        if ($this->seidenbergLongPositionsPivotPoints[$iteration] - $candlestick->getLow() > $this->stopLimit) {
            $this->numberOfStopsOrders++;
        }

        $this->seidenbergShortPositionsPivotPoints[$iteration] = $marketData[$iteration - 1]->getLow();
        $this->seidenbergShortPositionsPivotPoints[$iteration] -= $this->seidenbergFactor * $this->highLowDifferencesByIteration[$iteration];

        if ($candlestick->getLow() < $this->seidenbergShortPositionsPivotPoints[$iteration]) {
            $this->numberOfSeidenbergShortPosition++;
            $this->hasSeidenbergShortPosition = true;

            $this->seidenbergShortPositionsProfits[$iteration] = $this->seidenbergShortPositionsPivotPoints[$iteration];
            $this->seidenbergShortPositionsProfits[$iteration] -= $candlestick->getClose();
            $this->seidenbergShortPositionsProfits[$iteration] -= $this->exchangeConfig->getSpread();
        }

        if ($candlestick->getOpen() < $this->seidenbergShortPositionsPivotPoints[$iteration]) {
            $this->numberOfSeidenbergOpeningBelowYesterdayStop++;
            $this->seidenbergShortPositionsProfits[$iteration] = $candlestick->getOpen();
            $this->seidenbergShortPositionsProfits[$iteration] -= $candlestick->getClose();
            $this->seidenbergShortPositionsProfits[$iteration] -= $this->exchangeConfig->getSpread();
        }

        if ($candlestick->getHigh() - $this->seidenbergShortPositionsPivotPoints[$iteration] > $this->stopLimit) {
            $this->numberOfStopsOrders++;
        }
    }

    public function getStuckeyFactor(): float
    {
        return $this->stuckeyFactor;
    }

    public function setStuckeyFactor(float $stuckeyFactor): SeidenbergStuckeyStrategy
    {
        $this->stuckeyFactor = $stuckeyFactor;
        return $this;
    }

    public function getSeidenbergFactor(): float
    {
        return $this->seidenbergFactor;
    }

    public function setSeidenbergFactor(float $seidenbergFactor): SeidenbergStuckeyStrategy
    {
        $this->seidenbergFactor = $seidenbergFactor;
        return $this;
    }
}