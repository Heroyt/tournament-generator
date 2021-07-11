<?php


use PHPUnit\Framework\TestCase;
use TournamentGenerator\Helpers\Functions;
use TournamentGenerator\Preset\SingleElimination;
use TournamentGenerator\Team;
use TournamentGenerator\Tournament;

class SeedTest extends TestCase
{

	public function testSimpleSeed() : void {

		$tournament = new Tournament('Tournament');
		$round = $tournament->round('Round');
		$group1 = $round->group('Group1', 1);
		$group2 = $round->group('Group2', 2);

		for ($i = 0; $i < 10; $i++) {
			$tournament
				->team('Team '.($i + 1), $i)
				->seed($i);
		}

		$tournament->splitTeams();

		$team1Ids = $group1->getTeamContainer()->ids()->get();
		sort($team1Ids);
		$team2Ids = $group2->getTeamContainer()->ids()->get();
		sort($team2Ids);
		self::assertEquals(
			[
				0,
				2,
				5,
				7,
				9,
			],
			$team1Ids
		);
		self::assertEquals(
			[
				1,
				3,
				4,
				6,
				8,
			],
			$team2Ids
		);

	}

	public function singleElimination() : array {
		$tournament1 = new SingleElimination('Tournament');
		$games1 = [
			0 => [],
			1 => [],
			2 => [],
			3 => [],
		];
		for ($i = 0; $i < 8; $i++) {
			$team = $tournament1->team('Team '.($i + 1), $i)->seed($i);
			switch ($i) {
				case 0:
				case 7:
					$key = 0;
					break;
				case 1:
				case 6:
					$key = 3;
					break;
				case 2:
				case 5:
					$key = 2;
					break;
				case 3:
				case 4:
					$key = 1;
					break;
			}
			$games1[$key][] = $team;
		}

		return [
			[$tournament1, $games1]
		];
	}

	/**
	 * @dataProvider singleElimination
	 *
	 * @param SingleElimination $tournament
	 * @param array             $games
	 *
	 * @throws Exception
	 */
	public function testSingleEliminationSeed(SingleElimination $tournament, array $games) : void {
		$tournament->generate()->getRounds()[0]->genGames(); // Generate first round

		/** @var Team[][] $generated */
		$generated = $tournament->getGameContainer()->only('getTeams')->get();
		/*foreach ($generated as $game) {
			$names = array_map(static function(Team $a) {
				return $a->getName().' ('.$a->getSeed().')';
			}, $game);
			echo implode(' - ', $names).PHP_EOL;
		}*/

		self::assertTrue(true);
	}

}