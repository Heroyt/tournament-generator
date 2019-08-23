<?php
use PHPUnit\Framework\TestCase;

/**
 *
 */
class GeneratorTest extends TestCase
{

	/** @test */
	public function check_group_generator_r_r_2() {
		$group = new \TournamentGenerator\Group('Group name');

		for ($i=1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$group->setInGame(2)->setType(\TournamentGenerator\Constants::ROUND_ROBIN);

		$games = $group->genGames();

		$this->assertCount(6, $games);
	}

	/** @test */
	public function check_group_generator_r_r_3() {
		$group = new \TournamentGenerator\Group('Group name');

		for ($i=1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$group->setInGame(3)->setType(\TournamentGenerator\Constants::ROUND_ROBIN);

		$games = $group->genGames();

		$this->assertCount(4, $games);
	}

	/** @test */
	public function check_group_generator_r_r_4() {
		$group = new \TournamentGenerator\Group('Group name');

		for ($i=1; $i <= 5; $i++) {
			$group->team('Team '.$i);
		}

		$group->setInGame(4)->setType(\TournamentGenerator\Constants::ROUND_ROBIN);

		$games = $group->genGames();

		$this->assertCount(5, $games);
	}

	/** @test */
	public function check_group_generator_r_r_3_with_games_ordering() {
		$group = new \TournamentGenerator\Group('Group name');

		for ($i=1; $i <= 10; $i++) {
			$group->team('Team '.$i);
		}

		$group->setInGame(3)->setType(\TournamentGenerator\Constants::ROUND_ROBIN);

		$games = $group->genGames();

		$this->assertCount(120, $games);

		$games = $group->orderGames();

		$this->assertCount(120, $games);
	}

	/** @test */
	public function check_group_generator_r_r_4_with_games_ordering() {
		$group = new \TournamentGenerator\Group('Group name');

		for ($i=1; $i <= 9; $i++) {
			$group->team('Team '.$i);
		}

		$group->setInGame(4)->setType(\TournamentGenerator\Constants::ROUND_ROBIN);

		$games = $group->genGames();

		$this->assertCount(126, $games);

		$games = $group->orderGames();

		$this->assertCount(126, $games);
	}

	/** @test */
	public function check_group_generator_r_r_4_with_games_ordering2() {
		$group = new \TournamentGenerator\Group('Group name');

		for ($i=1; $i <= 10; $i++) {
			$group->team('Team '.$i);
		}

		$group->setInGame(4)->setType(\TournamentGenerator\Constants::ROUND_ROBIN);

		$games = $group->genGames();

		$this->assertCount(210, $games);

		$games = $group->orderGames();

		$this->assertCount(210, $games);
	}

	/** @test */
	public function check_group_generator_two_two() {
		$group = new \TournamentGenerator\Group('Group name');

		for ($i=1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$group->setType(\TournamentGenerator\Constants::ROUND_TWO);

		$games = $group->genGames();

		$this->assertCount(2, $games);
	}

	/** @test */
	public function check_group_generator_cond_split() {
		$group = new \TournamentGenerator\Group('Group name');

		for ($i=1; $i <= 8; $i++) {
			$group->team('Team '.$i);
		}

		$group->setInGame(2)->setType(\TournamentGenerator\Constants::ROUND_SPLIT)->setMaxSize(4);

		$games = $group->genGames();

		$this->assertCount(12, $games);
	}

	/** @test */
	public function check_group_generator_cond_split_without_splitting() {
		$group = new \TournamentGenerator\Group('Group name');

		for ($i=1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$group->setInGame(2)->setType(\TournamentGenerator\Constants::ROUND_SPLIT)->setMaxSize(4);

		$games = $group->genGames();

		$this->assertCount(6, $games);
	}
}
