<?php


namespace Export;


use PHPUnit\Framework\TestCase;
use TournamentGenerator\Constants;
use TournamentGenerator\Export\SetupExporter;
use TournamentGenerator\Round;
use TournamentGenerator\TeamFilter;
use TournamentGenerator\Tournament;

class SetupExporterTest extends TestCase
{

	public function testBasicExport() : void {
		$tournament = new Tournament('Tournament name');
		$tournament->setPlay(5);
		$category1 = $tournament->category('Category 1', 1);
		$category2 = $tournament->category('Category 2', 2);
		$round1 = $category1->round('Round 1', 1);
		$round2 = $category1->round('Round 2', 2);
		$round3 = $category2->round('Round 3', 3);
		$round4 = $category2->round('Round 4', 4);
		$group1 = $round1
			->group('Group 1', 1)
			->setType(Constants::ROUND_ROBIN)
			->setSkip(true)
			->setWinPoints(10)
			->setLostPoints(-3)
			->setDrawPoints(2)
			->setSecondPoints(6)
			->setThirdPoints(4)
			->setProgressPoints(100)
			->setInGame(2);
		$group2 = $round1
			->group('Group 2', 2)
			->setType(Constants::ROUND_TWO)
			->setSkip(false)
			->setWinPoints(11)
			->setLostPoints(-3)
			->setDrawPoints(2)
			->setSecondPoints(6)
			->setThirdPoints(4)
			->setProgressPoints(100)
			->setInGame(3);
		$round2
			->group('Group 3', 3)
			->setType(Constants::ROUND_SPLIT)
			->setSkip(true)
			->setWinPoints(10)
			->setLostPoints(-3)
			->setDrawPoints(2)
			->setSecondPoints(6)
			->setThirdPoints(4)
			->setProgressPoints(100)
			->setInGame(4);
		$round3
			->group('Group 4', 4)
			->setType(Constants::ROUND_ROBIN)
			->setSkip(false)
			->setWinPoints(20)
			->setLostPoints(-9)
			->setDrawPoints(5)
			->setSecondPoints(2)
			->setThirdPoints(4)
			->setProgressPoints(999)
			->setInGame(3);
		$round3
			->group('Group 5', 5)
			->setType(Constants::ROUND_ROBIN)
			->setSkip(true)
			->setWinPoints(10)
			->setLostPoints(-3)
			->setDrawPoints(2)
			->setSecondPoints(6)
			->setThirdPoints(4)
			->setProgressPoints(100)
			->setInGame(2);
		$round4
			->group('Group 6', 6)
			->setType(Constants::ROUND_TWO)
			->setSkip(true)
			->setWinPoints(10)
			->setLostPoints(-3)
			->setDrawPoints(2)
			->setSecondPoints(6)
			->setThirdPoints(4)
			->setProgressPoints(100)
			->setInGame(4);

		$expectedOutput = [
			'tournament'   => (object) [
				'type'       => 'general',
				'name'       => 'Tournament name',
				'skip'       => false,
				'timing'     => (object) [
					'play'         => 5,
					'gameWait'     => 0,
					'categoryWait' => 0,
					'roundWait'    => 0,
					'expectedTime' => 0,
				],
				'categories' => [1, 2],
				'rounds'     => [1, 2, 3, 4],
				'groups'     => [1, 2, 3, 4, 5, 6],
				'teams'      => [],
				'games'      => [],
			],
			'categories'   => [
				1 => (object) [
					'id'     => 1,
					'name'   => 'Category 1',
					'skip'   => false,
					'rounds' => [1, 2],
					'groups' => [1, 2, 3],
					'teams'  => [],
					'games'  => [],
				],
				2 => (object) [
					'id'     => 2,
					'name'   => 'Category 2',
					'skip'   => false,
					'rounds' => [3, 4],
					'groups' => [4, 5, 6],
					'teams'  => [],
					'games'  => [],
				],
			],
			'rounds'       => [
				1 => (object) [
					'id'     => 1,
					'name'   => 'Round 1',
					'skip'   => false,
					'played' => false,
					'groups' => [1, 2],
					'teams'  => [],
					'games'  => [],
				],
				2 => (object) [
					'id'     => 2,
					'name'   => 'Round 2',
					'skip'   => false,
					'played' => false,
					'groups' => [3],
					'teams'  => [],
					'games'  => [],
				],
				3 => (object) [
					'id'     => 3,
					'name'   => 'Round 3',
					'skip'   => false,
					'played' => false,
					'groups' => [4, 5],
					'teams'  => [],
					'games'  => [],
				],
				4 => (object) [
					'id'     => 4,
					'name'   => 'Round 4',
					'skip'   => false,
					'played' => false,
					'groups' => [6],
					'teams'  => [],
					'games'  => [],
				],
			],
			'groups'       => [
				1 => (object) [
					'id'      => 1,
					'name'    => 'Group 1',
					'type'    => Constants::ROUND_ROBIN,
					'skip'    => true,
					'points'  => (object) [
						'win'         => 10,
						'loss'        => -3,
						'draw'        => 2,
						'second'      => 6,
						'third'       => 4,
						'progression' => 100,
					],
					'played'  => false,
					'inGame'  => 2,
					'maxSize' => 4,
					'teams'   => [],
					'games'   => [],
				],
				2 => (object) [
					'id'      => 2,
					'name'    => 'Group 2',
					'type'    => Constants::ROUND_TWO,
					'skip'    => false,
					'points'  => (object) [
						'win'         => 11,
						'loss'        => -3,
						'draw'        => 2,
						'second'      => 6,
						'third'       => 4,
						'progression' => 100,
					],
					'played'  => false,
					'inGame'  => 3,
					'maxSize' => 4,
					'teams'   => [],
					'games'   => [],
				],
				3 => (object) [
					'id'      => 3,
					'name'    => 'Group 3',
					'type'    => Constants::ROUND_SPLIT,
					'skip'    => true,
					'points'  => (object) [
						'win'         => 10,
						'loss'        => -3,
						'draw'        => 2,
						'second'      => 6,
						'third'       => 4,
						'progression' => 100,
					],
					'played'  => false,
					'inGame'  => 4,
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
						'win'         => 20,
						'loss'        => -9,
						'draw'        => 5,
						'second'      => 2,
						'third'       => 4,
						'progression' => 999,
					],
					'played'  => false,
					'inGame'  => 3,
					'maxSize' => 4,
					'teams'   => [],
					'games'   => [],
				],
				5 => (object) [
					'id'      => 5,
					'name'    => 'Group 5',
					'type'    => Constants::ROUND_ROBIN,
					'skip'    => true,
					'points'  => (object) [
						'win'         => 10,
						'loss'        => -3,
						'draw'        => 2,
						'second'      => 6,
						'third'       => 4,
						'progression' => 100,
					],
					'played'  => false,
					'inGame'  => 2,
					'maxSize' => 4,
					'teams'   => [],
					'games'   => [],
				],
				6 => (object) [
					'id'      => 6,
					'name'    => 'Group 6',
					'type'    => Constants::ROUND_TWO,
					'skip'    => true,
					'points'  => (object) [
						'win'         => 10,
						'loss'        => -3,
						'draw'        => 2,
						'second'      => 6,
						'third'       => 4,
						'progression' => 100,
					],
					'played'  => false,
					'inGame'  => 4,
					'maxSize' => 4,
					'teams'   => [],
					'games'   => [],
				],
			],
			'progressions' => [],
		];

