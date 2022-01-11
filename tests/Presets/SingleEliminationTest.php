<?php

namespace Presets;

use PHPUnit\Framework\TestCase;
use TournamentGenerator\Preset\SingleElimination;

/**
 * Test the Single elimination generator
 */
class SingleEliminationTest extends TestCase
{

	public function teamCounts() : array {
		return [
			[3, 2],
			[4, 3],
			[5, 4],
			[6, 5],
			[7, 6],
			[8, 7],
			[9, 8],
			[16, 15],
			[25, 24],
			[32, 31],
		];
	}

	/**
	 * @test
	 * @dataProvider teamCounts
	 */
	public function single_elimination(int $teams, int $games) : void {
		$tournament = new SingleElimination('Tournament name');

		for ($i = 1; $i <= $teams; $i++) {
			$tournament->team('Team '.$i);
		}

		$tournament->generate();

		$tournament->genGamesSimulate();

		self::assertCount($games, $tournament->getGames());
	}

}
