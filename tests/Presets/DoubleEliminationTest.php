<?php

namespace Presets;

use Exception;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\Preset\DoubleElimination;

/**
 * Test the double elimination generator
 */
class DoubleEliminationTest extends TestCase
{

	/** @test */
	public function double_elimination_less_teams() : void {
		$tournament = new DoubleElimination('Tournament name');

		for ($i = 1; $i < 3; $i++) {
			$tournament->team('Team '.$i);
		}

		$this->expectException(Exception::class);
		$tournament->generate();
	}

	/** @test */
	public function double_elimination() : void {
		$tournament = new DoubleElimination('Tournament name');

		for ($i = 1; $i <= 8; $i++) {
			$tournament->team('Team '.$i);
		}

		$tournament->generate();

		$tournament->genGamesSimulate();

		self::assertCount(14, $tournament->getGames(), $tournament->printBracket());
	}

	/** @test */
	public function double_elimination_non_power_of_2() : void {
		$tournament = new DoubleElimination('Tournament name');

		for ($i = 1; $i <= 7; $i++) {
			$tournament->team('Team '.$i);
		}

		$tournament->generate();

		$tournament->genGamesSimulate();

		self::assertCount(12, $tournament->getGames());
	}
}
