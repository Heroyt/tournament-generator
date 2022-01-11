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
	public function single_elimination2() : void {
		$tournament = new SingleElimination('Tournament name');

		$tournament
			->setPlay(7)     // SET GAME TIME TO 7 MINUTES
			->setGameWait(2) // SET TIME BETWEEN GAMES TO 2 MINUTES
			->setRoundWait(0); // SET TIME BETWEEN ROUNDS TO 0 MINUTES

		for ($i = 1; $i <= 6; $i++) {
			$tournament->team('Team '.$i);
		}

		$tournament->generate();

		$tournament->genGamesSimulate();
		$tournament->genGamesSimulateReal();

		$tournament->getTeams(true);

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
