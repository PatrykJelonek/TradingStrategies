<?php

namespace TradingStrategies\Structures;

class Item
{
    private string $date;
    private OHLC $ohlc;
    private float $volumen = 0.0;
    private float $lop = 0.0;

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): Item
    {
        $this->date = $date;
        return $this;
    }

    public function getOhlc(): OHLC
    {
        return $this->ohlc;
    }

    public function setOhlc(OHLC $ohlc): Item
    {
        $this->ohlc = $ohlc;
        return $this;
    }

    public function getVolumen(): float
    {
        return $this->volumen;
    }

    public function setVolumen(float $volumen): Item
    {
        $this->volumen = $volumen;
        return $this;
    }

    public function getLop(): float
    {
        return $this->lop;
    }

    public function setLop(float $lop): Item
    {
        $this->lop = $lop;
        return $this;
    }
}
