<?php
use PHPUnit\Framework\TestCase;

/**
 *
 */
class GameTest extends TestCase
{

	/** @test */
	public function check_creation_game() {
		$group = new \TournamentGenerator\Group('Group', 'g1');

		$team1 = new \TournamentGenerator\Team('Team1', 1);
		$team2 = new \TournamentGenerator\Team('Team2', 2);

		$game = new \TournamentGenerator\Game([$team1, $team2], $group);

		$this->assertCount(2, $game->getTeams());

		$this->assertEquals(1, $team1->getGameWith($team2, $group));
		$this->assertSame(['g1' => [2 => 1]], $team1->getGameWith($team2));
		$this->assertSame([2 => 1], $team1->getGameWith(null, $group));

		$this->assertEquals(1, $team2->getGameWith($team1, $group));
		$this->assertSame(['g1' => [1 => 1]], $team2->getGameWith($team1));
		$this->assertSame([1 => 1], $team2->getGameWith(null, $group));
	}

	/** @test */
	public function check_creation_false_teams_game() {
		$group = new \TournamentGenerator\Group('Group', 'g1');

		$team1 = new \TournamentGenerator\Team('Team1', 1);
		$team2 = new \TournamentGenerator\Team('Team2', 2);

		$this->expectException(Exception::class);
		$game = new \TournamentGenerator\Game([$team1, 'not a team', $team2], $group);

		$this->assertCount(2, $game->getTeams());

		$this->assertEquals(1, $team1->getGameWith($team2, $group));
		$this->assertSame(['g1' => [2 => 1]], $team1->getGameWith($team2));
		$this->assertSame([2 => 1], $team1->getGameWith(null, $group));

		$this->assertEquals(1, $team2->getGameWith($team1, $group));
		$this->assertSame(['g1' => [1 => 1]], $team2->getGameWith($team1));
		$this->assertSame([1 => 1], $team2->getGameWith(null, $group));
	}

	/** @test */
	public function check_get_group_game() {
		$group = new \TournamentGenerator\Group('Group', 'g1');

		$team1 = new \TournamentGenerator\Team('Team1', 1);
		$team2 = new \TournamentGenerator\Team('Team2', 2);

		$game = new \TournamentGenerator\Game([$team1, $team2], $group);

		$this->assertSame($group, $game->getGroup());
	}

	/** @test */
	public function check_adding_teams_game() {
		$group = new \TournamentGenerator\Group('Group', 'g1');

		$team1 = new \TournamentGenerator\Team('Team1', 1);
		$team2 = new \TournamentGenerator\Team('Team2', 2);
		$team3 = new \TournamentGenerator\Team('Team3', 3);
		$team4 = new \TournamentGenerator\Team('Team4', 4);

		$game = new \TournamentGenerator\Game([$team1, $team2], $group);

		$this->assertCount(2, $game->getTeams());

		$game->addTeam($team3, $team4);
		$this->assertCount(4, $game->getTeams());

		$this->assertEquals(1, $team1->getGameWith($team3, $group));
		$this->assertEquals(1, $team1->getGameWith($team4, $group));
		$this->assertSame([2 => 1, 3 => 1, 4 => 1], $team1->getGameWith(null, $group));

		$this->assertEquals(1, $team2->getGameWith($team3, $group));
		$this->assertEquals(1, $team2->getGameWith($team4, $group));
		$this->assertSame([1 => 1, 3 => 1, 4 => 1], $team2->getGameWith(null, $group));

		$this->assertEquals(1, $team3->getGameWith($team1, $group));
		$this->assertEquals(1, $team3->getGameWith($team2, $group));
		$this->assertEquals(1, $team3->getGameWith($team4, $group));
		$this->assertSame([1 => 1, 2 => 1, 4 => 1], $team3->getGameWith(null, $group));

		$this->assertEquals(1, $team4->getGameWith($team1, $group));
		$this->assertEquals(1, $team4->getGameWith($team2, $group));
		$this->assertEquals(1, $team4->getGameWith($team3, $group));
		$this->assertSame([1 => 1, 2 => 1, 3 => 1], $team4->getGameWith(null, $group));
	}

