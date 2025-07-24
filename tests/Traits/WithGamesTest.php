<?php

declare(strict_types=1);

namespace Traits;

use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\Category;
use TournamentGenerator\Group;
use TournamentGenerator\Interfaces\WithGames;
use TournamentGenerator\Interfaces\WithGroups;
use TournamentGenerator\Interfaces\WithRounds;
use TournamentGenerator\Round;
use TournamentGenerator\Team;
use TournamentGenerator\Tournament;

/**
 * Tests for WithGames trait.
 *
 * @internal
 *
 * @coversNothing
 */
class WithGamesTest extends TestCase
{
    #[DataProvider('getClasses')]
    public function testGettingGames(WithGames $withGames) : void {
        $games = $this->setupGames($withGames);

        $gotGames = $withGames->getGames();
        self::assertCount(count($games), $gotGames);
        self::assertEquals($games, $gotGames);

        $gotGames = $withGames->getGameContainer()->get();
        self::assertCount(count($games), $gotGames);
        self::assertEquals($games, $gotGames);
    }

    /**
     * @throws Exception
     */
    protected function setupGames(WithGames $withGames) : array {
        $games = [];
        $teams = [];
        // Create 10 teams
        for ($i = 0; $i < 10; ++$i) {
            $teams[$i] = $withGames->team('Team ' . $i, $i);
        }

        // Create random rounds, groups and games
        if ($withGames instanceof WithRounds) {
            $rounds = random_int(2, 5);
            for ($i = 0; $i < $rounds; ++$i) {
                $round = $withGames->round('Round ' . $i, $i);
                $groups = random_int(2, 4);
                for ($ii = 0; $ii < $groups; ++$ii) {
                    $id = 4 * $i + $ii;
                    $group = $round->group('Group ' . $id, $id);
                    $groupTeams = array_rand($teams, 4);
                    foreach ($groupTeams as $groupTeam) {
                        $group->addTeam($teams[$groupTeam]);
                    }
                    $gamesNum = random_int(3, 10);
                    for ($iii = 0; $iii < $gamesNum; ++$iii) {
                        $teamsGame = array_map(static fn (int $key) => $teams[$groupTeams[$key]], array_rand($groupTeams, 2));
                        $games[] = $group->game($teamsGame);
                    }
                }
            }
        } elseif ($withGames instanceof WithGroups) {
            $groups = random_int(2, 4);
            for ($ii = 0; $ii < $groups; ++$ii) {
                $id = 4 * $i + $ii;
                $group = $withGames->group('Group ' . $id, $id);
                $groupTeams = array_rand($teams, 4);
                foreach ($groupTeams as $groupTeam) {
                    $group->addTeam($teams[$groupTeam]);
                }
                $gamesNum = random_int(3, 10);
                for ($iii = 0; $iii < $gamesNum; ++$iii) {
                    $teamsGame = array_map(static fn (int $key) => $teams[$groupTeams[$key]], array_rand($groupTeams, 2));
                    $games[] = $group->game($teamsGame);
                }
            }
        } else {
            $groupTeams = array_rand($teams, 4);
            foreach ($groupTeams as $groupTeam) {
                $withGames->addTeam($teams[$groupTeam]);
            }
            $gamesNum = random_int(3, 10);
            for ($iii = 0; $iii < $gamesNum; ++$iii) {
                $teamsGame = array_map(static fn (int $key) => $teams[$groupTeams[$key]], array_rand($groupTeams, 2));
                $games[] = $withGames->game($teamsGame);
            }
        }

        return $games;
    }

    #[DataProvider('getClasses')]
    public function testAutoincrement(WithGames $withGames) : void {
        $games = $this->setupGames($withGames);

        $expectedId = 1;
        foreach ($games as $game) {
            self::assertEquals($expectedId, $game->getId());
            ++$expectedId;
        }
    }

    #[DataProvider('getClassesWithIncrements')]
    public function testSetAutoincrement(WithGames $withGames, int $startIncrement) : void {
        $withGames->setGameAutoincrementId($startIncrement);
        $games = $this->setupGames($withGames);

        $expectedId = $startIncrement;
        foreach ($games as $key => $game) {
            self::assertEquals($expectedId, $game->getId(), 'Expected ID did not match for game ' . $key . '/' . count($games) . PHP_EOL . 'Start: ' . $startIncrement . PHP_EOL . 'Class: ' . $withGames::class);
            ++$expectedId;
        }
    }

    public static function getClassesWithIncrements() : array {
        $data = [];
        $classes = [Tournament::class, Category::class, Round::class, Group::class];
        foreach ($classes as $class) {
            $increments = range(2, 20);
            foreach ($increments as $increment) {
                $data[] = [new $class('Class name'), $increment];
            }
        }

        return $data;
    }

    #[DataProvider('getClasses')]
    public function testSettingResult(WithGames $withGames) : void {
        $this->setupGames($withGames);

        /** @var array<int|string, array<int|string, Team>> $teamGroups */
        $teamGroups = [];
        $games = [];
        if ($withGames instanceof WithGroups) {
            $groups = $withGames->getGroups();
            foreach ($groups as $group) {
                $teamGroups[$group->getId()] = [];
                foreach ($group->getTeams() as $team) {
                    $teamGroups[$group->getId()][$team->getId()] = $team;
                }
                $games[$group->getId()] = $group->getGames();
            }
        } elseif ($withGames instanceof Group) {
            $teamGroups[$withGames->getId()] = [];
            foreach ($withGames->getTeams() as $team) {
                $teamGroups[$withGames->getId()][$team->getId()] = $team;
            }
            $games[$withGames->getId()] = $withGames->getGames();
        }

        // Test setting results for existing games
        foreach ($games as $groupGames) {
            foreach ($groupGames as $groupGame) {
                $ids = $groupGame->getTeamsIds();
                $results = [];
                foreach ($ids as $id) {
                    $results[$id] = random_int(0, 10000);
                }
                $game2 = $withGames->setResults($results);
                self::assertSame($groupGame, $game2);
                self::assertCount(count($results), $groupGame->getResults());
            }
        }

        // Test setting results for invalid games
        if ($withGames instanceof WithGroups) {
            $this->assertGreaterThanOrEqual(2, count($teamGroups), 'Not enough groups created for testing results setting.');
            for ($i = 0; $i < 10; ++$i) {
                $groups = array_rand($teamGroups, 2);
                $results = [];
                foreach ($groups as $group) {
                    $results[array_rand($teamGroups[$group])] = random_int(0, 10000);
                }
                $game = $withGames->setResults($results);
                self::assertNull($game);
            }
        }
    }

    /**
     * @return iterable<string, array{0:WithGames}>
     */
    public static function getClasses() : iterable {
        yield 'Tournament' => [new Tournament('Tournament')];

        yield 'Category' => [new Category('Category')];

        yield 'Round' => [new Round('Round')];

        yield 'Group' => [new Group('Group')];
    }
}
