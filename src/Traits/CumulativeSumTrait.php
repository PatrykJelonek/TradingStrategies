<?php

namespace TradingStrategies\Traits;

trait CumulativeSumTrait
{
    public function cumulativeSum(array $array): array
    {
        $cumulativeSum = [];
        $sum = 0;

        foreach ($array as $value) {
            $sum += $value;
            $cumulativeSum[] = $sum;
        }

        return $cumulativeSum;
    }
}