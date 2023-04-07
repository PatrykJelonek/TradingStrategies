<?php

namespace TradingStrategies\Strategies;

use TradingStrategies\Structures\MarketAnalysisResult;
use TradingStrategies\Structures\CalculationConfig;
use TradingStrategies\Structures\ExchangeConfig;
use TradingStrategies\Structures\MarketData;
use TradingStrategies\Structures\CumulativeProfitsCalculationResult;
use TradingStrategies\Traits\CumulativeSumTrait;
use TradingStrategies\Traits\MeanTrait;

abstract class TradingStrategy
{
    use MeanTrait, CumulativeSumTrait;

    protected int $numberOfIteration = 0;

    protected ExchangeConfig $exchangeConfig;
    protected CalculationConfig $calculationConfig;
    protected MarketData $marketData;

    protected int $numberOfLongPositions = 0;
    protected int $numberOfShortPositions = 0;
    protected int $numberOfStopsOrders = 0;

    protected ?float $stopLimit = 50.0;
    protected float $rec = -1111.0;
    protected float $factor = 0.5;

    protected array $longPositionsPivotPoints = [];
    protected array $shortPositionsPivotPoints = [];

    protected array $longPositionsProfits = [];
    protected array $shortPositionsProfits = [];

    public function __construct(
        ExchangeConfig $exchangeConfig,
        CalculationConfig $calculationConfig,
        MarketData $marketData
    ) {
        $this->exchangeConfig = $exchangeConfig;
        $this->calculationConfig = $calculationConfig;
        $this->marketData = $marketData;
    }

    public function __invoke(): CumulativeProfitsCalculationResult
    {
        $this->analyzeMarketByStrategy();
        return $this->calculateCumulativeProfits();
    }

    abstract public function analyzeMarketByStrategy(): MarketAnalysisResult;

    public function calculateCumulativeProfits(): CumulativeProfitsCalculationResult
    {
        $cumulativeLongPositionsProfits = $this->cumulativeSum($this->longPositionsProfits);
        $cumulativeShortPositionsProfits = $this->cumulativeSum($this->shortPositionsProfits);
        $cumulativeLongAndShortPositionsProfits = [];

        foreach ($cumulativeLongPositionsProfits as $index => $longPositionsProfit) {
            $cumulativeLongAndShortPositionsProfits[] = $longPositionsProfit + $cumulativeShortPositionsProfits[$index];
        }

        $lastLongAndShortPositionsProfit = end($cumulativeLongAndShortPositionsProfits);
        if ($lastLongAndShortPositionsProfit > $this->rec) {
            $this->rec = $lastLongAndShortPositionsProfit;
        }

        return new CumulativeProfitsCalculationResult(
            $cumulativeLongPositionsProfits,
            $cumulativeShortPositionsProfits,
            $cumulativeLongAndShortPositionsProfits
        );
    }

    public function getTheBiggestProfit(array $cumulativeLongAndShortPositionsProfits): float
    {
        $theBiggestProfitPerIteration = [];
        $numberOfProfitsEntries = count($cumulativeLongAndShortPositionsProfits);

        for ($i = 1; $i < $numberOfProfitsEntries; $i++) {
            $theBiggestProfitPerIteration[$i] = 0;
            $maxProfitPerIteration[$i] = max(array_slice($cumulativeLongAndShortPositionsProfits, 0, $i));

            if ($cumulativeLongAndShortPositionsProfits[$i] < $maxProfitPerIteration[$i]) {
                $theBiggestProfitPerIteration[$i] = $maxProfitPerIteration[$i] - $cumulativeLongAndShortPositionsProfits[$i];
            }
        }

        return max($theBiggestProfitPerIteration);
    }

    public function getProfitRatio(array $cumulativeLongAndShortPositionsProfits): float
    {
        $theBiggestProfit = $this->getTheBiggestProfit($cumulativeLongAndShortPositionsProfits);

        if ($theBiggestProfit === 0.0) {
            throw new \DivisionByZeroError();
        }

        return end($cumulativeLongAndShortPositionsProfits) / $theBiggestProfit;
    }

    public function getFactor(): float
    {
        return $this->factor;
    }

    public function setFactor(float $factor): TradingStrategy
    {
        $this->factor = $factor;
        return $this;
    }

    public function getStopLimit(): ?float
    {
        return $this->stopLimit;
    }

    public function setStopLimit(?float $stopLimit): TradingStrategy
    {
        $this->stopLimit = $stopLimit;
        return $this;
    }
}