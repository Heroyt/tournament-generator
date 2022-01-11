<?php


namespace Import;


use PHPUnit\Framework\TestCase;
use TournamentGenerator\Constants;
use TournamentGenerator\Import\ImportValidator;
use TournamentGenerator\Import\InvalidImportDataException;

class ImportValidatorTest extends TestCase
{

	public function getExports() : array {
		return [
			[
				[
					'tournament'   => (object) [
						'type'       => 'general',
						'name'       => 'Tournament',
						'skip'       => false,
						'timing'     => (object) [
							'play'         => 0,
							'gameWait'     => 0,
							'categoryWait' => 0,
							'roundWait'    => 0,
							'expectedTime' => 0,
						],
						'categories' => [],
						'rounds'     => [1, 2],
						'groups'     => [1, 2, 3, 4],
						'teams'      => [0, 1, 2, 3, 4, 5, 6, 7],
						'games'      => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
					],
					'categories'   => [],
					'rounds'       => [
						1 => (object) [
							'id'     => 1,
							'name'   => 'Round 1',
							'skip'   => false,
							'played' => true,
							'groups' => [1, 2],
							'teams'  => [0, 1, 2, 3, 4, 5, 6, 7],
							'games'  => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
						],
						2 => (object) [
							'id'     => 2,
							'name'   => 'Round 2',
							'skip'   => false,
							'played' => false,
							'groups' => [3, 4],
							'teams'  => [],
							'games'  => [],
						],
					],
					'groups'       => [
						1 => (object) [
							'id'      => 1,
							'name'    => 'Group 1',
							'type'    => Constants::ROUND_ROBIN,
							'skip'    => false,
							'points'  => (object) [
								'win'         => 3,
								'loss'        => 0,
								'draw'        => 1,
								'second'      => 2,
								'third'       => 1,
								'progression' => 50,
							],
							'played'  => true,
							'inGame'  => 2,
							'maxSize' => 4,
							'teams'   => [0, 1, 2, 3],
							'games'   => [1, 2, 3, 4, 5, 6],
						],
						2 => (object) [
							'id'      => 2,
							'name'    => 'Group 2',
							'type'    => Constants::ROUND_ROBIN,
							'skip'    => false,
							'points'  => (object) [
								'win'         => 3,
								'loss'        => 0,
								'draw'        => 1,
								'second'      => 2,
								'third'       => 1,
								'progression' => 50,
							],
							'played'  => true,
							'inGame'  => 2,
							'maxSize' => 4,
							'teams'   => [4, 5, 6, 7],
							'games'   => [7, 8, 9, 10, 11, 12],
						],
						3 => (object) [
							'id'      => 3,
							'name'    => 'Group 3',
							'type'    => Constants::ROUND_ROBIN,
							'skip'    => false,
							'points'  => (object) [
								'win'         => 3,
								'loss'        => 0,
								'draw'        => 1,
								'second'      => 2,
								'third'       => 1,
								'progression' => 50,
							],
							'played'  => false,
							'inGame'  => 2,
							'maxSize' => 4,
							'teams'   => [],
							'games'   => [],
						],
						4 => (object) [
							'id'      => 4,
							'name'    => 'Group 4',
							'type'    => Constants::ROUND_ROBIN,
							'skip'    => false,
							'points'  => (object) [
								'win'         => 3,
								'loss'        => 0,
								'draw'        => 1,
								'second'      => 2,
								'third'       => 1,
								'progression' => 50,
							],
							'played'  => false,
							'inGame'  => 2,
							'maxSize' => 4,
							'teams'   => [],
							'games'   => [],
						],
					],
					'progressions' => [
						(object) [
							'from'       => 1,
							'to'         => 3,
							'offset'     => 0,
							'length'     => 2,
							'progressed' => false,
							'filters'    => [],
						],
						(object) [
							'from'       => 1,
							'to'         => 4,
							'offset'     => -2,
							'length'     => null,
							'progressed' => false,
							'filters'    => [],
						],
						(object) [
							'from'       => 2,
							'to'         => 3,
							'offset'     => 0,
							'length'     => 2,
							'progressed' => false,
							'filters'    => [],
						],
						(object) [
							'from'       => 2,
							'to'         => 4,
							'offset'     => -2,
							'length'     => null,
							'progressed' => false,
							'filters'    => [],
						],
					],
				],
				true
			],
			[
				[
					'tournament'   => [
						'type'       => 'general',
						'name'       => 'Tournament',
						'skip'       => false,
						'timing'     => [
							'play'         => 0,
							'gameWait'     => 0,
							'categoryWait' => 0,
							'roundWait'    => 0,
							'expectedTime' => 0,
						],
						'categories' => [],
						'rounds'     => [1, 2],
						'groups'     => [1, 2, 3, 4],
						'teams'      => [0, 1, 2, 3, 4, 5, 6, 7],
						'games'      => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
					],
					'categories'   => [],
					'rounds'       => [
						1 => [
							'id'     => 1,
							'name'   => 'Round 1',
							'skip'   => false,
							'played' => true,
							'groups' => [1, 2],
							'teams'  => [0, 1, 2, 3, 4, 5, 6, 7],
							'games'  => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
						],
						2 => [
							'id'     => 2,
							'name'   => 'Round 2',
							'skip'   => false,
							'played' => false,
							'groups' => [3, 4],
							'teams'  => [],
							'games'  => [],
						],
					],
					'groups'       => [
						1 => [
							'id'      => 1,
							'name'    => 'Group 1',
							'type'    => Constants::ROUND_ROBIN,
							'skip'    => false,
							'points'  => [
								'win'         => 3,
								'loss'        => 0,
								'draw'        => 1,
								'second'      => 2,
								'third'       => 1,
								'progression' => 50,
							],
							'played'  => true,
							'inGame'  => 2,
							'maxSize' => 4,
							'teams'   => [0, 1, 2, 3],
							'games'   => [1, 2, 3, 4, 5, 6],
						],
						2 => [
							'id'      => 2,
							'name'    => 'Group 2',
							'type'    => Constants::ROUND_ROBIN,
							'skip'    => false,
							'points'  => [
								'win'         => 3,
								'loss'        => 0,
								'draw'        => 1,
								'second'      => 2,
								'third'       => 1,
								'progression' => 50,
							],
							'played'  => true,
							'inGame'  => 2,
							'maxSize' => 4,
							'teams'   => [4, 5, 6, 7],
							'games'   => [7, 8, 9, 10, 11, 12],
						],
						3 => [
							'id'      => 3,
							'name'    => 'Group 3',
							'type'    => Constants::ROUND_ROBIN,
							'skip'    => false,
							'points'  => [
								'win'         => 3,
								'loss'        => 0,
								'draw'        => 1,
								'second'      => 2,
								'third'       => 1,
								'progression' => 50,
							],
							'played'  => false,
							'inGame'  => 2,
							'maxSize' => 4,
							'teams'   => [],
							'games'   => [],
						],
						4 => [
							'id'      => 4,
							'name'    => 'Group 4',
							'type'    => Constants::ROUND_ROBIN,
							'skip'    => false,
							'points'  => [
								'win'         => 3,
								'loss'        => 0,
								'draw'        => 1,
								'second'      => 2,
								'third'       => 1,
								'progression' => 50,
							],
							'played'  => false,
							'inGame'  => 2,
							'maxSize' => 4,
							'teams'   => [],
							'games'   => [],
						],
					],
					'progressions' => [
						[
							'from'       => 1,
							'to'         => 3,
							'offset'     => 0,
							'length'     => 2,
							'progressed' => false,
							'filters'    => [],
						],
						[
							'from'       => 1,
							'to'         => 4,
							'offset'     => -2,
							'length'     => null,
							'progressed' => false,
							'filters'    => [],
						],
						[
							'from'       => 2,
							'to'         => 3,
							'offset'     => 0,
							'length'     => 2,
							'progressed' => false,
							'filters'    => [],
						],
						[
							'from'       => 2,
							'to'         => 4,
							'offset'     => -2,
							'length'     => null,
							'progressed' => false,
							'filters'    => [],
						],
					],
				],
				true
			],
			[
				[
					'tournament'   => [
						'type'       => 'general',
						'name'       => 'Tournament',
						'skip'       => false,
						'timing'     => [
							'play'         => 0,
							'gameWait'     => 0,
							'categoryWait' => 0,
							'roundWait'    => 0,
							'expectedTime' => 0,
						],
						'categories' => [],
						'rounds'     => [1, 2],
						'groups'     => [1, 2, 3, 4],
						'teams'      => [0, 1, 2, 3, 4, 5, 6, 7],
						'games'      => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
					],
					'categories'   => [],
					'rounds'       => [
						1 => [
							'id'     => 1,
							'name'   => 'Round 1',
							'skip'   => false,
							'played' => true,
							'groups' => [1, 2],
							'teams'  => [0, 1, 2, 3, 4, 5, 6, 7],
							'games'  => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
						],
						2 => [
							'id'     => 2,
							'name'   => 'Round 2',
							'skip'   => false,
							'played' => false,
							'groups' => [3, 4],
							'teams'  => [],
							'games'  => [],
						],
					],
					'groups'       => [
						1 => [
							'id'      => 1,
							'name'    => 'Group 1',
							'type'    => Constants::ROUND_ROBIN,
							'skip'    => false,
							'points'  => [
								'win'         => 3,
								'loss'        => 0,
								'draw'        => 1,
								'second'      => 2,
								'third'       => 1,
								'progression' => 50,
							],
							'played'  => true,
							'inGame'  => 2,
							'maxSize' => 4,
							'teams'   => [0, 1, 2, 3],
							'games'   => [1, 2, 3, 4, 5, 6],
						],
						2 => [
							'id'      => 2,
							'name'    => 'Group 2',
							'type'    => Constants::ROUND_ROBIN,
							'skip'    => false,
							'points'  => [
								'win'         => 3,
								'loss'        => 0,
								'draw'        => 1,
								'second'      => 2,
								'third'       => 1,
								'progression' => 50,
							],
							'played'  => true,
							'inGame'  => 2,
							'maxSize' => 4,
							'teams'   => [4, 5, 6, 7],
							'games'   => [7, 8, 9, 10, 11, 12],
						],
						3 => [
							'id'      => 3,
							'name'    => 'Group 3',
							'type'    => Constants::ROUND_ROBIN,
							'skip'    => false,
							'points'  => [
								'win'         => 3,
								'loss'        => 0,
								'draw'        => 1,
								'second'      => 2,
								'third'       => 1,
								'progression' => 50,
							],
							'played'  => false,
							'inGame'  => 2,
							'maxSize' => 4,
							'teams'   => [],
							'games'   => [],
						],
						4 => [
							'id'      => 4,
							'name'    => 'Group 4',
							'type'    => Constants::ROUND_ROBIN,
							'skip'    => false,
							'points'  => [
								'win'         => 3,
								'loss'        => 0,
								'draw'        => 1,
								'second'      => 2,
								'third'       => 1,
								'progression' => 50,
							],
							'played'  => false,
							'inGame'  => 2,
							'maxSize' => 4,
							'teams'   => [],
							'games'   => [],
						],
					],
					'progressions' => [
						[
							'from'       => 1,
							'to'         => 3,
							'offset'     => 0,
							'length'     => 2,
							'progressed' => false,
							'filters'    => [],
						],
						[
							'from'       => 1,
							'to'         => 4,
							'offset'     => -2,
							'length'     => null,
							'progressed' => false,
							'filters'    => [],
						],
						[
							'from'       => 2,
							'to'         => 3,
							'offset'     => 0,
							'length'     => 2,
							'progressed' => false,
							'filters'    => [],
						],
						[
							'from'       => 2,
							'to'         => 4,
							'offset'     => -2,
							'length'     => null,
							'progressed' => false,
							'filters'    => [],
						],
					],
					'teams'        => [],
				],
				false
			],
			[
				[],
				false,
			],
			[
				[
					'unknown' => []
				],
				false,
			],
			[
				[
					'rounds' => '',
				],
				false,
			],
			[
				[
					'tournament' => '',
				],
				false,
			],
			[
				[
					'tournament' => [
						'type'       => 'invalid',
						'name'       => 'Tournament',
						'skip'       => false,
						'timing'     => [
							'play'         => 0,
							'gameWait'     => 0,
							'categoryWait' => 0,
							'roundWait'    => 0,
							'expectedTime' => 0,
						],
						'categories' => [],
						'rounds'     => [1, 2],
						'groups'     => [1, 2, 3, 4],
						'teams'      => [0, 1, 2, 3, 4, 5, 6, 7],
						'games'      => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
					],
				],
				false
			],
			[
				[
					'tournament' => [
						'type'       => 'general',
						'name'       => 'Tournament',
						'skip'       => false,
						'timing'     => [
							'play'         => 'aaa',
							'gameWait'     => 0,
							'categoryWait' => 0,
							'roundWait'    => 0,
							'expectedTime' => 0,
						],
						'categories' => [],
						'rounds'     => [1, 2],
						'groups'     => [1, 2, 3, 4],
						'teams'      => [0, 1, 2, 3, 4, 5, 6, 7],
						'games'      => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
					],
				],
				false
			],
			[
				json_decode('{"tournament":{"type":"general","name":"Tournament","skip":false,"timing":{"play":0,"gameWait":0,"categoryWait":0,"roundWait":0,"expectedTime":0},"categories":[],"rounds":[1,2],"groups":[1,2,3,4],"teams":[0,1,2,3,4,5,6,7],"games":[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24]},"categories":[],"rounds":{"1":{"id":1,"name":"Round 1","skip":false,"played":true,"groups":[1,2],"teams":[0,1,2,3,4,5,6,7],"games":[1,2,3,4,5,6,7,8,9,10,11,12]},"2":{"id":2,"name":"Round 2","skip":false,"played":true,"groups":[3,4],"teams":[0,3,5,4,1,2,7,6],"games":[13,14,15,16,17,18,19,20,21,22,23,24]}},"groups":{"1":{"id":1,"name":"Group 1","type":"Robin-Robin group type","skip":false,"points":{"win":3,"loss":0,"draw":1,"second":2,"third":1,"progression":50},"played":true,"inGame":2,"maxSize":4,"teams":[0,1,2,3],"games":[1,2,3,4,5,6]},"2":{"id":2,"name":"Group 2","type":"Robin-Robin group type","skip":false,"points":{"win":3,"loss":0,"draw":1,"second":2,"third":1,"progression":50},"played":true,"inGame":2,"maxSize":4,"teams":[4,5,6,7],"games":[7,8,9,10,11,12]},"3":{"id":3,"name":"Group 3","type":"Robin-Robin group type","skip":false,"points":{"win":3,"loss":0,"draw":1,"second":2,"third":1,"progression":50},"played":true,"inGame":2,"maxSize":4,"teams":[0,3,5,4],"games":[13,14,15,16,17,18]},"4":{"id":4,"name":"Group 4","type":"Robin-Robin group type","skip":false,"points":{"win":3,"loss":0,"draw":1,"second":2,"third":1,"progression":50},"played":true,"inGame":2,"maxSize":4,"teams":[1,2,7,6],"games":[19,20,21,22,23,24]}},"progressions":[{"from":1,"to":3,"offset":0,"length":2,"progressed":true,"filters":[]},{"from":1,"to":4,"offset":-2,"length":null,"progressed":true,"filters":[]},{"from":2,"to":3,"offset":0,"length":2,"progressed":true,"filters":[]},{"from":2,"to":4,"offset":-2,"length":null,"progressed":true,"filters":[]}],"teams":[{"name":"Team 0","id":0,"scores":{"1":{"points":6,"score":1200,"wins":2,"draws":0,"losses":1,"second":0,"third":0},"3":{"points":6,"score":1200,"wins":2,"draws":0,"losses":1,"second":0,"third":0}}},{"name":"Team 1","id":1,"scores":{"1":{"points":4,"score":400,"wins":1,"draws":1,"losses":1,"second":0,"third":0},"4":{"points":5,"score":800,"wins":1,"draws":2,"losses":0,"second":0,"third":0}}},{"name":"Team 2","id":2,"scores":{"1":{"points":3,"score":300,"wins":1,"draws":0,"losses":2,"second":0,"third":0},"4":{"points":7,"score":1200,"wins":2,"draws":1,"losses":0,"second":0,"third":0}}},{"name":"Team 3","id":3,"scores":{"1":{"points":4,"score":900,"wins":1,"draws":1,"losses":1,"second":0,"third":0},"3":{"points":4,"score":900,"wins":1,"draws":1,"losses":1,"second":0,"third":0}}},{"name":"Team 4","id":4,"scores":{"2":{"points":5,"score":800,"wins":1,"draws":2,"losses":0,"second":0,"third":0},"3":{"points":4,"score":400,"wins":1,"draws":1,"losses":1,"second":0,"third":0}}},{"name":"Team 5","id":5,"scores":{"2":{"points":7,"score":1200,"wins":2,"draws":1,"losses":0,"second":0,"third":0},"3":{"points":3,"score":300,"wins":1,"draws":0,"losses":2,"second":0,"third":0}}},{"name":"Team 6","id":6,"scores":{"2":{"points":1,"score":300,"wins":0,"draws":1,"losses":2,"second":0,"third":0},"4":{"points":1,"score":300,"wins":0,"draws":1,"losses":2,"second":0,"third":0}}},{"name":"Team 7","id":7,"scores":{"2":{"points":3,"score":700,"wins":1,"draws":0,"losses":2,"second":0,"third":0},"4":{"points":3,"score":700,"wins":1,"draws":0,"losses":2,"second":0,"third":0}}}],"games":[{"id":1,"teams":[0,1],"scores":[{"score":100,"points":0,"type":"loss"},{"score":200,"points":3,"type":"win"}]},{"id":2,"teams":[2,3],"scores":{"2":{"score":0,"points":0,"type":"loss"},"3":{"score":500,"points":3,"type":"win"}}},{"id":3,"teams":[0,2],"scores":{"0":{"score":300,"points":3,"type":"win"},"2":{"score":200,"points":0,"type":"loss"}}},{"id":4,"teams":[1,3],"scores":{"1":{"score":200,"points":1,"type":"draw"},"3":{"score":200,"points":1,"type":"draw"}}},{"id":5,"teams":[0,3],"scores":{"0":{"score":800,"points":3,"type":"win"},"3":{"score":200,"points":0,"type":"loss"}}},{"id":6,"teams":[1,2],"scores":{"1":{"score":0,"points":0,"type":"loss"},"2":{"score":100,"points":3,"type":"win"}}},{"id":7,"teams":[4,5],"scores":{"4":{"score":100,"points":1,"type":"draw"},"5":{"score":100,"points":1,"type":"draw"}}},{"id":8,"teams":[6,7],"scores":{"6":{"score":0,"points":0,"type":"loss"},"7":{"score":500,"points":3,"type":"win"}}},{"id":9,"teams":[4,6],"scores":{"4":{"score":100,"points":1,"type":"draw"},"6":{"score":100,"points":1,"type":"draw"}}},{"id":10,"teams":[5,7],"scores":{"5":{"score":800,"points":3,"type":"win"},"7":{"score":0,"points":0,"type":"loss"}}},{"id":11,"teams":[4,7],"scores":{"4":{"score":600,"points":3,"type":"win"},"7":{"score":200,"points":0,"type":"loss"}}},{"id":12,"teams":[5,6],"scores":{"5":{"score":300,"points":3,"type":"win"},"6":{"score":200,"points":0,"type":"loss"}}},{"id":13,"teams":[0,4],"scores":{"0":{"score":100,"points":0,"type":"loss"},"4":{"score":200,"points":3,"type":"win"}}},{"id":14,"teams":[5,3],"scores":{"3":{"score":500,"points":3,"type":"win"},"5":{"score":0,"points":0,"type":"loss"}}},{"id":15,"teams":[0,5],"scores":{"0":{"score":300,"points":3,"type":"win"},"5":{"score":200,"points":0,"type":"loss"}}},{"id":16,"teams":[4,3],"scores":{"3":{"score":200,"points":1,"type":"draw"},"4":{"score":200,"points":1,"type":"draw"}}},{"id":17,"teams":[0,3],"scores":{"0":{"score":800,"points":3,"type":"win"},"3":{"score":200,"points":0,"type":"loss"}}},{"id":18,"teams":[4,5],"scores":{"4":{"score":0,"points":0,"type":"loss"},"5":{"score":100,"points":3,"type":"win"}}},{"id":19,"teams":[1,2],"scores":{"1":{"score":100,"points":1,"type":"draw"},"2":{"score":100,"points":1,"type":"draw"}}},{"id":20,"teams":[6,7],"scores":{"6":{"score":0,"points":0,"type":"loss"},"7":{"score":500,"points":3,"type":"win"}}},{"id":21,"teams":[1,6],"scores":{"1":{"score":100,"points":1,"type":"draw"},"6":{"score":100,"points":1,"type":"draw"}}},{"id":22,"teams":[2,7],"scores":{"2":{"score":800,"points":3,"type":"win"},"7":{"score":0,"points":0,"type":"loss"}}},{"id":23,"teams":[1,7],"scores":{"1":{"score":600,"points":3,"type":"win"},"7":{"score":200,"points":0,"type":"loss"}}},{"id":24,"teams":[2,6],"scores":{"2":{"score":300,"points":3,"type":"win"},"6":{"score":200,"points":0,"type":"loss"}}}]}', true),
				true,
			]
		];
	}

