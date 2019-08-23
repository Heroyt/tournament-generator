<?php
use PHPUnit\Framework\TestCase;

/**
 *
 */
class DoubleEliminationTest extends TestCase
{

	/** @test */
	public function test_double_elimination() {
		$tournament = new \TournamentGenerator\Preset\DoubleElimination('Tournament name');

		for ($i=1; $i <= 8; $i++) {
			$tournament->team('Team '.$i);
		}

		$tournament->generate();

		$tournament->genGamesSimulate();

		$this->assertCount(14, $tournament->getGames());
	}

	/** @test */
	public function test_double_elimination_non_power_of_2() {
		$tournament = new \TournamentGenerator\Preset\DoubleElimination('Tournament name');

		for ($i=1; $i <= 7; $i++) {
			$tournament->team('Team '.$i);
		}

		$tournament->generate();

		$tournament->genGamesSimulate();

		$this->assertCount(12, $tournament->getGames());
	}
}
