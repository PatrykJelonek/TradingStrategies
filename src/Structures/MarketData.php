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

    /**
     * @throws StrategyException
     */
    public function readMarketDataFromJson(string $pathToJson): MarketData
    {
        $jsonFileContent = file_get_contents($pathToJson);

        if (empty($jsonFileContent)) {
            // @todo throw better exception
            throw new StrategyException('Error while parsing json');
        }

        try {
            $arrayFromJson = json_decode($jsonFileContent, true, 512, JSON_THROW_ON_ERROR);

            $marketData = [];
            foreach ($arrayFromJson as $candleStick) {
                $marketData[] = (new Candlestick())
                    ->setDate($candleStick['date'] ?? null)
                    ->setLop($candleStick['lop'] ?? null)
                    ->setVolumen($candleStick['volumen'] ?? null)
                    ->setHigh($candleStick['high'] ?? null)
                    ->setLow($candleStick['low'] ?? null)
                    ->setOpen($candleStick['open'] ?? null)
                    ->setClose($candleStick['close'] ?? null);
            }

            $this->setData($marketData);
        } catch (\JsonException|StrategyException $e) {
            // @todo throw better exception
            throw new StrategyException('Error while parsing json');
        }

        return $this;
    }

    public function readMarketDataForBitfinex(string $pathToJson): MarketData
    {
        $jsonFileContent = file_get_contents($pathToJson);
        $arrayFromJson = json_decode($jsonFileContent, true, 512, JSON_THROW_ON_ERROR);

        $marketData = [];
        foreach ($arrayFromJson as $candleStick) {
            $marketData[] = (new Candlestick())
                ->setDate($candleStick[0])
                ->setVolumen($candleStick[5] ?? null)
                ->setHigh($candleStick[3] ?? null)
                ->setLow($candleStick[4] ?? null)
                ->setOpen($candleStick[1] ?? null)
                ->setClose($candleStick[2] ?? null);
        }

        $this->setData($marketData);

        return $this;
    }
}