<?php

declare(strict_types=1);

namespace Presets;

use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\Preset\DoubleElimination;

/**
 * Test the double elimination generator.
 *
 * @internal
 *
 * @coversNothing
 */
class DoubleEliminationTest extends TestCase
{
    #[Test]
    public function doubleEliminationLessTeams() : void {
        $doubleElimination = new DoubleElimination('Tournament name');

        for ($i = 1; $i < 3; ++$i) {
            $doubleElimination->team('Team ' . $i);
        }

        $this->expectException(Exception::class);
        $doubleElimination->generate();
    }

    #[DataProvider('teamCounts')]
    #[Test]
    public function doubleElimination(int $teams, int $games) : void {
        $doubleElimination = new DoubleElimination('Tournament name');

        for ($i = 1; $i <= $teams; ++$i) {
            $doubleElimination->team('Team ' . $i);
        }

        $doubleElimination->generate();

        $doubleElimination->genGamesSimulateReal();

        $count = count($doubleElimination->getGames());
        // The last game can be repeated - therefore <games-1, games> count must be checked
        self::assertTrue($count === $games || $count === $games - 1, 'Expected: ' . $games . ', Actual: ' . $count . PHP_EOL . $doubleElimination->printBracket());
    }

    public static function teamCounts() : array {
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

    // TODO: Maybe test specific double elimination bracket, if correct games are generated
}
