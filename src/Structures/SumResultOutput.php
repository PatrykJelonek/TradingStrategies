<?php

namespace TradingStrategies\Structures;

class SumResultOutput
{
    private array $paropt;
    private array $zr;
    private array $zlr;
    private array $zsr;
    private int $lo = 0;

    public function getParopt(): array
    {
        return $this->paropt;
    }

    public function setParopt(array $paropt): SumResultOutput
    {
        $this->paropt = $paropt;
        return $this;
    }

    public function getZr(): array
    {
        return $this->zr;
    }

    public function setZr(array $zr): SumResultOutput
    {
        $this->zr = $zr;
        return $this;
    }

    public function getZlr(): array
    {
        return $this->zlr;
    }

    public function setZlr(array $zlr): SumResultOutput
    {
        $this->zlr = $zlr;
        return $this;
    }

    public function getZsr(): array
    {
        return $this->zsr;
    }

    public function setZsr(array $zsr): SumResultOutput
    {
        $this->zsr = $zsr;
        return $this;
    }

    public function getLo(): int
    {
        return $this->lo;
    }

    public function setLo(int $lo): SumResultOutput
    {
        $this->lo = $lo;
        return $this;
    }
}