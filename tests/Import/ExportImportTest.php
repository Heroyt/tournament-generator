<?php


namespace Import;


use Exception;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\Category;
use TournamentGenerator\Constants;
use TournamentGenerator\Game;
use TournamentGenerator\Group;
use TournamentGenerator\Import\Importer;
use TournamentGenerator\Round;
use TournamentGenerator\Team;
use TournamentGenerator\TeamFilter;
use TournamentGenerator\Tournament;

class ExportImportTest extends TestCase
{

	// TODO: More test setups
	public function getBasicTournamentData() : array {
		return [
			$this->tournamentSetup(),
			$this->tournamentSetup(true),
		];
	}

	/**
	 * Creates a basic tournament with games and results
	 *
	 * @param bool $withSecondRound Generate a second round of games
	 * @param bool $withScores
	 *
	 * @return array
	 * @throws Exception
	 */
	private function tournamentSetup(bool $withSecondRound = false, bool $withScores = false) : array {
		$tournament = new Tournament('Tournament');
		$round1 = $tournament->round('Round 1', 1);
		$group1 = $round1->group('Group 1', 1);
		$group2 = $round1->group('Group 2', 2);
		$round2 = $tournament->round('Round 2', 2);
		$group3 = $round2->group('Group 3', 3);
		$group4 = $round2->group('Group 4', 4);

		$group1->progression($group3, 0, 2);
		$group2->progression($group3, 0, 2);

		$group1->progression($group4, -2);
		$group2->progression($group4, -2);

		$expectedTeams = [];

		$teams = [];
		for ($i = 0; $i < 4; $i++) {
			$teams[] = $group1->team('Team '.$i, $i);
			$expectedTeams[$i] = (object) ['name' => 'Team '.$i, 'id' => $i];
		}
		for ($i = 4; $i < 8; $i++) {
			$teams[] = $group2->team('Team '.$i, $i);
			$expectedTeams[$i] = (object) ['name' => 'Team '.$i, 'id' => $i];
		}

		$expectedGames = [
			(object) [
				'id'     => 1,
				'teams'  => [0, 1],
				'scores' => [],
			],
			(object) [
				'id'     => 2,
				'teams'  => [2, 3],
				'scores' => [],
			],
			(object) [
				'id'     => 3,
				'teams'  => [0, 2],
				'scores' => [],
			],
			(object) [
				'id'     => 4,
				'teams'  => [1, 3],
				'scores' => [],
			],
			(object) [
				'id'     => 5,
				'teams'  => [0, 3],
				'scores' => [],
			],
			(object) [
				'id'     => 6,
				'teams'  => [1, 2],
				'scores' => [],
			],
			(object) [
				'id'     => 7,
				'teams'  => [4, 5],
				'scores' => [],
			],
			(object) [
				'id'     => 8,
				'teams'  => [6, 7],
				'scores' => [],
			],
			(object) [
				'id'     => 9,
				'teams'  => [4, 6],
				'scores' => [],
			],
			(object) [
				'id'     => 10,
				'teams'  => [5, 7],
				'scores' => [],
			],
			(object) [
				'id'     => 11,
				'teams'  => [4, 7],
				'scores' => [],
			],
			(object) [
				'id'     => 12,
				'teams'  => [5, 6],
				'scores' => [],
			],
		];

		$expectedSetup = [
			'tournament'   => (object) [
				'type'       => 'general',
				'name'       => 'Tournament',
				'skip'       => false,
				'iterations' => 1,
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
					'iterations' => 1,
					'played' => true,
					'groups' => [1, 2],
					'teams'  => [0, 1, 2, 3, 4, 5, 6, 7],
					'games'  => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
				],
				2 => (object) [
					'id'     => 2,
					'name'   => 'Round 2',
					'skip'   => false,
					'iterations' => 1,
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
					'iterations' => 1,
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
					'iterations' => 1,
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
					'iterations' => 1,
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
					'iterations' => 1,
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
                    'from' => 1,
                    'to' => 3,
                    'offset' => 0,
                    'length' => 2,
                    'progressed' => false,
                    'filters' => [],
                    'points' => null,
                ],
				(object)[
                    'from' => 1,
                    'to' => 4,
                    'offset' => -2,
                    'length' => null,
                    'progressed' => false,
                    'filters' => [],
                    'points' => null,
                ],
				(object)[
                    'from' => 2,
                    'to' => 3,
                    'offset' => 0,
                    'length' => 2,
                    'progressed' => false,
                    'filters' => [],
                    'points' => null,
                ],
				(object)[
                    'from' => 2,
                    'to' => 4,
                    'offset' => -2,
                    'length' => null,
                    'progressed' => false,
                    'filters' => [],
                    'points' => null,
                ],
			],
		];

