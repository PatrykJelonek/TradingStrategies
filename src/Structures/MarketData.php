<?php

namespace TradingStrategies\Structures;

use TradingStrategies\Exceptions\MarketDataException;

abstract class MarketData
{
    /**
     * @var Candlestick[]
     */
    private array $data;
    private int $size;

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @throws MarketDataException
     */
    public function setData(array $data): MarketData
    {
        if (empty($data)) {
            throw new MarketDataException(
                MarketDataException::STRATEGY_EXCEPTION_MESSAGES[MarketDataException::EMPTY_MARKET_DATA_EXCEPTION],
                MarketDataException::EMPTY_MARKET_DATA_EXCEPTION
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

    /**
     * @throws MarketDataException
     */
    abstract public function readMarketDataFromJson(string $pathToJson): MarketData;
}