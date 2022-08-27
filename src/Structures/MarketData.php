<?php

namespace TradingStrategies\Structures;

use TradingStrategies\Exceptions\StrategyException;

class MarketData
{
    /**
     * @var Candlestick[]
     */
    private array $data;
    private int $size;

    /**
     * @throws StrategyException
     */
    public function __construct(array $data = [])
    {
        $this->setData($data);
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @throws StrategyException
     */
    public function setData(array $data): MarketData
    {
        if (empty($data)) {
            throw new StrategyException(
                StrategyException::STRATEGY_EXCEPTION_MESSAGES[StrategyException::EMPTY_MARKET_DATA_EXCEPTION],
                StrategyException::EMPTY_MARKET_DATA_EXCEPTION
            );
        }

        $this->data = $data;
        $this->size = count($data);

        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}