<?php

use PHPUnit\Framework\TestCase;
use TournamentGenerator\Game;
use TournamentGenerator\Group;
use TournamentGenerator\Team;

/**
 *
 */
class GameTest extends TestCase
{
	
	/** @test */
	public function check_creation_game() : void {
		$group = new Group('Group', 'g1');

		$team1 = new Team('Team1', 1);
		$team2 = new Team('Team2', 2);

		$game = new Game([$team1, $team2], $group);

		self::assertCount(2, $game->getTeams());

		self::assertEquals(1, $team1->getGameWith($team2, $group));
		self::assertSame(['g1' => [2 => 1]], $team1->getGameWith($team2));
		self::assertSame([2 => 1], $team1->getGameWith(null, $group));

		self::assertEquals(1, $team2->getGameWith($team1, $group));
		self::assertSame(['g1' => [1 => 1]], $team2->getGameWith($team1));
		self::assertSame([1 => 1], $team2->getGameWith(null, $group));
	}

	/** @test */
	public function check_creation_false_teams_game() : void {
		$group = new Group('Group', 'g1');

		$team1 = new Team('Team1', 1);
		$team2 = new Team('Team2', 2);

		$this->expectException(TypeError::class);
		new Game([$team1, 'not a team', $team2], $group);
	}

	/** @test */
	public function check_get_group_game() : void {
		$group = new Group('Group', 'g1');

		$team1 = new Team('Team1', 1);
		$team2 = new Team('Team2', 2);

		$game = new Game([$team1, $team2], $group);

		self::assertSame($group, $game->getGroup());
	}

	/** @test */
	public function check_adding_teams_game() : void {
		$group = new Group('Group', 'g1');

		$team1 = new Team('Team1', 1);
		$team2 = new Team('Team2', 2);
		$team3 = new Team('Team3', 3);
		$team4 = new Team('Team4', 4);

		$game = new Game([$team1, $team2], $group);

		self::assertCount(2, $game->getTeams());

		$game->addTeam($team3, $team4);
		self::assertCount(4, $game->getTeams());

		self::assertEquals(1, $team1->getGameWith($team3, $group));
		self::assertEquals(1, $team1->getGameWith($team4, $group));
		self::assertSame([2 => 1, 3 => 1, 4 => 1], $team1->getGameWith(null, $group));

		self::assertEquals(1, $team2->getGameWith($team3, $group));
		self::assertEquals(1, $team2->getGameWith($team4, $group));
		self::assertSame([1 => 1, 3 => 1, 4 => 1], $team2->getGameWith(null, $group));

		self::assertEquals(1, $team3->getGameWith($team1, $group));
		self::assertEquals(1, $team3->getGameWith($team2, $group));
		self::assertEquals(1, $team3->getGameWith($team4, $group));
		self::assertSame([1 => 1, 2 => 1, 4 => 1], $team3->getGameWith(null, $group));

		self::assertEquals(1, $team4->getGameWith($team1, $group));
		self::assertEquals(1, $team4->getGameWith($team2, $group));
		self::assertEquals(1, $team4->getGameWith($team3, $group));
		self::assertSame([1 => 1, 2 => 1, 3 => 1], $team4->getGameWith(null, $group));
	}

	/** @test */
	public function check_adding_teams_false_game() : void {
		$group = new Group('Group', 'g1');

		$team1 = new Team('Team1', 1);
		$team2 = new Team('Team2', 2);
		$team3 = new Team('Team3', 3);
		$team4 = new Team('Team4', 4);

		$game = new Game([$team1, $team2], $group);

		self::assertCount(2, $game->getTeams());

		$this->expectException(TypeError::class);
		$game->addTeam($team3, $team4, 'not a team');
		self::assertCount(4, $game->getTeams());

		self::assertEquals(1, $team1->getGameWith($team3, $group));
		self::assertEquals(1, $team1->getGameWith($team4, $group));
		self::assertSame([2 => 1, 3 => 1, 4 => 1], $team1->getGameWith(null, $group));

		self::assertEquals(1, $team2->getGameWith($team3, $group));
		self::assertEquals(1, $team2->getGameWith($team4, $group));
		self::assertSame([1 => 1, 3 => 1, 4 => 1], $team2->getGameWith(null, $group));

		self::assertEquals(1, $team3->getGameWith($team1, $group));
		self::assertEquals(1, $team3->getGameWith($team2, $group));
		self::assertEquals(1, $team3->getGameWith($team4, $group));
		self::assertSame([1 => 1, 2 => 1, 4 => 1], $team3->getGameWith(null, $group));

		self::assertEquals(1, $team4->getGameWith($team1, $group));
		self::assertEquals(1, $team4->getGameWith($team2, $group));
		self::assertEquals(1, $team4->getGameWith($team3, $group));
		self::assertSame([1 => 1, 2 => 1, 3 => 1], $team4->getGameWith(null, $group));
	}