		$expectedGames[0]->scores = $group1->game([$teams[0], $teams[1]])->setResults([0 => 100, 1 => 200])->getResults();
		$expectedGames[1]->scores = $group1->game([$teams[2], $teams[3]])->setResults([2 => 000, 3 => 500])->getResults();
		$expectedGames[2]->scores = $group1->game([$teams[0], $teams[2]])->setResults([0 => 300, 2 => 200])->getResults();
		$expectedGames[3]->scores = $group1->game([$teams[1], $teams[3]])->setResults([1 => 200, 3 => 200])->getResults();
		$expectedGames[4]->scores = $group1->game([$teams[0], $teams[3]])->setResults([0 => 800, 3 => 200])->getResults();
		$expectedGames[5]->scores = $group1->game([$teams[1], $teams[2]])->setResults([1 => 000, 2 => 100])->getResults();

		// #1 - Team 0 - 1200 Score, 6 points, 2 wins, 1 loss  , 0 draws
		// #3 - Team 1 -  400 Score, 4 points, 1 wins, 1 loss  , 1 draw
		// #4 - Team 2 -  300 Score, 3 points, 1 win , 2 losses, 0 draws
		// #2 - Team 3 -  900 Score, 4 points, 1 win , 1 loss  , 1 draw

		$expectedGames[6]->scores = $group2->game([$teams[4], $teams[5]])->setResults([4 => 100, 5 => 100])->getResults();
		$expectedGames[7]->scores = $group2->game([$teams[6], $teams[7]])->setResults([6 => 000, 7 => 500])->getResults();
		$expectedGames[8]->scores = $group2->game([$teams[4], $teams[6]])->setResults([4 => 100, 6 => 100])->getResults();
		$expectedGames[9]->scores = $group2->game([$teams[5], $teams[7]])->setResults([5 => 800, 7 => 000])->getResults();
		$expectedGames[10]->scores = $group2->game([$teams[4], $teams[7]])->setResults([4 => 600, 7 => 200])->getResults();
		$expectedGames[11]->scores = $group2->game([$teams[5], $teams[6]])->setResults([5 => 300, 6 => 200])->getResults();

		// #2 - Team 4 -  800 Score, 5 points, 1 win , 0 losses, 2 draws
		// #1 - Team 5 - 1200 Score, 7 points, 2 wins, 0 losses, 1 draw
		// #4 - Team 6 -  300 Score, 1 point , 0 wins, 2 losses, 1 draw
		// #3 - Team 7 -  700 Score, 3 points, 1 win , 2 losses, 0 draws

