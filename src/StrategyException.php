<?php

namespace TradingStrategies\Strategies;

class StrategyException extends \Exception
{
    public const EMPTY_DATA_EXCEPTION = 'empty_data_exception';

    public const STRATEGY_EXCEPTION_MESSAGES = [
      self::EMPTY_DATA_EXCEPTION => 'Empty data.'
    ];
}