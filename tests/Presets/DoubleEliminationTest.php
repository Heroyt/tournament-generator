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

	public function teamCounts() : array {
		return [
			[3, 5],
			[4, 7],
			[5, 9],
			[6, 11],
			[7, 13],
			[8, 15],
			[9, 17],
			[10, 19],
			[11, 21],
			[12, 23],
			[13, 25],
			[14, 27],
			[15, 29],
			[16, 31],
		];
	}

	/**
	 * @test
	 * @dataProvider teamCounts
	 */
	public function double_elimination(int $teams, int $games) : void {
		$tournament = new DoubleElimination('Tournament name');

		for ($i = 1; $i <= $teams; $i++) {
			$tournament->team('Team '.$i);
		}

		$tournament->generate();

		$tournament->genGamesSimulateReal();

		$count = count($tournament->getGames());
		// The last game can be repeated - therefore <games-1, games> count must be checked
		self::assertTrue($count === $games || $count === $games - 1, 'Expected: '.$games.', Actual: '.$count.PHP_EOL.$tournament->printBracket());
	}

	// TODO: Maybe test specific double elimination bracket, if correct games are generated

}
