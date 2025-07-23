<?php

declare(strict_types=1);

namespace Presets;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\Preset\SingleElimination;

/**
 * Test the Single elimination generator.
 *
 * @internal
 *
 * @coversNothing
 */
class SingleEliminationTest extends TestCase
{
    #[DataProvider('teamCounts')]
    #[Test]
    public function singleElimination(int $teams, int $games) : void {
        $singleElimination = new SingleElimination('Tournament name');

        for ($i = 1; $i <= $teams; ++$i) {
            $singleElimination->team('Team ' . $i);
        }

        $singleElimination->generate();

        $singleElimination->genGamesSimulate();

        self::assertCount($games, $singleElimination->getGames());
    }

    public static function teamCounts() : array {
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
}
