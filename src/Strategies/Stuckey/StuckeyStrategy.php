<?php

namespace TradingStrategies\Strategies\Stuckey;

class StuckeyStrategy
{
  private const DEFAULT_FACTOR = 0.5;
  private const DEFAULT_ITERATION_OFFSET = 3900;
  private const DEFAULT_SPREAD = 1;
  
  private array $data = [];
  private int $dataSize = 0;
  private float $factor = self::DEFAULT_FACTOR;
  private float $iterationOffset = self::DEFAULT_ITERATION_OFFSET;
  private float $spread = self::DEFAULT_SPREAD;
  
  /**
  * @param Item[] $data
  */
  public function __construct(
    array $data, 
    float $factor = self::DEFAULT_FACTOR, 
    float $iterationOffset = self::DEFAULT_ITERATION_OFFSET, 
    float $spread = self::DEFAULT_SPREAD
  ) {
    $this->data = $data;
    $this->factor = $factor;
    $this->iterationOffset = $iterationOffset;
    $this->spread = $spread;
    $this->dataSize = count($data);
  }
}
