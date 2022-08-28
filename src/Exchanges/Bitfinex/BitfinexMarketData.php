<?php

namespace TradingStrategies\Exchanges\Bitfinex;

use TradingStrategies\Exceptions\MarketDataException;
use TradingStrategies\Structures\Candlestick;
use TradingStrategies\Structures\MarketData;

/**
 * @example ./Data/bitfinex_btc_usd.json
 */
class BitfinexMarketData extends MarketData
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
                    ->setDate($candleStick[0])
                    ->setVolumen($candleStick[5] ?? null)
                    ->setHigh($candleStick[3] ?? null)
                    ->setLow($candleStick[4] ?? null)
                    ->setOpen($candleStick[1] ?? null)
                    ->setClose($candleStick[2] ?? null);
            }

            $this->setData($marketData);
        } catch (\JsonException $exception) {
            throw new MarketDataException('Parsing error');
        }

        return $this;
    }
}