	/** @test */
	public function check_get_team_by_id_game() : void {
		$group = new Group('Group', 'g1');

		$team1 = new Team('Team1', 1);
		$team2 = new Team('Team2', 2);

		$game = new Game([$team1, $team2], $group);

		self::assertSame($team1, $game->getTeam(1));
	}

	/** @test */
	public function check_get_team_by_id_unexisting_team_game() : void {
		$group = new Group('Group', 'g1');

		$team1 = new Team('Team1', 1);
		$team2 = new Team('Team2', 2);

		$game = new Game([$team1, $team2], $group);

		self::assertNull($game->getTeam('not real id'));
	}

	/** @test */
	public function check_get_teams_ids_game() : void {
		$group = new Group('Group', 'g1');

		$team1 = new Team('Team1', 1);
		$team2 = new Team('Team2', 2);

		$game = new Game([$team1, $team2], $group);

		self::assertSame([1, 2], $game->getTeamsIds());
	}

	/** @test */
	public function check_setting_results_2_game() : void {
		$group = new Group('Group', 'g1');
		$group->setInGame(2);

		$team1 = new Team('Team1', 1);
		$team2 = new Team('Team2', 2);

		$group->addTeam($team1, $team2);

		$game = new Game([$team1, $team2], $group);

		$game->setResults([1 => 1000, 2 => 2000]);

		self::assertSame([1 => ['score' => 1000, 'points' => 0, 'type' => 'loss'], 2 => ['score' => 2000, 'points' => 3, 'type' => 'win']], $game->getResults());
		self::assertEquals(0, $team1->getSumPoints());
		self::assertEquals(1000, $team1->getSumScore());
		self::assertEquals(3, $team2->getSumPoints());
		self::assertEquals(2000, $team2->getSumScore());
		self::assertSame([
											 'group'  => $group,
											 'points' => 0,
											 'score'  => 1000,
											 'wins'   => 0,
											 'draws'  => 0,
											 'losses' => 1,
											 'second' => 0,
											 'third'  => 0
										 ],
										 $team1->getGroupResults('g1')
		);
		self::assertSame([
											 'group'  => $group,
											 'points' => 3,
											 'score'  => 2000,
											 'wins'   => 1,
											 'draws'  => 0,
											 'losses' => 0,
											 'second' => 0,
											 'third'  => 0
										 ]
			, $team2->getGroupResults('g1')
		);

		$game->resetResults();

		self::assertSame([], $game->getResults());
		self::assertEquals(0, $team1->getSumPoints());
		self::assertEquals(0, $team1->getSumScore());
		self::assertEquals(0, $team2->getSumPoints());
		self::assertEquals(0, $team2->getSumScore());
		self::assertSame([
											 'group'  => $group,
											 'points' => 0,
											 'score'  => 0,
											 'wins'   => 0,
											 'draws'  => 0,
											 'losses' => 0,
											 'second' => 0,
											 'third'  => 0
										 ],
										 $team1->getGroupResults('g1')
		);
		self::assertSame([
											 'group'  => $group,
											 'points' => 0,
											 'score'  => 0,
											 'wins'   => 0,
											 'draws'  => 0,
											 'losses' => 0,
											 'second' => 0,
											 'third'  => 0
										 ]
			, $team2->getGroupResults('g1')
		);
	}

