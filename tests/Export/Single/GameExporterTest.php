<?php


namespace Export\Single;


use PHPUnit\Framework\TestCase;
use TournamentGenerator\Export\Single\GameExporter;
use TournamentGenerator\Game;
use TournamentGenerator\Group;

class GameExporterTest extends TestCase
{

	public function getGames() : array {
		$group = new Group('Group', 1);
		$group3 = (new Group('Group', 1))->setInGame(3)->setGameAutoincrementId(5);
		$group4 = (new Group('Group', 1))->setInGame(4)->setGameAutoincrementId(10);
		$team1 = $group->team('Team 1', 1);
		$team2 = $group->team('Team 2', 2);
		$team3 = $group->team('Team 3', 3);
		$team4 = $group->team('Team 4', 4);
		$group3->addTeam($team1, $team2, $team3);
		$group4->addTeam($team1, $team2, $team3, $team4);

		$game1 = $group->game([$team1, $team2]);
		$game2 = $group->game([$team1, $team2])->setResults([1 => 500, 2 => 200]);
		$game3 = $group3->game([$team1, $team2, $team3])->setResults([1 => 111, 2 => 222, 3 => 333]);
		$game4 = $group4->game([$team1, $team2, $team3, $team4])->setResults([1 => 111, 2 => 222, 3 => 333, 4 => 444]);
		return [
			[$game1, ['id' => 1, 'teams' => [1, 2], 'scores' => []]],
			[
				$game2,
				[
					'id'     => 2,
					'teams'  => [1, 2],
					'scores' => [
						1 => ['score' => 500, 'points' => $group->getWinPoints(), 'type' => 'win'],
						2 => ['score' => 200, 'points' => $group->getLostPoints(), 'type' => 'loss'],
					]
				]
			],
			[
				$game3,
				[
					'id'     => 5,
					'teams'  => [1, 2, 3],
					'scores' => [
						1 => ['score' => 111, 'points' => $group->getLostPoints(), 'type' => 'loss'],
						2 => ['score' => 222, 'points' => $group->getSecondPoints(), 'type' => 'second'],
						3 => ['score' => 333, 'points' => $group->getWinPoints(), 'type' => 'win'],
					]
				]
			],
			[
				$game4,
				[
					'id'     => 10,
					'teams'  => [1, 2, 3, 4],
					'scores' => [
						1 => ['score' => 111, 'points' => $group->getLostPoints(), 'type' => 'loss'],
						2 => ['score' => 222, 'points' => $group->getThirdPoints(), 'type' => 'third'],
						3 => ['score' => 333, 'points' => $group->getSecondPoints(), 'type' => 'second'],
						4 => ['score' => 444, 'points' => $group->getWinPoints(), 'type' => 'win'],
					]
				]
			],
		];
	}

	/**
	 * @dataProvider getGames
	 */
	public function testBasicExport(Game $game, array $expectedOutput) : void {
		$export1 = GameExporter::export($game);
		$export2 = GameExporter::start($game)->get();
		$exportJson = GameExporter::start($game)->getJson();
		$exportJsonSerialized = json_encode(GameExporter::start($game), JSON_UNESCAPED_SLASHES);
		$exportBasic = GameExporter::exportBasic($game);

		self::assertEquals($expectedOutput, $export1);
		self::assertEquals($expectedOutput, $export2);
		self::assertEquals(json_encode($expectedOutput, JSON_UNESCAPED_SLASHES), $exportJson);
		self::assertEquals(json_encode($expectedOutput, JSON_UNESCAPED_SLASHES), $exportJsonSerialized);

		$expectedOutput['object'] = $game;
		self::assertEquals($expectedOutput, $exportBasic);
	}

}