	/**
	 * @dataProvider getExports
	 *
	 * @param array $export
	 * @param bool  $valid
	 */
	public function testBasicValidation(array $export, bool $valid) : void {
		self::assertEquals($valid, ImportValidator::validate($export));
	}

	/**
	 * @dataProvider getExports
	 *
	 * @param array $export
	 * @param bool  $valid
	 */
	public function testValidationExceptions(array $export, bool $valid) : void {
		if (!$valid) {
			$this->expectException(InvalidImportDataException::class);
		}
		$ret = ImportValidator::validate($export, true);
		if ($valid) {
			self::assertEquals($valid, $ret);
		}
	}

	public function getTestedTypes() : array {
		return [
			['aaa', ['string'], true],
			[['aaa'], ['string'], false],
			[1, ['int'], true],
			[1, ['id'], true],
			['aaa', ['id'], true],
			[true, ['bool'], true],
			[false, ['bool'], true],
			['asdad', ['bool'], false],
			[123, ['bool'], false],
			[['aaa'], ['id'], false],
			['aaa', ['int', 'string'], true],
			[1, ['int', 'string'], true],
			[[], ['array'], true],
			['', ['array'], false],
			[[], ['object'], false],
			['', ['object'], false],
			[2, ['object'], false],
			[(object) ['a' => 'asdasd'], ['object'], true],
			[['a' => 'asdasd'], ['object'], true],
		];
	}