	/** @test */
	public function check_setting_results_2_draw_game() : void {
		$group = new Group('Group', 'g1');
		$group->setInGame(2);

		$team1 = new Team('Team1', 1);
		$team2 = new Team('Team2', 2);

		$group->addTeam($team1, $team2);

		$game = new Game([$team1, $team2], $group);

		$game->setResults([1 => 1000, 2 => 1000]);

		self::assertSame([1 => ['score' => 1000, 'points' => 1, 'type' => 'draw'], 2 => ['score' => 1000, 'points' => 1, 'type' => 'draw']], $game->getResults());
		self::assertEquals(1, $team1->getSumPoints());
		self::assertEquals(1000, $team1->getSumScore());
		self::assertEquals(1, $team2->getSumPoints());
		self::assertEquals(1000, $team2->getSumScore());
		self::assertSame([
											 'group'  => $group,
											 'points' => 1,
											 'score'  => 1000,
											 'wins'   => 0,
											 'draws'  => 1,
											 'losses' => 0,
											 'second' => 0,
											 'third'  => 0
										 ],
										 $team1->getGroupResults('g1')
		);
		self::assertSame([
											 'group'  => $group,
											 'points' => 1,
											 'score'  => 1000,
											 'wins'   => 0,
											 'draws'  => 1,
											 'losses' => 0,
											 'second' => 0,
											 'third'  => 0
										 ]
			, $team2->getGroupResults('g1')
		);

		$game->resetResults();

		self::assertSame([], $game->getResults());
		self::assertEquals(0, $team1->getSumPoints());
		self::assertEquals(0, $team1->getSumScore());
		self::assertEquals(0, $team2->getSumPoints());
		self::assertEquals(0, $team2->getSumScore());
		self::assertSame([
											 'group'  => $group,
											 'points' => 0,
											 'score'  => 0,
											 'wins'   => 0,
											 'draws'  => 0,
											 'losses' => 0,
											 'second' => 0,
											 'third'  => 0
										 ],
										 $team1->getGroupResults('g1')
		);
		self::assertSame([
											 'group'  => $group,
											 'points' => 0,
											 'score'  => 0,
											 'wins'   => 0,
											 'draws'  => 0,
											 'losses' => 0,
											 'second' => 0,
											 'third'  => 0
										 ]
			, $team2->getGroupResults('g1')
		);
	}

	/** @test */
	public function check_setting_results_false_game() : void {
		$group = new Group('Group', 'g1');
		$group->setInGame(2);

		$team1 = new Team('Team1', 1);
		$team2 = new Team('Team2', 2);

		$group->addTeam($team1, $team2);

		$game = new Game([$team1, $team2], $group);

		$this->expectException(TypeError::class);
		$game->setResults([1 => 1000, 2 => [2000]]);

	}

	/** @test */
	public function check_setting_results_false_team_game() : void {
		$group = new Group('Group', 'g1');
		$group->setInGame(2);

		$team1 = new Team('Team1', 1);
		$team2 = new Team('Team2', 2);

		$group->addTeam($team1, $team2);

		$game = new Game([$team1, $team2], $group);

		$this->expectException(Exception::class);
		$game->setResults([1 => 1000, 23 => 2000]);

	}

