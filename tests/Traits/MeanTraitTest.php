<?php

namespace TradingStrategies\Exceptions\Tests\Traits;

use JetBrains\PhpStorm\ArrayShape;
use PHPUnit\Framework\TestCase;
use TradingStrategies\Exceptions\Traits\MeanTrait;

class MeanTraitTest extends TestCase
{
    /**
     * @covers       MeanTrait::mean
     * @dataProvider getShouldReturnExpectedMeanTestDataProvider
     */
    public function testShouldReturnExpectedMean(array $array, $expectedMean): void
    {
        /** @var MeanTrait $trait */
        $trait = $this->getMockForTrait(MeanTrait::class);
        self::assertSame($expectedMean, round($trait->mean($array), 2));
    }

    #[ArrayShape(['testCaseName' => ['array' => 'array', 'expectedMean' => 'float',]])]
    public function getShouldReturnExpectedMeanTestDataProvider(): array
    {
        return [
            '25.09' => [
                'array' => [1, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50],
                'expectedMean' => 25.09,
            ],
            '275.0' => [
                'array' => [50, 100, 150, 200, 250, 300, 350, 400, 450, 500],
                'expectedMean' => 275.0,
            ],
            'Empty array' => [
                'array' => [],
                'expectedMean' => 0.0,
            ],
        ];
    }

}