<?php

namespace Helpers;

use PHPUnit\Framework\TestCase;
use TournamentGenerator\Helpers\Functions;

/**
 * Test helper functions
 */
class FunctionsTest extends TestCase
{

	public function powers() : array {
		return [
			[1, true, 2, 1],
			[2, true, 4, 2],
			[8192, true, 16384, 8192],
			[3, false, 4, 2],
			[15, false, 16, 8],
			[255, false, 256, 128],
			[999, false, 1024, 512],
		];
	}

	public function arrays() : array {
		return [
			[
				[
					[1]
				],
				1
			],
			[
				[
					[1, 2]
				],
				2
			],
			[
				[
					[]
				],
				0
			],
			[
				[],
				0
			],
			[
				[
					[1, 2, 3, 4, 5, 6],
					[1, 2, 3, 4, 5, 6],
				],
				12
			],
			[
				[
					[1, 2, 3],
					[1, 2, 3],
					[1, 2, 3],
					[1, 2, 3],
					[1, 2, 3],
					[1, 2, 3],
					[1, 2, 3],
					[1, 2, 3],
				],
				24
			],
		];
	}

	/**
	 * @test
	 * @dataProvider powers
	 */
	public function check_power_of_2(int $num, bool $isPower, int $nextPower, int $prevPower) : void {
		self::assertEquals($isPower, Functions::isPowerOf2($num), 'The number is'.(!$isPower ? ' not' : '').' a power of 2, but the function returned the wrong output.');
		self::assertEquals($nextPower, Functions::nextPowerOf2($num));
		self::assertEquals($prevPower, Functions::previousPowerOf2($num));
	}

	/**
	 * @dataProvider arrays
	 */
	public function testNestedCount(array $array, int $expectedCount) : void {
		self::assertEquals($expectedCount, Functions::nestedCount($array));
	}

	public function arraysToSort() : array {
		return [
			[
				[1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
				[1, 10, 3, 8, 5, 6, 7, 4, 9, 2]
			],
			[
				[1, 2, 3, 4, 5],
				[1, 5, 3, 4, 2]
			]
		];
	}

	/**
	 * @dataProvider arraysToSort
	 * 
	 * @param array $input
	 * @param array $expected
	 */
	public function testSortAlternate(array $input, array $expected) : void {
		Functions::sortAlternate($input);
		self::assertEquals($expected, $input);
	}
}