		if ($withScores) {
			$expectedTeams[0]->scores = [
				1 => [
					'points' => 6,
					'score'  => 1200,
					'wins'   => 2,
					'draws'  => 0,
					'losses' => 1,
					'second' => 0,
					'third'  => 0,
				],
			];
			$expectedTeams[1]->scores = [
				1 => [
					'points' => 4,
					'score'  => 400,
					'wins'   => 1,
					'draws'  => 1,
					'losses' => 1,
					'second' => 0,
					'third'  => 0,
				],
			];
			$expectedTeams[2]->scores = [
				1 => [
					'points' => 3,
					'score'  => 300,
					'wins'   => 1,
					'draws'  => 0,
					'losses' => 2,
					'second' => 0,
					'third'  => 0,
				],
			];
			$expectedTeams[3]->scores = [
				1 => [
					'points' => 4,
					'score'  => 900,
					'wins'   => 1,
					'draws'  => 1,
					'losses' => 1,
					'second' => 0,
					'third'  => 0,
				],
			];
			$expectedTeams[4]->scores = [
				2 => [
					'points' => 5,
					'score'  => 800,
					'wins'   => 1,
					'draws'  => 2,
					'losses' => 0,
					'second' => 0,
					'third'  => 0,
				],
			];
			$expectedTeams[5]->scores = [
				2 => [
					'points' => 7,
					'score'  => 1200,
					'wins'   => 2,
					'draws'  => 1,
					'losses' => 0,
					'second' => 0,
					'third'  => 0,
				],
			];
			$expectedTeams[6]->scores = [
				2 => [
					'points' => 1,
					'score'  => 300,
					'wins'   => 0,
					'draws'  => 1,
					'losses' => 2,
					'second' => 0,
					'third'  => 0,
				],
			];
			$expectedTeams[7]->scores = [
				2 => [
					'points' => 3,
					'score'  => 700,
					'wins'   => 1,
					'draws'  => 0,
					'losses' => 2,
					'second' => 0,
					'third'  => 0,
				],
			];
		}