	/** @test */
	public function check_setting_results_3_game() : void {
		$group = new Group('Group', 'g1');
		$group->setInGame(3);

		$team1 = new Team('Team1', 1);
		$team2 = new Team('Team2', 2);
		$team3 = new Team('Team3', 3);

		$group->addTeam($team1, $team2, $team3);

		$game = new Game([$team1, $team2, $team3], $group);

		$game->setResults([1 => 1000, 2 => 2000, 3 => 3000]);

		self::assertSame([
											 1 => ['score' => 1000, 'points' => 0, 'type' => 'loss'],
											 2 => ['score' => 2000, 'points' => 2, 'type' => 'second'],
											 3 => ['score' => 3000, 'points' => 3, 'type' => 'win']
										 ], $game->getResults()
		);
		self::assertEquals(0, $team1->getSumPoints());
		self::assertEquals(1000, $team1->getSumScore());
		self::assertEquals(2, $team2->getSumPoints());
		self::assertEquals(2000, $team2->getSumScore());
		self::assertEquals(3, $team3->getSumPoints());
		self::assertEquals(3000, $team3->getSumScore());
		self::assertSame([
											 'group'  => $group,
											 'points' => 0,
											 'score'  => 1000,
											 'wins'   => 0,
											 'draws'  => 0,
											 'losses' => 1,
											 'second' => 0,
											 'third'  => 0
										 ],
										 $team1->getGroupResults('g1')
		);
		self::assertSame([
											 'group'  => $group,
											 'points' => 2,
											 'score'  => 2000,
											 'wins'   => 0,
											 'draws'  => 0,
											 'losses' => 0,
											 'second' => 1,
											 'third'  => 0
										 ]
			, $team2->getGroupResults('g1')
		);
		self::assertSame([
											 'group'  => $group,
											 'points' => 3,
											 'score'  => 3000,
											 'wins'   => 1,
											 'draws'  => 0,
											 'losses' => 0,
											 'second' => 0,
											 'third'  => 0
										 ]
			, $team3->getGroupResults('g1')
		);

		$game->resetResults();

		self::assertSame([], $game->getResults()
		);
		self::assertEquals(0, $team1->getSumPoints());
		self::assertEquals(0, $team1->getSumScore());
		self::assertEquals(0, $team2->getSumPoints());
		self::assertEquals(0, $team2->getSumScore());
		self::assertEquals(0, $team3->getSumPoints());
		self::assertEquals(0, $team3->getSumScore());
		self::assertSame([
											 'group'  => $group,
											 'points' => 0,
											 'score'  => 0,
											 'wins'   => 0,
											 'draws'  => 0,
											 'losses' => 0,
											 'second' => 0,
											 'third'  => 0
										 ],
										 $team1->getGroupResults('g1')
		);
		self::assertSame([
											 'group'  => $group,
											 'points' => 0,
											 'score'  => 0,
											 'wins'   => 0,
											 'draws'  => 0,
											 'losses' => 0,
											 'second' => 0,
											 'third'  => 0
										 ]
			, $team2->getGroupResults('g1')
		);
		self::assertSame([
											 'group'  => $group,
											 'points' => 0,
											 'score'  => 0,
											 'wins'   => 0,
											 'draws'  => 0,
											 'losses' => 0,
											 'second' => 0,
											 'third'  => 0
										 ]
			, $team3->getGroupResults('g1')
		);
	}

