<?php

namespace TradingStrategies\Exceptions\Tests\Traits;

use JetBrains\PhpStorm\ArrayShape;
use PHPUnit\Framework\TestCase;
use TradingStrategies\Exceptions\Traits\CumulativeSumTrait;

class CumulativeSumTraitTest extends TestCase
{
    /**
     * @dataProvider getShouldReturnExpectedCumulativeSumTestDataProvider
     */
    public function testShouldReturnExpectedCumulativeSum(array $array, array $expectedResult): void
    {
        /** @var CumulativeSumTrait $trait */
        $trait = $this->getMockForTrait(CumulativeSumTrait::class);

        self::assertSame($expectedResult, $trait->cumulativeSum($array));
    }

    #[ArrayShape(['testCaseName' => ['array' => 'array', 'expectedResult' => 'array']])]
    public function getShouldReturnExpectedCumulativeSumTestDataProvider(): array
    {
        return [
            '[1:10]' => [
                'array' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                'expectedResult' => [1, 3, 6, 10, 15, 21, 28, 36, 45, 55],
            ],
            '10,20,30,40,50,60' => [
                'array' => [10, 20, 30, 40, 50, 60],
                'expectedResult' => [10, 30, 60, 100, 150, 210],
            ],
        ];
    }

}