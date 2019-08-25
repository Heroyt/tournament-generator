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

		$this->assertCount(2, $group->getTeams(false, null, [$filter1]));
		$this->assertCount(1, $group->getTeams(false, null, [$filter1, $filter2]));
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

		$this->assertCount(3, $group->getTeams(false, null, $filter));

		$filter = [
			'or' => [ // Team 1, Team 2
				$filter3,
				$filter6
			]
		];
		$this->assertCount(2, $group->getTeams(false, null, $filter));

		$filter = [
			'or' => [ // Team 1, Team 2, Team 3, Team 4
				$filter5,
				$filter4
			]
		];
		$this->assertCount(4, $group->getTeams(false, null, $filter));

		$filter = [
			'and' => [ // Team2, Team3
				$filter1,
				$filter2
			]
		];
		$this->assertCount(2, $group->getTeams(false, null, $filter));

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
		$this->assertCount(2, $group->getTeams(false, null, $filter));

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
		$this->assertCount(3, $group->getTeams(false, null, $filter));
	}

	/** @test */
	public function check_filter_creation_multi_round() {
		$round = new \TournamentGenerator\Round('Round', 'r1');
		$group1 = $round->group('Group 1', 'g1');
		$group2 = $round->group('Group 2', 'g2');

		$team1 = $group1->team('Team 1', 't1'); // Score: 200 Points: 3 Losses: 2 Wins: 1 Draws: 0
		$team2 = $group1->team('Team 2', 't2'); // Score: 500 Points: 4 Losses: 1 Wins: 1 Draws: 1
		$team3 = $group1->team('Team 3', 't3'); // Score: 420 Points: 4 Losses: 1 Wins: 1 Draws: 1
		$team4 = $group1->team('Team 4', 't4'); // Score: 390 Points: 6 Losses: 1 Wins: 2 Draws: 0

		$team5 = $group2->team('Team 5', 't5'); // Score: 590 Points: 6 Losses: 0 Wins: 2 Draws: 0
		$team6 = $group2->team('Team 6', 't6'); // Score: 200 Points: 1 Losses: 1 Wins: 0 Draws: 1
		$team7 = $group2->team('Team 7', 't7'); // Score: 250 Points: 1 Losses: 1 Wins: 0 Draws: 1

		$group1->game([$team1, $team2])->setResults(['t1' => 100, 't2' => 200]);
		$group1->game([$team2, $team3])->setResults(['t3' => 200, 't2' => 200]);
		$group1->game([$team1, $team3])->setResults(['t1' => 90, 't3' => 50]);
		$group1->game([$team4, $team1])->setResults(['t4' => 30, 't1' => 10]);
		$group1->game([$team4, $team2])->setResults(['t4' => 300, 't2' => 100]);
		$group1->game([$team4, $team3])->setResults(['t4' => 60, 't3' => 170]);

		$group2->game([$team5, $team6])->setResults(['t5' => 500, 't6' => 0]);
		$group2->game([$team6, $team7])->setResults(['t7' => 200, 't6' => 200]);
		$group2->game([$team5, $team7])->setResults(['t5' => 90, 't7' => 50]);

		$filter1 = new \TournamentGenerator\TeamFilter('points', '>', 3, [$group1, $group2]);  // Team 2, Team 3, Team 4, Team 5
		$filter2 = new \TournamentGenerator\TeamFilter('score', '>', 400, [$group1, $group2]); // Team 2, Team 3, Team 5
		$filter3 = new \TournamentGenerator\TeamFilter('score', '=', 200, [$group1, $group2]); // Team 1, Team 6
		$filter4 = new \TournamentGenerator\TeamFilter('draws', '>=', 1, [$group1, $group2]);  // Team 2, Team 3, Team 6, Team 7
		$filter5 = new \TournamentGenerator\TeamFilter('score', '<', 450, [$group1, $group2]); // Team 1, Team 3, Team 4, Team 6, Team 7
		$filter6 = new \TournamentGenerator\TeamFilter('team', '=', $team2, [$group1, $group2]); // Team 2

		$filter = [
			'or' => [ // Team 1, Team 2, Team 3, Team 5, Team 6
				'and' => [$filter1, $filter2], // Team 2, Team 3, Team 5
				$filter3
			]
		];

		$this->assertCount(5, $round->getTeams(false, null, $filter));

		$filter = [
			'or' => [ // Team 1, Team 2, Team 6
				$filter3,
				$filter6
			]
		];
		$this->assertCount(3, $round->getTeams(false, null, $filter));

		$filter = [
			'or' => [ // Team 1, Team 2, Team 3, Team 4, Team6, Team 7
				$filter5,
				$filter4
			]
		];

		$this->assertCount(6, $round->getTeams(false, null, $filter));

		$filter = [
			'and' => [ // Team 2, Team 3
				'or' => [ // Team 1, Team 2, Team 3, Team 4, Team6, Team 7
					$filter5,
					$filter4
				],
				'and' => [ // Team 2, Team 3, Team 5
					$filter1,
					$filter2
				]
			]
		];
		$this->assertCount(2, $round->getTeams(false, null, $filter));

		$filter = [
			'or' => [ // Team 1, Team 2, Team 3, Team 6
				'or' => [ // Team 1, Team 2, Team 6
					$filter3,
					$filter6
				],
				'and' => [ // Team 2, Team 3
					'or' => [ // Team 1, Team 2, Team 3, Team 4, Team6, Team 7
						$filter5,
						$filter4
					],
					'and' => [ // Team 2, Team 3, Team 5
						$filter1,
						$filter2
					]
				]
			]
		];
		$this->assertCount(4, $round->getTeams(false, null, $filter));
	}

	/** @test */
	public function check_filter_creation_multi_tournament() {
		$tournament = new \TournamentGenerator\Tournament('Tournament');

		$round1 = $tournament->round('Round 1', 'r1');
		$round2 = $tournament->round('Round 2', 'r2');

		$group1 = $round1->group('Group 1', 'g1');
		$group2 = $round1->group('Group 2', 'g2');
		$group3 = $round2->group('Group 3', 'g2');

		$team1 = $group1->team('Team 1', 't1'); // Score: 200 Points: 3 Losses: 2 Wins: 1 Draws: 0
		$team2 = $group1->team('Team 2', 't2'); // Score: 500 Points: 4 Losses: 1 Wins: 1 Draws: 1
		$team3 = $group1->team('Team 3', 't3'); // Score: 420 Points: 4 Losses: 1 Wins: 1 Draws: 1
		$team4 = $group1->team('Team 4', 't4'); // Score: 390 Points: 6 Losses: 1 Wins: 2 Draws: 0

		$team5 = $group2->team('Team 5', 't5'); // Score: 590 Points: 6 Losses: 0 Wins: 2 Draws: 0
		$team6 = $group2->team('Team 6', 't6'); // Score: 200 Points: 1 Losses: 1 Wins: 0 Draws: 1
		$team7 = $group2->team('Team 7', 't7'); // Score: 250 Points: 1 Losses: 1 Wins: 0 Draws: 1

		$team8 = $group3->team('Team 8', 't8'); // Score: 373 Points: 3 Losses: 1 Wins: 1 Draws: 0
		$team9 = $group3->team('Team 9', 't9'); // Score: 522 Points: 3 Losses: 1 Wins: 1 Draws: 0
		$team0 = $group3->team('Team 0', 't0'); // Score: 628 Points: 6 Losses: 0 Wins: 2 Draws: 0

		$group1->game([$team1, $team2])->setResults(['t1' => 100, 't2' => 200]);
		$group1->game([$team2, $team3])->setResults(['t3' => 200, 't2' => 200]);
		$group1->game([$team1, $team3])->setResults(['t1' => 90,  't3' => 50]);
		$group1->game([$team4, $team1])->setResults(['t4' => 30,  't1' => 10]);
		$group1->game([$team4, $team2])->setResults(['t4' => 300, 't2' => 100]);
		$group1->game([$team4, $team3])->setResults(['t4' => 60,  't3' => 170]);

		$group2->game([$team5, $team6])->setResults(['t5' => 500, 't6' => 0]);
		$group2->game([$team6, $team7])->setResults(['t7' => 200, 't6' => 200]);
		$group2->game([$team5, $team7])->setResults(['t5' => 90,  't7' => 50]);

		$group3->game([$team8, $team9])->setResults(['t8' => 120, 't9' => 220]);
		$group3->game([$team9, $team0])->setResults(['t0' => 350, 't9' => 302]);
		$group3->game([$team8, $team0])->setResults(['t8' => 253, 't0' => 278]);

		$filter1 = new \TournamentGenerator\TeamFilter('points', '>', 3, [$group1, $group2, $group3]);  // Team 2, Team 3, Team 4, Team 5, Team 0
		$filter2 = new \TournamentGenerator\TeamFilter('score', '>', 400, [$group1, $group2, $group3]); // Team 2, Team 3, Team 5, Team 9, Team 0
		$filter3 = new \TournamentGenerator\TeamFilter('score', '=', 200, [$group1, $group2, $group3]); // Team 1, Team 6
		$filter4 = new \TournamentGenerator\TeamFilter('draws', '>=', 1, [$group1, $group2, $group3]);  // Team 2, Team 3, Team 6, Team 7
		$filter5 = new \TournamentGenerator\TeamFilter('score', '<', 450, [$group1, $group2, $group3]); // Team 1, Team 3, Team 4, Team 6, Team 7, Team 8
		$filter6 = new \TournamentGenerator\TeamFilter('team', '=', $team2, [$group1, $group2, $group3]); // Team 2

		$filter = [
			'or' => [ // Team 1, Team 2, Team 3, Team 5, Team 6, Team 0
				'and' => [$filter1, $filter2], // Team 2, Team 3, Team 5, Team 0
				$filter3
			]
		];

		$this->assertCount(6, $tournament->getTeams(false, null, $filter));

		$filter = [
			'or' => [ // Team 1, Team 2, Team 6
				$filter3,
				$filter6
			]
		];
		$this->assertCount(3, $tournament->getTeams(false, null, $filter));

		$filter = [
			'or' => [ // Team 1, Team 2, Team 3, Team 4, Team6, Team 7, Team 8
				$filter5,
				$filter4
			]
		];

		$this->assertCount(7, $tournament->getTeams(false, null, $filter));

		$filter = [
			'and' => [ // Team 2, Team 3
				'or' => [ // Team 1, Team 2, Team 3, Team 4, Team6, Team 7, Team 8
					$filter5,
					$filter4
				],
				'and' => [ // Team 2, Team 3, Team 5, Team 0
					$filter1,
					$filter2
				]
			]
		];
		$this->assertCount(2, $tournament->getTeams(false, null, $filter));

		$filter = [
			'or' => [ // Team 1, Team 2, Team 3, Team 6
				'or' => [ // Team 1, Team 2, Team 6
					$filter3,
					$filter6
				],
				'and' => [ // Team 2, Team 3
					'or' => [ // Team 1, Team 2, Team 3, Team 4, Team6, Team 7, Team 8
						$filter5,
						$filter4
					],
					'and' => [ // Team 2, Team 3, Team 5, Team 0
						$filter1,
						$filter2
					]
				]
			]
		];
		$this->assertCount(4, $tournament->getTeams(false, null, $filter));
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
		$group->getTeams(false, null, ['not a valid filter']);
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
		$group->getTeams(false, null, $filter);

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
		$group->getTeams(false, null, $filter);
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
		$group->getTeams(false, null, $filter);

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
		$group->getTeams(false, null, $filter);
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
		$group->getTeams(false, null, $filter);
	}

	/** @test */
	public function check_filter_creation_multi_with_double_comp() {
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
			'and' => [ // 2, 3
				['or' => [$filter1, $filter2]], // 2, 3, 4
				['or' => [$filter3, $filter4]] // 1, 2, 3
			]
		];
		$this->assertCount(2, $group->getTeams(false, null, $filter));
	}

}
