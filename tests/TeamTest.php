<?php
use PHPUnit\Framework\TestCase;

/**
 *
 */
class TeamTest extends TestCase
{

	/** @test */
	public function check_name_setup_team() {
		$team = new \TournamentGenerator\Team('Team name 1');

		$this->assertEquals('Team name 1', $team->getName());
		$this->assertEquals('Team name 1', (string) $team);

		$team->setName('Team name 2');

		$this->assertEquals('Team name 2', $team->getName());
	}

	/** @test */
	public function check_id_setup_team() {
		$team = new \TournamentGenerator\Team('Team name 1', 123);

		$this->assertEquals(123, $team->getId());

		$team->setId('ID2');

		$this->assertEquals('ID2', $team->getId());

		$this->expectException(InvalidArgumentException::class);
		$team->setId(['This', 'is', 'not', 'a', 'valid' => 'id']);
	}

	/** @test */
	public function check_getting_games_with_team() {
		$group = new \TournamentGenerator\Group('Group', 'g1');

		$team1 = new \TournamentGenerator\Team('Team1', 1);
		$team2 = new \TournamentGenerator\Team('Team2', 2);
		$team3 = new \TournamentGenerator\Team('Team3', 3);
		$team4 = new \TournamentGenerator\Team('Team4', 4);

		$game = new \TournamentGenerator\Game([$team1, $team2], $group);
		$game2 = new \TournamentGenerator\Game([$team1, $team3], $group);
		$game3 = new \TournamentGenerator\Game([$team1, $team4], $group);

		$this->assertEquals(1, $team1->getGameWith($team3, $group));
		$this->assertEquals(1, $team1->getGameWith($team4, $group));
		$this->assertSame([2 => 1, 3 => 1, 4 => 1], $team1->getGameWith(null, $group));
		$this->assertSame(['g1' => [2 => 1, 3 => 1, 4 => 1]], $team1->getGameWith());

	}

	/** @test */
	public function check_adding_group_team() {
		$group = new \TournamentGenerator\Group('Group', 'g1');

		$team = new \TournamentGenerator\Team('Team1', 1);

		$this->assertSame([], $team->getGames());

		$team->addGroup($group);

		$this->assertSame(['g1' => []], $team->getGames());
		$this->assertSame([], $team->getGames($group));

	}

	/** @test */
	public function check_adding_and_removing_scores_team() {

		$team = new \TournamentGenerator\Team('Team1', 1);

		$this->assertEquals(0, $team->getSumScore());

		$team->addScore(500);
		$this->assertEquals(500, $team->getSumScore());

		$team->addScore(1000);
		$this->assertEquals(1500, $team->getSumScore());

		$team->removeScore(100);
		$this->assertEquals(1400, $team->getSumScore());

	}

	/** @test */
	public function check_adding_and_removing_points_team() {

		$team = new \TournamentGenerator\Team('Team1', 1);

		$this->assertEquals(0, $team->getSumPoints());

		$team->addPoints(500);
		$this->assertEquals(500, $team->getSumPoints());

		$team->addPoints(1000);
		$this->assertEquals(1500, $team->getSumPoints());

		$team->removePoints(100);
		$this->assertEquals(1400, $team->getSumPoints());

	}

}
