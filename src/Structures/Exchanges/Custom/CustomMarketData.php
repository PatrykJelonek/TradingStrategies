<?php

namespace TradingStrategies\Structures\Exchanges\Custom;

use TradingStrategies\Exceptions\MarketDataException;
use TradingStrategies\Structures\Candlestick;
use TradingStrategies\Structures\MarketData;

class CustomMarketData extends MarketData
{
    /**
     * @throws MarketDataException
     */
    public function readMarketDataFromJson(string $pathToJson): MarketData
    {
        $jsonFileContent = file_get_contents($pathToJson);

        if (empty($jsonFileContent)) {
            throw new MarketDataException('Error while parsing json');
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
        } catch (\JsonException | MarketDataException $e) {
            throw new MarketDataException('Error while parsing json');
        }

        return $this;
    }
}