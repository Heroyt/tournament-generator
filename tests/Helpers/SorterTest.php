<?php

declare(strict_types=1);

namespace Helpers;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\Containers\BaseContainer;
use TournamentGenerator\Game;
use TournamentGenerator\Group;
use TournamentGenerator\Helpers\Generator;
use TournamentGenerator\Helpers\Sorter\TeamSorter;

/**
 * @internal
 *
 * @coversNothing
 */
class SorterTest extends TestCase
{
    public function testTeamSorterInvalid() : void {
        $this->expectException(InvalidArgumentException::class);
        new TeamSorter(new BaseContainer(0), 'invalid ordering');
    }

    public function testGameSorterLessGamesSorting() : void {
        $group = new Group('Group 1');
        $group->team('Team 1', 1);
        $group->team('Team 2', 2);
        $group->team('Team 3', 3);

        $group->genGames();
        $generator = new Generator($group);
        $games = $generator->orderGames();
        $games2 = $group->orderGames();
        self::assertCount(3, $games);
        self::assertCount(3, $games2);
        self::assertEquals($games, $games2);
        $expected = [
            [1, 3],
            [2, 3],
            [1, 2],
        ];
        $ids = array_map(static function (Game $game) {
            $ids = $game->getTeamsIds();
            sort($ids);

            return $ids;
        }, $games);
        foreach ($ids as $id) {
            self::assertContains($id, $expected);
        }
    }
}