	/**
	 * @dataProvider getTestedTypes
	 *
	 * @param       $data
	 * @param array $types
	 * @param bool  $valid
	 */
	public function testTypeValidation($data, array $types, bool $valid) : void {
		if (!$valid) {
			$this->expectException(InvalidImportDataException::class);
		}
		ImportValidator::validateType($data, [], ...$types);
		if ($valid) {
			self::assertTrue(true);
		}
	}

	public function groupParents() : array {
		return [
			[
				[
					'groups' => [
						[
							'id'   => 1,
							'name' => 'Group 1',
						],
					],
					'rounds' => [],
				],
				false
			],
			[
				[
					'groups' => [
						[
							'id'   => 1,
							'name' => 'Group 1',
						],
						[
							'id'   => 2,
							'name' => 'Group 2',
						],
						[
							'id'   => 3,
							'name' => 'Group 3',
						],
					],
					'rounds' => [
						[
							'id'     => 1,
							'name'   => 'Round 1',
							'groups' => [1, 2],
						],
					],
				],
				false
			],
			[
				[
					'groups' => [
						[
							'id'   => 1,
							'name' => 'Group 1',
						],
					],
					'rounds' => [
						[
							'id'     => 1,
							'name'   => 'Round 1',
							'groups' => [1],
						],
					],
				],
				true
			],
		];
	}

	/**
	 * @dataProvider groupParents
	 *
	 * @param array $data
	 * @param bool  $success
	 *
	 * @return void
	 */
	public function testGroupParentsValidation(array $data, bool $success) : void {
		if (!$success) {
			$this->expectException(InvalidImportDataException::class);
		}
		else {
			$this->assertTrue(true);
		}
		ImportValidator::validateGroupParents($data);
	}

}