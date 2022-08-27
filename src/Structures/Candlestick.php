<?php

namespace TradingStrategies\Structures;

use JetBrains\PhpStorm\Pure;

class Candlestick
{
    private string $date;
    private float $open = 0.0;
    private float $high = 0.0;
    private float $low = 0.0;
    private float $close = 0.0;
    private float $volumen = 0.0;
    private float $lop = 0.0;

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): Candlestick
    {
        $this->date = $date;
        return $this;
    }

    public function getOpen(): float
    {
        return $this->open;
    }

    public function setOpen(float $open): Candlestick
    {
        $this->open = $open;
        return $this;
    }

    public function getHigh(): float
    {
        return $this->high;
    }

    public function setHigh(float $high): Candlestick
    {
        $this->high = $high;
        return $this;
    }

    public function getLow(): float
    {
        return $this->low;
    }

    public function setLow(float $low): Candlestick
    {
        $this->low = $low;
        return $this;
    }

    public function getClose(): float
    {
        return $this->close;
    }

    public function setClose(float $close): Candlestick
    {
        $this->close = $close;
        return $this;
    }

    public function getVolumen(): float
    {
        return $this->volumen;
    }

    public function setVolumen(float $volumen): Candlestick
    {
        $this->volumen = $volumen;
        return $this;
    }

    public function getLop(): float
    {
        return $this->lop;
    }

    public function setLop(float $lop): Candlestick
    {
        $this->lop = $lop;
        return $this;
    }

    #[Pure] public function getHighLowDifference(): float
    {
        return $this->getHigh() - $this->getLow();
    }
}