	/** @test */
	public function check_adding_teams_false_game() {
		$group = new \TournamentGenerator\Group('Group', 'g1');

		$team1 = new \TournamentGenerator\Team('Team1', 1);
		$team2 = new \TournamentGenerator\Team('Team2', 2);
		$team3 = new \TournamentGenerator\Team('Team3', 3);
		$team4 = new \TournamentGenerator\Team('Team4', 4);

		$game = new \TournamentGenerator\Game([$team1, $team2], $group);

		$this->assertCount(2, $game->getTeams());

		$this->expectException(Exception::class);
		$game->addTeam($team3, $team4, 'not a team');
		$this->assertCount(4, $game->getTeams());

		$this->assertEquals(1, $team1->getGameWith($team3, $group));
		$this->assertEquals(1, $team1->getGameWith($team4, $group));
		$this->assertSame([2 => 1, 3 => 1, 4 => 1], $team1->getGameWith(null, $group));

		$this->assertEquals(1, $team2->getGameWith($team3, $group));
		$this->assertEquals(1, $team2->getGameWith($team4, $group));
		$this->assertSame([1 => 1, 3 => 1, 4 => 1], $team2->getGameWith(null, $group));

		$this->assertEquals(1, $team3->getGameWith($team1, $group));
		$this->assertEquals(1, $team3->getGameWith($team2, $group));
		$this->assertEquals(1, $team3->getGameWith($team4, $group));
		$this->assertSame([1 => 1, 2 => 1, 4 => 1], $team3->getGameWith(null, $group));

		$this->assertEquals(1, $team4->getGameWith($team1, $group));
		$this->assertEquals(1, $team4->getGameWith($team2, $group));
		$this->assertEquals(1, $team4->getGameWith($team3, $group));
		$this->assertSame([1 => 1, 2 => 1, 3 => 1], $team4->getGameWith(null, $group));
	}

	/** @test */
	public function check_get_team_by_id_game() {
		$group = new \TournamentGenerator\Group('Group', 'g1');

		$team1 = new \TournamentGenerator\Team('Team1', 1);
		$team2 = new \TournamentGenerator\Team('Team2', 2);

		$game = new \TournamentGenerator\Game([$team1, $team2], $group);

		$this->assertSame($team1, $game->getTeam(1));
	}

	/** @test */
	public function check_get_team_by_id_unexisting_team_game() {
		$group = new \TournamentGenerator\Group('Group', 'g1');

		$team1 = new \TournamentGenerator\Team('Team1', 1);
		$team2 = new \TournamentGenerator\Team('Team2', 2);

		$game = new \TournamentGenerator\Game([$team1, $team2], $group);

		$this->assertFalse($game->getTeam('not real id'));
	}

	/** @test */
	public function check_get_teams_ids_game() {
		$group = new \TournamentGenerator\Group('Group', 'g1');

		$team1 = new \TournamentGenerator\Team('Team1', 1);
		$team2 = new \TournamentGenerator\Team('Team2', 2);

		$game = new \TournamentGenerator\Game([$team1, $team2], $group);

		$this->assertSame([1, 2], $game->getTeamsIds());
	}

	/** @test */
	public function check_setting_results_2_game() {
		$group = new \TournamentGenerator\Group('Group', 'g1');
		$group->setInGame(2);

		$team1 = new \TournamentGenerator\Team('Team1', 1);
		$team2 = new \TournamentGenerator\Team('Team2', 2);

		$group->addTeam($team1, $team2);

		$game = new \TournamentGenerator\Game([$team1, $team2], $group);

		$game->setResults([1 => 1000, 2 => 2000]);

		$this->assertSame([1 => ['score' => 1000, 'points' => 0, 'type' => 'loss'], 2 => ['score' => 2000, 'points' => 3, 'type' => 'win']], $game->getResults());
		$this->assertEquals(0, $team1->getSumPoints());
		$this->assertEquals(1000, $team1->getSumScore());
		$this->assertEquals(3, $team2->getSumPoints());
		$this->assertEquals(2000, $team2->getSumScore());
		$this->assertSame([
				'group' => $group,
				'points' => 0,
				'score' => 1000,
				'wins' => 0,
				'draws' => 0,
				'losses' => 1,
				'second' => 0,
				'third' => 0],
			$team1->getGroupResults('g1')
		);
		$this->assertSame([
				'group' => $group,
				'points' => 3,
				'score' => 2000,
				'wins' => 1,
				'draws' => 0,
				'losses' => 0,
				'second' => 0,
				'third' => 0]
			, $team2->getGroupResults('g1')
		);

		$game->resetResults();

		$this->assertSame([], $game->getResults());
		$this->assertEquals(0, $team1->getSumPoints());
		$this->assertEquals(0, $team1->getSumScore());
		$this->assertEquals(0, $team2->getSumPoints());
		$this->assertEquals(0, $team2->getSumScore());
		$this->assertSame([
				'group' => $group,
				'points' => 0,
				'score' => 0,
				'wins' => 0,
				'draws' => 0,
				'losses' => 0,
				'second' => 0,
				'third' => 0],
			$team1->getGroupResults('g1')
		);
		$this->assertSame([
				'group' => $group,
				'points' => 0,
				'score' => 0,
				'wins' => 0,
				'draws' => 0,
				'losses' => 0,
				'second' => 0,
				'third' => 0]
			, $team2->getGroupResults('g1')
		);
	}

