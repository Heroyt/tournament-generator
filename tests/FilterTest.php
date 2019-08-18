<?php
use PHPUnit\Framework\TestCase;

/**
 *
 */
class FilterTest extends TestCase
{

	/** @test */
	public function check_filter_creation() {
		$group = new \TournamentGenerator\Group('Group', 'g1');

		$team1 = $group->team('Team 1', 't1'); // Score: 200 Points: 3
		$team2 = $group->team('Team 2', 't2'); // Score: 450 Points: 3
		$team3 = $group->team('Team 3', 't3'); // Score: 420 Points: 6
		$team4 = $group->team('Team 4', 't4'); // Score: 390 Points: 6

		$group->game([$team1, $team2])->setResults(['t1' => 100, 't2' => 200]);
		$group->game([$team2, $team3])->setResults(['t3' => 300, 't2' => 150]);
		$group->game([$team1, $team3])->setResults(['t1' => 90, 't3' => 50]);
		$group->game([$team4, $team1])->setResults(['t4' => 30, 't1' => 10]);
		$group->game([$team4, $team2])->setResults(['t4' => 300, 't2' => 100]);
		$group->game([$team4, $team3])->setResults(['t4' => 60, 't3' => 70]);

		$filter1 = new \TournamentGenerator\TeamFilter('points', '>', 3, [$group]);
		$filter2 = new \TournamentGenerator\TeamFilter('score', '>', 400, [$group]);

		$this->assertCount(2, $group->getTeams([$filter1]));
		$this->assertCount(1, $group->getTeams([$filter1, $filter2]));
	}

	/** @test */
	public function check_filter_creation_multi() {
		$group = new \TournamentGenerator\Group('Group', 'g1');

		$team1 = $group->team('Team 1', 't1'); // Score: 200 Points: 3 Losses: 2 Wins: 1 Draws: 0
		$team2 = $group->team('Team 2', 't2'); // Score: 500 Points: 4 Losses: 1 Wins: 1 Draws: 1
		$team3 = $group->team('Team 3', 't3'); // Score: 420 Points: 4 Losses: 1 Wins: 1 Draws: 1
		$team4 = $group->team('Team 4', 't4'); // Score: 390 Points: 6 Losses: 1 Wins: 2 Draws: 0

		$group->game([$team1, $team2])->setResults(['t1' => 100, 't2' => 200]);
		$group->game([$team2, $team3])->setResults(['t3' => 200, 't2' => 200]);
		$group->game([$team1, $team3])->setResults(['t1' => 90, 't3' => 50]);
		$group->game([$team4, $team1])->setResults(['t4' => 30, 't1' => 10]);
		$group->game([$team4, $team2])->setResults(['t4' => 300, 't2' => 100]);
		$group->game([$team4, $team3])->setResults(['t4' => 60, 't3' => 170]);

		$filter1 = new \TournamentGenerator\TeamFilter('points', '>', 3, [$group]);  // Team 2, Team 3, Team 4
		$filter2 = new \TournamentGenerator\TeamFilter('score', '>', 400, [$group]); // Team 2, Team 3
		$filter3 = new \TournamentGenerator\TeamFilter('score', '=', 200, [$group]); // Team 1
		$filter4 = new \TournamentGenerator\TeamFilter('draws', '>=', 1, [$group]);  // Team 2, Team 3
		$filter5 = new \TournamentGenerator\TeamFilter('score', '<', 450, [$group]); // Team 1, Team 3, Team 4
		$filter6 = new \TournamentGenerator\TeamFilter('team', '=', $team2, [$group]); // Team 2

		$filter = [
			'or' => [
				'and' => [$filter1, $filter2],
				$filter3
			]
		];

		$this->assertCount(3, $group->getTeams($filter));

		$filter = [
			'or' => [ // Team 1, Team 2
				$filter3,
				$filter6
			]
		];
		$this->assertCount(2, $group->getTeams($filter));

		$filter = [
			'or' => [ // Team 1, Team 2, Team 3, Team 4
				$filter5,
				$filter4
			]
		];
		$this->assertCount(4, $group->getTeams($filter));

		$filter = [
			'and' => [ // Team2, Team3
				$filter1,
				$filter2
			]
		];
		$this->assertCount(2, $group->getTeams($filter));

		$filter = [
			'and' => [ // Team 2, Team 3
				'or' => [ // Team 1, Team 2, Team 3, Team 4
					$filter5,
					$filter4
				],
				'and' => [ // Team2, Team3
					$filter1,
					$filter2
				]
			]
		];
		$this->assertCount(2, $group->getTeams($filter));

		$filter = [
			'or' => [ // Team 1, Team 2, Team 3
				'or' => [ // Team 1, Team 2
					$filter3,
					$filter6
				],
				'and' => [ // Team 2, Team 3
					'or' => [ // Team 1, Team 2, Team 3, Team 4
						$filter5,
						$filter4
					],
					'and' => [ // Team2, Team3
						$filter1,
						$filter2
					]
				]
			]
		];
		$this->assertCount(3, $group->getTeams($filter));
	}

