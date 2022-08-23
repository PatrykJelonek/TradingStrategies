<?php

namespace TradingStrategies\Structures;

class CalculationParams
{
    private const DEFAULT_FACTOR = 0.5;

    /**
     * @var Item[]
     */
    private array $stockData;
    private int $stockDataSize;

    private int $calculationOffset = 0;
    private int $calculationBuffer = 0;
    private float $factor = self::DEFAULT_FACTOR;

    private float $rec = -1111.0;

    public function getStockData(): array
    {
        return $this->stockData;
    }

    public function setStockData(array $stockData): CalculationParams
    {
        $this->stockData = $stockData;
        return $this;
    }

    public function getStockDataSize(): int
    {
        return $this->stockDataSize;
    }

    public function setStockDataSize(int $stockDataSize): CalculationParams
    {
        $this->stockDataSize = $stockDataSize;
        return $this;
    }

    public function getCalculationOffset(): int
    {
        return $this->calculationOffset;
    }

    public function setCalculationOffset(int $calculationOffset): CalculationParams
    {
        $this->calculationOffset = $calculationOffset;
        return $this;
    }

    public function getCalculationBuffer(): int
    {
        return $this->calculationBuffer;
    }

    public function setCalculationBuffer(int $calculationBuffer): CalculationParams
    {
        $this->calculationBuffer = $calculationBuffer;
        return $this;
    }

    public function getFactor(): float
    {
        return $this->factor;
    }

    public function setFactor(float $factor): CalculationParams
    {
        $this->factor = $factor;
        return $this;
    }

    public function getRec(): float
    {
        return $this->rec;
    }

    public function setRec(float $rec): CalculationParams
    {
        $this->rec = $rec;
        return $this;
    }

}