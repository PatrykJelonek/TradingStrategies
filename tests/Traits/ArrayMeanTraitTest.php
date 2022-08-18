<?php

namespace TradingStrategies\Tests\Traits;

use JetBrains\PhpStorm\ArrayShape;
use PHPUnit\Framework\TestCase;
use TradingStrategies\Traits\ArrayMeanTrait;

class ArrayMeanTraitTest extends TestCase
{
    /**
     * @covers ArrayMeanTrait::arrayMean
     * @dataProvider getShouldReturnExpectedMeanTestDataProvider
     */
    public function testShouldReturnExpectedMean(array $array, $expectedMean): void
    {
        /** @var ArrayMeanTrait $trait */
        $trait = $this->getMockForTrait(ArrayMeanTrait::class);
        self::assertSame($expectedMean, round($trait->arrayMean($array), 2));
    }

    #[ArrayShape(
        [
            'testCaseName' => [
                'array' => 'array',
                'expectedMean' => 'float',
            ],
        ]
    )]
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