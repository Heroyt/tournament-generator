<?php

namespace Presets;

use Exception;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\Preset\R2G;

/**
 *
 */
class TwoRoundsTest extends TestCase
{

	/** @test */
	public function R2G_elimination_even_teams() : void {
		$tournament = new R2G('Tournament name');

		for ($i = 1; $i <= 8; $i++) {
			$tournament->team('Team '.$i);
		}

		$tournament->generate();

		$tournament->genGamesSimulate();

		self::assertCount(12, $tournament->getGames());
	}

	/** @test */
	public function R2G_elimination_nondivisible_by_4() : void {
		$tournament = new R2G('Tournament name');

		for ($i = 1; $i <= 6; $i++) {
			$tournament->team('Team '.$i);
		}

		$tournament->generate();

		$tournament->genGamesSimulate();

		self::assertGreaterThanOrEqual(7, count($tournament->getGames()));
		self::assertLessThanOrEqual(9, count($tournament->getGames()));
	}

	/** @test */
	public function R2G_elimination_odd_teams() : void {
		$tournament = new R2G('Tournament name');

		for ($i = 1; $i <= 7; $i++) {
			$tournament->team('Team '.$i);
		}

		$tournament->generate();

		$this->expectException(Exception::class);
		$tournament->genGamesSimulate();
	}

	/** @test */
	public function R2G_elimination_no_teams() : void {
		$tournament = new R2G('Tournament name');

		$this->expectException(Exception::class);
		$tournament->generate();
	}
}
