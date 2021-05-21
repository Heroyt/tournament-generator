<?php

use PHPUnit\Framework\TestCase;
use TournamentGenerator\Constants;
use TournamentGenerator\Group;

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
}
