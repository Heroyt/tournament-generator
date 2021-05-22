<?php


namespace Helpers;


use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\Containers\BaseContainer;
use TournamentGenerator\Game;
use TournamentGenerator\Group;
use TournamentGenerator\Helpers\Generator;
use TournamentGenerator\Helpers\Sorter\TeamSorter;

class SorterTest extends TestCase
{

	public function testTeamSorterInvalid() : void {
		$this->expectException(InvalidArgumentException::class);
		new TeamSorter(new BaseContainer(0), 'invalid ordering');
	}

	public function testGameSorterLessGamesSorting() : void {
		$group1 = new Group('Group 1');
		$group1->team('Team 1', 1);
		$group1->team('Team 2', 2);
		$group1->team('Team 3', 3);

		$group1->genGames();
		$generator = new Generator($group1);
		$games = $generator->orderGames();
		$games2 = $group1->orderGames();
		self::assertCount(3, $games);
		self::assertCount(3, $games2);
		self::assertEquals($games, $games2);
		$expected = [
			[1, 3],
			[2, 3],
			[1, 2],
		];
		$ids = array_map(static function(Game $game) {
			$ids = $game->getTeamsIds();
			sort($ids);
			return $ids;
		}, $games);
		foreach ($ids as $teams) {
			self::assertContains($teams, $expected);
		}
	}
}