<?php

namespace TradingStrategies\Traits;

trait ArrayMeanTrait
{
    public function arrayMean(array $array): float
    {
        $mean = 0;

        if (!empty($array)) {
            $total = 0;

            foreach ($array as $value) {
                $total += $value;
            }

            $mean = $total / count($array);
        }

        return $mean;
    }
}