<?php

use PHPUnit\Framework\TestCase;
use TournamentGenerator\Game;
use TournamentGenerator\Group;
use TournamentGenerator\Team;

/**
 *
 */
class TeamTest extends TestCase
{

	public function testInvalidAddWin() : void {
		$team = new Team('Team');
		$this->expectException(Exception::class);
		$team->addWin('invalid ID');
	}

	public function testInvalidRemoveWin() : void {
		$team = new Team('Team');
		$this->expectException(Exception::class);
		$team->removeWin('invalid ID');
	}

	public function testInvalidAddLoss() : void {
		$team = new Team('Team');
		$this->expectException(Exception::class);
		$team->addLoss('invalid ID');
	}

	public function testInvalidRemoveLoss() : void {
		$team = new Team('Team');
		$this->expectException(Exception::class);
		$team->removeLoss('invalid ID');
	}

	public function testInvalidAddDraw() : void {
		$team = new Team('Team');
		$this->expectException(Exception::class);
		$team->addDraw('invalid ID');
	}

	public function testInvalidRemoveDraw() : void {
		$team = new Team('Team');
		$this->expectException(Exception::class);
		$team->removeDraw('invalid ID');
	}

	public function testInvalidAddSecond() : void {
		$team = new Team('Team');
		$this->expectException(Exception::class);
		$team->addSecond('invalid ID');
	}

	public function testInvalidRemoveSecond() : void {
		$team = new Team('Team');
		$this->expectException(Exception::class);
		$team->removeSecond('invalid ID');
	}

	public function testInvalidAddThird() : void {
		$team = new Team('Team');
		$this->expectException(Exception::class);
		$team->addThird('invalid ID');
	}

	public function testInvalidRemoveThird() : void {
		$team = new Team('Team');
		$this->expectException(Exception::class);
		$team->removeThird('invalid ID');
	}

	public function testSumScore() : void {
		$team = new Team('Team');
		$team->addScore(100);
		$team->addScore(200);
		self::assertSame(300, $team->sumScore());
	}

	public function testSumPoints() : void {
		$team = new Team('Team');
		$team->addPoints(100);
		$team->addPoints(200);
		self::assertSame(300, $team->sumPoints());
	}

	public function testInvalidGroupResult() : void {
		$team = new Team('Team');
		$this->expectException(Exception::class);
		$team->getGroupResults('invalid id');
	}

	/** @test */
	public function check_name_setup_team() : void {
		$team = new Team('Team name 1');

		self::assertEquals('Team name 1', $team->getName());
		self::assertEquals('Team name 1', (string) $team);

		$team->setName('Team name 2');

		self::assertEquals('Team name 2', $team->getName());
	}

	/** @test */
	public function check_id_setup_team() : void {
		$team = new Team('Team name 1', 123);

		self::assertEquals(123, $team->getId());

		$team->setId('ID2');

		self::assertEquals('ID2', $team->getId());

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Unsupported id type (array) - expected type of string or int');
		$team->setId(['This', 'is', 'not', 'a', 'valid' => 'id']);
	}

	/** @test */
	public function check_getting_games_with_team() : void {
		$group = new Group('Group', 'g1');

		$team1 = new Team('Team1', 1);
		$team2 = new Team('Team2', 2);
		$team3 = new Team('Team3', 3);
		$team4 = new Team('Team4', 4);

		new Game([$team1, $team2], $group);
		new Game([$team1, $team3], $group);
		new Game([$team1, $team4], $group);

		self::assertEquals(1, $team1->getGameWith($team3, $group));
		self::assertEquals(1, $team1->getGameWith($team4, $group));
		self::assertSame([2 => 1, 3 => 1, 4 => 1], $team1->getGameWith(null, $group));
		self::assertSame(['g1' => [2 => 1, 3 => 1, 4 => 1]], $team1->getGameWith());

	}

	/** @test */
	public function check_adding_group_team() : void {
		$group = new Group('Group', 'g1');

		$team = new Team('Team1', 1);

		self::assertSame([], $team->getGames());

		$team->addGroup($group);

		self::assertSame(['g1' => []], $team->getGames());
		self::assertSame([], $team->getGames($group));

	}

	/** @test */
	public function check_adding_and_removing_scores_team() : void {

		$team = new Team('Team1', 1);

		self::assertEquals(0, $team->getSumScore());

		$team->addScore(500);
		self::assertEquals(500, $team->getSumScore());

		$team->addScore(1000);
		self::assertEquals(1500, $team->getSumScore());

		$team->removeScore(100);
		self::assertEquals(1400, $team->getSumScore());

	}

	/** @test */
	public function check_adding_and_removing_points_team() : void {

		$team = new Team('Team1', 1);

		self::assertEquals(0, $team->getSumPoints());

		$team->addPoints(500);
		self::assertEquals(500, $team->getSumPoints());

		$team->addPoints(1000);
		self::assertEquals(1500, $team->getSumPoints());

		$team->removePoints(100);
		self::assertEquals(1400, $team->getSumPoints());

	}

}
