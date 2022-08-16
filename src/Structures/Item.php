<?php

namespace TradingStrategies\Structures;

class Item
{
  private ?string $date = null;
  private ?OHLC $ohlc = null;
  private float $volumen = 0.0;
  private float $lop = 0.0;
}