	/** @test */
	public function check_filter_creation_multi_false_filter_multi() {
		$group = new \TournamentGenerator\Group('Group', 'g1');

		$team1 = $group->team('Team 1', 't1'); // Score: 200 Points: 3 Losses: 2 Wins: 1 Draws: 0
		$team2 = $group->team('Team 2', 't2'); // Score: 500 Points: 4 Losses: 1 Wins: 1 Draws: 1
		$team3 = $group->team('Team 3', 't3'); // Score: 420 Points: 4 Losses: 1 Wins: 1 Draws: 1
		$team4 = $group->team('Team 4', 't4'); // Score: 390 Points: 6 Losses: 1 Wins: 2 Draws: 0

		$group->game([$team1, $team2])->setResults(['t1' => 100, 't2' => 200]);
		$group->game([$team2, $team3])->setResults(['t3' => 200, 't2' => 200]);
		$group->game([$team1, $team3])->setResults(['t1' => 90, 't3' => 50]);
		$group->game([$team4, $team1])->setResults(['t4' => 30, 't1' => 10]);
		$group->game([$team4, $team2])->setResults(['t4' => 300, 't2' => 100]);
		$group->game([$team4, $team3])->setResults(['t4' => 60, 't3' => 170]);

		$filter1 = new \TournamentGenerator\TeamFilter('points', '>', 3, [$group]);  // Team 2, Team 3, Team 4
		$filter2 = new \TournamentGenerator\TeamFilter('score', '>', 400, [$group]); // Team 2, Team 3
		$filter3 = new \TournamentGenerator\TeamFilter('score', '=', 200, [$group]); // Team 1
		$filter4 = new \TournamentGenerator\TeamFilter('draws', '>=', 1, [$group]);  // Team 2, Team 3
		$filter5 = new \TournamentGenerator\TeamFilter('score', '<', 450, [$group]); // Team 1, Team 3, Team 4
		$filter6 = new \TournamentGenerator\TeamFilter('team', '=', $team2, [$group]); // Team 2

		$this->expectException(Exception::class);
		$group->getTeams(['not a valid filter']);
	}