	/** @test */
	public function check_setting_results_2_draw_game() {
		$group = new \TournamentGenerator\Group('Group', 'g1');
		$group->setInGame(2);

		$team1 = new \TournamentGenerator\Team('Team1', 1);
		$team2 = new \TournamentGenerator\Team('Team2', 2);

		$group->addTeam($team1, $team2);

		$game = new \TournamentGenerator\Game([$team1, $team2], $group);

		$game->setResults([1 => 1000, 2 => 1000]);

		$this->assertSame([1 => ['score' => 1000, 'points' => 1, 'type' => 'draw'], 2 => ['score' => 1000, 'points' => 1, 'type' => 'draw']], $game->getResults());
		$this->assertEquals(1, $team1->getSumPoints());
		$this->assertEquals(1000, $team1->getSumScore());
		$this->assertEquals(1, $team2->getSumPoints());
		$this->assertEquals(1000, $team2->getSumScore());
		$this->assertSame([
				'group' => $group,
				'points' => 1,
				'score' => 1000,
				'wins' => 0,
				'draws' => 1,
				'losses' => 0,
				'second' => 0,
				'third' => 0],
			$team1->getGroupResults('g1')
		);
		$this->assertSame([
				'group' => $group,
				'points' => 1,
				'score' => 1000,
				'wins' => 0,
				'draws' => 1,
				'losses' => 0,
				'second' => 0,
				'third' => 0]
			, $team2->getGroupResults('g1')
		);

		$game->resetResults();

		$this->assertSame([], $game->getResults());
		$this->assertEquals(0, $team1->getSumPoints());
		$this->assertEquals(0, $team1->getSumScore());
		$this->assertEquals(0, $team2->getSumPoints());
		$this->assertEquals(0, $team2->getSumScore());
		$this->assertSame([
				'group' => $group,
				'points' => 0,
				'score' => 0,
				'wins' => 0,
				'draws' => 0,
				'losses' => 0,
				'second' => 0,
				'third' => 0],
			$team1->getGroupResults('g1')
		);
		$this->assertSame([
				'group' => $group,
				'points' => 0,
				'score' => 0,
				'wins' => 0,
				'draws' => 0,
				'losses' => 0,
				'second' => 0,
				'third' => 0]
			, $team2->getGroupResults('g1')
		);
	}

	/** @test */
	public function check_setting_results_false_game() {
		$group = new \TournamentGenerator\Group('Group', 'g1');
		$group->setInGame(2);

		$team1 = new \TournamentGenerator\Team('Team1', 1);
		$team2 = new \TournamentGenerator\Team('Team2', 2);

		$group->addTeam($team1, $team2);

		$game = new \TournamentGenerator\Game([$team1, $team2], $group);

		$this->expectException(TypeError::class);
		$game->setResults([1 => 1000, 2 => [2000]]);

	}

	/** @test */
	public function check_setting_results_false_team_game() {
		$group = new \TournamentGenerator\Group('Group', 'g1');
		$group->setInGame(2);

		$team1 = new \TournamentGenerator\Team('Team1', 1);
		$team2 = new \TournamentGenerator\Team('Team2', 2);

		$group->addTeam($team1, $team2);

		$game = new \TournamentGenerator\Game([$team1, $team2], $group);

		$this->expectException(Exception::class);
		$game->setResults([1 => 1000, 23 => 2000]);

	}

