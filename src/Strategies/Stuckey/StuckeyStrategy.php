<?php

namespace TradingStrategies\Strategies\Stuckey;

use JetBrains\PhpStorm\Pure;
use TradingStrategies\Interfaces\TradingStrategy;
use TradingStrategies\StrategyException;
use TradingStrategies\Structures\CalculationOutput;
use TradingStrategies\Structures\CalculationParams;
use TradingStrategies\Structures\SumResultOutput;
use TradingStrategies\Traits\CumulativeSumTrait;
use TradingStrategies\Traits\MeanTrait;
use TradingStrategies\Structures\Candlestick;

/**
 * Class to calculate trading pivots by Stuckey's strategy
 */
class StuckeyStrategy implements TradingStrategy
{
    use MeanTrait, CumulativeSumTrait;

    private const ORIGINAL_STUCKEY_FACTOR = 0.5;
    private const DEFAULT_ITERATION_OFFSET = 3899;
    private const DEFAULT_SPREAD = 1;
    private const DEFAULT_CALCULATION_BUFFER = 100;

    private float $iterationOffset = self::DEFAULT_ITERATION_OFFSET;
    private float $calculationBuffer = self::DEFAULT_CALCULATION_BUFFER;
    private float $spread = self::DEFAULT_SPREAD;
    private float $factor = self::ORIGINAL_STUCKEY_FACTOR;

    private float $stuckeyFactor = self::ORIGINAL_STUCKEY_FACTOR;

    private int $numberOfLongPositions = 0;
    private int $numberOfShortPositions = 0;

    private int $rec = -1111;
    private int $stopLossLimit = 50;
    private int $numberOfIterationBelowStopLoss = 0;

    private array $longPositionsPivotPoints = [];
    private array $shortPositionsPivotPoints = [];

    private array $longPositionProfits = [];
    private array $shortPositionProfits = [];


    public function __invoke()
    {
        try {

        } catch (StrategyException $exception) {
            echo "[{$exception->getCode()}] - {$exception->getMessage()}";
        }
    }

    public function calculatePivots(CalculationParams $params): CalculationOutput
    {
        $numberOfIterations = 0;
        $marketData = $params->getMarketData();
        $output = new CalculationOutput();

        $highLowDifferences = [];
        $meanHighLowDifferences = [];

        for ($iteration = $params->getCalculationOffset(); $iteration < $params->getMarketDataSize(); $iteration++) {
            $numberOfIterations++;

            $this->longPositionProfits[$iteration] = 0;
            $this->shortPositionProfits[$iteration] = 0;
            $currentIterationItem = $marketData[$iteration];

            $highLowDifferences[$iteration] = $this->calculateHighLowDifference($marketData[$iteration - 1]);

            if ($this->isCalculationBufferReached($iteration)) {
                $meanHighLowDifferences[$iteration] = $this->getMeanHighLowDifference($highLowDifferences);

                $this->longPositionsPivotPoints[$iteration] = $this->getLongPositionPivotPoint(
                    $currentIterationItem,
                    $meanHighLowDifferences[$iteration]
                );

                if ($currentIterationItem->getOhlc()->getHigh() > $this->longPositionsPivotPoints[$iteration]) {
                    $this->numberOfLongPositions++;
                    $this->longPositionProfits[$iteration] = $currentIterationItem->getOhlc()->getClose(
                        ) - $this->longPositionsPivotPoints[$iteration] - $this->spread;
                }

                if (!empty($this->longPositionProfits[$iteration]) && $this->longPositionProfits[$iteration] < -$this->stopLossLimit) {
                    $this->longPositionProfits[$iteration] = -$this->stopLossLimit - $this->spread;
                    $this->numberOfIterationBelowStopLoss++;
                }

                $this->shortPositionsPivotPoints[$iteration] = $this->getShortPositionPivotPoint(
                    $currentIterationItem,
                    $meanHighLowDifferences[$iteration]
                );

                $isBelow = $this->isBelowShortPositionSellStop(
                    $currentIterationItem,
                    $this->shortPositionsPivotPoints[$iteration]
                );

                if ($isBelow) {
                    $this->numberOfShortPositions++;
                    $this->shortPositionProfits[$iteration] = $this->shortPositionsPivotPoints[$iteration] - $currentIterationItem->getOhlc(
                        )->getClose() - $this->spread;
                }

                if (!empty($this->shortPositionProfits[$iteration]) && $this->shortPositionProfits[$iteration] < -$this->stopLossLimit) {
                    $this->shortPositionProfits[$iteration] = -$this->stopLossLimit - $this->spread;
                    $this->numberOfIterationBelowStopLoss++;
                }
            }
        }

        $output
            ->setCalculationParams($params)
            ->setNumberOfIteration($numberOfIterations)
            ->setNumberOfLongPositions($this->numberOfLongPositions)
            ->setNumberOfShortPositions($this->numberOfShortPositions)
            ->setSl($this->numberOfIterationBelowStopLoss)
            ->setZl($this->longPositionProfits)
            ->setZs($this->shortPositionProfits);

        return $output;
    }

