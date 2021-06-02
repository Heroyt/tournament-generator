<?php


namespace Export\Single;


use PHPUnit\Framework\TestCase;
use TournamentGenerator\Export\Single\TeamExporter;
use TournamentGenerator\Group;
use TournamentGenerator\Team;

class TeamExporterTest extends TestCase
{

	public function getTeams() : array {
		$group1 = new Group('Group', 1);
		$group2 = new Group('Group', 2);
		$team1 = new Team('Team with scores', 999);
		$team2 = new Team('Team with scores', 124);
		$group1->addTeam($team1, $team2);
		$group2->addTeam($team2);
		for ($i = 0; $i < 55; $i++) {
			$team1->addWin(1);
		}
		for ($i = 0; $i < 5; $i++) {
			$team2->addWin(1);
			$team2->addWin(2);
		}
		for ($i = 0; $i < 2; $i++) {
			$team2->addWin(2);
		}
		for ($i = 0; $i < 5; $i++) {
			$team1->addLoss(1);
			$team2->addDraw(1);
		}
		$group1->game([$team1, $team2])->setResults([999 => 5000, 124 => 1234]);
		return [
			[new Team('Team name', 1), ['id' => 1, 'name' => 'Team name'], []],
			[
				$team1,
				['id' => 999, 'name' => 'Team with scores'],
				[
					1 => [
						'points' => ($group1->getWinPoints() * 56) + ($group1->getLostPoints() * 5),
						'score'  => 5000,
						'wins'   => 56,
						'draws'  => 0,
						'losses' => 5,
						'second' => 0,
						'third'  => 0,
					],
				],
			],
			[
				$team2,
				['id' => 124, 'name' => 'Team with scores'],
				[
					1 => [
						'points' => ($group1->getWinPoints() * 5) + ($group1->getDrawPoints() * 5) + ($group1->getLostPoints() * 1),
						'score'  => 1234,
						'wins'   => 5,
						'draws'  => 5,
						'losses' => 1,
						'second' => 0,
						'third'  => 0,
					],
					2 => [
						'points' => ($group1->getWinPoints() * 7),
						'score'  => 0,
						'wins'   => 7,
						'draws'  => 0,
						'losses' => 0,
						'second' => 0,
						'third'  => 0,
					],
				],
			],
		];
	}

	/**
	 * @dataProvider getTeams
	 */
	public function testBasicExport(Team $team, array $expectedOutput) : void {
		$export1 = TeamExporter::export($team);
		$export2 = TeamExporter::start($team)->get();
		$exportJson = TeamExporter::start($team)->getJson();
		$exportJsonSerialized = json_encode(TeamExporter::start($team), JSON_UNESCAPED_SLASHES);
		$exportBasic = TeamExporter::exportBasic($team);

		self::assertEquals($expectedOutput, $export1);
		self::assertEquals($expectedOutput, $export2);
		self::assertEquals(json_encode($expectedOutput, JSON_UNESCAPED_SLASHES), $exportJson);
		self::assertEquals(json_encode($expectedOutput, JSON_UNESCAPED_SLASHES), $exportJsonSerialized);

		$expectedOutput['object'] = $team;
		self::assertEquals($expectedOutput, $exportBasic);
	}

	/**
	 * @dataProvider getTeams
	 */
	public function testWithScoresExport(Team $team, array $expectedOutput, array $scores) : void {
		$export = TeamExporter::start($team)->withScores()->get();
		$expectedOutput['scores'] = $scores;
		self::assertEquals($expectedOutput, $export);
	}

}