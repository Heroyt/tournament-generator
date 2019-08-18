<?php
use PHPUnit\Framework\TestCase;

/**
 *
 */
class TeamFilterTest extends TestCase
{

	/** @test */
	public function check_filter_setup_teamFilter() {
		$group1 = new \TournamentGenerator\Group('Group 1');
		$group2 = new \TournamentGenerator\Group('Group 2');

		$filter = new \TournamentGenerator\TeamFilter('points', '>', 2, [$group1, $group2]);

		$this->assertEquals('Filter: points > 2', (string) $filter);

	}

	/** @test */
	public function check_filter_setup_incorrect_teamFilter() {
		$group1 = new \TournamentGenerator\Group('Group 1');
		$group2 = new \TournamentGenerator\Group('Group 2');

		$this->expectException(Exception::class);
		$filter = new \TournamentGenerator\TeamFilter('not a correct type', '>', 2, [$group1, $group2]);

		$this->expectException(Exception::class);
		$filter = new \TournamentGenerator\TeamFilter('score', 'not a correct operator', 2, [$group1, $group2]);

		$this->expectException(Exception::class);
		$filter = new \TournamentGenerator\TeamFilter('score', '>', 'not a correct value', [$group1, $group2]);

	}

	/** @test */
	public function check_filter_validate_points_teamFilter() {
		$group = new \TournamentGenerator\Group('Group 1', 'g1');

		$team1 = $group->team('Team 1', 't1');
		$team2 = $group->team('Team 2', 't2');
		$team3 = $group->team('Team 3', 't3');

		$team1->addWin('g1') ->addWin('g1');   // 6 points
		$team2->addLoss('g1')->addLoss('g1'); // 0 points
		$team3->addDraw('g1')->addWin('g1');  // 4 points

		$filter_greater = new \TournamentGenerator\TeamFilter('points', '>', 2, [$group]);
		$filter_less = new \TournamentGenerator\TeamFilter('points', '<', 4, [$group]);
		$filter_greater_equal = new \TournamentGenerator\TeamFilter('points', '>=', 5, [$group]);
		$filter_less_equal = new \TournamentGenerator\TeamFilter('points', '<=', 4, [$group]);
		$filter_is = new \TournamentGenerator\TeamFilter('points', '=', 4, [$group]);
		$filter_isnt = new \TournamentGenerator\TeamFilter('points', '!=', 6, [$group]);

		$this->assertCount(2, $group->getTeams([$filter_greater]));
		$this->assertCount(1, $group->getTeams([$filter_less]));
		$this->assertCount(1, $group->getTeams([$filter_greater_equal]));
		$this->assertCount(2, $group->getTeams([$filter_less_equal]));
		$this->assertCount(1, $group->getTeams([$filter_is]));
		$this->assertCount(2, $group->getTeams([$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_score_teamFilter() {
		$group = new \TournamentGenerator\Group('Group 1', 'g1');

		$team1 = $group->team('Team 1', 't1'); // Score: 200
		$team2 = $group->team('Team 2', 't2'); // Score: 450
		$team3 = $group->team('Team 3', 't3'); // Score: 420
		$team4 = $group->team('Team 4', 't4'); // Score: 390

		$group->game([$team1, $team2])->setResults(['t1' => 100, 't2' => 200]);
		$group->game([$team2, $team3])->setResults(['t3' => 300, 't2' => 150]);
		$group->game([$team1, $team3])->setResults(['t1' => 90, 't3' => 50]);
		$group->game([$team4, $team1])->setResults(['t4' => 30, 't1' => 10]);
		$group->game([$team4, $team2])->setResults(['t4' => 300, 't2' => 100]);
		$group->game([$team4, $team3])->setResults(['t4' => 60, 't3' => 70]);

		$filter_greater = new \TournamentGenerator\TeamFilter('score', '>', 400, [$group]);
		$filter_less = new \TournamentGenerator\TeamFilter('score', '<', 400, [$group]);
		$filter_greater_equal = new \TournamentGenerator\TeamFilter('score', '>=', 390, [$group]);
		$filter_less_equal = new \TournamentGenerator\TeamFilter('score', '<=', 390, [$group]);
		$filter_is = new \TournamentGenerator\TeamFilter('score', '=', 420, [$group]);
		$filter_isnt = new \TournamentGenerator\TeamFilter('score', '!=', 420, [$group]);

		$this->assertCount(2, $group->getTeams([$filter_greater]));
		$this->assertCount(2, $group->getTeams([$filter_less]));
		$this->assertCount(3, $group->getTeams([$filter_greater_equal]));
		$this->assertCount(2, $group->getTeams([$filter_less_equal]));
		$this->assertCount(1, $group->getTeams([$filter_is]));
		$this->assertCount(3, $group->getTeams([$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_wins_teamFilter() {
		$group = new \TournamentGenerator\Group('Group 1', 'g1');

		$team1 = $group->team('Team 1', 't1'); // Wins: 1
		$team2 = $group->team('Team 2', 't2'); // Wins: 1
		$team3 = $group->team('Team 3', 't3'); // Wins: 2
		$team4 = $group->team('Team 4', 't4'); // Wins: 2

		$g1 = $group->game([$team1, $team2])->setResults(['t1' => 100, 't2' => 200]);
		$group->game([$team2, $team3])->setResults(['t3' => 300, 't2' => 150]);
		$group->game([$team1, $team3])->setResults(['t1' => 90, 't3' => 50]);
		$group->game([$team4, $team1])->setResults(['t4' => 30, 't1' => 10]);
		$group->game([$team4, $team2])->setResults(['t4' => 300, 't2' => 100]);
		$group->game([$team4, $team3])->setResults(['t4' => 60, 't3' => 70]);

		$filter_greater = new \TournamentGenerator\TeamFilter('wins', '>', 1, [$group]);
		$filter_less = new \TournamentGenerator\TeamFilter('wins', '<', 2, [$group]);
		$filter_greater_equal = new \TournamentGenerator\TeamFilter('wins', '>=', 1, [$group]);
		$filter_less_equal = new \TournamentGenerator\TeamFilter('wins', '<=', 1, [$group]);
		$filter_is = new \TournamentGenerator\TeamFilter('wins', '=', 2, [$group]);
		$filter_isnt = new \TournamentGenerator\TeamFilter('wins', '!=', 2, [$group]);

		$this->assertCount(2, $group->getTeams([$filter_greater]));
		$this->assertCount(2, $group->getTeams([$filter_less]));
		$this->assertCount(4, $group->getTeams([$filter_greater_equal]));
		$this->assertCount(2, $group->getTeams([$filter_less_equal]));
		$this->assertCount(2, $group->getTeams([$filter_is]));
		$this->assertCount(2, $group->getTeams([$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_losses_teamFilter() {
		$group = new \TournamentGenerator\Group('Group 1', 'g1');

		$team1 = $group->team('Team 1', 't1'); // Losses: 2
		$team2 = $group->team('Team 2', 't2'); // Losses: 2
		$team3 = $group->team('Team 3', 't3'); // Losses: 1
		$team4 = $group->team('Team 4', 't4'); // Losses: 1

		$g1 = $group->game([$team1, $team2])->setResults(['t1' => 100, 't2' => 200]);
		$group->game([$team2, $team3])->setResults(['t3' => 300, 't2' => 150]);
		$group->game([$team1, $team3])->setResults(['t1' => 90, 't3' => 50]);
		$group->game([$team4, $team1])->setResults(['t4' => 30, 't1' => 10]);
		$group->game([$team4, $team2])->setResults(['t4' => 300, 't2' => 100]);
		$group->game([$team4, $team3])->setResults(['t4' => 60, 't3' => 70]);

		$filter_greater = new \TournamentGenerator\TeamFilter('losses', '>', 1, [$group]);
		$filter_less = new \TournamentGenerator\TeamFilter('losses', '<', 2, [$group]);
		$filter_greater_equal = new \TournamentGenerator\TeamFilter('losses', '>=', 1, [$group]);
		$filter_less_equal = new \TournamentGenerator\TeamFilter('losses', '<=', 1, [$group]);
		$filter_is = new \TournamentGenerator\TeamFilter('losses', '=', 2, [$group]);
		$filter_isnt = new \TournamentGenerator\TeamFilter('losses', '!=', 2, [$group]);

		$this->assertCount(2, $group->getTeams([$filter_greater]));
		$this->assertCount(2, $group->getTeams([$filter_less]));
		$this->assertCount(4, $group->getTeams([$filter_greater_equal]));
		$this->assertCount(2, $group->getTeams([$filter_less_equal]));
		$this->assertCount(2, $group->getTeams([$filter_is]));
		$this->assertCount(2, $group->getTeams([$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_draws_teamFilter() {
		$group = new \TournamentGenerator\Group('Group 1', 'g1');

		$team1 = $group->team('Team 1', 't1'); // Draws: 0
		$team2 = $group->team('Team 2', 't2'); // Draws: 2
		$team3 = $group->team('Team 3', 't3'); // Draws: 1
		$team4 = $group->team('Team 4', 't4'); // Draws: 1

		$g1 = $group->game([$team1, $team2])->setResults(['t1' => 100, 't2' => 200]);
		$group->game([$team2, $team3])->setResults(['t3' => 150, 't2' => 150]);
		$group->game([$team1, $team3])->setResults(['t1' => 90, 't3' => 100]);
		$group->game([$team4, $team1])->setResults(['t4' => 30, 't1' => 10]);
		$group->game([$team4, $team2])->setResults(['t4' => 100, 't2' => 100]);
		$group->game([$team4, $team3])->setResults(['t4' => 60, 't3' => 70]);

		$filter_greater = new \TournamentGenerator\TeamFilter('draws', '>', 1, [$group]);
		$filter_less = new \TournamentGenerator\TeamFilter('draws', '<', 2, [$group]);
		$filter_greater_equal = new \TournamentGenerator\TeamFilter('draws', '>=', 1, [$group]);
		$filter_less_equal = new \TournamentGenerator\TeamFilter('draws', '<=', 1, [$group]);
		$filter_is = new \TournamentGenerator\TeamFilter('draws', '=', 2, [$group]);
		$filter_isnt = new \TournamentGenerator\TeamFilter('draws', '!=', 2, [$group]);

		$this->assertCount(1, $group->getTeams([$filter_greater]));
		$this->assertCount(3, $group->getTeams([$filter_less]));
		$this->assertCount(3, $group->getTeams([$filter_greater_equal]));
		$this->assertCount(3, $group->getTeams([$filter_less_equal]));
		$this->assertCount(1, $group->getTeams([$filter_is]));
		$this->assertCount(3, $group->getTeams([$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_second_teamFilter() {
		$group = new \TournamentGenerator\Group('Group 1', 'g1');
		$group->setInGame(4);

		$team1 = $group->team('Team 1', 't1'); // Second: 0
		$team2 = $group->team('Team 2', 't2'); // Second: 1
		$team3 = $group->team('Team 3', 't3'); // Second: 3
		$team4 = $group->team('Team 4', 't4'); // Second: 0
		$team5 = $group->team('Team 5', 't5'); // Second: 1

		$g1 = $group->game([$team1, $team2, $team3, $team4])->setResults(['t1' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$g1 = $group->game([$team5, $team2, $team3, $team4])->setResults(['t5' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$g1 = $group->game([$team1, $team5, $team3, $team4])->setResults(['t1' => 100, 't5' => 200, 't3' => 120, 't4' => 75]);
		$g1 = $group->game([$team1, $team2, $team5, $team4])->setResults(['t1' => 100, 't2' => 200, 't5' => 120, 't4' => 75]);
		$g1 = $group->game([$team1, $team2, $team3, $team5])->setResults(['t1' => 100, 't3' => 200, 't2' => 120, 't5' => 75]);

		$filter_greater = new \TournamentGenerator\TeamFilter('second', '>', 1, [$group]);
		$filter_less = new \TournamentGenerator\TeamFilter('second', '<', 2, [$group]);
		$filter_greater_equal = new \TournamentGenerator\TeamFilter('second', '>=', 1, [$group]);
		$filter_less_equal = new \TournamentGenerator\TeamFilter('second', '<=', 1, [$group]);
		$filter_is = new \TournamentGenerator\TeamFilter('second', '=', 0, [$group]);
		$filter_isnt = new \TournamentGenerator\TeamFilter('second', '!=', 2, [$group]);

		$this->assertCount(1, $group->getTeams([$filter_greater]));
		$this->assertCount(4, $group->getTeams([$filter_less]));
		$this->assertCount(3, $group->getTeams([$filter_greater_equal]));
		$this->assertCount(4, $group->getTeams([$filter_less_equal]));
		$this->assertCount(2, $group->getTeams([$filter_is]));
		$this->assertCount(5, $group->getTeams([$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_third_teamFilter() {
		$group = new \TournamentGenerator\Group('Group 1', 'g1');
		$group->setInGame(4);

		$team1 = $group->team('Team 1', 't1'); // Third: 3
		$team2 = $group->team('Team 2', 't2'); // Third: 0
		$team3 = $group->team('Team 3', 't3'); // Third: 0
		$team4 = $group->team('Team 4', 't4'); // Third: 1
		$team5 = $group->team('Team 5', 't5'); // Third: 1

		$g1 = $group->game([$team1, $team2, $team3, $team4])->setResults(['t1' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$g1 = $group->game([$team5, $team2, $team3, $team4])->setResults(['t5' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$g1 = $group->game([$team1, $team5, $team3, $team4])->setResults(['t4' => 100, 't5' => 200, 't3' => 120, 't1' => 75]);
		$g1 = $group->game([$team1, $team2, $team5, $team4])->setResults(['t1' => 100, 't2' => 200, 't5' => 120, 't4' => 75]);
		$g1 = $group->game([$team1, $team2, $team3, $team5])->setResults(['t1' => 100, 't3' => 200, 't2' => 120, 't5' => 75]);

		$filter_greater = new \TournamentGenerator\TeamFilter('third', '>', 1, [$group]);
		$filter_less = new \TournamentGenerator\TeamFilter('third', '<', 2, [$group]);
		$filter_greater_equal = new \TournamentGenerator\TeamFilter('third', '>=', 1, [$group]);
		$filter_less_equal = new \TournamentGenerator\TeamFilter('third', '<=', 1, [$group]);
		$filter_is = new \TournamentGenerator\TeamFilter('third', '=', 0, [$group]);
		$filter_isnt = new \TournamentGenerator\TeamFilter('third', '!=', 2, [$group]);

		$this->assertCount(1, $group->getTeams([$filter_greater]));
		$this->assertCount(4, $group->getTeams([$filter_less]));
		$this->assertCount(3, $group->getTeams([$filter_greater_equal]));
		$this->assertCount(4, $group->getTeams([$filter_less_equal]));
		$this->assertCount(2, $group->getTeams([$filter_is]));
		$this->assertCount(5, $group->getTeams([$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_team_teamFilter() {
		$group = new \TournamentGenerator\Group('Group 1', 'g1');
		$group->setInGame(4);

		$team1 = $group->team('Team 1', 't1'); // Second: 0
		$team2 = $group->team('Team 2', 't2'); // Second: 1
		$team3 = $group->team('Team 3', 't3'); // Second: 3
		$team4 = $group->team('Team 4', 't4'); // Second: 0
		$team5 = $group->team('Team 5', 't5'); // Second: 1

		$filter_greater = new \TournamentGenerator\TeamFilter('team', '>', $team1, [$group]);
		$filter_less = new \TournamentGenerator\TeamFilter('team', '<', $team1, [$group]);
		$filter_greater_equal = new \TournamentGenerator\TeamFilter('team', '>=', $team1, [$group]);
		$filter_less_equal = new \TournamentGenerator\TeamFilter('team', '<=', $team1, [$group]);
		$filter_is = new \TournamentGenerator\TeamFilter('team', '=', $team1, [$group]);
		$filter_isnt = new \TournamentGenerator\TeamFilter('team', '!=', $team1, [$group]);

		$this->assertCount(1, $group->getTeams([$filter_greater]));
		$this->assertCount(1, $group->getTeams([$filter_less]));
		$this->assertCount(1, $group->getTeams([$filter_greater_equal]));
		$this->assertCount(1, $group->getTeams([$filter_less_equal]));
		$this->assertCount(1, $group->getTeams([$filter_is]));
		$this->assertCount(4, $group->getTeams([$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_progressed_teamFilter() {
		$group = new \TournamentGenerator\Group('Group 1', 'g1');
		$group->setInGame(4);
		$group2 = new \TournamentGenerator\Group('Group 2', 'g2');
		$group2->setInGame(4);

		$group->progression($group2, 0, 3);

		$team1 = $group->team('Team 1', 't1'); // Points: 4
		$team2 = $group->team('Team 2', 't2'); // Points: 11
		$team3 = $group->team('Team 3', 't3'); // Points: 9
		$team4 = $group->team('Team 4', 't4'); // Points: 0
		$team5 = $group->team('Team 5', 't5'); // Points: 6

		$g1 = $group->game([$team1, $team2, $team3, $team4])->setResults(['t1' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$g1 = $group->game([$team5, $team2, $team3, $team4])->setResults(['t5' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$g1 = $group->game([$team1, $team5, $team3, $team4])->setResults(['t1' => 100, 't5' => 200, 't3' => 120, 't4' => 75]);
		$g1 = $group->game([$team1, $team2, $team5, $team4])->setResults(['t1' => 100, 't2' => 200, 't5' => 120, 't4' => 75]);
		$g1 = $group->game([$team1, $team2, $team3, $team5])->setResults(['t1' => 100, 't3' => 200, 't2' => 120, 't5' => 75]);

		$group->progress();

		$filter_greater = new \TournamentGenerator\TeamFilter('progressed', '>', 1, [$group]);
		$filter_less = new \TournamentGenerator\TeamFilter('progressed', '<', 2, [$group]);
		$filter_greater_equal = new \TournamentGenerator\TeamFilter('progressed', '>=', 1, [$group]);
		$filter_less_equal = new \TournamentGenerator\TeamFilter('progressed', '<=', 1, [$group]);
		$filter_is = new \TournamentGenerator\TeamFilter('progressed', '=', 0, [$group]);
		$filter_isnt = new \TournamentGenerator\TeamFilter('progressed', '!=', 2, [$group]);

		$this->assertCount(3, $group->getTeams([$filter_greater]));
		$this->assertCount(3, $group->getTeams([$filter_less]));
		$this->assertCount(3, $group->getTeams([$filter_greater_equal]));
		$this->assertCount(3, $group->getTeams([$filter_less_equal]));
		$this->assertCount(3, $group->getTeams([$filter_is]));
		$this->assertCount(3, $group->getTeams([$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_notprogressed_teamFilter() {
		$group = new \TournamentGenerator\Group('Group 1', 'g1');
		$group->setInGame(4);
		$group2 = new \TournamentGenerator\Group('Group 2', 'g2');
		$group2->setInGame(4);

		$group->progression($group2, 0, 3);

		$team1 = $group->team('Team 1', 't1'); // Points: 4
		$team2 = $group->team('Team 2', 't2'); // Points: 11
		$team3 = $group->team('Team 3', 't3'); // Points: 9
		$team4 = $group->team('Team 4', 't4'); // Points: 0
		$team5 = $group->team('Team 5', 't5'); // Points: 6

		$g1 = $group->game([$team1, $team2, $team3, $team4])->setResults(['t1' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$g1 = $group->game([$team5, $team2, $team3, $team4])->setResults(['t5' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$g1 = $group->game([$team1, $team5, $team3, $team4])->setResults(['t1' => 100, 't5' => 200, 't3' => 120, 't4' => 75]);
		$g1 = $group->game([$team1, $team2, $team5, $team4])->setResults(['t1' => 100, 't2' => 200, 't5' => 120, 't4' => 75]);
		$g1 = $group->game([$team1, $team2, $team3, $team5])->setResults(['t1' => 100, 't3' => 200, 't2' => 120, 't5' => 75]);

		$group->progress();

		$filter_greater = new \TournamentGenerator\TeamFilter('notprogressed', '>', 1, [$group]);
		$filter_less = new \TournamentGenerator\TeamFilter('notprogressed', '<', 2, [$group]);
		$filter_greater_equal = new \TournamentGenerator\TeamFilter('notprogressed', '>=', 1, [$group]);
		$filter_less_equal = new \TournamentGenerator\TeamFilter('notprogressed', '<=', 1, [$group]);
		$filter_is = new \TournamentGenerator\TeamFilter('notprogressed', '=', 0, [$group]);
		$filter_isnt = new \TournamentGenerator\TeamFilter('notprogressed', '!=', 2, [$group]);

		$this->assertCount(2, $group->getTeams([$filter_greater]));
		$this->assertCount(2, $group->getTeams([$filter_less]));
		$this->assertCount(2, $group->getTeams([$filter_greater_equal]));
		$this->assertCount(2, $group->getTeams([$filter_less_equal]));
		$this->assertCount(2, $group->getTeams([$filter_is]));
		$this->assertCount(2, $group->getTeams([$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_avg_teamFilter() {
		$group = new \TournamentGenerator\Group('Group 1', 'g1');
		$group->setInGame(4);

		$team1 = $group->team('Team 1', 't1'); // AVG: 100
		$team2 = $group->team('Team 2', 't2'); // AVG: 180
		$team3 = $group->team('Team 3', 't3'); // AVG: 140
		$team4 = $group->team('Team 4', 't4'); // AVG: 75
		$team5 = $group->team('Team 5', 't5'); // AVG: 123.75

		$group->game([$team1, $team2, $team3, $team4])->setResults(['t1' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$group->game([$team5, $team2, $team3, $team4])->setResults(['t5' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$group->game([$team1, $team5, $team3, $team4])->setResults(['t1' => 100, 't5' => 200, 't3' => 120, 't4' => 75]);
		$group->game([$team1, $team2, $team5, $team4])->setResults(['t1' => 100, 't2' => 200, 't5' => 120, 't4' => 75]);
		$group->game([$team1, $team2, $team3, $team5])->setResults(['t1' => 100, 't3' => 200, 't2' => 120, 't5' => 75]);

		$filter_greater = new \TournamentGenerator\TeamFilter('score', '>', 140, [$group]);
		$filter_less = new \TournamentGenerator\TeamFilter('score', '<', 140, [$group]);
		$filter_greater_equal = new \TournamentGenerator\TeamFilter('score', '>=', 140, [$group]);
		$filter_less_equal = new \TournamentGenerator\TeamFilter('score', '<=', 140, [$group]);
		$filter_is = new \TournamentGenerator\TeamFilter('score', '=', 140, [$group]);
		$filter_isnt = new \TournamentGenerator\TeamFilter('score', '!=', 140, [$group]);

		$this->assertTrue($filter_greater->validate($team2, ['g1'], 'avg'));
		$this->assertFalse($filter_greater->validate($team4, ['g1'], 'avg'));

		$this->assertTrue($filter_less->validate($team4, ['g1'], 'avg'));
		$this->assertFalse($filter_less->validate($team3, ['g1'], 'avg'));

		$this->assertTrue($filter_greater_equal->validate($team3, ['g1'], 'avg'));
		$this->assertFalse($filter_greater_equal->validate($team5, ['g1'], 'avg'));

		$this->assertTrue($filter_less_equal->validate($team1, ['g1'], 'avg'));
		$this->assertFalse($filter_less_equal->validate($team2, ['g1'], 'avg'));

		$this->assertTrue($filter_is->validate($team3, ['g1'], 'avg'));
		$this->assertFalse($filter_is->validate($team4, ['g1'], 'avg'));

		$this->assertTrue($filter_isnt->validate($team1, ['g1'], 'avg'));
		$this->assertFalse($filter_isnt->validate($team3, ['g1'], 'avg'));

	}

	/** @test */
	public function check_filter_validate_max_teamFilter() {
		$group = new \TournamentGenerator\Group('Group 1', 'g1');
		$group->setInGame(4);

		$team1 = $group->team('Team 1', 't1'); // MAX: 100
		$team2 = $group->team('Team 2', 't2'); // MAX: 200
		$team3 = $group->team('Team 3', 't3'); // MAX: 200
		$team4 = $group->team('Team 4', 't4'); // MAX: 75
		$team5 = $group->team('Team 5', 't5'); // MAX: 150

		$group->game([$team1, $team2, $team3, $team4])->setResults(['t1' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$group->game([$team5, $team2, $team3, $team4])->setResults(['t5' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$group->game([$team1, $team5, $team3, $team4])->setResults(['t1' => 100, 't5' => 150, 't3' => 120, 't4' => 75]);
		$group->game([$team1, $team2, $team5, $team4])->setResults(['t1' => 100, 't2' => 200, 't5' => 120, 't4' => 75]);
		$group->game([$team1, $team2, $team3, $team5])->setResults(['t1' => 100, 't3' => 200, 't2' => 120, 't5' => 75]);

		$filter_greater = new \TournamentGenerator\TeamFilter('score', '>', 150, [$group]);
		$filter_less = new \TournamentGenerator\TeamFilter('score', '<', 150, [$group]);
		$filter_greater_equal = new \TournamentGenerator\TeamFilter('score', '>=', 150, [$group]);
		$filter_less_equal = new \TournamentGenerator\TeamFilter('score', '<=', 150, [$group]);
		$filter_is = new \TournamentGenerator\TeamFilter('score', '=', 150, [$group]);
		$filter_isnt = new \TournamentGenerator\TeamFilter('score', '!=', 150, [$group]);

		$this->assertTrue($filter_greater->validate($team3, ['g1'], 'max'));
		$this->assertFalse($filter_greater->validate($team5, ['g1'], 'max'));

		$this->assertTrue($filter_less->validate($team4, ['g1'], 'max'));
		$this->assertFalse($filter_less->validate($team2, ['g1'], 'max'));

		$this->assertTrue($filter_greater_equal->validate($team5, ['g1'], 'max'));
		$this->assertFalse($filter_greater_equal->validate($team1, ['g1'], 'max'));

		$this->assertTrue($filter_less_equal->validate($team5, ['g1'], 'max'));
		$this->assertFalse($filter_less_equal->validate($team2, ['g1'], 'max'));

		$this->assertTrue($filter_is->validate($team5, ['g1'], 'max'));
		$this->assertFalse($filter_is->validate($team1, ['g1'], 'max'));

		$this->assertTrue($filter_isnt->validate($team2, ['g1'], 'max'));
		$this->assertFalse($filter_isnt->validate($team5, ['g1'], 'max'));

	}

	/** @test */
	public function check_filter_validate_max_more_groups_teamFilter() {
		$group = new \TournamentGenerator\Group('Group 1', 'g1');
		$group->setInGame(2);
		$group2 = new \TournamentGenerator\Group('Group 2', 'g2');
		$group2->setInGame(2);

		$group->progression($group2, 0, 3);

		$team1 = $group->team('Team 1', 't1'); // SUM1: 200 SUM2: 500 MAX: 500
		$team2 = $group->team('Team 2', 't2'); // SUM1: 400 SUM2: 530 MAX: 530
		$team3 = $group->team('Team 3', 't3'); // SUM1: 220 SUM2: 230 MAX: 230

		$group->game([$team1, $team2])->setResults(['t1' => 100, 't2' => 200]);
		$group->game([$team3, $team2])->setResults(['t3' => 100, 't2' => 200]);
		$group->game([$team1, $team3])->setResults(['t1' => 100, 't3' => 120]);

		$group->progress();

		$group2->game([$team1, $team2])->setResults(['t1' => 300, 't2' => 400]);
		$group2->game([$team3, $team2])->setResults(['t3' => 150, 't2' => 130]);
		$group2->game([$team1, $team3])->setResults(['t1' => 200, 't3' => 80]);

		$filter_greater = new \TournamentGenerator\TeamFilter('score', '>', 500, [$group, $group2]);
		$filter_less = new \TournamentGenerator\TeamFilter('score', '<', 500, [$group, $group2]);
		$filter_greater_equal = new \TournamentGenerator\TeamFilter('score', '>=', 500, [$group, $group2]);
		$filter_less_equal = new \TournamentGenerator\TeamFilter('score', '<=', 500, [$group, $group2]);
		$filter_is = new \TournamentGenerator\TeamFilter('score', '=', 500, [$group, $group2]);
		$filter_isnt = new \TournamentGenerator\TeamFilter('score', '!=', 500, [$group, $group2]);

		$this->assertTrue($filter_greater->validate($team2, ['g1', 'g2'], 'max'));
		$this->assertFalse($filter_greater->validate($team1, ['g1', 'g2'], 'max'));

		$this->assertTrue($filter_less->validate($team3, ['g1', 'g2'], 'max'));
		$this->assertFalse($filter_less->validate($team1, ['g1', 'g2'], 'max'));

		$this->assertTrue($filter_greater_equal->validate($team1, ['g1', 'g2'], 'max'));
		$this->assertFalse($filter_greater_equal->validate($team3, ['g1', 'g2'], 'max'));

		$this->assertTrue($filter_less_equal->validate($team3, ['g1', 'g2'], 'max'));
		$this->assertFalse($filter_less_equal->validate($team2, ['g1', 'g2'], 'max'));

		$this->assertTrue($filter_is->validate($team1, ['g1', 'g2'], 'max'));
		$this->assertFalse($filter_is->validate($team3, ['g1', 'g2'], 'max'));

		$this->assertTrue($filter_isnt->validate($team2, ['g1', 'g2'], 'max'));
		$this->assertFalse($filter_isnt->validate($team1, ['g1', 'g2'], 'max'));

	}

	/** @test */
	public function check_filter_validate_min_teamFilter() {
		$group = new \TournamentGenerator\Group('Group 1', 'g1');
		$group->setInGame(4);

		$team1 = $group->team('Team 1', 't1'); // MIN: 100
		$team2 = $group->team('Team 2', 't2'); // MIN: 120
		$team3 = $group->team('Team 3', 't3'); // MIN: 120
		$team4 = $group->team('Team 4', 't4'); // MIN: 75
		$team5 = $group->team('Team 5', 't5'); // MIN: 75

		$group->game([$team1, $team2, $team3, $team4])->setResults(['t1' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$group->game([$team5, $team2, $team3, $team4])->setResults(['t5' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$group->game([$team1, $team5, $team3, $team4])->setResults(['t1' => 100, 't5' => 150, 't3' => 120, 't4' => 75]);
		$group->game([$team1, $team2, $team5, $team4])->setResults(['t1' => 100, 't2' => 200, 't5' => 120, 't4' => 75]);
		$group->game([$team1, $team2, $team3, $team5])->setResults(['t1' => 100, 't3' => 200, 't2' => 120, 't5' => 75]);

		$filter_greater = new \TournamentGenerator\TeamFilter('score', '>', 100, [$group]);
		$filter_less = new \TournamentGenerator\TeamFilter('score', '<', 100, [$group]);
		$filter_greater_equal = new \TournamentGenerator\TeamFilter('score', '>=', 100, [$group]);
		$filter_less_equal = new \TournamentGenerator\TeamFilter('score', '<=', 100, [$group]);
		$filter_is = new \TournamentGenerator\TeamFilter('score', '=', 100, [$group]);
		$filter_isnt = new \TournamentGenerator\TeamFilter('score', '!=', 100, [$group]);

		$this->assertTrue($filter_greater->validate($team3, ['g1'], 'min'));
		$this->assertFalse($filter_greater->validate($team5, ['g1'], 'min'));

		$this->assertTrue($filter_less->validate($team4, ['g1'], 'min'));
		$this->assertFalse($filter_less->validate($team1, ['g1'], 'min'));

		$this->assertTrue($filter_greater_equal->validate($team1, ['g1'], 'min'));
		$this->assertFalse($filter_greater_equal->validate($team4, ['g1'], 'min'));

		$this->assertTrue($filter_less_equal->validate($team5, ['g1'], 'min'));
		$this->assertFalse($filter_less_equal->validate($team2, ['g1'], 'min'));

		$this->assertTrue($filter_is->validate($team1, ['g1'], 'min'));
		$this->assertFalse($filter_is->validate($team2, ['g1'], 'min'));

		$this->assertTrue($filter_isnt->validate($team3, ['g1'], 'min'));
		$this->assertFalse($filter_isnt->validate($team1, ['g1'], 'min'));

	}

	/** @test */
	public function check_filter_validate_min_more_groups_teamFilter() {
		$group = new \TournamentGenerator\Group('Group 1', 'g1');
		$group->setInGame(2);
		$group2 = new \TournamentGenerator\Group('Group 2', 'g2');
		$group2->setInGame(2);

		$group->progression($group2, 0, 3);

		$team1 = $group->team('Team 1', 't1'); // SUM1: 200 SUM2: 500 MIN: 200
		$team2 = $group->team('Team 2', 't2'); // SUM1: 400 SUM2: 530 MIN: 400
		$team3 = $group->team('Team 3', 't3'); // SUM1: 220 SUM2: 230 MIN: 220

		$group->game([$team1, $team2])->setResults(['t1' => 100, 't2' => 200]);
		$group->game([$team3, $team2])->setResults(['t3' => 100, 't2' => 200]);
		$group->game([$team1, $team3])->setResults(['t1' => 100, 't3' => 120]);

		$group->progress();

		$group2->game([$team1, $team2])->setResults(['t1' => 300, 't2' => 400]);
		$group2->game([$team3, $team2])->setResults(['t3' => 150, 't2' => 130]);
		$group2->game([$team1, $team3])->setResults(['t1' => 200, 't3' => 80]);

		$filter_greater = new \TournamentGenerator\TeamFilter('score', '>', 220, [$group, $group2]);
		$filter_less = new \TournamentGenerator\TeamFilter('score', '<', 220, [$group, $group2]);
		$filter_greater_equal = new \TournamentGenerator\TeamFilter('score', '>=', 220, [$group, $group2]);
		$filter_less_equal = new \TournamentGenerator\TeamFilter('score', '<=', 220, [$group, $group2]);
		$filter_is = new \TournamentGenerator\TeamFilter('score', '=', 220, [$group, $group2]);
		$filter_isnt = new \TournamentGenerator\TeamFilter('score', '!=', 220, [$group, $group2]);

		$this->assertTrue($filter_greater->validate($team2, ['g1', 'g2'], 'min'));
		$this->assertFalse($filter_greater->validate($team1, ['g1', 'g2'], 'min'));

		$this->assertTrue($filter_less->validate($team1, ['g1', 'g2'], 'min'));
		$this->assertFalse($filter_less->validate($team3, ['g1', 'g2'], 'min'));

		$this->assertTrue($filter_greater_equal->validate($team3, ['g1', 'g2'], 'min'));
		$this->assertFalse($filter_greater_equal->validate($team1, ['g1', 'g2'], 'min'));

		$this->assertTrue($filter_less_equal->validate($team1, ['g1', 'g2'], 'min'));
		$this->assertFalse($filter_less_equal->validate($team2, ['g1', 'g2'], 'min'));

		$this->assertTrue($filter_is->validate($team3, ['g1', 'g2'], 'min'));
		$this->assertFalse($filter_is->validate($team1, ['g1', 'g2'], 'min'));

		$this->assertTrue($filter_isnt->validate($team2, ['g1', 'g2'], 'min'));
		$this->assertFalse($filter_isnt->validate($team3, ['g1', 'g2'], 'min'));

	}

}