	/** @test */
	public function check_setting_results_3_game() {
		$group = new \TournamentGenerator\Group('Group', 'g1');
		$group->setInGame(3);

		$team1 = new \TournamentGenerator\Team('Team1', 1);
		$team2 = new \TournamentGenerator\Team('Team2', 2);
		$team3 = new \TournamentGenerator\Team('Team3', 3);

		$group->addTeam($team1, $team2, $team3);

		$game = new \TournamentGenerator\Game([$team1, $team2, $team3], $group);

		$game->setResults([1 => 1000, 2 => 2000, 3 => 3000]);

		$this->assertSame([
				1 => ['score' => 1000, 'points' => 0, 'type' => 'loss'],
				2 => ['score' => 2000, 'points' => 2, 'type' => 'second'],
				3 => ['score' => 3000, 'points' => 3, 'type' => 'win']
			], $game->getResults()
		);
		$this->assertEquals(0, $team1->getSumPoints());
		$this->assertEquals(1000, $team1->getSumScore());
		$this->assertEquals(2, $team2->getSumPoints());
		$this->assertEquals(2000, $team2->getSumScore());
		$this->assertEquals(3, $team3->getSumPoints());
		$this->assertEquals(3000, $team3->getSumScore());
		$this->assertSame([
				'group' => $group,
				'points' => 0,
				'score' => 1000,
				'wins' => 0,
				'draws' => 0,
				'losses' => 1,
				'second' => 0,
				'third' => 0],
			$team1->getGroupResults('g1')
		);
		$this->assertSame([
				'group' => $group,
				'points' => 2,
				'score' => 2000,
				'wins' => 0,
				'draws' => 0,
				'losses' => 0,
				'second' => 1,
				'third' => 0]
			, $team2->getGroupResults('g1')
		);
		$this->assertSame([
				'group' => $group,
				'points' => 3,
				'score' => 3000,
				'wins' => 1,
				'draws' => 0,
				'losses' => 0,
				'second' => 0,
				'third' => 0]
			, $team3->getGroupResults('g1')
		);

		$game->resetResults();

		$this->assertSame([], $game->getResults()
		);
		$this->assertEquals(0, $team1->getSumPoints());
		$this->assertEquals(0, $team1->getSumScore());
		$this->assertEquals(0, $team2->getSumPoints());
		$this->assertEquals(0, $team2->getSumScore());
		$this->assertEquals(0, $team3->getSumPoints());
		$this->assertEquals(0, $team3->getSumScore());
		$this->assertSame([
				'group' => $group,
				'points' => 0,
				'score' => 0,
				'wins' => 0,
				'draws' => 0,
				'losses' => 0,
				'second' => 0,
				'third' => 0],
			$team1->getGroupResults('g1')
		);
		$this->assertSame([
				'group' => $group,
				'points' => 0,
				'score' => 0,
				'wins' => 0,
				'draws' => 0,
				'losses' => 0,
				'second' => 0,
				'third' => 0]
			, $team2->getGroupResults('g1')
		);
		$this->assertSame([
				'group' => $group,
				'points' => 0,
				'score' => 0,
				'wins' => 0,
				'draws' => 0,
				'losses' => 0,
				'second' => 0,
				'third' => 0]
			, $team3->getGroupResults('g1')
		);
	}

