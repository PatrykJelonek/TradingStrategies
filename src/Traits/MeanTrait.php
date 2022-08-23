<?php

namespace TradingStrategies\Traits;

trait MeanTrait
{
    public function mean(array $array): float
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