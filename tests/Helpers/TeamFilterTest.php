<?php

namespace Helpers;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\Group;
use TournamentGenerator\Team;
use TournamentGenerator\TeamFilter;

/**
 *
 */
class TeamFilterTest extends TestCase
{

	public function testConstructDefault() : void {
		// Default values
		$filter = new TeamFilter();
		self::assertEquals('points', $filter->getWhat());
		self::assertEquals('>', $filter->getHow());
		self::assertEquals(0, $filter->getVal());
		self::assertEquals([], $filter->getGroups());
	}

	public function filterConstruct() : array {
		return [
			['points', '=', 10, [new Group('Group'), new Group('Group', 123)]],
			['score', '<', 9999, []],
			['wins', '>', 10, [new Group('Group'), new Group('Group')]],
			['losses', '>=', 54, [new Group('Group'), new Group('Group', 123)]],
			['draws', '<=', 168, [new Group('Group'), new Group('Group', 123), new Group('Group'), new Group('Group', 8888)]],
			['second', '!=', 85, [new Group('Group'), new Group('Group', 123)]],
			['third', '<', 16, [new Group('Group'), new Group('Group', 123)]],
			['team', '=', new Team('Team'), [new Group('Group'), new Group('Group', 123)]],
			['team', '!=', new Team('Team'), []],
			['progressed', '=', 0, [new Group('Group')]],
			['not-progressed', '=', 0, [new Group('Group'), new Group('Group', 'aaaaa')]],
		];
	}

	/**
	 * @dataProvider filterConstruct
	 *
	 * @param string $what
	 * @param string $how
	 * @param        $value
	 * @param array  $groups
	 */
	public function testConstructCustom(string $what, string $how, $value, array $groups) : void {
		$filter = new TeamFilter($what, $how, $value, $groups);
		self::assertEquals($what, $filter->getWhat());
		self::assertEquals($how, $filter->getHow());
		self::assertEquals($value, $filter->getVal());
		self::assertEquals(array_map(static function(Group $group) {
			return $group->getId();
		}, $groups), $filter->getGroups());
	}

	public function testConstructorInvalidType() : void {
		$this->expectException(InvalidArgumentException::class);
		new TeamFilter('nonexistent type');
	}

	public function testConstructorInvalidHow() : void {
		$this->expectException(InvalidArgumentException::class);
		new TeamFilter('points', 'nonexistent how');
	}

	public function testConstructorInvalidVal() : void {
		$this->expectException(InvalidArgumentException::class);
		new TeamFilter('team', '=', 'not a team');
	}

	public function testConstructorInvalidProgressedValidation() : void {
		$filter = new TeamFilter('progressed');
		$this->expectException(InvalidArgumentException::class);
		$filter->validate(new Team('Team'), []);
	}

	public function testConstructorInvalidCalcValidation() : void {
		$filter = new TeamFilter('points', '=', 1000);
		$this->expectException(InvalidArgumentException::class);
		$filter->validate(new Team('Team'), [1, 2, 3, 4], 'not a valid operation');
	}

	/** @test */
	public function check_filter_setup_teamFilter() : void {
		$group1 = new Group('Group 1');
		$group2 = new Group('Group 2');

		$filter = new TeamFilter('points', '>', 2, [$group1, $group2]);

		self::assertEquals('Filter: points > 2', (string) $filter);

	}

	/** @test */
	public function check_filter_setup_incorrect_teamFilter() : void {
		$group1 = new Group('Group 1');
		$group2 = new Group('Group 2');

		$this->expectException(Exception::class);
		$filter = new TeamFilter('not a correct type', '>', 2, [$group1, $group2]);

		$this->expectException(Exception::class);
		$filter = new TeamFilter('score', 'not a correct operator', 2, [$group1, $group2]);

		$this->expectException(Exception::class);
		$filter = new TeamFilter('score', '>', 'not a correct value', [$group1, $group2]);

	}

