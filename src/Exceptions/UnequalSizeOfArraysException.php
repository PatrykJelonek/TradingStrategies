<?php

namespace TradingStrategies\Exceptions;

use JetBrains\PhpStorm\Pure;
use Throwable;

class UnequalSizeOfArraysException extends \Exception
{
    #[Pure] public function __construct($message = 'Unequal sizes of arrays', $code = 'unequal_sizes_of_arrays')
    {
        parent::__construct($message, $code);
    }
}