	/** @test */
	public function check_filter_creation_multi_false_operrand_multi() {
		$group = new \TournamentGenerator\Group('Group', 'g1');

		$team1 = $group->team('Team 1', 't1'); // Score: 200 Points: 3 Losses: 2 Wins: 1 Draws: 0
		$team2 = $group->team('Team 2', 't2'); // Score: 500 Points: 4 Losses: 1 Wins: 1 Draws: 1
		$team3 = $group->team('Team 3', 't3'); // Score: 420 Points: 4 Losses: 1 Wins: 1 Draws: 1
		$team4 = $group->team('Team 4', 't4'); // Score: 390 Points: 6 Losses: 1 Wins: 2 Draws: 0

		$group->game([$team1, $team2])->setResults(['t1' => 100, 't2' => 200]);
		$group->game([$team2, $team3])->setResults(['t3' => 200, 't2' => 200]);
		$group->game([$team1, $team3])->setResults(['t1' => 90, 't3' => 50]);
		$group->game([$team4, $team1])->setResults(['t4' => 30, 't1' => 10]);
		$group->game([$team4, $team2])->setResults(['t4' => 300, 't2' => 100]);
		$group->game([$team4, $team3])->setResults(['t4' => 60, 't3' => 170]);

		$filter1 = new \TournamentGenerator\TeamFilter('points', '>', 3, [$group]);  // Team 2, Team 3, Team 4
		$filter2 = new \TournamentGenerator\TeamFilter('score', '>', 400, [$group]); // Team 2, Team 3
		$filter3 = new \TournamentGenerator\TeamFilter('score', '=', 200, [$group]); // Team 1
		$filter4 = new \TournamentGenerator\TeamFilter('draws', '>=', 1, [$group]);  // Team 2, Team 3
		$filter5 = new \TournamentGenerator\TeamFilter('score', '<', 450, [$group]); // Team 1, Team 3, Team 4
		$filter6 = new \TournamentGenerator\TeamFilter('team', '=', $team2, [$group]); // Team 2

		$filter = [
			'not valid operrand' => [ // Team 1, Team 2, Team 3
				'or' => [ // Team 1, Team 2
					$filter3,
					$filter6
				],
				'and' => [ // Team 2, Team 3
					'or' => [ // Team 1, Team 2, Team 3, Team 4
						$filter5,
						$filter4
					],
					'and' => [ // Team2, Team3
						$filter1,
						 $filter2
					]
				]
			]
		];
		$this->expectException(Exception::class);
		$group->getTeams($filter);

	}

	/** @test */
	public function check_filter_creation_multi_false_filter_and() {
		$group = new \TournamentGenerator\Group('Group', 'g1');

		$team1 = $group->team('Team 1', 't1'); // Score: 200 Points: 3 Losses: 2 Wins: 1 Draws: 0
		$team2 = $group->team('Team 2', 't2'); // Score: 500 Points: 4 Losses: 1 Wins: 1 Draws: 1
		$team3 = $group->team('Team 3', 't3'); // Score: 420 Points: 4 Losses: 1 Wins: 1 Draws: 1
		$team4 = $group->team('Team 4', 't4'); // Score: 390 Points: 6 Losses: 1 Wins: 2 Draws: 0

		$group->game([$team1, $team2])->setResults(['t1' => 100, 't2' => 200]);
		$group->game([$team2, $team3])->setResults(['t3' => 200, 't2' => 200]);
		$group->game([$team1, $team3])->setResults(['t1' => 90, 't3' => 50]);
		$group->game([$team4, $team1])->setResults(['t4' => 30, 't1' => 10]);
		$group->game([$team4, $team2])->setResults(['t4' => 300, 't2' => 100]);
		$group->game([$team4, $team3])->setResults(['t4' => 60, 't3' => 170]);

		$filter1 = new \TournamentGenerator\TeamFilter('points', '>', 3, [$group]);  // Team 2, Team 3, Team 4
		$filter2 = new \TournamentGenerator\TeamFilter('score', '>', 400, [$group]); // Team 2, Team 3
		$filter3 = new \TournamentGenerator\TeamFilter('score', '=', 200, [$group]); // Team 1
		$filter4 = new \TournamentGenerator\TeamFilter('draws', '>=', 1, [$group]);  // Team 2, Team 3
		$filter5 = new \TournamentGenerator\TeamFilter('score', '<', 450, [$group]); // Team 1, Team 3, Team 4
		$filter6 = new \TournamentGenerator\TeamFilter('team', '=', $team2, [$group]); // Team 2

		$filter = [
			'or' => [ // Team 1, Team 2, Team 3
				'or' => [ // Team 1, Team 2
					$filter3,
					$filter6
				],
				'and' => [ // Team 2, Team 3
					'or' => [ // Team 1, Team 2, Team 3, Team 4
						$filter5,
						$filter4
					],
					'and' => [ // Team2, Team3
						'not a valid filter',
						 $filter2
					]
				]
			]
		];
		$this->expectException(Exception::class);
		$group->getTeams($filter);
	}