		// Tournament
		$export1 = SetupExporter::export($tournament);
		$export2 = SetupExporter::start($tournament)->get();

		self::assertEquals($expectedOutput, $export1);
		self::assertEquals($expectedOutput, $export2);

		// Category
		unset($expectedOutput['tournament']);
		$expectedOutputCategory1 = [];
		$this->cloneArray($expectedOutputCategory1, $expectedOutput);
		unset(
			$expectedOutputCategory1['categories'][2],
			$expectedOutputCategory1['rounds'][3],
			$expectedOutputCategory1['rounds'][4],
			$expectedOutputCategory1['groups'][4],
			$expectedOutputCategory1['groups'][5],
			$expectedOutputCategory1['groups'][6],
		);
		$expectedOutputCategory2 = [];
		$this->cloneArray($expectedOutputCategory2, $expectedOutput);
		unset(
			$expectedOutputCategory2['categories'][1],
			$expectedOutputCategory2['rounds'][1],
			$expectedOutputCategory2['rounds'][2],
			$expectedOutputCategory2['groups'][1],
			$expectedOutputCategory2['groups'][2],
			$expectedOutputCategory2['groups'][3],
		);

		$export1 = SetupExporter::export($category1);
		$export2 = SetupExporter::start($category1)->get();

		self::assertEquals($expectedOutputCategory1, $export1);
		self::assertEquals($expectedOutputCategory1, $export2);

