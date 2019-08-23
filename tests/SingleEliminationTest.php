<?php
use PHPUnit\Framework\TestCase;

/**
 *
 */
class SingleEliminationTest extends TestCase
{

	/** @test */
	public function test_single_elimination() {
		$tournament = new \TournamentGenerator\Preset\SingleElimination('Tournament name');

		for ($i=1; $i <= 8; $i++) {
			$tournament->team('Team '.$i);
		}

		$tournament->generate();

		$tournament->genGamesSimulate();

		$this->assertCount(7, $tournament->getGames());
	}

	/** @test */
	public function test_single_elimination_non_power_of_2() {
		$tournament = new \TournamentGenerator\Preset\SingleElimination('Tournament name');

		for ($i=1; $i <= 7; $i++) {
			$tournament->team('Team '.$i);
		}

		$tournament->generate();

		$tournament->genGamesSimulate();

		$this->assertCount(6, $tournament->getGames());
	}
}