	/** @test */
	public function check_filter_creation_multi_false_operrand_and() {
		$group = new \TournamentGenerator\Group('Group', 'g1');

		$team1 = $group->team('Team 1', 't1'); // Score: 200 Points: 3 Losses: 2 Wins: 1 Draws: 0
		$team2 = $group->team('Team 2', 't2'); // Score: 500 Points: 4 Losses: 1 Wins: 1 Draws: 1
		$team3 = $group->team('Team 3', 't3'); // Score: 420 Points: 4 Losses: 1 Wins: 1 Draws: 1
		$team4 = $group->team('Team 4', 't4'); // Score: 390 Points: 6 Losses: 1 Wins: 2 Draws: 0

		$group->game([$team1, $team2])->setResults(['t1' => 100, 't2' => 200]);
		$group->game([$team2, $team3])->setResults(['t3' => 200, 't2' => 200]);
		$group->game([$team1, $team3])->setResults(['t1' => 90, 't3' => 50]);
		$group->game([$team4, $team1])->setResults(['t4' => 30, 't1' => 10]);
		$group->game([$team4, $team2])->setResults(['t4' => 300, 't2' => 100]);
		$group->game([$team4, $team3])->setResults(['t4' => 60, 't3' => 170]);

		$filter1 = new \TournamentGenerator\TeamFilter('points', '>', 3, [$group]);  // Team 2, Team 3, Team 4
		$filter2 = new \TournamentGenerator\TeamFilter('score', '>', 400, [$group]); // Team 2, Team 3
		$filter3 = new \TournamentGenerator\TeamFilter('score', '=', 200, [$group]); // Team 1
		$filter4 = new \TournamentGenerator\TeamFilter('draws', '>=', 1, [$group]);  // Team 2, Team 3
		$filter5 = new \TournamentGenerator\TeamFilter('score', '<', 450, [$group]); // Team 1, Team 3, Team 4
		$filter6 = new \TournamentGenerator\TeamFilter('team', '=', $team2, [$group]); // Team 2

		$filter = [
			'or' => [ // Team 1, Team 2, Team 3
				'or' => [ // Team 1, Team 2
					$filter3,
					$filter6
				],
				'and' => [ // Team 2, Team 3
					'not a valid operrand' => [ // Team 1, Team 2, Team 3, Team 4
						$filter5,
						$filter4
					],
					'and' => [ // Team2, Team3
						'not a valid filter', //$filter1,
						 $filter2
					]
				]
			]
		];
		$this->expectException(Exception::class);
		$group->getTeams($filter);

	}

