<?php

namespace TradingStrategies\Strategies\Stuckey;

use JetBrains\PhpStorm\Pure;
use TradingStrategies\Interfaces\TradingStrategy;
use TradingStrategies\Strategies\StrategyException;
use TradingStrategies\Structures\CalculationOutput;
use TradingStrategies\Structures\CalculationParams;
use TradingStrategies\Structures\SumResultOutput;
use TradingStrategies\Traits\CumulativeSumTrait;
use TradingStrategies\Traits\MeanTrait;
use TradingStrategies\Structures\Item;

/**
 * Class to calculate trading pivots by Stuckey's strategy
 */
class StuckeyStrategy implements TradingStrategy
{
    use MeanTrait, CumulativeSumTrait;

    private const DEFAULT_FACTOR = 0.5;
    private const DEFAULT_ITERATION_OFFSET = 3899;
    private const DEFAULT_SPREAD = 1;
    private const DEFAULT_CALCULATION_BUFFER = 100;

    /**
     * @var Item[]
     */
    private array $data;
    private int $dataSize;

    private float $iterationOffset = self::DEFAULT_ITERATION_OFFSET;
    private float $calculationBuffer = self::DEFAULT_CALCULATION_BUFFER;
    private float $spread = self::DEFAULT_SPREAD;
    private float $factor = self::DEFAULT_FACTOR;

    private int $numberOfLongPositions = 0;
    private int $numberOfShortPositions = 0;
    private int $numberOfIterations = 0;

    private int $rec = -1111;
    private int $SL = 50;
    private int $sl = 0;

    private array $longPositionsSellStop = [];
    private array $shortPositionsSellStop = [];

    private array $zl = [];
    private array $zs = [];

    /**
     * @param Item[] $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->dataSize = count($data);
    }

    public function __invoke()
    {
        try {
//            $calculationParams = new CalculationParams();
//            $calculationParams
//                ->setStockData($this->data)
//                ->setStockDataSize($this->dataSize)
//                ->setCalculationOffset(self::DEFAULT_ITERATION_OFFSET)
//                ->setCalculationBuffer(self::DEFAULT_CALCULATION_BUFFER)
//                ->setFactor(self::DEFAULT_FACTOR);
//
//            $calculationOutput = $this->calculatePivots($calculationParams);
//            $sumResult = $this->sumResult($calculationOutput);
        } catch (StrategyException $exception) {
            echo "[{$exception->getCode()}] - {$exception->getMessage()}";
        }
    }

    public function calculatePivots(CalculationParams $params): CalculationOutput
    {
        $data = $params->getMarketData();
        $output = new CalculationOutput();

        if (empty($data)) {
            throw new StrategyException(
                StrategyException::STRATEGY_EXCEPTION_MESSAGES[StrategyException::EMPTY_DATA_EXCEPTION],
                StrategyException::EMPTY_DATA_EXCEPTION
            );
        }

        $highLowDifferences = [];
        $meanHighLowDifferences = [];

        for ($iteration = $params->getCalculationOffset(); $iteration < $params->getMarketDataSize(); $iteration++) {
            $this->numberOfIterations++;
            $this->zl[$iteration] = 0;
            $this->zs[$iteration] = 0;
            $currentIterationItem = $data[$iteration];

            $highLowDifferences[$iteration] = $this->calculateHighLowDifference($data[$iteration - 1]);

            if ($this->isCalculationBufferReached($iteration)) {
                $meanHighLowDifferences[$iteration] = $this->getMeanHighLowDifference($highLowDifferences);

                $this->longPositionsSellStop[$iteration] = $this->getLongPositionSellStop(
                    $currentIterationItem,
                    $meanHighLowDifferences[$iteration]
                );

                if ($currentIterationItem->getOhlc()->getHigh() > $this->longPositionsSellStop[$iteration]) {
                    $this->numberOfLongPositions++;
                    $this->zl[$iteration] = $currentIterationItem->getOhlc()->getClose(
                        ) - $this->longPositionsSellStop[$iteration] - $this->spread;
                }

                if (!empty($this->zl[$iteration]) && $this->zl[$iteration] < -$this->SL) {
                    $this->zl[$iteration] = -$this->SL - $this->spread;
                    $this->sl++;
                }

                $this->shortPositionsSellStop[$iteration] = $this->getShortPositionSellStop(
                    $currentIterationItem,
                    $meanHighLowDifferences[$iteration]
                );

                $isBelow = $this->isBelowShortPositionSellStop(
                    $currentIterationItem,
                    $this->shortPositionsSellStop[$iteration]
                );

                if ($isBelow) {
                    $this->numberOfShortPositions++;
                    $this->zs[$iteration] = $this->shortPositionsSellStop[$iteration] - $currentIterationItem->getOhlc(
                        )->getClose() - $this->spread;
                }

                if (!empty($this->zs[$iteration]) && $this->zs[$iteration] < -$this->SL) {
                    $this->zs[$iteration] = -$this->SL - $this->spread;
                    $this->sl++;
                }
            }
        }

        $output
            ->setCalculationParams($params)
            ->setNumberOfIteration($this->numberOfIterations)
            ->setNumberOfLongPositions($this->numberOfLongPositions)
            ->setNumberOfShortPositions($this->numberOfShortPositions)
            ->setSl($this->sl)
            ->setZl($this->zl)
            ->setZs($this->zs);

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

    #[Pure] private function calculateHighLowDifference(Item $item): float
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

    #[Pure] private function getLongPositionSellStop(Item $item, float $highLowDifferenceMean): float
    {
        return $item->getOhlc()->getOpen() + $this->factor * $highLowDifferenceMean;
    }

    #[Pure] private function getShortPositionSellStop(Item $item, float $highLowDifferenceMean): float
    {
        return $item->getOhlc()->getOpen() - $this->factor * $highLowDifferenceMean;
    }

    #[Pure] private function isBelowShortPositionSellStop(Item $item, float $shortPositionsSellStop): bool
    {
        return $item->getOhlc()->getLow() < $shortPositionsSellStop;
    }
}
