<?php

namespace TradingStrategies\Interfaces;

use TradingStrategies\Strategies\StrategyException;
use TradingStrategies\Structures\CalculationOutput;

interface TradingStrategy
{
    /**
     * @throws StrategyException
     */
    public function calculatePivots(): CalculationOutput;
}