	/** @test */
	public function check_filter_creation_multi_false_filter_or() {
		$group = new \TournamentGenerator\Group('Group', 'g1');

		$team1 = $group->team('Team 1', 't1'); // Score: 200 Points: 3 Losses: 2 Wins: 1 Draws: 0
		$team2 = $group->team('Team 2', 't2'); // Score: 500 Points: 4 Losses: 1 Wins: 1 Draws: 1
		$team3 = $group->team('Team 3', 't3'); // Score: 420 Points: 4 Losses: 1 Wins: 1 Draws: 1
		$team4 = $group->team('Team 4', 't4'); // Score: 390 Points: 6 Losses: 1 Wins: 2 Draws: 0

		$group->game([$team1, $team2])->setResults(['t1' => 100, 't2' => 200]);
		$group->game([$team2, $team3])->setResults(['t3' => 200, 't2' => 200]);
		$group->game([$team1, $team3])->setResults(['t1' => 90, 't3' => 50]);
		$group->game([$team4, $team1])->setResults(['t4' => 30, 't1' => 10]);
		$group->game([$team4, $team2])->setResults(['t4' => 300, 't2' => 100]);
		$group->game([$team4, $team3])->setResults(['t4' => 60, 't3' => 170]);

		$filter1 = new \TournamentGenerator\TeamFilter('points', '>', 3, [$group]);  // Team 2, Team 3, Team 4
		$filter2 = new \TournamentGenerator\TeamFilter('score', '>', 400, [$group]); // Team 2, Team 3
		$filter3 = new \TournamentGenerator\TeamFilter('score', '=', 200, [$group]); // Team 1
		$filter4 = new \TournamentGenerator\TeamFilter('draws', '>=', 1, [$group]);  // Team 2, Team 3
		$filter5 = new \TournamentGenerator\TeamFilter('score', '<', 450, [$group]); // Team 1, Team 3, Team 4
		$filter6 = new \TournamentGenerator\TeamFilter('team', '=', $team2, [$group]); // Team 2

		$filter = [
			'or' => [ // Team 1, Team 2, Team 3
				'or' => [ // Team 1, Team 2
					'not a valid filter',
					$filter6
				],
				'and' => [ // Team 2, Team 3
					'or' => [ // Team 1, Team 2, Team 3, Team 4
						$filter5,
						$filter4
					],
					'and' => [ // Team2, Team3
						$filter1,
						 $filter2
					]
				]
			]
		];
		$this->expectException(Exception::class);
		$group->getTeams($filter);
	}

	/** @test */
	public function check_filter_creation_multi_false_operrand_or() {
		$group = new \TournamentGenerator\Group('Group', 'g1');

		$team1 = $group->team('Team 1', 't1'); // Score: 200 Points: 3 Losses: 2 Wins: 1 Draws: 0
		$team2 = $group->team('Team 2', 't2'); // Score: 500 Points: 4 Losses: 1 Wins: 1 Draws: 1
		$team3 = $group->team('Team 3', 't3'); // Score: 420 Points: 4 Losses: 1 Wins: 1 Draws: 1
		$team4 = $group->team('Team 4', 't4'); // Score: 390 Points: 6 Losses: 1 Wins: 2 Draws: 0

		$group->game([$team1, $team2])->setResults(['t1' => 100, 't2' => 200]);
		$group->game([$team2, $team3])->setResults(['t3' => 200, 't2' => 200]);
		$group->game([$team1, $team3])->setResults(['t1' => 90, 't3' => 50]);
		$group->game([$team4, $team1])->setResults(['t4' => 30, 't1' => 10]);
		$group->game([$team4, $team2])->setResults(['t4' => 300, 't2' => 100]);
		$group->game([$team4, $team3])->setResults(['t4' => 60, 't3' => 170]);

		$filter1 = new \TournamentGenerator\TeamFilter('points', '>', 3, [$group]);  // Team 2, Team 3, Team 4
		$filter2 = new \TournamentGenerator\TeamFilter('score', '>', 400, [$group]); // Team 2, Team 3
		$filter3 = new \TournamentGenerator\TeamFilter('score', '=', 200, [$group]); // Team 1
		$filter4 = new \TournamentGenerator\TeamFilter('draws', '>=', 1, [$group]);  // Team 2, Team 3
		$filter5 = new \TournamentGenerator\TeamFilter('score', '<', 450, [$group]); // Team 1, Team 3, Team 4
		$filter6 = new \TournamentGenerator\TeamFilter('team', '=', $team2, [$group]); // Team 2

		$filter = [
			'or' => [ // Team 1, Team 2, Team 3
				'not a valid operrand' => [ // Team 1, Team 2
					$filter3,
					$filter6
				],
				'and' => [ // Team 2, Team 3
					'or' => [ // Team 1, Team 2, Team 3, Team 4
						$filter5,
						$filter4
					],
					'and' => [ // Team2, Team3
						$filter1,
						 $filter2
					]
				]
			]
		];
		$this->expectException(Exception::class);
		$group->getTeams($filter);
	}

}
