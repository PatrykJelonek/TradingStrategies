<?php

namespace TradingStrategies\Interfaces;

use TradingStrategies\Exceptions\StrategyException;
use TradingStrategies\Exceptions\Structures\CalculationOutput;
use TradingStrategies\Exceptions\Structures\CalculationParams;
use TradingStrategies\Exceptions\Structures\SumResultOutput;

interface TradingStrategy
{
    /**
     * @throws StrategyException
     */
    public function calculatePivots(CalculationParams $params): CalculationOutput;

    public function sumResult(CalculationOutput $calculationOutput): SumResultOutput;

    public function calculateRecordResult(SumResultOutput $params): array;
}