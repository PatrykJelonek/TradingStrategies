<?php

namespace TradingStrategies\Structures;

class ExchangeConfig
{
    public const DEFAULT_SPREAD = 1.0;

    public float $spread;

    public function __construct(float $spread = self::DEFAULT_SPREAD)
    {
        $this->spread = $spread;
    }

    public function getSpread(): float
    {
        return $this->spread;
    }

    public function setSpread(float $spread): ExchangeConfig
    {
        $this->spread = $spread;
        return $this;
    }
}