		if ($withSecondRound) {
			$round1->progress();

			foreach ($expectedSetup['progressions'] as $key => $progression) {
				$expectedSetup['progressions'][$key]->progressed = true;
			}
			$expectedSetup['tournament']->games = array_merge($expectedSetup['tournament']->games, [
				13,
				14,
				15,
				16,
				17,
				18,
				19,
				20,
				21,
				22,
				23,
				24,
			]);
			$expectedSetup['rounds'][2]->played = true;
			$expectedSetup['rounds'][2]->teams = [
				0,
				3,
				5,
				4,
				1,
				2,
				7,
				6,
			];
			$expectedSetup['rounds'][2]->games = [
				13,
				14,
				15,
				16,
				17,
				18,
				19,
				20,
				21,
				22,
				23,
				24,
			];
			$expectedSetup['groups'][3]->played = true;
			$expectedSetup['groups'][4]->played = true;
			$expectedSetup['groups'][3]->teams = [
				0,
				3,
				5,
				4,
			];
			$expectedSetup['groups'][3]->games = [
				13,
				14,
				15,
				16,
				17,
				18,
			];
			$expectedSetup['groups'][4]->teams = [
				1,
				2,
				7,
				6,
			];
			$expectedSetup['groups'][4]->games = [
				19,
				20,
				21,
				22,
				23,
				24,
			];

			$expectedGames = array_merge($expectedGames, [
				(object) [
					'id'     => 13,
					'teams'  => [0, 4],
					'scores' => [],
				],
				(object) [
					'id'     => 14,
					'teams'  => [5, 3],
					'scores' => [],
				],
				(object) [
					'id'     => 15,
					'teams'  => [0, 5],
					'scores' => [],
				],
				(object) [
					'id'     => 16,
					'teams'  => [4, 3],
					'scores' => [],
				],
				(object) [
					'id'     => 17,
					'teams'  => [0, 3],
					'scores' => [],
				],
				(object) [
					'id'     => 18,
					'teams'  => [4, 5],
					'scores' => [],
				],
				(object) [
					'id'     => 19,
					'teams'  => [1, 2],
					'scores' => [],
				],
				(object) [
					'id'     => 20,
					'teams'  => [6, 7],
					'scores' => [],
				],
				(object) [
					'id'     => 21,
					'teams'  => [1, 6],
					'scores' => [],
				],
				(object) [
					'id'     => 22,
					'teams'  => [2, 7],
					'scores' => [],
				],
				(object) [
					'id'     => 23,
					'teams'  => [1, 7],
					'scores' => [],
				],
				(object) [
					'id'     => 24,
					'teams'  => [2, 6],
					'scores' => [],
				],
			]);

			// Group 3 teams: 0, 3, 4, 5
			$expectedGames[12]->scores = $group3->game([$teams[0], $teams[4]])->setResults([0 => 100, 4 => 200])->getResults();
			$expectedGames[13]->scores = $group3->game([$teams[5], $teams[3]])->setResults([5 => 000, 3 => 500])->getResults();
			$expectedGames[14]->scores = $group3->game([$teams[0], $teams[5]])->setResults([0 => 300, 5 => 200])->getResults();
			$expectedGames[15]->scores = $group3->game([$teams[4], $teams[3]])->setResults([4 => 200, 3 => 200])->getResults();
			$expectedGames[16]->scores = $group3->game([$teams[0], $teams[3]])->setResults([0 => 800, 3 => 200])->getResults();
			$expectedGames[17]->scores = $group3->game([$teams[4], $teams[5]])->setResults([4 => 000, 5 => 100])->getResults();

			// #1 - Team 0 - 1200 Score, 6 points, 2 wins, 1 loss  , 0 draws
			// #3 - Team 4 -  400 Score, 4 points, 1 win , 1 loss  , 1 draw
			// #4 - Team 5 -  300 Score, 3 points, 1 win , 2 losses, 0 draws
			// #2 - Team 3 -  900 Score, 4 points, 1 win , 1 loss  , 1 draw

			// Group 4 teams: 1, 2, 6, 7
			$expectedGames[18]->scores = $group4->game([$teams[1], $teams[2]])->setResults([1 => 100, 2 => 100])->getResults();
			$expectedGames[19]->scores = $group4->game([$teams[6], $teams[7]])->setResults([6 => 000, 7 => 500])->getResults();
			$expectedGames[20]->scores = $group4->game([$teams[1], $teams[6]])->setResults([1 => 100, 6 => 100])->getResults();
			$expectedGames[21]->scores = $group4->game([$teams[2], $teams[7]])->setResults([2 => 800, 7 => 000])->getResults();
			$expectedGames[22]->scores = $group4->game([$teams[1], $teams[7]])->setResults([1 => 600, 7 => 200])->getResults();
			$expectedGames[23]->scores = $group4->game([$teams[2], $teams[6]])->setResults([2 => 300, 6 => 200])->getResults();

			// #2 - Team 1 -  800 Score, 5 points, 1 win , 0 losses, 2 draws
			// #1 - Team 2 - 1200 Score, 7 points, 2 wins, 0 losses, 1 draw
			// #4 - Team 6 -  300 Score, 1 point , 0 wins, 2 losses, 1 draw
			// #3 - Team 7 -  700 Score, 3 points, 1 wins, 2 losses, 0 draws

			if ($withScores) {
				$expectedTeams[0]->scores[3] = [
					'points' => 6,
					'score'  => 1200,
					'wins'   => 2,
					'draws'  => 0,
					'losses' => 1,
					'second' => 0,
					'third'  => 0,
				];
				$expectedTeams[4]->scores[3] = [
					'points' => 4,
					'score'  => 400,
					'wins'   => 1,
					'draws'  => 1,
					'losses' => 1,
					'second' => 0,
					'third'  => 0,
				];
				$expectedTeams[5]->scores[3] = [
					'points' => 3,
					'score'  => 300,
					'wins'   => 1,
					'draws'  => 0,
					'losses' => 2,
					'second' => 0,
					'third'  => 0,
				];
				$expectedTeams[3]->scores[3] = [
					'points' => 4,
					'score'  => 900,
					'wins'   => 1,
					'draws'  => 1,
					'losses' => 1,
					'second' => 0,
					'third'  => 0,
				];
				$expectedTeams[1]->scores[4] = [
					'points' => 5,
					'score'  => 800,
					'wins'   => 1,
					'draws'  => 2,
					'losses' => 0,
					'second' => 0,
					'third'  => 0,
				];
				$expectedTeams[2]->scores[4] = [
					'points' => 7,
					'score'  => 1200,
					'wins'   => 2,
					'draws'  => 1,
					'losses' => 0,
					'second' => 0,
					'third'  => 0,
				];
				$expectedTeams[6]->scores[4] = [
					'points' => 1,
					'score'  => 300,
					'wins'   => 0,
					'draws'  => 1,
					'losses' => 2,
					'second' => 0,
					'third'  => 0,
				];
				$expectedTeams[7]->scores[4] = [
					'points' => 3,
					'score'  => 700,
					'wins'   => 1,
					'draws'  => 0,
					'losses' => 2,
					'second' => 0,
					'third'  => 0,
				];
			}

			// Total
			///////////////////////////////////////////////////////////////////
			// #1   - Team 0 - 2400 Score, 12 points, 4 wins, 2 loss  , 0 draws
			// #4-5 - Team 1 - 1200 Score, 10 points, 2 wins, 0 losses, 3 draws
			// #2-3 - Team 2 - 1500 Score, 10 points, 3 wins, 2 losses, 0 draws
			// #6   - Team 3 - 1800 Score,  8 points, 2 wins, 0 losses, 2 draws
			// #4-5 - Team 4 - 1200 Score, 10 points, 2 wins, 0 losses, 3 draws
			// #2-3 - Team 5 - 1500 Score, 10 points, 3 wins, 2 losses, 1 draw
			// #7   - Team 6 -  600 Score,  2 points, 0 wins, 4 losses, 2 draws
			// #8   - Team 7 - 1400 Score,  6 points, 2 wins, 4 losses, 0 draws

		}

