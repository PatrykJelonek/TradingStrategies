<?php

namespace TradingStrategies\Structures;

class CalculationOutput
{
    public CalculationParams $calculationParams;

    private int $numberOfIteration = 0;
    private int $numberOfLongPositions = 0;
    private int $numberOfShortPositions = 0;
    private int $sl = 0;

    private array $zl = [];
    private array $zs = [];

    public function getCalculationParams(): CalculationParams
    {
        return $this->calculationParams;
    }

    public function setCalculationParams(CalculationParams $calculationParams): CalculationOutput
    {
        $this->calculationParams = $calculationParams;
        return $this;
    }

    public function getNumberOfIteration(): int
    {
        return $this->numberOfIteration;
    }

    public function setNumberOfIteration(int $numberOfIteration): CalculationOutput
    {
        $this->numberOfIteration = $numberOfIteration;
        return $this;
    }

    public function getNumberOfLongPositions(): int
    {
        return $this->numberOfLongPositions;
    }

    public function setNumberOfLongPositions(int $numberOfLongPositions): CalculationOutput
    {
        $this->numberOfLongPositions = $numberOfLongPositions;
        return $this;
    }

    public function getNumberOfShortPositions(): int
    {
        return $this->numberOfShortPositions;
    }

    public function setNumberOfShortPositions(int $numberOfShortPositions): CalculationOutput
    {
        $this->numberOfShortPositions = $numberOfShortPositions;
        return $this;
    }

    public function getSl(): int
    {
        return $this->sl;
    }

    public function setSl(int $sl): CalculationOutput
    {
        $this->sl = $sl;
        return $this;
    }

    public function getZl(): array
    {
        return $this->zl;
    }

    public function setZl(array $zl): CalculationOutput
    {
        $this->zl = $zl;
        return $this;
    }

    public function getZs(): array
    {
        return $this->zs;
    }

    public function setZs(array $zs): CalculationOutput
    {
        $this->zs = $zs;
        return $this;
    }

}