	/** @test */
	public function check_filter_validate_points_teamFilter() : void {
		$group = new Group('Group 1', 'g1');

		$team1 = $group->team('Team 1', 't1');
		$team2 = $group->team('Team 2', 't2');
		$team3 = $group->team('Team 3', 't3');

		$team1->addWin('g1')->addWin('g1');   // 6 points
		$team2->addLoss('g1')->addLoss('g1'); // 0 points
		$team3->addDraw('g1')->addWin('g1');  // 4 points

		$filter_greater = new TeamFilter('points', '>', 2, [$group]);
		$filter_less = new TeamFilter('points', '<', 4, [$group]);
		$filter_greater_equal = new TeamFilter('points', '>=', 5, [$group]);
		$filter_less_equal = new TeamFilter('points', '<=', 4, [$group]);
		$filter_is = new TeamFilter('points', '=', 4, [$group]);
		$filter_isnt = new TeamFilter('points', '!=', 6, [$group]);

		self::assertCount(2, $group->getTeams(false, null, [$filter_greater]));
		self::assertCount(1, $group->getTeams(false, null, [$filter_less]));
		self::assertCount(1, $group->getTeams(false, null, [$filter_greater_equal]));
		self::assertCount(2, $group->getTeams(false, null, [$filter_less_equal]));
		self::assertCount(1, $group->getTeams(false, null, [$filter_is]));
		self::assertCount(2, $group->getTeams(false, null, [$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_score_teamFilter() : void {
		$group = new Group('Group 1', 'g1');

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

		$filter_greater = new TeamFilter('score', '>', 400, [$group]);
		$filter_less = new TeamFilter('score', '<', 400, [$group]);
		$filter_greater_equal = new TeamFilter('score', '>=', 390, [$group]);
		$filter_less_equal = new TeamFilter('score', '<=', 390, [$group]);
		$filter_is = new TeamFilter('score', '=', 420, [$group]);
		$filter_isnt = new TeamFilter('score', '!=', 420, [$group]);

		self::assertCount(2, $group->getTeams(false, null, [$filter_greater]));
		self::assertCount(2, $group->getTeams(false, null, [$filter_less]));
		self::assertCount(3, $group->getTeams(false, null, [$filter_greater_equal]));
		self::assertCount(2, $group->getTeams(false, null, [$filter_less_equal]));
		self::assertCount(1, $group->getTeams(false, null, [$filter_is]));
		self::assertCount(3, $group->getTeams(false, null, [$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_wins_teamFilter() : void {
		$group = new Group('Group 1', 'g1');

		$team1 = $group->team('Team 1', 't1'); // Wins: 1
		$team2 = $group->team('Team 2', 't2'); // Wins: 1
		$team3 = $group->team('Team 3', 't3'); // Wins: 2
		$team4 = $group->team('Team 4', 't4'); // Wins: 2

		$group->game([$team1, $team2])->setResults(['t1' => 100, 't2' => 200]);
		$group->game([$team2, $team3])->setResults(['t3' => 300, 't2' => 150]);
		$group->game([$team1, $team3])->setResults(['t1' => 90, 't3' => 50]);
		$group->game([$team4, $team1])->setResults(['t4' => 30, 't1' => 10]);
		$group->game([$team4, $team2])->setResults(['t4' => 300, 't2' => 100]);
		$group->game([$team4, $team3])->setResults(['t4' => 60, 't3' => 70]);

		$filter_greater = new TeamFilter('wins', '>', 1, [$group]);
		$filter_less = new TeamFilter('wins', '<', 2, [$group]);
		$filter_greater_equal = new TeamFilter('wins', '>=', 1, [$group]);
		$filter_less_equal = new TeamFilter('wins', '<=', 1, [$group]);
		$filter_is = new TeamFilter('wins', '=', 2, [$group]);
		$filter_isnt = new TeamFilter('wins', '!=', 2, [$group]);

		self::assertCount(2, $group->getTeams(false, null, [$filter_greater]));
		self::assertCount(2, $group->getTeams(false, null, [$filter_less]));
		self::assertCount(4, $group->getTeams(false, null, [$filter_greater_equal]));
		self::assertCount(2, $group->getTeams(false, null, [$filter_less_equal]));
		self::assertCount(2, $group->getTeams(false, null, [$filter_is]));
		self::assertCount(2, $group->getTeams(false, null, [$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_losses_teamFilter() : void {
		$group = new Group('Group 1', 'g1');

		$team1 = $group->team('Team 1', 't1'); // Losses: 2
		$team2 = $group->team('Team 2', 't2'); // Losses: 2
		$team3 = $group->team('Team 3', 't3'); // Losses: 1
		$team4 = $group->team('Team 4', 't4'); // Losses: 1

		$group->game([$team1, $team2])->setResults(['t1' => 100, 't2' => 200]);
		$group->game([$team2, $team3])->setResults(['t3' => 300, 't2' => 150]);
		$group->game([$team1, $team3])->setResults(['t1' => 90, 't3' => 50]);
		$group->game([$team4, $team1])->setResults(['t4' => 30, 't1' => 10]);
		$group->game([$team4, $team2])->setResults(['t4' => 300, 't2' => 100]);
		$group->game([$team4, $team3])->setResults(['t4' => 60, 't3' => 70]);

		$filter_greater = new TeamFilter('losses', '>', 1, [$group]);
		$filter_less = new TeamFilter('losses', '<', 2, [$group]);
		$filter_greater_equal = new TeamFilter('losses', '>=', 1, [$group]);
		$filter_less_equal = new TeamFilter('losses', '<=', 1, [$group]);
		$filter_is = new TeamFilter('losses', '=', 2, [$group]);
		$filter_isnt = new TeamFilter('losses', '!=', 2, [$group]);

		self::assertCount(2, $group->getTeams(false, null, [$filter_greater]));
		self::assertCount(2, $group->getTeams(false, null, [$filter_less]));
		self::assertCount(4, $group->getTeams(false, null, [$filter_greater_equal]));
		self::assertCount(2, $group->getTeams(false, null, [$filter_less_equal]));
		self::assertCount(2, $group->getTeams(false, null, [$filter_is]));
		self::assertCount(2, $group->getTeams(false, null, [$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_draws_teamFilter() : void {
		$group = new Group('Group 1', 'g1');

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

		$filter_greater = new TeamFilter('draws', '>', 1, [$group]);
		$filter_less = new TeamFilter('draws', '<', 2, [$group]);
		$filter_greater_equal = new TeamFilter('draws', '>=', 1, [$group]);
		$filter_less_equal = new TeamFilter('draws', '<=', 1, [$group]);
		$filter_is = new TeamFilter('draws', '=', 2, [$group]);
		$filter_isnt = new TeamFilter('draws', '!=', 2, [$group]);

		self::assertCount(1, $group->getTeams(false, null, [$filter_greater]));
		self::assertCount(3, $group->getTeams(false, null, [$filter_less]));
		self::assertCount(3, $group->getTeams(false, null, [$filter_greater_equal]));
		self::assertCount(3, $group->getTeams(false, null, [$filter_less_equal]));
		self::assertCount(1, $group->getTeams(false, null, [$filter_is]));
		self::assertCount(3, $group->getTeams(false, null, [$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_second_teamFilter() : void {
		$group = new Group('Group 1', 'g1');
		$group->setInGame(4);

		$team1 = $group->team('Team 1', 't1'); // Second: 0
		$team2 = $group->team('Team 2', 't2'); // Second: 1
		$team3 = $group->team('Team 3', 't3'); // Second: 3
		$team4 = $group->team('Team 4', 't4'); // Second: 0
		$team5 = $group->team('Team 5', 't5'); // Second: 1

		$group->game([$team1, $team2, $team3, $team4])->setResults(['t1' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$group->game([$team5, $team2, $team3, $team4])->setResults(['t5' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$group->game([$team1, $team5, $team3, $team4])->setResults(['t1' => 100, 't5' => 200, 't3' => 120, 't4' => 75]);
		$group->game([$team1, $team2, $team5, $team4])->setResults(['t1' => 100, 't2' => 200, 't5' => 120, 't4' => 75]);
		$group->game([$team1, $team2, $team3, $team5])->setResults(['t1' => 100, 't3' => 200, 't2' => 120, 't5' => 75]);

		$filter_greater = new TeamFilter('second', '>', 1, [$group]);
		$filter_less = new TeamFilter('second', '<', 2, [$group]);
		$filter_greater_equal = new TeamFilter('second', '>=', 1, [$group]);
		$filter_less_equal = new TeamFilter('second', '<=', 1, [$group]);
		$filter_is = new TeamFilter('second', '=', 0, [$group]);
		$filter_isnt = new TeamFilter('second', '!=', 2, [$group]);

		self::assertCount(1, $group->getTeams(false, null, [$filter_greater]));
		self::assertCount(4, $group->getTeams(false, null, [$filter_less]));
		self::assertCount(3, $group->getTeams(false, null, [$filter_greater_equal]));
		self::assertCount(4, $group->getTeams(false, null, [$filter_less_equal]));
		self::assertCount(2, $group->getTeams(false, null, [$filter_is]));
		self::assertCount(5, $group->getTeams(false, null, [$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_third_teamFilter() : void {
		$group = new Group('Group 1', 'g1');
		$group->setInGame(4);

		$team1 = $group->team('Team 1', 't1'); // Third: 3
		$team2 = $group->team('Team 2', 't2'); // Third: 0
		$team3 = $group->team('Team 3', 't3'); // Third: 0
		$team4 = $group->team('Team 4', 't4'); // Third: 1
		$team5 = $group->team('Team 5', 't5'); // Third: 1

		$group->game([$team1, $team2, $team3, $team4])->setResults(['t1' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$group->game([$team5, $team2, $team3, $team4])->setResults(['t5' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$group->game([$team1, $team5, $team3, $team4])->setResults(['t4' => 100, 't5' => 200, 't3' => 120, 't1' => 75]);
		$group->game([$team1, $team2, $team5, $team4])->setResults(['t1' => 100, 't2' => 200, 't5' => 120, 't4' => 75]);
		$group->game([$team1, $team2, $team3, $team5])->setResults(['t1' => 100, 't3' => 200, 't2' => 120, 't5' => 75]);

		$filter_greater = new TeamFilter('third', '>', 1, [$group]);
		$filter_less = new TeamFilter('third', '<', 2, [$group]);
		$filter_greater_equal = new TeamFilter('third', '>=', 1, [$group]);
		$filter_less_equal = new TeamFilter('third', '<=', 1, [$group]);
		$filter_is = new TeamFilter('third', '=', 0, [$group]);
		$filter_isnt = new TeamFilter('third', '!=', 2, [$group]);

		self::assertCount(1, $group->getTeams(false, null, [$filter_greater]));
		self::assertCount(4, $group->getTeams(false, null, [$filter_less]));
		self::assertCount(3, $group->getTeams(false, null, [$filter_greater_equal]));
		self::assertCount(4, $group->getTeams(false, null, [$filter_less_equal]));
		self::assertCount(2, $group->getTeams(false, null, [$filter_is]));
		self::assertCount(5, $group->getTeams(false, null, [$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_team_teamFilter() : void {
		$group = new Group('Group 1', 'g1');
		$group->setInGame(4);

		$team1 = $group->team('Team 1', 't1'); // Second: 0
		$team2 = $group->team('Team 2', 't2'); // Second: 1
		$team3 = $group->team('Team 3', 't3'); // Second: 3
		$team4 = $group->team('Team 4', 't4'); // Second: 0
		$team5 = $group->team('Team 5', 't5'); // Second: 1

		$filter_greater = new TeamFilter('team', '>', $team1, [$group]);
		$filter_less = new TeamFilter('team', '<', $team1, [$group]);
		$filter_greater_equal = new TeamFilter('team', '>=', $team1, [$group]);
		$filter_less_equal = new TeamFilter('team', '<=', $team1, [$group]);
		$filter_is = new TeamFilter('team', '=', $team1, [$group]);
		$filter_isnt = new TeamFilter('team', '!=', $team1, [$group]);

		self::assertCount(1, $group->getTeams(false, null, [$filter_greater]));
		self::assertCount(1, $group->getTeams(false, null, [$filter_less]));
		self::assertCount(1, $group->getTeams(false, null, [$filter_greater_equal]));
		self::assertCount(1, $group->getTeams(false, null, [$filter_less_equal]));
		self::assertCount(1, $group->getTeams(false, null, [$filter_is]));
		self::assertCount(4, $group->getTeams(false, null, [$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_progressed_teamFilter() : void {
		$group = new Group('Group 1', 'g1');
		$group->setInGame(4);
		$group2 = new Group('Group 2', 'g2');
		$group2->setInGame(4);

		$group->progression($group2, 0, 3);

		$team1 = $group->team('Team 1', 't1'); // Points: 4
		$team2 = $group->team('Team 2', 't2'); // Points: 11
		$team3 = $group->team('Team 3', 't3'); // Points: 9
		$team4 = $group->team('Team 4', 't4'); // Points: 0
		$team5 = $group->team('Team 5', 't5'); // Points: 6

		$group->game([$team1, $team2, $team3, $team4])->setResults(['t1' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$group->game([$team5, $team2, $team3, $team4])->setResults(['t5' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$group->game([$team1, $team5, $team3, $team4])->setResults(['t1' => 100, 't5' => 200, 't3' => 120, 't4' => 75]);
		$group->game([$team1, $team2, $team5, $team4])->setResults(['t1' => 100, 't2' => 200, 't5' => 120, 't4' => 75]);
		$group->game([$team1, $team2, $team3, $team5])->setResults(['t1' => 100, 't3' => 200, 't2' => 120, 't5' => 75]);

		$group->progress();

		$filter_greater = new TeamFilter('progressed', '>', 1, [$group]);
		$filter_less = new TeamFilter('progressed', '<', 2, [$group]);
		$filter_greater_equal = new TeamFilter('progressed', '>=', 1, [$group]);
		$filter_less_equal = new TeamFilter('progressed', '<=', 1, [$group]);
		$filter_is = new TeamFilter('progressed', '=', 0, [$group]);
		$filter_isnt = new TeamFilter('progressed', '!=', 2, [$group]);

		self::assertCount(3, $group->getTeams(false, null, [$filter_greater]));
		self::assertCount(3, $group->getTeams(false, null, [$filter_less]));
		self::assertCount(3, $group->getTeams(false, null, [$filter_greater_equal]));
		self::assertCount(3, $group->getTeams(false, null, [$filter_less_equal]));
		self::assertCount(3, $group->getTeams(false, null, [$filter_is]));
		self::assertCount(3, $group->getTeams(false, null, [$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_notprogressed_teamFilter() : void {
		$group = new Group('Group 1', 'g1');
		$group->setInGame(4);
		$group2 = new Group('Group 2', 'g2');
		$group2->setInGame(4);

		$group->progression($group2, 0, 3);

		$team1 = $group->team('Team 1', 't1'); // Points: 4
		$team2 = $group->team('Team 2', 't2'); // Points: 11
		$team3 = $group->team('Team 3', 't3'); // Points: 9
		$team4 = $group->team('Team 4', 't4'); // Points: 0
		$team5 = $group->team('Team 5', 't5'); // Points: 6

		$group->game([$team1, $team2, $team3, $team4])->setResults(['t1' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$group->game([$team5, $team2, $team3, $team4])->setResults(['t5' => 100, 't2' => 200, 't3' => 120, 't4' => 75]);
		$group->game([$team1, $team5, $team3, $team4])->setResults(['t1' => 100, 't5' => 200, 't3' => 120, 't4' => 75]);
		$group->game([$team1, $team2, $team5, $team4])->setResults(['t1' => 100, 't2' => 200, 't5' => 120, 't4' => 75]);
		$group->game([$team1, $team2, $team3, $team5])->setResults(['t1' => 100, 't3' => 200, 't2' => 120, 't5' => 75]);

		$group->progress();

		$filter_greater = new TeamFilter('not-progressed', '>', 1, [$group]);
		$filter_less = new TeamFilter('not-progressed', '<', 2, [$group]);
		$filter_greater_equal = new TeamFilter('not-progressed', '>=', 1, [$group]);
		$filter_less_equal = new TeamFilter('not-progressed', '<=', 1, [$group]);
		$filter_is = new TeamFilter('not-progressed', '=', 0, [$group]);
		$filter_isnt = new TeamFilter('not-progressed', '!=', 2, [$group]);

		self::assertCount(2, $group->getTeams(false, null, [$filter_greater]));
		self::assertCount(2, $group->getTeams(false, null, [$filter_less]));
		self::assertCount(2, $group->getTeams(false, null, [$filter_greater_equal]));
		self::assertCount(2, $group->getTeams(false, null, [$filter_less_equal]));
		self::assertCount(2, $group->getTeams(false, null, [$filter_is]));
		self::assertCount(2, $group->getTeams(false, null, [$filter_isnt]));

	}

	/** @test */
	public function check_filter_validate_avg_teamFilter() : void {
		$group = new Group('Group 1', 'g1');
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

		$filter_greater = new TeamFilter('score', '>', 140, [$group]);
		$filter_less = new TeamFilter('score', '<', 140, [$group]);
		$filter_greater_equal = new TeamFilter('score', '>=', 140, [$group]);
		$filter_less_equal = new TeamFilter('score', '<=', 140, [$group]);
		$filter_is = new TeamFilter('score', '=', 140, [$group]);
		$filter_isnt = new TeamFilter('score', '!=', 140, [$group]);

		self::assertTrue($filter_greater->validate($team2, ['g1'], 'avg'));
		self::assertFalse($filter_greater->validate($team4, ['g1'], 'avg'));

		self::assertTrue($filter_less->validate($team4, ['g1'], 'avg'));
		self::assertFalse($filter_less->validate($team3, ['g1'], 'avg'));

		self::assertTrue($filter_greater_equal->validate($team3, ['g1'], 'avg'));
		self::assertFalse($filter_greater_equal->validate($team5, ['g1'], 'avg'));

		self::assertTrue($filter_less_equal->validate($team1, ['g1'], 'avg'));
		self::assertFalse($filter_less_equal->validate($team2, ['g1'], 'avg'));

		self::assertTrue($filter_is->validate($team3, ['g1'], 'avg'));
		self::assertFalse($filter_is->validate($team4, ['g1'], 'avg'));

		self::assertTrue($filter_isnt->validate($team1, ['g1'], 'avg'));
		self::assertFalse($filter_isnt->validate($team3, ['g1'], 'avg'));

	}

	/** @test */
	public function check_filter_validate_max_teamFilter() : void {
		$group = new Group('Group 1', 'g1');
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

		$filter_greater = new TeamFilter('score', '>', 150, [$group]);
		$filter_less = new TeamFilter('score', '<', 150, [$group]);
		$filter_greater_equal = new TeamFilter('score', '>=', 150, [$group]);
		$filter_less_equal = new TeamFilter('score', '<=', 150, [$group]);
		$filter_is = new TeamFilter('score', '=', 150, [$group]);
		$filter_isnt = new TeamFilter('score', '!=', 150, [$group]);

		self::assertTrue($filter_greater->validate($team3, ['g1'], 'max'));
		self::assertFalse($filter_greater->validate($team5, ['g1'], 'max'));

		self::assertTrue($filter_less->validate($team4, ['g1'], 'max'));
		self::assertFalse($filter_less->validate($team2, ['g1'], 'max'));

		self::assertTrue($filter_greater_equal->validate($team5, ['g1'], 'max'));
		self::assertFalse($filter_greater_equal->validate($team1, ['g1'], 'max'));

		self::assertTrue($filter_less_equal->validate($team5, ['g1'], 'max'));
		self::assertFalse($filter_less_equal->validate($team2, ['g1'], 'max'));

		self::assertTrue($filter_is->validate($team5, ['g1'], 'max'));
		self::assertFalse($filter_is->validate($team1, ['g1'], 'max'));

		self::assertTrue($filter_isnt->validate($team2, ['g1'], 'max'));
		self::assertFalse($filter_isnt->validate($team5, ['g1'], 'max'));

	}

	/** @test */
	public function check_filter_validate_max_more_groups_teamFilter() : void {
		$group = new Group('Group 1', 'g1');
		$group->setInGame(2);
		$group2 = new Group('Group 2', 'g2');
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

		$filter_greater = new TeamFilter('score', '>', 500, [$group, $group2]);
		$filter_less = new TeamFilter('score', '<', 500, [$group, $group2]);
		$filter_greater_equal = new TeamFilter('score', '>=', 500, [$group, $group2]);
		$filter_less_equal = new TeamFilter('score', '<=', 500, [$group, $group2]);
		$filter_is = new TeamFilter('score', '=', 500, [$group, $group2]);
		$filter_isnt = new TeamFilter('score', '!=', 500, [$group, $group2]);

		self::assertTrue($filter_greater->validate($team2, ['g1', 'g2'], 'max'));
		self::assertFalse($filter_greater->validate($team1, ['g1', 'g2'], 'max'));

		self::assertTrue($filter_less->validate($team3, ['g1', 'g2'], 'max'));
		self::assertFalse($filter_less->validate($team1, ['g1', 'g2'], 'max'));

		self::assertTrue($filter_greater_equal->validate($team1, ['g1', 'g2'], 'max'));
		self::assertFalse($filter_greater_equal->validate($team3, ['g1', 'g2'], 'max'));

		self::assertTrue($filter_less_equal->validate($team3, ['g1', 'g2'], 'max'));
		self::assertFalse($filter_less_equal->validate($team2, ['g1', 'g2'], 'max'));

		self::assertTrue($filter_is->validate($team1, ['g1', 'g2'], 'max'));
		self::assertFalse($filter_is->validate($team3, ['g1', 'g2'], 'max'));

		self::assertTrue($filter_isnt->validate($team2, ['g1', 'g2'], 'max'));
		self::assertFalse($filter_isnt->validate($team1, ['g1', 'g2'], 'max'));

	}

	/** @test */
	public function check_filter_validate_min_teamFilter() : void {
		$group = new Group('Group 1', 'g1');
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

		$filter_greater = new TeamFilter('score', '>', 100, [$group]);
		$filter_less = new TeamFilter('score', '<', 100, [$group]);
		$filter_greater_equal = new TeamFilter('score', '>=', 100, [$group]);
		$filter_less_equal = new TeamFilter('score', '<=', 100, [$group]);
		$filter_is = new TeamFilter('score', '=', 100, [$group]);
		$filter_isnt = new TeamFilter('score', '!=', 100, [$group]);

		self::assertTrue($filter_greater->validate($team3, ['g1'], 'min'));
		self::assertFalse($filter_greater->validate($team5, ['g1'], 'min'));

		self::assertTrue($filter_less->validate($team4, ['g1'], 'min'));
		self::assertFalse($filter_less->validate($team1, ['g1'], 'min'));

		self::assertTrue($filter_greater_equal->validate($team1, ['g1'], 'min'));
		self::assertFalse($filter_greater_equal->validate($team4, ['g1'], 'min'));

		self::assertTrue($filter_less_equal->validate($team5, ['g1'], 'min'));
		self::assertFalse($filter_less_equal->validate($team2, ['g1'], 'min'));

		self::assertTrue($filter_is->validate($team1, ['g1'], 'min'));
		self::assertFalse($filter_is->validate($team2, ['g1'], 'min'));

		self::assertTrue($filter_isnt->validate($team3, ['g1'], 'min'));
		self::assertFalse($filter_isnt->validate($team1, ['g1'], 'min'));

	}

	/** @test */
	public function check_filter_validate_min_more_groups_teamFilter() : void {
		$group = new Group('Group 1', 'g1');
		$group->setInGame(2);
		$group2 = new Group('Group 2', 'g2');
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

		$filter_greater = new TeamFilter('score', '>', 220, [$group, $group2]);
		$filter_less = new TeamFilter('score', '<', 220, [$group, $group2]);
		$filter_greater_equal = new TeamFilter('score', '>=', 220, [$group, $group2]);
		$filter_less_equal = new TeamFilter('score', '<=', 220, [$group, $group2]);
		$filter_is = new TeamFilter('score', '=', 220, [$group, $group2]);
		$filter_isnt = new TeamFilter('score', '!=', 220, [$group, $group2]);

		self::assertTrue($filter_greater->validate($team2, ['g1', 'g2'], 'min'));
		self::assertFalse($filter_greater->validate($team1, ['g1', 'g2'], 'min'));

		self::assertTrue($filter_less->validate($team1, ['g1', 'g2'], 'min'));
		self::assertFalse($filter_less->validate($team3, ['g1', 'g2'], 'min'));

		self::assertTrue($filter_greater_equal->validate($team3, ['g1', 'g2'], 'min'));
		self::assertFalse($filter_greater_equal->validate($team1, ['g1', 'g2'], 'min'));

		self::assertTrue($filter_less_equal->validate($team1, ['g1', 'g2'], 'min'));
		self::assertFalse($filter_less_equal->validate($team2, ['g1', 'g2'], 'min'));

		self::assertTrue($filter_is->validate($team3, ['g1', 'g2'], 'min'));
		self::assertFalse($filter_is->validate($team1, ['g1', 'g2'], 'min'));

		self::assertTrue($filter_isnt->validate($team2, ['g1', 'g2'], 'min'));
		self::assertFalse($filter_isnt->validate($team3, ['g1', 'g2'], 'min'));

	}

}
