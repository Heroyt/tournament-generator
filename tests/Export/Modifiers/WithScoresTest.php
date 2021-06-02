<?php


namespace Export\Modifiers;


use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\Category;
use TournamentGenerator\Export\Modifiers\WithScoresModifier;
use TournamentGenerator\Group;
use TournamentGenerator\Tournament;

class WithScoresTest extends TestCase
{

	public function invalidData() : array {
		return [
			[
				['something' => 123] // Without object
			],
			[
				['object' => new Tournament('Name')] // Invalid object
			],
			[
				[ // Objects without object
					(object) [
						'data' => 123,
					],
					(object) [
						'data' => 567,
					],
				]
			],
			[
				[ // Objects with invalid objects
					(object) [
						'object' => new Group('Group'),
						'data'   => 123,
					],
					(object) [
						'object' => new Category('Group'),
						'data'   => 567,
					],
				]
			],
		];
	}

	/**
	 * @dataProvider invalidData
	 *
	 * @param array $data
	 *
	 * @throws Exception
	 */
	public function testInvalidObjectArgument(array $data) : void {
		$this->expectException(InvalidArgumentException::class);
		WithScoresModifier::process($data);
	}

}