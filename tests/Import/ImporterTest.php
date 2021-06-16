<?php


namespace Import;


use PHPUnit\Framework\TestCase;
use TournamentGenerator\Category;
use TournamentGenerator\Constants;
use TournamentGenerator\Game;
use TournamentGenerator\Group;
use TournamentGenerator\Import\Importer;
use TournamentGenerator\Preset\DoubleElimination;
use TournamentGenerator\Preset\R2G;
use TournamentGenerator\Preset\SingleElimination;
use TournamentGenerator\Round;
use TournamentGenerator\Team;
use TournamentGenerator\TeamFilter;
use TournamentGenerator\Tournament;

class ImporterTest extends TestCase
{

	public function complexExports() : array {
		return [
			[
				[
					'tournament' => (object) [
						'type'       => 'general',
						'name'       => 'Test',
						'skip'       => false,
						'timing'     => (object) [
							'play'         => random_int(1, 99),
							'gameWait'     => random_int(1, 99),
							'categoryWait' => random_int(1, 99),
							'roundWait'    => random_int(1, 99),
						],
						'categories' => [1, 2],
					],
					'categories' => [
						(object) [
							'name' => 'Category 1',
							'id'   => 1,
							'skip' => true,
						],
						(object) [
							'name' => 'Category 2',
							'id'   => 2,
							'skip' => true,
						],
					],
				]
			],
			[
				[
					'tournament' => (object) [
						'type'   => 'general',
						'name'   => 'Test',
						'skip'   => false,
						'timing' => (object) [
							'play'         => random_int(1, 99),
							'gameWait'     => random_int(1, 99),
							'categoryWait' => random_int(1, 99),
							'roundWait'    => random_int(1, 99),
						],
						'rounds' => [1, 2],
					],
					'rounds'     => [
						(object) [
							'name' => 'Round 1',
							'id'   => 1,
							'skip' => true,
						],
						(object) [
							'name' => 'Round 2',
							'id'   => 2,
							'skip' => true,
						],
					],
				]
			],
			[
				[
					'tournament' => (object) [
						'type'       => 'general',
						'name'       => 'Test',
						'skip'       => false,
						'timing'     => (object) [
							'play'         => random_int(1, 99),
							'gameWait'     => random_int(1, 99),
							'categoryWait' => random_int(1, 99),
							'roundWait'    => random_int(1, 99),
						],
						'categories' => [1, 2],
						'rounds'     => [1, 2],
					],
					'categories' => [
						(object) [
							'name'   => 'Category 1',
							'id'     => 1,
							'skip'   => true,
							'rounds' => [1],
						],
						(object) [
							'name'   => 'Category 2',
							'id'     => 2,
							'skip'   => true,
							'rounds' => [2],
						],
					],
					'rounds'     => [
						(object) [
							'name' => 'Round 1',
							'id'   => 1,
							'skip' => true,
						],
						(object) [
							'name' => 'Round 2',
							'id'   => 2,
							'skip' => true,
						],
					],
				]
			],
			[
				[
					'tournament' => (object) [
						'type'       => 'general',
						'name'       => 'Test',
						'skip'       => false,
						'timing'     => (object) [
							'play'         => random_int(1, 99),
							'gameWait'     => random_int(1, 99),
							'categoryWait' => random_int(1, 99),
							'roundWait'    => random_int(1, 99),
						],
						'categories' => [1, 2],
						'rounds'     => [1, 2],
						'groups'     => [1, 2, 3, 4],
					],
					'categories' => [
						(object) [
							'name'   => 'Category 1',
							'id'     => 1,
							'skip'   => true,
							'rounds' => [1],
							'groups' => [1, 2],
						],
						(object) [
							'name'   => 'Category 2',
							'id'     => 2,
							'skip'   => true,
							'rounds' => [2],
							'groups' => [3, 4],
						],
					],
					'rounds'     => [
						(object) [
							'name'   => 'Round 1',
							'id'     => 1,
							'skip'   => true,
							'groups' => [1, 2],
						],
						(object) [
							'name'   => 'Round 2',
							'id'     => 2,
							'skip'   => true,
							'groups' => [3, 4],
						],
					],
					'groups'     => [
						(object) [
							'name'    => 'Group 1',
							'id'      => 1,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
						],
						(object) [
							'name'    => 'Group 2',
							'id'      => 2,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
						],
						(object) [
							'name'    => 'Group 3',
							'id'      => 3,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
						],
						(object) [
							'name'    => 'Group 4',
							'id'      => 4,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
						],
					],
				]
			],
			[
				[
					'tournament' => (object) [
						'type'       => 'general',
						'name'       => 'Test',
						'skip'       => false,
						'timing'     => (object) [
							'play'         => random_int(1, 99),
							'gameWait'     => random_int(1, 99),
							'categoryWait' => random_int(1, 99),
							'roundWait'    => random_int(1, 99),
						],
						'categories' => [1, 2],
						'rounds'     => [1, 2],
						'groups'     => [1, 2, 3, 4],
						'teams'      => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
					],
					'categories' => [
						(object) [
							'name'   => 'Category 1',
							'id'     => 1,
							'skip'   => true,
							'rounds' => [1],
							'groups' => [1, 2],
							'teams'  => [1, 2, 3, 4, 5],
						],
						(object) [
							'name'   => 'Category 2',
							'id'     => 2,
							'skip'   => true,
							'rounds' => [2],
							'groups' => [3, 4],
							'teams'  => [6, 7, 8, 9, 10],
						],
					],
					'rounds'     => [
						(object) [
							'name'   => 'Round 1',
							'id'     => 1,
							'skip'   => true,
							'groups' => [1, 2],
							'teams'  => [1, 2, 3, 4, 5],
						],
						(object) [
							'name'   => 'Round 2',
							'id'     => 2,
							'skip'   => true,
							'groups' => [3, 4],
							'teams'  => [6, 7, 8, 9, 10],
						],
					],
					'groups'     => [
						(object) [
							'name'    => 'Group 1',
							'id'      => 1,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [1, 2],
						],
						(object) [
							'name'    => 'Group 2',
							'id'      => 2,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [3, 4, 5],
						],
						(object) [
							'name'    => 'Group 3',
							'id'      => 3,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [6, 7],
						],
						(object) [
							'name'    => 'Group 4',
							'id'      => 4,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [8, 9, 10],
						],
					],
					'teams'      => [
						(object) [
							'id'   => 1,
							'name' => 'Team 1',
						],
						(object) [
							'id'   => 2,
							'name' => 'Team 2',
						],
						(object) [
							'id'   => 3,
							'name' => 'Team 3',
						],
						(object) [
							'id'   => 4,
							'name' => 'Team 4',
						],
						(object) [
							'id'   => 5,
							'name' => 'Team 5',
						],
						(object) [
							'id'   => 6,
							'name' => 'Team 6',
						],
						(object) [
							'id'   => 7,
							'name' => 'Team 7',
						],
						(object) [
							'id'   => 8,
							'name' => 'Team 8',
						],
						(object) [
							'id'   => 9,
							'name' => 'Team 9',
						],
						(object) [
							'id'   => 10,
							'name' => 'Team 10',
						],
					],
				]
			],
			[
				[
					'tournament' => (object) [
						'type'       => 'general',
						'name'       => 'Test',
						'skip'       => false,
						'timing'     => (object) [
							'play'         => random_int(1, 99),
							'gameWait'     => random_int(1, 99),
							'categoryWait' => random_int(1, 99),
							'roundWait'    => random_int(1, 99),
						],
						'categories' => [1, 2],
						'rounds'     => [1, 2],
						'groups'     => [1, 2, 3, 4],
						'teams'      => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
						'games'      => [1, 2, 3, 4, 5, 6, 7, 8],
					],
					'categories' => [
						(object) [
							'name'   => 'Category 1',
							'id'     => 1,
							'skip'   => true,
							'rounds' => [1],
							'groups' => [1, 2],
							'teams'  => [1, 2, 3, 4, 5],
							'games'  => [1, 2, 3, 4],
						],
						(object) [
							'name'   => 'Category 2',
							'id'     => 2,
							'skip'   => true,
							'rounds' => [2],
							'groups' => [3, 4],
							'teams'  => [6, 7, 8, 9, 10],
							'games'  => [5, 6, 7, 8],
						],
					],
					'rounds'     => [
						(object) [
							'name'   => 'Round 1',
							'id'     => 1,
							'skip'   => true,
							'groups' => [1, 2],
							'teams'  => [1, 2, 3, 4, 5],
							'games'  => [1, 2, 3, 4],
						],
						(object) [
							'name'   => 'Round 2',
							'id'     => 2,
							'skip'   => true,
							'groups' => [3, 4],
							'teams'  => [6, 7, 8, 9, 10],
							'games'  => [5, 6, 7, 8],
						],
					],
					'groups'     => [
						(object) [
							'name'    => 'Group 1',
							'id'      => 1,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [1, 2],
							'games'   => [1],
						],
						(object) [
							'name'    => 'Group 2',
							'id'      => 2,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [3, 4, 5],
							'games'   => [2, 3, 4],
						],
						(object) [
							'name'    => 'Group 3',
							'id'      => 3,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [6, 7],
							'games'   => [5],
						],
						(object) [
							'name'    => 'Group 4',
							'id'      => 4,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [8, 9, 10],
							'games'   => [6, 7, 8],
						],
					],
					'teams'      => [
						(object) [
							'id'   => 1,
							'name' => 'Team 1',
						],
						(object) [
							'id'   => 2,
							'name' => 'Team 2',
						],
						(object) [
							'id'   => 3,
							'name' => 'Team 3',
						],
						(object) [
							'id'   => 4,
							'name' => 'Team 4',
						],
						(object) [
							'id'   => 5,
							'name' => 'Team 5',
						],
						(object) [
							'id'   => 6,
							'name' => 'Team 6',
						],
						(object) [
							'id'   => 7,
							'name' => 'Team 7',
						],
						(object) [
							'id'   => 8,
							'name' => 'Team 8',
						],
						(object) [
							'id'   => 9,
							'name' => 'Team 9',
						],
						(object) [
							'id'   => 10,
							'name' => 'Team 10',
						],
					],
					'games'      => [
						(object) [
							'id'    => 1,
							'teams' => [1, 2],
						],
						(object) [
							'id'    => 2,
							'teams' => [3, 4],
						],
						(object) [
							'id'    => 3,
							'teams' => [4, 5],
						],
						(object) [
							'id'    => 4,
							'teams' => [3, 5],
						],
						(object) [
							'id'    => 5,
							'teams' => [6, 7],
						],
						(object) [
							'id'    => 6,
							'teams' => [8, 9],
						],
						(object) [
							'id'    => 7,
							'teams' => [9, 10],
						],
						(object) [
							'id'    => 8,
							'teams' => [8, 10],
						],
					],
				]
			],
			[
				[
					'categories' => [
						(object) [
							'name'   => 'Category 1',
							'id'     => 1,
							'skip'   => true,
							'rounds' => [1],
							'groups' => [1, 2],
							'teams'  => [1, 2, 3, 4, 5],
							'games'  => [1, 2, 3, 4],
						],
					],
					'rounds'     => [
						(object) [
							'name'   => 'Round 1',
							'id'     => 1,
							'skip'   => true,
							'groups' => [1, 2],
							'teams'  => [1, 2, 3, 4, 5],
							'games'  => [1, 2, 3, 4],
						],
					],
					'groups'     => [
						(object) [
							'name'    => 'Group 1',
							'id'      => 1,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [1, 2],
							'games'   => [1],
						],
						(object) [
							'name'    => 'Group 2',
							'id'      => 2,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [3, 4, 5],
							'games'   => [2, 3, 4],
						],
					],
					'teams'      => [
						(object) [
							'id'   => 1,
							'name' => 'Team 1',
						],
						(object) [
							'id'   => 2,
							'name' => 'Team 2',
						],
						(object) [
							'id'   => 3,
							'name' => 'Team 3',
						],
						(object) [
							'id'   => 4,
							'name' => 'Team 4',
						],
						(object) [
							'id'   => 5,
							'name' => 'Team 5',
						],
					],
					'games'      => [
						(object) [
							'id'    => 1,
							'teams' => [1, 2],
						],
						(object) [
							'id'    => 2,
							'teams' => [3, 4],
						],
						(object) [
							'id'    => 3,
							'teams' => [4, 5],
						],
						(object) [
							'id'    => 4,
							'teams' => [3, 5],
						],
					],
				]
			],
			[
				[
					'rounds' => [
						(object) [
							'name'   => 'Round 1',
							'id'     => 1,
							'skip'   => true,
							'groups' => [1, 2],
							'teams'  => [1, 2, 3, 4, 5],
							'games'  => [1, 2, 3, 4],
						],
					],
					'groups' => [
						(object) [
							'name'    => 'Group 1',
							'id'      => 1,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [1, 2],
							'games'   => [1],
						],
						(object) [
							'name'    => 'Group 2',
							'id'      => 2,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [3, 4, 5],
							'games'   => [2, 3, 4],
						],
					],
					'teams'  => [
						(object) [
							'id'   => 1,
							'name' => 'Team 1',
						],
						(object) [
							'id'   => 2,
							'name' => 'Team 2',
						],
						(object) [
							'id'   => 3,
							'name' => 'Team 3',
						],
						(object) [
							'id'   => 4,
							'name' => 'Team 4',
						],
						(object) [
							'id'   => 5,
							'name' => 'Team 5',
						],
					],
					'games'  => [
						(object) [
							'id'    => 1,
							'teams' => [1, 2],
						],
						(object) [
							'id'    => 2,
							'teams' => [3, 4],
						],
						(object) [
							'id'    => 3,
							'teams' => [4, 5],
						],
						(object) [
							'id'    => 4,
							'teams' => [3, 5],
						],
					],
				]
			],
			[
				[
					'rounds'       => [
						(object) [
							'name'   => 'Round 1',
							'id'     => 1,
							'skip'   => true,
							'groups' => [1, 2],
							'teams'  => [1, 2, 3, 4, 5],
							'games'  => [1, 2, 3, 4],
						],
					],
					'groups'       => [
						(object) [
							'name'    => 'Group 1',
							'id'      => 1,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [1, 2],
							'games'   => [1],
						],
						(object) [
							'name'    => 'Group 2',
							'id'      => 2,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [3, 4, 5],
							'games'   => [2, 3, 4],
						],
					],
					'progressions' => [
						(object) [
							'from'       => 1,
							'to'         => 2,
							'offset'     => 0,
							'length'     => 2,
							'progressed' => false,
						],
						(object) [
							'from'       => 1,
							'to'         => 2,
							'offset'     => -2,
							'progressed' => true,
						],
						(object) [
							'from'    => 1,
							'to'      => 2,
							'filters' => [
								(object) [
									'what' => 'points',
									'how'  => '>',
									'val'  => 10,
								],
								(object) [
									'what'   => 'wins',
									'how'    => '=',
									'val'    => 1,
									'groups' => [1, 2],
								],
							],
						],
					],
					'teams'        => [
						(object) [
							'id'   => 1,
							'name' => 'Team 1',
						],
						(object) [
							'id'   => 2,
							'name' => 'Team 2',
						],
						(object) [
							'id'   => 3,
							'name' => 'Team 3',
						],
						(object) [
							'id'   => 4,
							'name' => 'Team 4',
						],
						(object) [
							'id'   => 5,
							'name' => 'Team 5',
						],
					],
					'games'        => [
						(object) [
							'id'    => 1,
							'teams' => [1, 2],
						],
						(object) [
							'id'    => 2,
							'teams' => [3, 4],
						],
						(object) [
							'id'    => 3,
							'teams' => [4, 5],
						],
						(object) [
							'id'    => 4,
							'teams' => [3, 5],
						],
					],
				]
			],
			[
				[
					'rounds'       => [
						(object) [
							'name'   => 'Round 1',
							'id'     => 1,
							'skip'   => true,
							'groups' => [1, 2],
							'teams'  => [1, 2, 3, 4, 5],
							'games'  => [1, 2, 3, 4],
						],
					],
					'groups'       => [
						(object) [
							'name'    => 'Group 1',
							'id'      => 1,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [1, 2],
							'games'   => [1],
						],
						(object) [
							'name'    => 'Group 2',
							'id'      => 2,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [3, 4, 5],
							'games'   => [2, 3, 4],
						],
					],
					'progressions' => [
						(object) [
							'from'       => 1,
							'to'         => 2,
							'length'     => 2,
						],
						(object) [
							'from'       => 1,
							'to'         => 2,
							'offset'     => -2,
							'progressed' => true,
						],
						(object) [
							'from'    => 1,
							'to'      => 2,
							'filters' => [
								(object) [
									'how'  => '>',
									'val'  => 10,
								],
								(object) [
									'what'   => 'wins',
									'val'    => 1,
									'groups' => [1, 2],
								],
								(object) [
									'val'    => 1,
								],
								(object) [
									'what'   => 'wins',
								],
								(object) [
									'groups' => [1, 2],
								],
							],
						],
					],
					'teams'        => [
						(object) [
							'id'   => 1,
							'name' => 'Team 1',
						],
						(object) [
							'id'   => 2,
							'name' => 'Team 2',
						],
						(object) [
							'id'   => 3,
							'name' => 'Team 3',
						],
						(object) [
							'id'   => 4,
							'name' => 'Team 4',
						],
						(object) [
							'id'   => 5,
							'name' => 'Team 5',
						],
					],
					'games'        => [
						(object) [
							'id'    => 1,
							'teams' => [1, 2],
						],
						(object) [
							'id'    => 2,
							'teams' => [3, 4],
						],
						(object) [
							'id'    => 3,
							'teams' => [4, 5],
						],
						(object) [
							'id'    => 4,
							'teams' => [3, 5],
						],
					],
				]
			],
			[
				[
					'groups' => [
						(object) [
							'name'    => 'Group 1',
							'id'      => 1,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [1, 2],
							'games'   => [1],
						],
					],
					'teams'  => [
						(object) [
							'id'   => 1,
							'name' => 'Team 1',
						],
						(object) [
							'id'   => 2,
							'name' => 'Team 2',
						],
					],
					'games'  => [
						(object) [
							'id'    => 1,
							'teams' => [1, 2],
						],
					],
				]
			],
			[
				[
					'groups' => [
						(object) [
							'name'    => 'Group 1',
							'id'      => 1,
							'skip'    => true,
							'inGame'  => 2,
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => 10,
								'loss'        => -5,
								'draw'        => 1,
								'second'      => 2,
								'third'       => 3,
								'progression' => 40,
							],
							'teams'   => [1, 2],
							'games'   => [1],
						],
					],
					'teams'  => [
						(object) [
							'id'   => 1,
							'name' => 'Team 1',
						],
						(object) [
							'id'   => 2,
							'name' => 'Team 2',
						],
					],
					'games'  => [
						(object) [
							'id'     => 1,
							'teams'  => [1, 2],
							'scores' => [
								1 => (object) [
									'score'  => 5000,
									'points' => 10,
									'type'   => 'win',
								],
								2 => (object) [
									'score'  => 1000,
									'points' => -5,
									'type'   => 'loss',
								],
							],
						],
					],
				]
			],
			[
				[
					'categories' => [
						(object) [
							'name'   => 'Category 1',
							'id'     => 1,
							'skip'   => true,
							'rounds' => [1, 2],
							'groups' => [1, 2, 3, 4],
							'teams'  => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
						],
					],
					'rounds'     => [
						(object) [
							'name'   => 'Round 1',
							'id'     => 1,
							'skip'   => true,
							'groups' => [1, 2],
							'teams'  => [1, 2, 3, 4, 5],
						],
						(object) [
							'name'   => 'Round 2',
							'id'     => 2,
							'skip'   => true,
							'groups' => [3, 4],
							'teams'  => [6, 7, 8, 9, 10],
						],
					],
					'groups'     => [
						(object) [
							'name'    => 'Group 1',
							'id'      => 1,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [1, 2],
						],
						(object) [
							'name'    => 'Group 2',
							'id'      => 2,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [3, 4, 5],
						],
						(object) [
							'name'    => 'Group 3',
							'id'      => 3,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [6, 7],
						],
						(object) [
							'name'    => 'Group 4',
							'id'      => 4,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
							'teams'   => [8, 9, 10],
						],
					],
					'teams'      => [
						(object) [
							'id'   => 1,
							'name' => 'Team 1',
						],
						(object) [
							'id'   => 2,
							'name' => 'Team 2',
						],
						(object) [
							'id'   => 3,
							'name' => 'Team 3',
						],
						(object) [
							'id'   => 4,
							'name' => 'Team 4',
						],
						(object) [
							'id'   => 5,
							'name' => 'Team 5',
						],
						(object) [
							'id'   => 6,
							'name' => 'Team 6',
						],
						(object) [
							'id'   => 7,
							'name' => 'Team 7',
						],
						(object) [
							'id'   => 8,
							'name' => 'Team 8',
						],
						(object) [
							'id'   => 9,
							'name' => 'Team 9',
						],
						(object) [
							'id'   => 10,
							'name' => 'Team 10',
						],
					],
				]
			],
			[
				[
					'rounds' => [
						(object) [
							'name'   => 'Round 1',
							'id'     => 1,
							'skip'   => true,
							'groups' => [1, 2],
						],
					],
					'groups' => [
						(object) [
							'name'    => 'Group 1',
							'id'      => 1,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
						],
						(object) [
							'name'    => 'Group 2',
							'id'      => 2,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
						],
					],
				]
			],
		];
	}

	public function tournamentExports() : array {
		return [
			[
				[
					'tournament' => (object) [
						'type' => 'general',
						'name' => 'Test',
					]
				],
			],
			[
				[
					'tournament' => (object) [
						'type' => SingleElimination::class,
						'name' => 'Test',
					]
				],
			],
			[
				[
					'tournament' => (object) [
						'type' => DoubleElimination::class,
						'name' => 'Test',
					]
				],
			],
			[
				[
					'tournament' => (object) [
						'type'   => R2G::class,
						'name'   => 'Test',
						'timing' => (object) [
							'play' => random_int(1, 99),
						],
					]
				],
			],
			[
				[
					'tournament' => (object) [
						'type' => 'general',
						'name' => 'Test2',
						'skip' => true,
					]
				],
			],
			[
				[
					'tournament' => (object) [
						'type'   => 'general',
						'name'   => 'Test2',
						'skip'   => false,
						'timing' => (object) [
							'play'         => random_int(1, 99),
							'gameWait'     => random_int(1, 99),
							'categoryWait' => random_int(1, 99),
							'roundWait'    => random_int(1, 99),
						],
					]
				],
			],
		];
	}

	public function categoryExports() : array {
		return [
			[
				[
					'categories' => [
						(object) [
							'name' => 'Test',
							'id'   => 1,
						],
					],
				],
			],
			[
				[
					'categories' => [
						(object) [
							'name' => 'Test',
							'id'   => 1,
							'skip' => true,
						],
					],
				],
			],
			[
				[
					'categories' => [
						(object) [
							'name' => 'Test123',
							'id'   => 'hello',
							'skip' => false,
						],
					],
				],
			],
			[
				[
					'categories' => [
						(object) [
							'name' => 'Test123',
							'skip' => false,
						],
					],
				],
			],
		];
	}

	public function roundExports() : array {
		return [
			[
				[
					'rounds' => [
						(object) [
							'name' => 'Test',
							'id'   => 1,
						],
					],
				],
			],
			[
				[
					'rounds' => [
						(object) [
							'name' => 'Test',
							'id'   => 1,
							'skip' => true,
						],
					],
				],
			],
			[
				[
					'rounds' => [
						(object) [
							'name' => 'Test123',
							'id'   => 'hello',
							'skip' => false,
						],
					],
				],
			],
			[
				[
					'rounds' => [
						(object) [
							'name' => 'Test123',
							'skip' => false,
						],
					],
				],
			],
		];
	}

	public function groupExports() : array {
		return [
			[
				[
					'groups' => [
						(object) [
							'name'    => 'Test',
							'id'      => 1,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
						],
					],
				],
			],
			[
				[
					'groups' => [
						(object) [
							'name'    => 'Test',
							'id'      => 1,
							'skip'    => true,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
						],
					],
				],
			],
			[
				[
					'groups' => [
						(object) [
							'name'    => 'Test123',
							'id'      => 'hello',
							'skip'    => false,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
						],
					],
				],
			],
			[
				[
					'groups' => [
						(object) [
							'name'    => 'Test123',
							'skip'    => false,
							'inGame'  => random_int(2, 4),
							'maxSize' => random_int(5, 99),
							'type'    => Constants::GroupTypes[array_rand(Constants::GroupTypes)],
							'points'  => (object) [
								'win'         => random_int(1, 100),
								'loss'        => random_int(1, 100),
								'draw'        => random_int(1, 100),
								'second'      => random_int(1, 100),
								'third'       => random_int(1, 100),
								'progression' => random_int(1, 100),
							],
						],
					],
				],
			],
		];
	}

	public function teamExports() : array {
		return [
			[
				[
					'teams' => [
						(object) [
							'name' => 'Test',
							'id'   => 1,
						],
					],
				],
			],
			[
				[
					'teams' => [
						(object) [
							'name' => 'Test2',
							'id'   => 'asda',
						],
					],
				],
			],
			[
				[
					'teams' => [
						(object) [
							'name' => 'Test3',
						],
					],
				],
			],
		];
	}

	/**
	 * @dataProvider tournamentExports
	 *
	 * @param array $data
	 */
	public function testTournamentImport(array $data) : void {
		$object = Importer::import($data);
		$object2 = Importer::importJson(json_encode($data, JSON_THROW_ON_ERROR));
		$setting = $data['tournament'];
		if (isset($setting->type)) {
			if ($setting->type === 'general') {
				self::assertInstanceOf(Tournament::class, $object);
				self::assertInstanceOf(Tournament::class, $object2);
			}
			else {
				self::assertInstanceOf($setting->type, $object);
				self::assertInstanceOf($setting->type, $object2);
			}
		}
		if (isset($setting->name)) {
			self::assertEquals($setting->name, $object->getName());
			self::assertEquals($setting->name, $object2->getName());
		}
		if (isset($setting->skip)) {
			self::assertEquals($setting->skip, $object->getSkip());
			self::assertEquals($setting->skip, $object2->getSkip());
		}
		if (isset($setting->timing)) {
			if (isset($setting->timing->play)) {
				self::assertEquals($setting->timing->play, $object->getPlay());
				self::assertEquals($setting->timing->play, $object2->getPlay());
			}
			if (isset($setting->timing->gameWait)) {
				self::assertEquals($setting->timing->gameWait, $object->getGameWait());
				self::assertEquals($setting->timing->gameWait, $object2->getGameWait());
			}
			if (isset($setting->timing->categoryWait)) {
				self::assertEquals($setting->timing->categoryWait, $object->getCategoryWait());
				self::assertEquals($setting->timing->categoryWait, $object2->getCategoryWait());
			}
			if (isset($setting->timing->roundWait)) {
				self::assertEquals($setting->timing->roundWait, $object->getRoundWait());
				self::assertEquals($setting->timing->roundWait, $object2->getRoundWait());
			}
		}
	}

	/**
	 * @dataProvider categoryExports
	 *
	 * @param array $data
	 */
	public function testCategoryImport(array $data) : void {
		$object = Importer::import($data);
		$object2 = Importer::importJson(json_encode($data, JSON_THROW_ON_ERROR));
		$setting = $data['categories'][0];
		self::assertInstanceOf(Category::class, $object);
		self::assertInstanceOf(Category::class, $object2);
		if (isset($setting->id)) {
			self::assertEquals($setting->id, $object->getId());
			self::assertEquals($setting->id, $object2->getId());
		}
		if (isset($setting->name)) {
			self::assertEquals($setting->name, $object->getName());
			self::assertEquals($setting->name, $object2->getName());
		}
		if (isset($setting->skip)) {
			self::assertEquals($setting->skip, $object->getSkip());
			self::assertEquals($setting->skip, $object2->getSkip());
		}
	}

	/**
	 * @dataProvider roundExports
	 *
	 * @param array $data
	 */
	public function testRoundImport(array $data) : void {
		$object = Importer::import($data);
		$object2 = Importer::importJson(json_encode($data, JSON_THROW_ON_ERROR));
		$setting = $data['rounds'][0];
		self::assertInstanceOf(Round::class, $object);
		self::assertInstanceOf(Round::class, $object2);
		if (isset($setting->id)) {
			self::assertEquals($setting->id, $object->getId());
			self::assertEquals($setting->id, $object2->getId());
		}
		if (isset($setting->name)) {
			self::assertEquals($setting->name, $object->getName());
			self::assertEquals($setting->name, $object2->getName());
		}
		if (isset($setting->skip)) {
			self::assertEquals($setting->skip, $object->getSkip());
			self::assertEquals($setting->skip, $object2->getSkip());
		}
	}

	/**
	 * @dataProvider groupExports
	 *
	 * @param array $data
	 */
	public function testGroupImport(array $data) : void {
		$object = Importer::import($data);
		$object2 = Importer::importJson(json_encode($data, JSON_THROW_ON_ERROR));
		$setting = $data['groups'][0];
		self::assertInstanceOf(Group::class, $object);
		self::assertInstanceOf(Group::class, $object2);
		if (isset($setting->id)) {
			self::assertEquals($setting->id, $object->getId());
			self::assertEquals($setting->id, $object2->getId());
		}
		if (isset($setting->name)) {
			self::assertEquals($setting->name, $object->getName());
			self::assertEquals($setting->name, $object2->getName());
		}
		if (isset($setting->skip)) {
			self::assertEquals($setting->skip, $object->getSkip());
			self::assertEquals($setting->skip, $object2->getSkip());
		}
		if (isset($setting->type)) {
			self::assertEquals($setting->type, $object->getType());
			self::assertEquals($setting->type, $object2->getType());
		}
		if (isset($setting->inGame)) {
			self::assertEquals($setting->inGame, $object->getInGame());
			self::assertEquals($setting->inGame, $object2->getInGame());
		}
		if (isset($setting->maxSize)) {
			self::assertEquals($setting->maxSize, $object->getMaxSize());
			self::assertEquals($setting->maxSize, $object2->getMaxSize());
		}
		if (isset($setting->points)) {
			if (isset($setting->points->win)) {
				self::assertEquals($setting->points->win, $object->getWinPoints());
				self::assertEquals($setting->points->win, $object2->getWinPoints());
			}
			if (isset($setting->points->loss)) {
				self::assertEquals($setting->points->loss, $object->getLostPoints());
				self::assertEquals($setting->points->loss, $object2->getLostPoints());
			}
			if (isset($setting->points->draw)) {
				self::assertEquals($setting->points->draw, $object->getDrawPoints());
				self::assertEquals($setting->points->draw, $object2->getDrawPoints());
			}
			if (isset($setting->points->second)) {
				self::assertEquals($setting->points->second, $object->getSecondPoints());
				self::assertEquals($setting->points->second, $object2->getSecondPoints());
			}
			if (isset($setting->points->third)) {
				self::assertEquals($setting->points->third, $object->getThirdPoints());
				self::assertEquals($setting->points->third, $object2->getThirdPoints());
			}
			if (isset($setting->points->progression)) {
				self::assertEquals($setting->points->progression, $object->getProgressPoints());
				self::assertEquals($setting->points->progression, $object2->getProgressPoints());
			}
		}
	}

	/**
	 * @dataProvider teamExports
	 *
	 * @param array $data
	 */
	public function testTeamImport(array $data) : void {
		$object = Importer::import($data);
		$object2 = Importer::importJson(json_encode($data, JSON_THROW_ON_ERROR));
		$setting = $data['teams'][0];
		self::assertInstanceOf(Team::class, $object);
		self::assertInstanceOf(Team::class, $object2);
		if (isset($setting->id)) {
			self::assertEquals($setting->id, $object->getId());
			self::assertEquals($setting->id, $object2->getId());
		}
		if (isset($setting->name)) {
			self::assertEquals($setting->name, $object->getName());
			self::assertEquals($setting->name, $object2->getName());
		}
	}

	/**
	 * @dataProvider complexExports
	 *
	 * @param array $data
	 */
	public function testComplexImport(array $data) : void {
		$object = Importer::import($data);
		$object2 = Importer::importJson(json_encode($data, JSON_THROW_ON_ERROR));

		if (isset($data['tournament'])) {
			/** @var Tournament $tournament1 */
			$tournament1 = $object;
			/** @var Tournament $tournament2 */
			$tournament2 = $object2;

			$setting = $data['tournament'];
			if (isset($setting->type)) {
				if ($setting->type === 'general') {
					self::assertInstanceOf(Tournament::class, $object);
					self::assertInstanceOf(Tournament::class, $object2);
				}
				else {
					self::assertInstanceOf($setting->type, $object);
					self::assertInstanceOf($setting->type, $object2);
				}
			}
			if (isset($setting->name)) {
				self::assertEquals($setting->name, $object->getName());
				self::assertEquals($setting->name, $object2->getName());
			}
			if (isset($setting->skip)) {
				self::assertEquals($setting->skip, $object->getSkip());
				self::assertEquals($setting->skip, $object2->getSkip());
			}
			if (isset($setting->timing)) {
				if (isset($setting->timing->play)) {
					self::assertEquals($setting->timing->play, $object->getPlay());
					self::assertEquals($setting->timing->play, $object2->getPlay());
				}
				if (isset($setting->timing->gameWait)) {
					self::assertEquals($setting->timing->gameWait, $object->getGameWait());
					self::assertEquals($setting->timing->gameWait, $object2->getGameWait());
				}
				if (isset($setting->timing->categoryWait)) {
					self::assertEquals($setting->timing->categoryWait, $object->getCategoryWait());
					self::assertEquals($setting->timing->categoryWait, $object2->getCategoryWait());
				}
				if (isset($setting->timing->roundWait)) {
					self::assertEquals($setting->timing->roundWait, $object->getRoundWait());
					self::assertEquals($setting->timing->roundWait, $object2->getRoundWait());
				}
			}
			if (isset($setting->categories)) {
				$ids1 = $object->getContainer()->getHierarchyLevelQuery(Category::class)->ids()->get();
				$ids2 = $object2->getContainer()->getHierarchyLevelQuery(Category::class)->ids()->get();
				self::assertEquals($setting->categories, $ids1);
				self::assertEquals($setting->categories, $ids2);
			}
			if (isset($setting->rounds)) {
				$ids1 = $object->getContainer()->getHierarchyLevelQuery(Round::class)->ids()->get();
				$ids2 = $object2->getContainer()->getHierarchyLevelQuery(Round::class)->ids()->get();
				self::assertEquals($setting->rounds, $ids1);
				self::assertEquals($setting->rounds, $ids2);
			}
			if (isset($setting->groups)) {
				$ids1 = $object->getContainer()->getHierarchyLevelQuery(Group::class)->ids()->get();
				$ids2 = $object2->getContainer()->getHierarchyLevelQuery(Group::class)->ids()->get();
				self::assertEquals($setting->groups, $ids1);
				self::assertEquals($setting->groups, $ids2);
			}
		}

		foreach ($data['categories'] ?? [] as $setting) {
			if (!isset($data['tournament'])) {
				/** @var Category $category1 */
				$category1 = $object;
				/** @var Category $category2 */
				$category2 = $object2;
				self::assertInstanceOf(Category::class, $object);
				self::assertInstanceOf(Category::class, $object2);
			}
			else {
				$object = $tournament1->getContainer()->getHierarchyLevelQuery(Category::class)->whereId($setting->id)->getFirst();
				$object2 = $tournament2->getContainer()->getHierarchyLevelQuery(Category::class)->whereId($setting->id)->getFirst();
			}
			if (isset($setting->id)) {
				self::assertEquals($setting->id, $object->getId());
				self::assertEquals($setting->id, $object2->getId());
			}
			if (isset($setting->name)) {
				self::assertEquals($setting->name, $object->getName());
				self::assertEquals($setting->name, $object2->getName());
			}
			if (isset($setting->skip)) {
				self::assertEquals($setting->skip, $object->getSkip());
				self::assertEquals($setting->skip, $object2->getSkip());
			}
			if (isset($setting->rounds)) {
				$ids1 = $object->getContainer()->getHierarchyLevelQuery(Round::class)->ids()->get();
				$ids2 = $object2->getContainer()->getHierarchyLevelQuery(Round::class)->ids()->get();
				self::assertEquals($setting->rounds, $ids1);
				self::assertEquals($setting->rounds, $ids2);
			}
			if (isset($setting->groups)) {
				$ids1 = $object->getContainer()->getHierarchyLevelQuery(Group::class)->ids()->get();
				$ids2 = $object2->getContainer()->getHierarchyLevelQuery(Group::class)->ids()->get();
				self::assertEquals($setting->groups, $ids1);
				self::assertEquals($setting->groups, $ids2);
			}
		}

		foreach ($data['rounds'] ?? [] as $setting) {
			if (!isset($data['tournament']) && !isset($data['categories'])) {
				/** @var Round $round1 */
				$round1 = $object;
				/** @var Round $round2 */
				$round2 = $object2;
				self::assertInstanceOf(Round::class, $object);
				self::assertInstanceOf(Round::class, $object2);
			}
			elseif (isset($data['tournament'])) {
				$object = $tournament1->getContainer()->getHierarchyLevelQuery(Round::class)->whereId($setting->id)->getFirst();
				$object2 = $tournament2->getContainer()->getHierarchyLevelQuery(Round::class)->whereId($setting->id)->getFirst();
			}
			else {
				$object = $category1->getContainer()->getHierarchyLevelQuery(Round::class)->whereId($setting->id)->getFirst();
				$object2 = $category2->getContainer()->getHierarchyLevelQuery(Round::class)->whereId($setting->id)->getFirst();
			}
			if (isset($setting->id)) {
				self::assertEquals($setting->id, $object->getId());
				self::assertEquals($setting->id, $object2->getId());
			}
			if (isset($setting->name)) {
				self::assertEquals($setting->name, $object->getName());
				self::assertEquals($setting->name, $object2->getName());
			}
			if (isset($setting->skip)) {
				self::assertEquals($setting->skip, $object->getSkip());
				self::assertEquals($setting->skip, $object2->getSkip());
			}
			if (isset($setting->groups)) {
				$ids1 = $object->getContainer()->getHierarchyLevelQuery(Group::class)->ids()->get();
				$ids2 = $object2->getContainer()->getHierarchyLevelQuery(Group::class)->ids()->get();
				self::assertEquals($setting->groups, $ids1);
				self::assertEquals($setting->groups, $ids2);
			}
		}

		foreach ($data['groups'] ?? [] as $setting) {
			if (!isset($data['tournament']) && !isset($data['categories']) && !isset($data['rounds'])) {
				/** @var Group $group1 */
				$group1 = $object;
				/** @var Group $group2 */
				$group2 = $object2;
				self::assertInstanceOf(Group::class, $object);
				self::assertInstanceOf(Group::class, $object2);
			}
			elseif (isset($data['tournament'])) {
				$object = $tournament1->getContainer()->getHierarchyLevelQuery(Group::class)->whereId($setting->id)->getFirst();
				$object2 = $tournament2->getContainer()->getHierarchyLevelQuery(Group::class)->whereId($setting->id)->getFirst();
			}
			elseif (isset($data['categories'])) {
				$object = $category1->getContainer()->getHierarchyLevelQuery(Group::class)->whereId($setting->id)->getFirst();
				$object2 = $category2->getContainer()->getHierarchyLevelQuery(Group::class)->whereId($setting->id)->getFirst();
			}
			else {
				$object = $round1->getContainer()->getHierarchyLevelQuery(Group::class)->whereId($setting->id)->getFirst();
				$object2 = $round2->getContainer()->getHierarchyLevelQuery(Group::class)->whereId($setting->id)->getFirst();
			}
			if (isset($setting->id)) {
				self::assertEquals($setting->id, $object->getId());
				self::assertEquals($setting->id, $object2->getId());
			}
			if (isset($setting->name)) {
				self::assertEquals($setting->name, $object->getName());
				self::assertEquals($setting->name, $object2->getName());
			}
			if (isset($setting->skip)) {
				self::assertEquals($setting->skip, $object->getSkip());
				self::assertEquals($setting->skip, $object2->getSkip());
			}
			if (isset($setting->type)) {
				self::assertEquals($setting->type, $object->getType());
				self::assertEquals($setting->type, $object2->getType());
			}
			if (isset($setting->inGame)) {
				self::assertEquals($setting->inGame, $object->getInGame());
				self::assertEquals($setting->inGame, $object2->getInGame());
			}
			if (isset($setting->maxSize)) {
				self::assertEquals($setting->maxSize, $object->getMaxSize());
				self::assertEquals($setting->maxSize, $object2->getMaxSize());
			}
			if (isset($setting->points)) {
				if (isset($setting->points->win)) {
					self::assertEquals($setting->points->win, $object->getWinPoints());
					self::assertEquals($setting->points->win, $object2->getWinPoints());
				}
				if (isset($setting->points->loss)) {
					self::assertEquals($setting->points->loss, $object->getLostPoints());
					self::assertEquals($setting->points->loss, $object2->getLostPoints());
				}
				if (isset($setting->points->draw)) {
					self::assertEquals($setting->points->draw, $object->getDrawPoints());
					self::assertEquals($setting->points->draw, $object2->getDrawPoints());
				}
				if (isset($setting->points->second)) {
					self::assertEquals($setting->points->second, $object->getSecondPoints());
					self::assertEquals($setting->points->second, $object2->getSecondPoints());
				}
				if (isset($setting->points->third)) {
					self::assertEquals($setting->points->third, $object->getThirdPoints());
					self::assertEquals($setting->points->third, $object2->getThirdPoints());
				}
				if (isset($setting->points->progression)) {
					self::assertEquals($setting->points->progression, $object->getProgressPoints());
					self::assertEquals($setting->points->progression, $object2->getProgressPoints());
				}
			}
		}

		foreach ($data['teams'] ?? [] as $setting) {
			if (!isset($data['tournament']) && !isset($data['categories']) && !isset($data['rounds']) && !isset($data['groups'])) {
				self::assertInstanceOf(Team::class, $object);
				self::assertInstanceOf(Team::class, $object2);
			}
			elseif (isset($data['tournament'])) {
				$object = $tournament1->getTeamContainer()->whereId($setting->id)->getFirst();
				$object2 = $tournament2->getTeamContainer()->whereId($setting->id)->getFirst();
			}
			elseif (isset($data['categories'])) {
				$object = $category1->getTeamContainer()->whereId($setting->id)->getFirst();
				$object2 = $category2->getTeamContainer()->whereId($setting->id)->getFirst();
			}
			elseif (isset($data['rounds'])) {
				$object = $round1->getTeamContainer()->whereId($setting->id)->getFirst();
				$object2 = $round2->getTeamContainer()->whereId($setting->id)->getFirst();
			}
			else {
				$object = $group1->getTeamContainer()->whereId($setting->id)->getFirst();
				$object2 = $group2->getTeamContainer()->whereId($setting->id)->getFirst();
			}
			if (isset($setting->id)) {
				self::assertEquals($setting->id, $object->getId());
				self::assertEquals($setting->id, $object2->getId());
			}
			if (isset($setting->name)) {
				self::assertEquals($setting->name, $object->getName());
				self::assertEquals($setting->name, $object2->getName());
			}
		}

		foreach ($data['games'] ?? [] as $setting) {
			if (!isset($data['tournament']) && !isset($data['categories']) && !isset($data['rounds']) && !isset($data['groups'])) {
				self::assertTrue(false, 'No container to get the game objects from.');
			}

			if (isset($data['tournament'])) {
				$object = $tournament1->getGameContainer()->whereId($setting->id)->getFirst();
				$object2 = $tournament2->getGameContainer()->whereId($setting->id)->getFirst();
			}
			elseif (isset($data['categories'])) {
				$object = $category1->getGameContainer()->whereId($setting->id)->getFirst();
				$object2 = $category2->getGameContainer()->whereId($setting->id)->getFirst();
			}
			elseif (isset($data['rounds'])) {
				$object = $round1->getGameContainer()->whereId($setting->id)->getFirst();
				$object2 = $round2->getGameContainer()->whereId($setting->id)->getFirst();
			}
			else {
				$object = $group1->getGameContainer()->whereId($setting->id)->getFirst();
				$object2 = $group2->getGameContainer()->whereId($setting->id)->getFirst();
			}
			/** @var Game|null $object */
			/** @var Game|null $object1 */

			if (isset($setting->id)) {
				self::assertEquals($setting->id, $object->getId());
				self::assertEquals($setting->id, $object2->getId());
			}
			if (isset($setting->teams)) {
				self::assertEquals($setting->teams, $object->getTeamsIds());
				self::assertEquals($setting->teams, $object2->getTeamsIds());
			}
			if (isset($setting->scores)) {
				$expected = array_map(static function($object) {
					return (array) $object;
				}, $setting->scores);
				self::assertEquals($expected, $object->getResults());
				self::assertEquals($expected, $object2->getResults());
			}
		}

		foreach ($data['progressions'] ?? [] as $key => $setting) {
			if (!isset($setting->from, $setting->to)) {
				continue;
			}

			if (!isset($data['tournament']) && !isset($data['categories']) && !isset($data['rounds'])) {
				self::assertTrue(false, 'No container to get the group object from.');
			}

			if (isset($data['tournament'])) {
				$from1 = $tournament1->getContainer()->getHierarchyLevelQuery(Group::class)->whereId($setting->from)->getFirst();
				$from2 = $tournament2->getContainer()->getHierarchyLevelQuery(Group::class)->whereId($setting->from)->getFirst();
				$to1 = $tournament1->getContainer()->getHierarchyLevelQuery(Group::class)->whereId($setting->to)->getFirst();
				$to2 = $tournament2->getContainer()->getHierarchyLevelQuery(Group::class)->whereId($setting->to)->getFirst();
			}
			elseif (isset($data['categories'])) {
				$from1 = $category1->getContainer()->getHierarchyLevelQuery(Group::class)->whereId($setting->from)->getFirst();
				$from2 = $category2->getContainer()->getHierarchyLevelQuery(Group::class)->whereId($setting->from)->getFirst();
				$to1 = $category1->getContainer()->getHierarchyLevelQuery(Group::class)->whereId($setting->to)->getFirst();
				$to2 = $category2->getContainer()->getHierarchyLevelQuery(Group::class)->whereId($setting->to)->getFirst();
			}
			else {
				$from1 = $round1->getContainer()->getHierarchyLevelQuery(Group::class)->whereId($setting->from)->getFirst();
				$from2 = $round2->getContainer()->getHierarchyLevelQuery(Group::class)->whereId($setting->from)->getFirst();
				$to1 = $round1->getContainer()->getHierarchyLevelQuery(Group::class)->whereId($setting->to)->getFirst();
				$to2 = $round2->getContainer()->getHierarchyLevelQuery(Group::class)->whereId($setting->to)->getFirst();
			}

			/** @var Group $from1 */
			/** @var Group $from2 */
			/** @var Group $to1 */
			/** @var Group $to2 */

			$progressions1 = $from1->getProgressions();
			$progressions2 = $from2->getProgressions();

			foreach ([[$progressions1, $to1], [$progressions2, $to2]] as [$progressions, $to]) {
				$found = false;
				foreach ($progressions as $progression) {
					if ($progression->getTo() === $to) {
						if (isset($setting->offset, $setting->length, $setting->filters)
						&& $progression->getStart() === $setting->offset
						&& $progression->getLen() === $setting->length
						&& count($progression->getFilters()) === count($setting->filters)) {
							$found = true;
							$this->checkFilters($setting->filters, $progression->getFilters());
							if (isset($setting->progressed)) {
								self::assertSame($setting->progressed, $progression->isProgressed());
							}
							break;
						}

						if (isset($setting->offset, $setting->length)
						&& $progression->getStart() === $setting->offset
						&& $progression->getLen() === $setting->length
						&& count($progression->getFilters()) === 0) {
							$found = true;
							if (isset($setting->progressed)) {
								self::assertSame($setting->progressed, $progression->isProgressed());
							}
							break;
						}

						if (isset($setting->offset, $setting->filters)
						&& $progression->getStart() === $setting->offset
						&& $progression->getLen() === null
						&& count($progression->getFilters()) === count($setting->filters)) {
							$found = true;
							$this->checkFilters($setting->filters, $progression->getFilters());
							if (isset($setting->progressed)) {
								self::assertSame($setting->progressed, $progression->isProgressed());
							}
							break;
						}

						if (isset($setting->length, $setting->filters)
						&& $progression->getStart() === 0
						&& $progression->getLen() === $setting->length
						&& count($progression->getFilters()) === count($setting->filters)) {
							$found = true;
							$this->checkFilters($setting->filters, $progression->getFilters());
							if (isset($setting->progressed)) {
								self::assertSame($setting->progressed, $progression->isProgressed());
							}
							break;
						}

						if (isset($setting->offset)
						&& $progression->getStart() === $setting->offset
						&& $progression->getLen() === null
						&& count($progression->getFilters()) === 0) {
							$found = true;
							if (isset($setting->progressed)) {
								self::assertSame($setting->progressed, $progression->isProgressed());
							}
							break;
						}

						if (isset($setting->length)
						&& $progression->getStart() === 0
						&& $progression->getLen() === $setting->length
						&& count($progression->getFilters()) === 0) {
							$found = true;
							if (isset($setting->progressed)) {
								self::assertSame($setting->progressed, $progression->isProgressed());
							}
							break;
						}

						if (
							isset($setting->filters)
							&& $progression->getStart() === 0
							&& $progression->getLen() === null
							&& count($progression->getFilters()) === count($setting->filters)
						) {
							$found = true;
							$this->checkFilters($setting->filters, $progression->getFilters());
							if (isset($setting->progressed)) {
								self::assertSame($setting->progressed, $progression->isProgressed());
							}
							break;
						}
					}
				}
				if (!$found) {
					self::assertTrue(false, 'Progression ('.$key.') is not set on group ('.$setting->from.')');
				}
			}
		}

	}

	/**
	 * @param object[]     $setting
	 * @param TeamFilter[] $filters
	 */
	private function checkFilters(array $setting, array $filters) : void {
		foreach ($setting as $key => $filterSetting) {
			$found = false;
			foreach ($filters as $filter) {

				if (isset($filterSetting->what, $filterSetting->how, $filterSetting->val, $filterSetting->groups)
				&& $filterSetting->what === $filter->getWhat()
				&& $filterSetting->how === $filter->getHow()
				&& $filterSetting->val === $filter->getVal()
				&& $filterSetting->groups === $filter->getGroups()) {
					$found = true;
					break;
				}

				if (isset($filterSetting->how, $filterSetting->val, $filterSetting->groups)
				&& 'points' === $filter->getWhat()
				&& $filterSetting->how === $filter->getHow()
				&& $filterSetting->val === $filter->getVal()
				&& $filterSetting->groups === $filter->getGroups()) {
					$found = true;
					break;
				}

				if (isset($filterSetting->what, $filterSetting->val, $filterSetting->groups)
				&& $filterSetting->what === $filter->getWhat()
				&& '>' === $filter->getHow()
				&& $filterSetting->val === $filter->getVal()
				&& $filterSetting->groups === $filter->getGroups()) {
					$found = true;
					break;
				}

				if (isset($filterSetting->what, $filterSetting->how, $filterSetting->groups)
				&& $filterSetting->what === $filter->getWhat()
				&& $filterSetting->how === $filter->getHow()
				&& 0 === $filter->getVal()
				&& $filterSetting->groups === $filter->getGroups()) {
					$found = true;
					break;
				}

				if (
					isset($filterSetting->what, $filterSetting->how, $filterSetting->val)
					&& $filterSetting->what === $filter->getWhat()
					&& $filterSetting->how === $filter->getHow()
					&& $filterSetting->val === $filter->getVal()
					&& [] === $filter->getGroups()
				) {
					$found = true;
					break;
				}

				if (isset($filterSetting->what, $filterSetting->how)
					&& $filterSetting->what === $filter->getWhat()
					&& $filterSetting->how === $filter->getHow()
					&& 0 === $filter->getVal()
					&& [] === $filter->getGroups()) {
					$found = true;
					break;
				}

				if (isset($filterSetting->what, $filterSetting->groups)
					&& $filterSetting->what === $filter->getWhat()
					&& '>' === $filter->getHow()
					&& 0 === $filter->getVal()
					&& $filterSetting->groups === $filter->getGroups()) {
					$found = true;
					break;
				}

				if (isset($filterSetting->val, $filterSetting->groups)
					&& 'points' === $filter->getWhat()
					&& '>' === $filter->getHow()
					&& $filterSetting->val === $filter->getVal()
					&& $filterSetting->groups === $filter->getGroups()) {
					$found = true;
					break;
				}

				if (isset($filterSetting->what, $filterSetting->val)
					&& $filterSetting->what === $filter->getWhat()
					&& '>' === $filter->getHow()
					&& $filterSetting->val === $filter->getVal()
					&& [] === $filter->getGroups()) {
					$found = true;
					break;
				}

				if (isset($filterSetting->how, $filterSetting->groups)
					&& 'points' === $filter->getWhat()
					&& $filterSetting->how === $filter->getHow()
					&& 0 === $filter->getVal()
					&& $filterSetting->groups === $filter->getGroups()) {
					$found = true;
					break;
				}

				if (isset($filterSetting->what, $filterSetting->groups)
					&& $filterSetting->what === $filter->getWhat()
					&& '>' === $filter->getHow()
					&& 0 === $filter->getVal()
					&& $filterSetting->groups === $filter->getGroups()) {
					$found = true;
					break;
				}

				if (isset($filterSetting->what)
					&& $filterSetting->what === $filter->getWhat()
					&& '>' === $filter->getHow()
					&& 0 === $filter->getVal()
					&& [] === $filter->getGroups()) {
					$found = true;
					break;
				}

				if (isset($filterSetting->how)
					&& 'points' === $filter->getWhat()
					&& $filterSetting->how === $filter->getHow()
					&& 0 === $filter->getVal()
					&& [] === $filter->getGroups()) {
					$found = true;
					break;
				}

				if (isset($filterSetting->val)
					&& 'points' === $filter->getWhat()
					&& '>' === $filter->getHow()
					&& $filterSetting->val === $filter->getVal()
					&& [] === $filter->getGroups()) {
					$found = true;
					break;
				}

				if (isset($filterSetting->groups)
					&& 'points' === $filter->getWhat()
					&& '>' === $filter->getHow()
					&& 0 === $filter->getVal()
					&& $filterSetting->groups === $filter->getGroups()) {
					$found = true;
					break;
				}

			}
			if (!$found) {
				self::assertTrue(false, 'Filter ('.$key.') not found');
			}
		}
	}

	// TODO: Test invalid values

}