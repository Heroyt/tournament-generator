<?php

namespace Helpers;

use PHPUnit\Framework\TestCase;
use TournamentGenerator\Constants;
use TournamentGenerator\Group;
use TournamentGenerator\Tournament;

/**
 *
 */
class GeneratorTest extends TestCase
{

	/** @test */
	public function check_group_generator_r_r_2() : void {
		$group = new Group('Group name');

		for ($i = 1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$group->setInGame(2);

		$games = $group->genGames();

		self::assertCount(6, $games);
	}

	/** @test */
	public function check_group_generator_r_r_3() : void {
		$group = new Group('Group name');

		for ($i = 1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$group->setInGame(3)->setType(Constants::ROUND_ROBIN);

		$games = $group->genGames();

		self::assertCount(4, $games);
	}

	/** @test */
	public function check_group_generator_r_r_4() : void {
		$group = new Group('Group name');

		for ($i = 1; $i <= 5; $i++) {
			$group->team('Team '.$i);
		}

		$group->setInGame(4)->setType(Constants::ROUND_ROBIN);

		$games = $group->genGames();

		self::assertCount(5, $games);
	}

	/** @test */
	public function check_group_generator_r_r_3_with_games_ordering() : void {
		$group = new Group('Group name');

		for ($i = 1; $i <= 10; $i++) {
			$group->team('Team '.$i);
		}

		$group->setInGame(3)->setType(Constants::ROUND_ROBIN);

		$games = $group->genGames();

		self::assertCount(120, $games);

		$games = $group->orderGames();

		self::assertCount(120, $games);
	}

	/** @test */
	public function check_group_generator_r_r_4_with_games_ordering() : void {
		$group = new Group('Group name');

		for ($i = 1; $i <= 9; $i++) {
			$group->team('Team '.$i);
		}

		$group->setInGame(4)->setType(Constants::ROUND_ROBIN);

		$games = $group->genGames();

		self::assertCount(126, $games);

		$games = $group->orderGames();

		self::assertCount(126, $games);
	}

	/** @test */
	public function check_group_generator_r_r_4_with_games_ordering2() : void {
		$group = new Group('Group name');

		for ($i = 1; $i <= 10; $i++) {
			$group->team('Team '.$i);
		}

		$group->setInGame(4)->setType(Constants::ROUND_ROBIN);

		$games = $group->genGames();

		self::assertCount(210, $games);

		$games = $group->orderGames();

		self::assertCount(210, $games);
	}

	/** @test */
	public function check_group_generator_two_two() : void {
		$group = new Group('Group name');

		for ($i = 1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$group->setType(Constants::ROUND_TWO);

		$games = $group->genGames();

		self::assertCount(2, $games);
	}

	/** @test */
	public function check_group_generator_cond_split() : void {
		$group = new Group('Group name');

		for ($i = 1; $i <= 8; $i++) {
			$group->team('Team '.$i);
		}

		$group->setInGame(2)->setType(Constants::ROUND_SPLIT)->setMaxSize(4);

		$games = $group->genGames();

		self::assertCount(12, $games);
	}

	/** @test */
	public function check_group_generator_cond_split_without_splitting() : void {
		$group = new Group('Group name');

		for ($i = 1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$group->setInGame(2)->setType(Constants::ROUND_SPLIT)->setMaxSize(4);

		$games = $group->genGames();

		self::assertCount(6, $games);
	}

	public function getGroupGenerationIterationsParams(): array {
		return [
			[
				1,
				Constants::ROUND_ROBIN,
				5,
				10,
			],
			[
				2,
				Constants::ROUND_ROBIN,
				5,
				20,
			],
			[
				1,
				Constants::ROUND_TWO,
				4,
				2,
			],
			[
				2,
				Constants::ROUND_TWO,
				4,
				4,
			],
			[
				1,
				Constants::ROUND_ROBIN,
				8,
				28,
			],
			[
				2,
				Constants::ROUND_ROBIN,
				8,
				56,
			],
			[
				3,
				Constants::ROUND_ROBIN,
				8,
				84,
			],
			[
				1,
				Constants::ROUND_TWO,
				8,
				4,
			],
			[
				2,
				Constants::ROUND_TWO,
				8,
				8,
			],
			[
				3,
				Constants::ROUND_TWO,
				8,
				12,
			],
			[
				1,
				Constants::ROUND_SPLIT,
				8,
				12,
			],
			[
				2,
				Constants::ROUND_SPLIT,
				8,
				24,
			],
			[
				3,
				Constants::ROUND_SPLIT,
				8,
				36,
			],
		];
	}

	/**
	 * @dataProvider getGroupGenerationIterationsParams
	 *
	 * @param int    $iterations
	 * @param string $type
	 * @param int    $teams
	 * @param int    $expectedGames
	 *
	 * @return void
	 */
	public function testGroupGenerationWithIterations(int $iterations, string $type, int $teams, int $expectedGames): void {
		$group = new Group('Group');
		$group->setInGame(2)->setType($type)->setIterationCount($iterations);

		for ($i = 1; $i <= $teams; $i++) {
			$group->team('Team ' . $i);
		}

		$games = $group->genGames();
		self::assertCount($expectedGames, $games);
	}

	public function testIterationGeneration(): void {
		$tournament = new Tournament('Tournament');
		$round = $tournament->round('Round');
		$group = $round->group('Group');

		for ($i = 1; $i <= 5; $i++) {
			$group->team('Team ' . $i);
		}

		$tournament->setIterationCount(3);

		$games = $tournament->genGamesSimulate();

		self::assertCount(30, $games);

		$iteration1Games = array_splice($games, 10, 10);
		$iteration2Games = array_splice($games, 10, 10);

		self::assertCount(10, $games);
		self::assertCount(10, $iteration1Games);
		self::assertCount(10, $iteration2Games);

		foreach ($games as $key => $game) {
			$iteration1Game = $iteration1Games[$key];
			$iteration2Game = $iteration2Games[$key];

			$teams = $game->getTeamsIds();
			$iteration1Teams = $iteration1Game->getTeamsIds();
			$iteration2Teams = $iteration2Game->getTeamsIds();

			self::assertEquals($teams, $iteration2Teams);
			self::assertEquals(array_reverse($teams), $iteration1Teams);
		}
	}
}