		$export1 = SetupExporter::export($category2);
		$export2 = SetupExporter::start($category2)->get();

		self::assertEquals($expectedOutputCategory2, $export1);
		self::assertEquals($expectedOutputCategory2, $export2);

		// Round
		$expectedOutputRound1 = [];
		$expectedOutputRound2 = [];
		$expectedOutputRound3 = [];
		$expectedOutputRound4 = [];
		unset($expectedOutputCategory1['categories'], $expectedOutputCategory2['categories']);
		$this->cloneArray($expectedOutputRound1, $expectedOutputCategory1);
		$this->cloneArray($expectedOutputRound2, $expectedOutputCategory1);
		$this->cloneArray($expectedOutputRound3, $expectedOutputCategory2);
		$this->cloneArray($expectedOutputRound4, $expectedOutputCategory2);
		unset(
			$expectedOutputRound1['rounds'][2],
			$expectedOutputRound1['groups'][3],
			$expectedOutputRound2['rounds'][1],
			$expectedOutputRound2['groups'][1],
			$expectedOutputRound2['groups'][2],
			$expectedOutputRound3['rounds'][4],
			$expectedOutputRound3['groups'][6],
			$expectedOutputRound4['rounds'][3],
			$expectedOutputRound4['groups'][4],
			$expectedOutputRound4['groups'][5],
		);

		$export1 = SetupExporter::export($round1);
		$export2 = SetupExporter::start($round1)->get();

		self::assertEquals($expectedOutputRound1, $export1);
		self::assertEquals($expectedOutputRound1, $export2);

		$export1 = SetupExporter::export($round2);
		$export2 = SetupExporter::start($round2)->get();

		self::assertEquals($expectedOutputRound2, $export1);
		self::assertEquals($expectedOutputRound2, $export2);

		$export1 = SetupExporter::export($round3);
		$export2 = SetupExporter::start($round3)->get();

		self::assertEquals($expectedOutputRound3, $export1);
		self::assertEquals($expectedOutputRound3, $export2);

		$export1 = SetupExporter::export($round4);
		$export2 = SetupExporter::start($round4)->get();

		self::assertEquals($expectedOutputRound4, $export1);
		self::assertEquals($expectedOutputRound4, $export2);

		// Group
		$expectedOutputGroup1 = [];
		$expectedOutputGroup2 = [];
		unset($expectedOutputRound1['rounds']);
		$this->cloneArray($expectedOutputGroup1, $expectedOutputRound1);
		$this->cloneArray($expectedOutputGroup2, $expectedOutputRound1);
		unset(
			$expectedOutputGroup1['groups'][2],
			$expectedOutputGroup2['groups'][1],
		);

		$export1 = SetupExporter::export($group1);
		$export2 = SetupExporter::start($group1)->get();

		self::assertEquals($expectedOutputGroup1, $export1);
		self::assertEquals($expectedOutputGroup1, $export2);

