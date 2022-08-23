<?php

namespace TradingStrategies\Interfaces;

use TradingStrategies\Strategies\StrategyException;
use TradingStrategies\Structures\CalculationOutput;
use TradingStrategies\Structures\CalculationParams;
use TradingStrategies\Structures\SumResultOutput;

interface TradingStrategy
{
    /**
     * @throws StrategyException
     */
    public function calculatePivots(CalculationParams $params): CalculationOutput;

    public function sumResult(CalculationOutput $calculationOutput): SumResultOutput;

    public function calculateRecordResult(): float;
}