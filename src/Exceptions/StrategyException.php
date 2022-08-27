<?php

namespace TradingStrategies\Exceptions;

class StrategyException extends \Exception
{
    public const EMPTY_MARKET_DATA_EXCEPTION = 'empty_data_exception';

    public const STRATEGY_EXCEPTION_MESSAGES = [
      self::EMPTY_MARKET_DATA_EXCEPTION => 'Not set market data.'
    ];
}