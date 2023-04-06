<?php

namespace TradingStrategies\Exchanges\Poloniex;

use TradingStrategies\Exceptions\MarketDataException;
use TradingStrategies\Structures\Candlestick;
use TradingStrategies\Structures\MarketData;

class PoloniexMarketData extends MarketData
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
                    ->setDate($candleStick[9])
                    ->setHigh($candleStick[1] ?? null)
                    ->setLow($candleStick[0] ?? null)
                    ->setOpen($candleStick[2] ?? null)
                    ->setClose($candleStick[3] ?? null);
            }

            $this->setData($marketData);
        } catch (\JsonException $exception) {
            throw new MarketDataException('Parsing error');
        }

        return $this;
    }
}