	/** @test */
	public function check_setting_results_4_game() : void {
		$group = new Group('Group', 'g1');
		$group->setInGame(4);

		$team1 = new Team('Team1', 1);
		$team2 = new Team('Team2', 2);
		$team3 = new Team('Team3', 3);
		$team4 = new Team('Team4', 4);

		$group->addTeam($team1, $team2, $team3, $team4);

		$game = new Game([$team1, $team2, $team3, $team4], $group);

		$game->setResults([1 => 1000, 2 => 2000, 3 => 3000, 4 => 4000]);

		self::assertSame([
											 1 => ['score' => 1000, 'points' => 0, 'type' => 'loss'],
											 2 => ['score' => 2000, 'points' => 1, 'type' => 'third'],
											 3 => ['score' => 3000, 'points' => 2, 'type' => 'second'],
											 4 => ['score' => 4000, 'points' => 3, 'type' => 'win']
										 ], $game->getResults()
		);
		self::assertEquals(0, $team1->getSumPoints());
		self::assertEquals(1000, $team1->getSumScore());
		self::assertEquals(1, $team2->getSumPoints());
		self::assertEquals(2000, $team2->getSumScore());
		self::assertEquals(2, $team3->getSumPoints());
		self::assertEquals(3000, $team3->getSumScore());
		self::assertEquals(3, $team4->getSumPoints());
		self::assertEquals(4000, $team4->getSumScore());
		self::assertSame([
											 'group'  => $group,
											 'points' => 0,
											 'score'  => 1000,
											 'wins'   => 0,
											 'draws'  => 0,
											 'losses' => 1,
											 'second' => 0,
											 'third'  => 0
										 ],
										 $team1->getGroupResults('g1')
		);
		self::assertSame([
											 'group'  => $group,
											 'points' => 1,
											 'score'  => 2000,
											 'wins'   => 0,
											 'draws'  => 0,
											 'losses' => 0,
											 'second' => 0,
											 'third'  => 1
										 ]
			, $team2->getGroupResults('g1')
		);
		self::assertSame([
											 'group'  => $group,
											 'points' => 2,
											 'score'  => 3000,
											 'wins'   => 0,
											 'draws'  => 0,
											 'losses' => 0,
											 'second' => 1,
											 'third'  => 0
										 ]
			, $team3->getGroupResults('g1')
		);
		self::assertSame([
											 'group'  => $group,
											 'points' => 3,
											 'score'  => 4000,
											 'wins'   => 1,
											 'draws'  => 0,
											 'losses' => 0,
											 'second' => 0,
											 'third'  => 0
										 ]
			, $team4->getGroupResults('g1')
		);

		$game->resetResults();

		self::assertSame([], $game->getResults());
		self::assertEquals(0, $team1->getSumPoints());
		self::assertEquals(0, $team1->getSumScore());
		self::assertEquals(0, $team2->getSumPoints());
		self::assertEquals(0, $team2->getSumScore());
		self::assertEquals(0, $team3->getSumPoints());
		self::assertEquals(0, $team3->getSumScore());
		self::assertEquals(0, $team4->getSumPoints());
		self::assertEquals(0, $team4->getSumScore());
		self::assertSame([
											 'group'  => $group,
											 'points' => 0,
											 'score'  => 0,
											 'wins'   => 0,
											 'draws'  => 0,
											 'losses' => 0,
											 'second' => 0,
											 'third'  => 0
										 ],
										 $team1->getGroupResults('g1')
		);
		self::assertSame([
											 'group'  => $group,
											 'points' => 0,
											 'score'  => 0,
											 'wins'   => 0,
											 'draws'  => 0,
											 'losses' => 0,
											 'second' => 0,
											 'third'  => 0
										 ]
			, $team2->getGroupResults('g1')
		);
		self::assertSame([
											 'group'  => $group,
											 'points' => 0,
											 'score'  => 0,
											 'wins'   => 0,
											 'draws'  => 0,
											 'losses' => 0,
											 'second' => 0,
											 'third'  => 0
										 ]
			, $team3->getGroupResults('g1')
		);
		self::assertSame([
											 'group'  => $group,
											 'points' => 0,
											 'score'  => 0,
											 'wins'   => 0,
											 'draws'  => 0,
											 'losses' => 0,
											 'second' => 0,
											 'third'  => 0
										 ]
			, $team4->getGroupResults('g1')
		);
	}

	/** @test */
	public function check_getting_loss_and_win_teams_2_game() : void {
		$group = new Group('Group', 'g1');
		$group->setInGame(2);

		$team1 = new Team('Team1', 1);
		$team2 = new Team('Team2', 2);

		$group->addTeam($team1, $team2);

		$game = new Game([$team1, $team2], $group);

		$game->setResults([1 => 1000, 2 => 2000]);

		self::assertEquals(1, $game->getLoss());
		self::assertEquals(2, $game->getWin());

	}

	/** @test */
	public function check_getting_draw_teams_2_game() : void {
		$group = new Group('Group', 'g1');
		$group->setInGame(2);

		$team1 = new Team('Team1', 1);
		$team2 = new Team('Team2', 2);

		$group->addTeam($team1, $team2);

		$game = new Game([$team1, $team2], $group);

		$game->setResults([1 => 1000, 2 => 1000]);

		self::assertSame([1, 2], $game->getDraw());

	}

	/** @test */
	public function check_getting_win_second_third_and_loss_teams_4_game() : void {
		$group = new Group('Group', 'g1');
		$group->setInGame(4);

		$team1 = new Team('Team1', 1);
		$team2 = new Team('Team2', 2);
		$team3 = new Team('Team3', 3);
		$team4 = new Team('Team4', 4);

		$group->addTeam($team1, $team2, $team3, $team4);

		$game = new Game([$team1, $team2, $team3, $team4], $group);

		$game->setResults([1 => 1000, 2 => 2000, 3 => 3000, 4 => 4000]);

		self::assertEquals(1, $game->getLoss());
		self::assertEquals(2, $game->getThird());
		self::assertEquals(3, $game->getSecond());
		self::assertEquals(4, $game->getWin());

	}


}
