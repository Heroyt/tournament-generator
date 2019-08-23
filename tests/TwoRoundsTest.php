<?php
use PHPUnit\Framework\TestCase;

/**
 *
 */
class TwoRoundsEliminationTest extends TestCase
{

	/** @test */
	public function test_2R2G_elimination_even_teams() {
		$tournament = new \TournamentGenerator\Preset\R2G('Tournament name');

		for ($i=1; $i <= 8; $i++) {
			$tournament->team('Team '.$i);
		}

		$tournament->generate();

		$tournament->genGamesSimulate();

		$this->assertCount(12, $tournament->getGames());
	}

	/** @test */
	public function test_2R2G_elimination_nondivisible_by_4() {
		$tournament = new \TournamentGenerator\Preset\R2G('Tournament name');

		for ($i=1; $i <= 6; $i++) {
			$tournament->team('Team '.$i);
		}

		$tournament->generate();

		$tournament->genGamesSimulate();

		$this->assertCount(8, $tournament->getGames());
	}

	/** @test */
	public function test_2R2G_elimination_odd_teams() {
		$tournament = new \TournamentGenerator\Preset\R2G('Tournament name');

		for ($i=1; $i <= 7; $i++) {
			$tournament->team('Team '.$i);
		}

		$tournament->generate();

		$this->expectException(Exception::class);
		$tournament->genGamesSimulate();
	}
}
