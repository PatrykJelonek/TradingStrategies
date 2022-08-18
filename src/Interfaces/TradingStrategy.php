<?php

namespace TradingStrategies\Interfaces;

use TradingStrategies\Strategies\StrategyException;

interface TradingStrategy
{
    /**
     * @throws StrategyException
     */
    public function calculatePivots(): void;
}