    public function sumResult(CalculationOutput $calculationOutput): SumResultOutput
    {
        $sumResultOutput = new SumResultOutput();

        $zsl = $this->cumulativeSum($calculationOutput->getZl());
        $zss = $this->cumulativeSum($calculationOutput->getZs());
        $zcum = [];

        foreach ($zsl as $index => $value) {
            $zcum[] = $value + $zss[$index];
        }

        if (end($zcum) > $calculationOutput->getCalculationParams()->getRec()) {
            $calculationOutput->getCalculationParams()->setRec(end($zcum));

            $sumResultOutput
                ->setZr($zcum)
                ->setZlr($zsl)
                ->setZsr($zss)
                ->setParopt(
                    [
                        $calculationOutput->getCalculationParams()->getFactor(),
                        $calculationOutput->getNumberOfIteration(),
                        $calculationOutput->getNumberOfLongPositions(),
                        $calculationOutput->getNumberOfShortPositions(),
                        $calculationOutput->getSl(),
                    ]
                );
        }

        return $sumResultOutput;
    }

    public function calculateRecordResult(SumResultOutput $params): array
    {
        $obni = [];
        $mloc = [];
        $zr = $params->getZr();
        $zrSize = count($zr);

        for ($j = 1; $j < $zrSize; $j++) {
            $obni[$j] = 0;
            $mloc[$j] = max(array_slice($zr, 0, $j));

            if ($params->getZr()[$j] < $mloc[$j]) {
                $obni[$j] = $mloc[$j] - $zr[$j];
            }
        }

        return $obni;
    }

    #[Pure] private function calculateHighLowDifference(Candlestick $item): float
    {
        return $item->getOhlc()->getHigh() - $item->getOhlc()->getLow();
    }

    #[Pure] private function isCalculationBufferReached(int $iteration): bool
    {
        return $iteration >= $this->iterationOffset + self::DEFAULT_CALCULATION_BUFFER;
    }

    #[Pure] private function getMeanHighLowDifference(array $highLowDifferences): float
    {
        return $this->mean(array_slice($highLowDifferences, -6, 6));
    }

    #[Pure] private function getLongPositionPivotPoint(Candlestick $item, float $highLowDifferenceMean): float
    {
        return $item->getOhlc()->getOpen() + $this->factor * $highLowDifferenceMean;
    }

    #[Pure] private function getShortPositionPivotPoint(Candlestick $item, float $highLowDifferenceMean): float
    {
        return $item->getOhlc()->getOpen() - $this->factor * $highLowDifferenceMean;
    }

    #[Pure] private function isBelowShortPositionSellStop(Candlestick $item, float $shortPositionsSellStop): bool
    {
        return $item->getOhlc()->getLow() < $shortPositionsSellStop;
    }

    /**
     * @throws StrategyException
     */
    private function checkMarketData(array $marketData): void
    {
        if (empty($marketData)) {
            throw new StrategyException(
                StrategyException::STRATEGY_EXCEPTION_MESSAGES[StrategyException::EMPTY_MARKET_DATA_EXCEPTION],
                StrategyException::EMPTY_MARKET_DATA_EXCEPTION
            );
        }
    }
}
