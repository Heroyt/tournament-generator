<?php

declare(strict_types=1);

namespace Presets;

use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\Preset\R2G;

/**
 * @internal
 *
 * @coversNothing
 */
class TwoRoundsTest extends TestCase
{
    #[Test]
    public function r2GEliminationEvenTeams() : void {
        $r2g = new R2G('Tournament name');

        for ($i = 1; $i <= 8; ++$i) {
            $r2g->team('Team ' . $i);
        }

        $r2g->generate();

        $r2g->genGamesSimulate();

        self::assertCount(12, $r2g->getGames());
    }

    #[Test]
    public function r2GEliminationNondivisibleBy4() : void {
        $r2g = new R2G('Tournament name');

        for ($i = 1; $i <= 6; ++$i) {
            $r2g->team('Team ' . $i);
        }

        $r2g->generate();

        $r2g->genGamesSimulate();

        self::assertGreaterThanOrEqual(7, count($r2g->getGames()));
        self::assertLessThanOrEqual(9, count($r2g->getGames()));
    }

    #[Test]
    public function r2GEliminationOddTeams() : void {
        $r2g = new R2G('Tournament name');

        for ($i = 1; $i <= 7; ++$i) {
            $r2g->team('Team ' . $i);
        }

        $r2g->generate();

        $this->expectException(Exception::class);
        $r2g->genGamesSimulate();
    }

    #[Test]
    public function r2GEliminationNoTeams() : void {
        $r2g = new R2G('Tournament name');

        $this->expectException(Exception::class);
        $r2g->generate();
    }
}
