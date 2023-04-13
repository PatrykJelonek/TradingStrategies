<?php

namespace TradingStrategies\Structures;

class MarketAnalysisResult
{
    private ?float $factor = null;
    private int $numberOfIterations = 0;
    private int $numberOfLongPositions = 0;
    private int $numberOfShortPositions = 0;
    private ?int $numberOfOpeningBelowYesterdayStop = null;
    private int $numberOfStopLossOrders = 0;
    private int $biggestLongPositionProfit = 0;
    private int $biggestShortPositionProfit = 0;
    private int $biggestLongPositionLoss = 0;
    private int $biggestShortPositionLoss = 0;

    public function getFactor(): ?float
    {
        return $this->factor;
    }

    public function setFactor(?float $factor): MarketAnalysisResult
    {
        $this->factor = $factor;
        return $this;
    }

    public function getNumberOfIterations(): int
    {
        return $this->numberOfIterations;
    }

    public function setNumberOfIterations(int $numberOfIterations): MarketAnalysisResult
    {
        $this->numberOfIterations = $numberOfIterations;
        return $this;
    }

    public function getNumberOfLongPositions(): int
    {
        return $this->numberOfLongPositions;
    }

    public function setNumberOfLongPositions(int $numberOfLongPositions): MarketAnalysisResult
    {
        $this->numberOfLongPositions = $numberOfLongPositions;
        return $this;
    }

    public function getNumberOfShortPositions(): int
    {
        return $this->numberOfShortPositions;
    }

    public function setNumberOfShortPositions(int $numberOfShortPositions): MarketAnalysisResult
    {
        $this->numberOfShortPositions = $numberOfShortPositions;
        return $this;
    }

    public function getNumberOfOpeningBelowYesterdayStop(): ?int
    {
        return $this->numberOfOpeningBelowYesterdayStop;
    }

    public function setNumberOfOpeningBelowYesterdayStop(?int $numberOfOpeningBelowYesterdayStop): MarketAnalysisResult
    {
        $this->numberOfOpeningBelowYesterdayStop = $numberOfOpeningBelowYesterdayStop;
        return $this;
    }

    public function getNumberOfStopLossOrders(): int
    {
        return $this->numberOfStopLossOrders;
    }

    public function setNumberOfStopLossOrders(int $numberOfStopLossOrders): MarketAnalysisResult
    {
        $this->numberOfStopLossOrders = $numberOfStopLossOrders;
        return $this;
    }

    public function getBiggestLongPositionProfit(): int
    {
        return $this->biggestLongPositionProfit;
    }

    public function setBiggestLongPositionProfit(int $biggestLongPositionProfit): MarketAnalysisResult
    {
        $this->biggestLongPositionProfit = $biggestLongPositionProfit;
        return $this;
    }

    public function getBiggestShortPositionProfit(): int
    {
        return $this->biggestShortPositionProfit;
    }

    public function setBiggestShortPositionProfit(int $biggestShortPositionProfit): MarketAnalysisResult
    {
        $this->biggestShortPositionProfit = $biggestShortPositionProfit;
        return $this;
    }

    public function getBiggestLongPositionLoss(): int
    {
        return $this->biggestLongPositionLoss;
    }

    public function setBiggestLongPositionLoss(int $biggestLongPositionLoss): MarketAnalysisResult
    {
        $this->biggestLongPositionLoss = $biggestLongPositionLoss;
        return $this;
    }

    public function getBiggestShortPositionLoss(): int
    {
        return $this->biggestShortPositionLoss;
    }

    public function setBiggestShortPositionLoss(int $biggestShortPositionLoss): MarketAnalysisResult
    {
        $this->biggestShortPositionLoss = $biggestShortPositionLoss;
        return $this;
    }

}