		return [$tournament, $expectedTeams, $expectedGames, $expectedSetup];
	}

	/**
	 * @dataProvider getBasicTournamentData
	 *
	 * @param Tournament $tournament
	 * @param array      $expectedTeams
	 * @param array      $expectedGames
	 * @param array      $expectedSetup
	 */
	public function testExportImport(Tournament $tournament, array $expectedTeams, array $expectedGames, array $expectedSetup) : void {

		$export = $tournament->export()->withSetup()->get();

		$expected = [
			'teams' => $expectedTeams,
			'games' => $expectedGames,
		];
		$expected += $expectedSetup;

		self::assertEquals($expected, $export);

		/** @var Tournament $imported */
		$imported = Importer::import($export);

		self::assertInstanceOf(get_class($tournament), $imported);
		self::assertCount(count($tournament->getCategories()), $imported->getCategories());
		self::assertCount(count($tournament->getRounds()), $imported->getRounds());
		self::assertCount(count($tournament->getGroups()), $imported->getGroups());
		self::assertCount(count($tournament->getTeams()), $imported->getTeams());
		self::assertCount(count($tournament->getGames()), $imported->getGames());

		self::assertEquals($tournament->getName(), $imported->getName());
		self::assertEquals($tournament->getSkip(), $imported->getSkip());
		self::assertEquals($tournament->getPlay(), $imported->getPlay());
		self::assertEquals($tournament->getRoundWait(), $imported->getRoundWait());
		self::assertEquals($tournament->getCategoryWait(), $imported->getCategoryWait());
		self::assertEquals($tournament->getGameWait(), $imported->getGameWait());

		foreach ($tournament->getCategories() as $category) {
			/** @var Category $importedCategory */
			$importedCategory = $imported->getContainer()->getHierarchyLevelQuery(Category::class)->whereId($category->getId())->getFirst();
			self::assertInstanceOf(Category::class, $importedCategory);
			self::assertEquals($category->getName(), $importedCategory->getName());
			self::assertEquals($category->getSkip(), $importedCategory->getSkip());
		}

		foreach ($tournament->getRounds() as $round) {
			/** @var Round $importedRound */
			$importedRound = $imported->getContainer()->getHierarchyLevelQuery(Round::class)->whereId($round->getId())->getFirst();
			self::assertInstanceOf(Round::class, $importedRound);
			self::assertEquals($round->getName(), $importedRound->getName());
			self::assertEquals($round->getSkip(), $importedRound->getSkip());
			self::assertEquals($round->getGroupsIds(), $importedRound->getGroupsIds());
		}

		foreach ($tournament->getGroups() as $group) {
			/** @var Group $importedGroup */
			$importedGroup = $imported->getContainer()->getHierarchyLevelQuery(Group::class)->whereId($group->getId())->getFirst();
			self::assertInstanceOf(Group::class, $importedGroup);
			self::assertEquals($group->getName(), $importedGroup->getName());
			self::assertEquals($group->getSkip(), $importedGroup->getSkip());
			self::assertEquals($group->getMaxSize(), $importedGroup->getMaxSize());
			self::assertEquals($group->getWinPoints(), $importedGroup->getWinPoints());
			self::assertEquals($group->getLostPoints(), $importedGroup->getLostPoints());
			self::assertEquals($group->getDrawPoints(), $importedGroup->getDrawPoints());
			self::assertEquals($group->getSecondPoints(), $importedGroup->getSecondPoints());
			self::assertEquals($group->getThirdPoints(), $importedGroup->getThirdPoints());
			self::assertEquals($group->getProgressPoints(), $importedGroup->getProgressPoints());
			self::assertEquals($group->getInGame(), $importedGroup->getInGame());
			self::assertEquals($group->getType(), $importedGroup->getType());
			self::assertEquals($group->getOrdering(), $importedGroup->getOrdering());

			$importedProgressions = $importedGroup->getProgressions();
			foreach ($group->getProgressions() as $key => $progression) {
				self::assertEquals($progression->getStart(), $importedProgressions[$key]->getStart());
				self::assertEquals($progression->getLen(), $importedProgressions[$key]->getLen());
				self::assertEquals($progression->getFrom()->getId(), $importedProgressions[$key]->getFrom()->getId());
				self::assertEquals($progression->getTo()->getId(), $importedProgressions[$key]->getTo()->getId());
				self::assertEquals(array_map(static function(TeamFilter $filter) { return (string) $filter; }, $progression->getFilters()), array_map(static function(TeamFilter $filter) { return (string) $filter; }, $importedProgressions[$key]->getFilters()));
			}
		}

		foreach ($tournament->getTeams() as $team) {
			/** @var Team $importedTeam */
			$importedTeam = $imported->getTeamContainer()->whereId($team->getId())->getFirst();
			self::assertInstanceOf(Team::class, $importedTeam);
			self::assertEquals($team->getName(), $importedTeam->getName());
			self::assertEquals($team->getSumScore(), $importedTeam->getSumScore());
			foreach ($team->getGroupResults() as $id => $results) {
				self::assertEquals($team->getGamesInfo($id), $importedTeam->getGamesInfo($id));
			}
		}

		foreach ($tournament->getGames() as $game) {
			/** @var Game $importedGame */
			$importedGame = $imported->getGameContainer()->whereId($game->getId())->getFirst();
			self::assertInstanceOf(Game::class, $importedGame);
			self::assertEquals($game->getTeamsIds(), $importedGame->getTeamsIds());
			self::assertEquals($game->getResults(), $importedGame->getResults());
			self::assertEquals($game->getGroup()->getId(), $importedGame->getGroup()->getId());
		}

		$export2 = $imported->export()->withSetup()->get();

		foreach ($expected as $key => $values) {
			if (is_array($values)) {
				foreach ($values as $value) {
					if (isset($value->teams)) {
						sort($value->teams);
					}
				}
			}
		}
		foreach ($export2 as $key => $values) {
			if (is_array($values)) {
				foreach ($values as $value) {
					if (isset($value->teams)) {
						sort($value->teams);
					}
				}
			}
		}

		self::assertEquals($expected, $export2);
	}
}