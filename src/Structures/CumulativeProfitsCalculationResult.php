<?php

namespace TradingStrategies\Structures;

class CumulativeProfitsCalculationResult
{
    public array $cumulativeLongPositionsProfits = [];
    public array $cumulativeShortPositionsProfits = [];
    public array $cumulativeLongAndShortPositionsProfits = [];

    public function __construct(
        array $cumulativeLongPositionsProfits,
        array $cumulativeShortPositionsProfits,
        array $cumulativeLongAndShortPositionsProfits
    ) {
        $this->cumulativeLongPositionsProfits = $cumulativeLongPositionsProfits;
        $this->cumulativeShortPositionsProfits = $cumulativeShortPositionsProfits;
        $this->cumulativeLongAndShortPositionsProfits = $cumulativeLongAndShortPositionsProfits;
    }

    public function getCumulativeLongPositionsProfits(): array
    {
        return $this->cumulativeLongPositionsProfits;
    }

    public function setCumulativeLongPositionsProfits(array $cumulativeLongPositionsProfits): CumulativeProfitsCalculationResult
    {
        $this->cumulativeLongPositionsProfits = $cumulativeLongPositionsProfits;
        return $this;
    }

    public function getCumulativeShortPositionsProfits(): array
    {
        return $this->cumulativeShortPositionsProfits;
    }

    public function setCumulativeShortPositionsProfits(array $cumulativeShortPositionsProfits): CumulativeProfitsCalculationResult
    {
        $this->cumulativeShortPositionsProfits = $cumulativeShortPositionsProfits;
        return $this;
    }

    public function getCumulativeLongAndShortPositionsProfits(): array
    {
        return $this->cumulativeLongAndShortPositionsProfits;
    }

    public function setCumulativeLongAndShortPositionsProfits(array $cumulativeLongAndShortPositionsProfits
    ): CumulativeProfitsCalculationResult {
        $this->cumulativeLongAndShortPositionsProfits = $cumulativeLongAndShortPositionsProfits;
        return $this;
    }

}