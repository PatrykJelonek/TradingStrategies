<?php

namespace TradingStrategies\Structures;

class CalculationConfig
{
    public int $offset = 0;
    public int $buffer = 0;

    public function __construct(int $iterationsOffset, int $calculationBuffer)
    {
        $this->offset = $iterationsOffset;
        $this->buffer = $calculationBuffer;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): CalculationConfig
    {
        $this->offset = $offset;
        return $this;
    }

    public function getBuffer(): int
    {
        return $this->buffer;
    }

    public function setBuffer(int $buffer): CalculationConfig
    {
        $this->buffer = $buffer;
        return $this;
    }

    public function isBufferReached(int $iteration): bool
    {
        return $iteration >= $this->offset + $this->buffer;
    }
}