	/** @test */
	public function check_setting_results_4_game() {
		$group = new \TournamentGenerator\Group('Group', 'g1');
		$group->setInGame(4);

		$team1 = new \TournamentGenerator\Team('Team1', 1);
		$team2 = new \TournamentGenerator\Team('Team2', 2);
		$team3 = new \TournamentGenerator\Team('Team3', 3);
		$team4 = new \TournamentGenerator\Team('Team4', 4);

		$group->addTeam($team1, $team2, $team3, $team4);

		$game = new \TournamentGenerator\Game([$team1, $team2, $team3, $team4], $group);

		$game->setResults([1 => 1000, 2 => 2000, 3 => 3000, 4 => 4000]);

		$this->assertSame([
				1 => ['score' => 1000, 'points' => 0, 'type' => 'loss'],
				2 => ['score' => 2000, 'points' => 1, 'type' => 'third'],
				3 => ['score' => 3000, 'points' => 2, 'type' => 'second'],
				4 => ['score' => 4000, 'points' => 3, 'type' => 'win']
			], $game->getResults()
		);
		$this->assertEquals(0, $team1->getSumPoints());
		$this->assertEquals(1000, $team1->getSumScore());
		$this->assertEquals(1, $team2->getSumPoints());
		$this->assertEquals(2000, $team2->getSumScore());
		$this->assertEquals(2, $team3->getSumPoints());
		$this->assertEquals(3000, $team3->getSumScore());
		$this->assertEquals(3, $team4->getSumPoints());
		$this->assertEquals(4000, $team4->getSumScore());
		$this->assertSame([
				'group' => $group,
				'points' => 0,
				'score' => 1000,
				'wins' => 0,
				'draws' => 0,
				'losses' => 1,
				'second' => 0,
				'third' => 0],
			$team1->getGroupResults('g1')
		);
		$this->assertSame([
				'group' => $group,
				'points' => 1,
				'score' => 2000,
				'wins' => 0,
				'draws' => 0,
				'losses' => 0,
				'second' => 0,
				'third' => 1]
			, $team2->getGroupResults('g1')
		);
		$this->assertSame([
				'group' => $group,
				'points' => 2,
				'score' => 3000,
				'wins' => 0,
				'draws' => 0,
				'losses' => 0,
				'second' => 1,
				'third' => 0]
			, $team3->getGroupResults('g1')
		);
		$this->assertSame([
				'group' => $group,
				'points' => 3,
				'score' => 4000,
				'wins' => 1,
				'draws' => 0,
				'losses' => 0,
				'second' => 0,
				'third' => 0]
			, $team4->getGroupResults('g1')
		);

		$game->resetResults();

		$this->assertSame([], $game->getResults());
		$this->assertEquals(0, $team1->getSumPoints());
		$this->assertEquals(0, $team1->getSumScore());
		$this->assertEquals(0, $team2->getSumPoints());
		$this->assertEquals(0, $team2->getSumScore());
		$this->assertEquals(0, $team3->getSumPoints());
		$this->assertEquals(0, $team3->getSumScore());
		$this->assertEquals(0, $team4->getSumPoints());
		$this->assertEquals(0, $team4->getSumScore());
		$this->assertSame([
				'group' => $group,
				'points' => 0,
				'score' => 0,
				'wins' => 0,
				'draws' => 0,
				'losses' => 0,
				'second' => 0,
				'third' => 0],
			$team1->getGroupResults('g1')
		);
		$this->assertSame([
				'group' => $group,
				'points' => 0,
				'score' => 0,
				'wins' => 0,
				'draws' => 0,
				'losses' => 0,
				'second' => 0,
				'third' => 0]
			, $team2->getGroupResults('g1')
		);
		$this->assertSame([
				'group' => $group,
				'points' => 0,
				'score' => 0,
				'wins' => 0,
				'draws' => 0,
				'losses' => 0,
				'second' => 0,
				'third' => 0]
			, $team3->getGroupResults('g1')
		);
		$this->assertSame([
				'group' => $group,
				'points' => 0,
				'score' => 0,
				'wins' => 0,
				'draws' => 0,
				'losses' => 0,
				'second' => 0,
				'third' => 0]
			, $team4->getGroupResults('g1')
		);
	}

	/** @test */
	public function check_getting_loss_and_win_teams_2_game() {
		$group = new \TournamentGenerator\Group('Group', 'g1');
		$group->setInGame(2);

		$team1 = new \TournamentGenerator\Team('Team1', 1);
		$team2 = new \TournamentGenerator\Team('Team2', 2);

		$group->addTeam($team1, $team2);

		$game = new \TournamentGenerator\Game([$team1, $team2], $group);

		$game->setResults([1 => 1000, 2 => 2000]);

		$this->assertEquals(1, $game->getLoss());
		$this->assertEquals(2, $game->getWin());

	}

	/** @test */
	public function check_getting_draw_teams_2_game() {
		$group = new \TournamentGenerator\Group('Group', 'g1');
		$group->setInGame(2);

		$team1 = new \TournamentGenerator\Team('Team1', 1);
		$team2 = new \TournamentGenerator\Team('Team2', 2);

		$group->addTeam($team1, $team2);

		$game = new \TournamentGenerator\Game([$team1, $team2], $group);

		$game->setResults([1 => 1000, 2 => 1000]);

		$this->assertSame([1,2], $game->getDraw());

	}

	/** @test */
	public function check_getting_win_second_third_and_loss_teams_4_game() {
		$group = new \TournamentGenerator\Group('Group', 'g1');
		$group->setInGame(4);

		$team1 = new \TournamentGenerator\Team('Team1', 1);
		$team2 = new \TournamentGenerator\Team('Team2', 2);
		$team3 = new \TournamentGenerator\Team('Team3', 3);
		$team4 = new \TournamentGenerator\Team('Team4', 4);

		$group->addTeam($team1, $team2, $team3, $team4);

		$game = new \TournamentGenerator\Game([$team1, $team2, $team3, $team4], $group);

		$game->setResults([1 => 1000, 2 => 2000, 3 => 3000, 4 => 4000]);

		$this->assertEquals(1, $game->getLoss());
		$this->assertEquals(2, $game->getThird());
		$this->assertEquals(3, $game->getSecond());
		$this->assertEquals(4, $game->getWin());

	}


}
