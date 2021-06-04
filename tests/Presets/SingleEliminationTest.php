<?php

namespace Presets;

use PHPUnit\Framework\TestCase;
use TournamentGenerator\Preset\SingleElimination;

/**
 * Test the Single elimination generator
 */
class SingleEliminationTest extends TestCase
{

	/** @test */
	public function single_elimination() : void {
		$tournament = new SingleElimination('Tournament name');

		for ($i = 1; $i <= 8; $i++) {
			$tournament->team('Team '.$i);
		}

		$tournament->generate();

		$tournament->genGamesSimulate();

		self::assertCount(7, $tournament->getGames());
	}

	/** @test */
	public function single_elimination_non_power_of_2() : void {
		$tournament = new SingleElimination('Tournament name');

		for ($i = 1; $i <= 7; $i++) {
			$tournament->team('Team '.$i);
		}

		$tournament->generate();

		$tournament->genGamesSimulate();

		self::assertCount(6, $tournament->getGames());
	}
}
