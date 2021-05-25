<?php


namespace Export;


use PHPUnit\Framework\TestCase;
use TournamentGenerator\Constants;
use TournamentGenerator\Export\SetupExporter;
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
		$round1
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
		$round1
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
			'tournament' => (object) [
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
			'categories' => [
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
			'rounds'     => [
				1 => (object) [
					'id'   => 1,
					'name' => 'Round 1',
					'skip' => false,
					'played' => false,
					'groups' => [1, 2],
					'teams'  => [],
					'games'  => [],
				],
				2 => (object) [
					'id'   => 2,
					'name' => 'Round 2',
					'skip' => false,
					'played' => false,
					'groups' => [3],
					'teams'  => [],
					'games'  => [],
				],
				3 => (object) [
					'id'   => 3,
					'name' => 'Round 3',
					'skip' => false,
					'played' => false,
					'groups' => [4, 5],
					'teams'  => [],
					'games'  => [],
				],
				4 => (object) [
					'id'   => 4,
					'name' => 'Round 4',
					'skip' => false,
					'played' => false,
					'groups' => [6],
					'teams'  => [],
					'games'  => [],
				],
			],
			'groups' => [
				1 => (object) [
					'id' => 1,
					'name' => 'Group 1',
					'type' => Constants::ROUND_ROBIN,
					'skip' => true,
					'points' => (object) [
						'win' => 10,
						'loss' => -3,
						'draw' => 2,
						'second' => 6,
						'third' => 4,
						'progression' => 100,
					],
					'played' => false,
					'inGame' => 2,
					'maxSize' => 4,
					'teams'  => [],
					'games'  => [],
				],
				2 => (object) [
					'id' => 2,
					'name' => 'Group 2',
					'type' => Constants::ROUND_TWO,
					'skip' => false,
					'points' => (object) [
						'win' => 11,
						'loss' => -3,
						'draw' => 2,
						'second' => 6,
						'third' => 4,
						'progression' => 100,
					],
					'played' => false,
					'inGame' => 3,
					'maxSize' => 4,
					'teams'  => [],
					'games'  => [],
				],
				3 => (object) [
					'id' => 3,
					'name' => 'Group 3',
					'type' => Constants::ROUND_SPLIT,
					'skip' => true,
					'points' => (object) [
						'win' => 10,
						'loss' => -3,
						'draw' => 2,
						'second' => 6,
						'third' => 4,
						'progression' => 100,
					],
					'played' => false,
					'inGame' => 4,
					'maxSize' => 4,
					'teams'  => [],
					'games'  => [],
				],
				4 => (object) [
					'id' => 4,
					'name' => 'Group 4',
					'type' => Constants::ROUND_ROBIN,
					'skip' => false,
					'points' => (object) [
						'win' => 20,
						'loss' => -9,
						'draw' => 5,
						'second' => 2,
						'third' => 4,
						'progression' => 999,
					],
					'played' => false,
					'inGame' => 3,
					'maxSize' => 4,
					'teams'  => [],
					'games'  => [],
				],
				5 => (object) [
					'id' => 5,
					'name' => 'Group 5',
					'type' => Constants::ROUND_ROBIN,
					'skip' => true,
					'points' => (object) [
						'win' => 10,
						'loss' => -3,
						'draw' => 2,
						'second' => 6,
						'third' => 4,
						'progression' => 100,
					],
					'played' => false,
					'inGame' => 2,
					'maxSize' => 4,
					'teams'  => [],
					'games'  => [],
				],
				6 => (object) [
					'id' => 6,
					'name' => 'Group 6',
					'type' => Constants::ROUND_TWO,
					'skip' => true,
					'points' => (object) [
						'win' => 10,
						'loss' => -3,
						'draw' => 2,
						'second' => 6,
						'third' => 4,
						'progression' => 100,
					],
					'played' => false,
					'inGame' => 4,
					'maxSize' => 4,
					'teams'  => [],
					'games'  => [],
				],
			],
			'progressions' => [],
		];

		$export1 = SetupExporter::export($tournament);
		$export2 = SetupExporter::start($tournament)->get();

		self::assertEquals($expectedOutput, $export1);
		self::assertEquals($expectedOutput, $export2);
	}

}