		$export1 = SetupExporter::export($group2);
		$export2 = SetupExporter::start($group2)->get();

		self::assertEquals($expectedOutputGroup2, $export1);
		self::assertEquals($expectedOutputGroup2, $export2);
	}

	protected function cloneArray(array &$output, array $values) : void {
		foreach ($values as $key => $value) {
			if (is_object($value)) {
				$output[$key] = clone $value;
			}
			elseif (is_array($value)) {
				$output[$key] = [];
				$this->cloneArray($output[$key], $value);
			}
			else {
				$output[$key] = $value;
			}
		}
	}

	public function testProgressionExport() : void {
		$round1 = new Round('Round1', 1);
		$group1 = $round1->group('Group1', 1);
		$group2 = $round1->group('Group2', 2);
		$round2 = new Round('Round2', 2);
		$group3 = $round2->group('Group3', 3);
		$group4 = $round2->group('Group4', 4);

		$group1->progression($group3, 0, 2);
		$group1->progression($group4, -2, 2);
		$group1->progression($group3, -2);
		$group2->progression($group3)->addFilter(new TeamFilter('points', '<', 3));
		$group2->progression($group4)->addFilter(new TeamFilter('score', '>=', 1000), new TeamFilter('not-progressed'));

		$export = SetupExporter::export($round1);
		self::assertEquals([
												 'rounds'       => [
													 1 => (object) [
														 'id'     => 1,
														 'name'   => 'Round1',
														 'skip'   => false,
														 'played' => false,
														 'groups' => [1, 2],
														 'teams'  => [],
														 'games'  => [],
													 ],
												 ],
												 'groups'       => [
													 1 => (object) [
														 'id'      => 1,
														 'name'    => 'Group1',
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
													 2 => (object) [
														 'id'      => 2,
														 'name'    => 'Group2',
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
														 'filters'    => [],
														 'progressed' => false,
													 ],
													 (object) [
														 'from'       => 1,
														 'to'         => 4,
														 'offset'     => -2,
														 'length'     => 2,
														 'filters'    => [],
														 'progressed' => false,
													 ],
													 (object) [
														 'from'       => 1,
														 'to'         => 3,
														 'offset'     => -2,
														 'length'     => null,
														 'filters'    => [],
														 'progressed' => false,
													 ],
													 (object) [
														 'from'       => 2,
														 'to'         => 3,
														 'offset'     => 0,
														 'length'     => null,
														 'filters'    => [
															 (object) [
																 'what'   => 'points',
																 'how'    => '<',
																 'val'    => 3,
																 'groups' => [],
															 ],
														 ],
														 'progressed' => false,
													 ],
													 (object) [
														 'from'       => 2,
														 'to'         => 4,
														 'offset'     => 0,
														 'length'     => null,
														 'filters'    => [
															 (object) [
																 'what'   => 'score',
																 'how'    => '>=',
																 'val'    => 1000,
																 'groups' => [],
															 ],
															 (object) [
																 'what'   => 'not-progressed',
																 'how'    => '>',
																 'val'    => 0,
																 'groups' => [],
															 ],
														 ],
														 'progressed' => false,
													 ],
												 ],
											 ], $export);
		$export = SetupExporter::export($group1);
		self::assertEquals([
												 'groups'       => [
													 1 => (object) [
														 'id'      => 1,
														 'name'    => 'Group1',
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
														 'filters'    => [],
														 'progressed' => false,
													 ],
													 (object) [
														 'from'       => 1,
														 'to'         => 4,
														 'offset'     => -2,
														 'length'     => 2,
														 'filters'    => [],
														 'progressed' => false,
													 ],
													 (object) [
														 'from'       => 1,
														 'to'         => 3,
														 'offset'     => -2,
														 'length'     => null,
														 'filters'    => [],
														 'progressed' => false,
													 ],
												 ],
											 ], $export);
	}

}