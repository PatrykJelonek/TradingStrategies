<?php

namespace TradingStrategies\Structures;

class OHLC
{
    private float $open = 0.0;
    private float $high = 0.0;
    private float $low = 0.0;
    private float $close = 0.0;

    public function getOpen(): float
    {
        return $this->open;
    }

    public function setOpen(float $open): OHLC
    {
        $this->open = $open;
        return $this;
    }

    public function getHigh(): float
    {
        return $this->high;
    }

    public function setHigh(float $high): OHLC
    {
        $this->high = $high;
        return $this;
    }

    public function getLow(): float
    {
        return $this->low;
    }

    public function setLow(float $low): OHLC
    {
        $this->low = $low;
        return $this;
    }

    public function getClose(): float
    {
        return $this->close;
    }

    public function setClose(float $close): OHLC
    {
        $this->close = $close;
        return $this;
    }

}
