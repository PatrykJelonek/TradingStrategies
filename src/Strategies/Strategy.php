<?php

namespace TradingStrategies\Strategies;

use TradingStrategies\Structures\CalculationOutput;
use TradingStrategies\Structures\CalculationParams;

abstract class Strategy
{
    /**
     * @throws StrategyException
     */
    abstract public function calculatePivots(CalculationParams $params): CalculationOutput;

    public function calculateSummary(CalculationOutput $params): array
    {
        # @todo Implement calculateSummary function
        return [];
    }
}