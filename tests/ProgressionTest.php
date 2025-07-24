<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\BlankTeam;
use TournamentGenerator\Group;
use TournamentGenerator\Progression;
use TournamentGenerator\TeamFilter;
use TournamentGenerator\Tournament;

/**
 * @internal
 *
 * @coversNothing
 */
class ProgressionTest extends TestCase
{
    public function testProgressedSetting() : void {
        $group1 = new Group('Group1');
        $group2 = new Group('Group2');
        $progression = new Progression($group1, $group2);

        self::assertFalse($progression->isProgressed());
        $progression->setProgressed(true);
        self::assertTrue($progression->isProgressed());
    }

    #[Test]
    public function checkProgressing() : void {
        $tournament = new Tournament('Name of tournament 1');

        for ($i = 1; $i <= 8; ++$i) {
            $tournament->team('Team ' . $i);
        }
        // Create a round and a final round
        $round = $tournament->round("First's round's name");
        $final = $tournament->round("Final's round's name");

        // Create 2 groups for the first round
        $group_1 = $round->group('Round 1')->setInGame(2);
        $group_2 = $round->group('Round 2')->setInGame(2);

        // Create a final group
        $group = $final->group('Teams 1-4')->setInGame(2);
        $second_group = $final->group('Teams 5-8')->setInGame(2);

        $tournament->splitTeams($round);

        $group_1->progression($group, 0, 2);  // PROGRESS 2 BEST WINNING TEAMS
        $group_2->progression($group, 0, 2);  // PROGRESS 2 BEST WINNING TEAMS
        $group_1->progression($second_group, 2, 2); // PROGRESS 2 BEST WINNING TEAMS
        $group_2->progression($second_group, 2, 2); // PROGRESS 2 BEST WINNING TEAMS

        $round->genGames();
        $round->simulate();

        $round->progress();

        self::assertCount(4, $group->getTeams());
        self::assertCount(4, $second_group->getTeams());
    }

    #[Test]
    public function checkProgressingDuplicates() : void {
        $tournament = new Tournament('Name of tournament 1');

        for ($i = 1; $i <= 8; ++$i) {
            $tournament->team('Team ' . $i);
        }
        // Create a round and a final round
        $round = $tournament->round("First's round's name");
        $final = $tournament->round("Final's round's name");

        // Create 2 groups for the first round
        $group_1 = $round->group('Round 1')->setInGame(2);
        $group_2 = $round->group('Round 2')->setInGame(2);

        // Create a final group
        $group = $final->group('Teams 1-4')->setInGame(2);
        $second_group = $final->group('Teams 5-8')->setInGame(2);

        $tournament->splitTeams($round);

        $progression1 = $group_1->progression($group, 0, 2);  // PROGRESS 2 BEST WINNING TEAMS
        $progression2 = $group_2->progression($group, 0, 2);  // PROGRESS 2 BEST WINNING TEAMS
        $progression3 = $group_1->progression($second_group, 2, 2); // PROGRESS 2 BEST WINNING TEAMS
        $progression4 = $group_2->progression($second_group, 2, 2); // PROGRESS 2 BEST WINNING TEAMS

        $round->simulate();

        $round->progress();

        self::assertCount(4, $group->getTeams());
        self::assertCount(4, $second_group->getTeams());

        $round->progress();

        self::assertCount(4, $group->getTeams());
        self::assertCount(4, $second_group->getTeams());

        $progression1->reset();
        $progression2->reset();
        $progression3->reset();
        $progression4->reset();

        $round->progress();

        self::assertCount(4, $group->getTeams());
        self::assertCount(4, $second_group->getTeams());
    }

    #[Test]
    public function checkProgressingBlank() : void {
        $tournament = new Tournament('Name of tournament 1');

        for ($i = 1; $i <= 8; ++$i) {
            $tournament->team('Team ' . $i);
        }
        // Create a round and a final round
        $round = $tournament->round("First's round's name");
        $final = $tournament->round("Final's round's name");

        // Create 2 groups for the first round
        $group_1 = $round->group('Round 1')->setInGame(2);
        $group_2 = $round->group('Round 2')->setInGame(2);

        // Create a final group
        $group = $final->group('Teams 1-4')->setInGame(2);
        $second_group = $final->group('Teams 5-8')->setInGame(2);

        $tournament->splitTeams($round);

        $group_1->progression($group, 0, 2);  // PROGRESS 2 BEST WINNING TEAMS
        $group_2->progression($group, 0, 2);  // PROGRESS 2 BEST WINNING TEAMS
        $group_1->progression($second_group, 2, 2); // PROGRESS 2 BEST WINNING TEAMS
        $group_2->progression($second_group, 2, 2); // PROGRESS 2 BEST WINNING TEAMS

        $round->simulate();

        $round->progress(true);

        self::assertCount(4, $group->getTeams());
        self::assertCount(4, $second_group->getTeams());

        self::assertInstanceOf(BlankTeam::class, $group->getTeams()[0]);
        self::assertInstanceOf(BlankTeam::class, $second_group->getTeams()[0]);
    }

    #[Test]
    public function checkProgressingWithFilters() : void {
        $tournament = new Tournament('Name of tournament 1');

        for ($i = 1; $i <= 8; ++$i) {
            $tournament->team('Team ' . $i);
        }
        // Create a round and a final round
        $round = $tournament->round("First's round's name");
        $final = $tournament->round("Final's round's name");

        // Create 2 groups for the first round
        $group_1 = $round->group('Round 1')->setInGame(2);
        $group_2 = $round->group('Round 2')->setInGame(2);

        // Create a final group
        $group = $final->group('Teams 1-4')->setInGame(2);
        $second_group = $final->group('Teams 5-8')->setInGame(2);

        $tournament->splitTeams($round);

        $filter1 = new TeamFilter('points', '>', 3, [$group_1, $group_2]);
        $filter2 = new TeamFilter('losses', '<=', 3, [$group_1, $group_2]);

        $group_1->progression($group)->addfilter($filter1);
        $group_2->progression($group)->addFilter($filter1);
        $group_1->progression($second_group)->addFilter($filter2);
        $group_2->progression($second_group)->addFilter($filter2);

        $round->genGames();

        $round->simulate();

        $round->progress();

        $filtered1 = $round->getTeams(false, null, [$filter1]);
        $filtered2 = $round->getTeams(false, null, [$filter2]);

        self::assertCount(count($filtered1), $group->getTeams());
        self::assertCount(count($filtered2), $second_group->getTeams());
    }

    #[Test]
    public function checkProgressingWithFiltersSetting() : void {
        $tournament = new Tournament('Name of tournament 1');

        for ($i = 1; $i <= 8; ++$i) {
            $tournament->team('Team ' . $i);
        }
        // Create a round and a final round
        $round = $tournament->round("First's round's name");
        $final = $tournament->round("Final's round's name");

        // Create 2 groups for the first round
        $group_1 = $round->group('Round 1')->setInGame(2);
        $group_2 = $round->group('Round 2')->setInGame(2);

        // Create a final group
        $group = $final->group('Teams 1-4')->setInGame(2);
        $second_group = $final->group('Teams 5-8')->setInGame(2);

        $tournament->splitTeams($round);

        $filter1 = new TeamFilter('points', '>', 3, [$group_1, $group_2]);
        $filter2 = new TeamFilter('losses', '<=', 3, [$group_1, $group_2]);

        $group_1->progression($group)->setFilters([$filter1]);
        $group_2->progression($group)->setFilters([$filter1]);
        $group_1->progression($second_group)->setFilters([$filter2]);
        $group_2->progression($second_group)->setFilters([$filter2]);

        $round->genGames();

        $round->simulate();

        $round->progress();

        $filtered1 = $round->getTeams(false, null, [$filter1]);
        $filtered2 = $round->getTeams(false, null, [$filter2]);

        self::assertCount(count($filtered1), $group->getTeams());
        self::assertCount(count($filtered2), $second_group->getTeams());
    }

    #[Test]
    public function checkProgressingBlankWintFilters() : void {
        $tournament = new Tournament('Name of tournament 1');

        for ($i = 1; $i <= 8; ++$i) {
            $tournament->team('Team ' . $i);
        }
        // Create a round and a final round
        $round = $tournament->round("First's round's name");
        $final = $tournament->round("Final's round's name");

        // Create 2 groups for the first round
        $group_1 = $round->group('Round 1')->setInGame(2);
        $group_2 = $round->group('Round 2')->setInGame(2);

        // Create a final group
        $group = $final->group('Teams 1-4')->setInGame(2);
        $second_group = $final->group('Teams 5-8')->setInGame(2);

        $tournament->splitTeams($round);

        $filter1 = new TeamFilter('points', '>', 3, [$group_1, $group_2]);
        $filter2 = new TeamFilter('losses', '<=', 3, [$group_1, $group_2]);

        $group_1->progression($group)->addfilter($filter1);
        $group_2->progression($group)->addFilter($filter1);
        $group_1->progression($second_group)->addFilter($filter2);
        $group_2->progression($second_group)->addFilter($filter2);

        $round->genGames();

        $round->simulate();

        $round->progress(true);

        self::assertCount(count($round->getTeams(false, null, [$filter1])), $group->getTeams());
        self::assertCount(count($round->getTeams(false, null, [$filter2])), $second_group->getTeams());

        self::assertInstanceOf(BlankTeam::class, $group->getTeams()[0]);
        self::assertInstanceOf(BlankTeam::class, $second_group->getTeams()[0]);
    }

    public function testProgressionPoints() : void {
        $tournament = new Tournament('Name of tournament 1');

        for ($i = 1; $i <= 8; ++$i) {
            $tournament->team('Team ' . $i);
        }
        // Create a round and a final round
        $round = $tournament->round("First's round's name");
        $final = $tournament->round("Final's round's name");

        // Create 2 groups for the first round
        $group_1 = $round->group('Round 1')->setInGame(2);
        $group_2 = $round->group('Round 2')->setInGame(2);

        // Create a final group
        $group = $final->group('Teams 1-4')->setInGame(2);
        $second_group = $final->group('Teams 5-8')->setInGame(2);

        $tournament->splitTeams($round);

        $group_1->progression($group, 0, 2)->setPoints(69);  // PROGRESS 2 BEST WINNING TEAMS
        $group_2->progression($group, 0, 2)->setPoints(69);  // PROGRESS 2 BEST WINNING TEAMS
        $group_1->progression($second_group, 2, 2)->setPoints(0); // PROGRESS 2 BEST WINNING TEAMS
        $group_2->progression($second_group, 2, 2)->setPoints(0); // PROGRESS 2 BEST WINNING TEAMS

        $round->simulate();

        $round->progress();

        foreach ($group->getTeams() as $team) {
            $points = 0;
            foreach ($team->getGroupResults() as $results) {
                $points += $results['points'];
            }
            self::assertEquals(69 + $points, $team->getSumPoints());
        }

        foreach ($second_group->getTeams() as $team) {
            $points = 0;
            foreach ($team->getGroupResults() as $results) {
                $points += $results['points'];
            }
            self::assertEquals($points, $team->getSumPoints());
        }
    }

    public function testGetProgressedTeams() : void {
        $tournament = new Tournament('Name of tournament 1');

        // Create a round and a final round
        $round = $tournament->round("First's round's name");
        $final = $tournament->round("Final's round's name");

        // Create 1 group for the first round
        $group_1 = $round->group('Round 1')->setInGame(2);

        for ($i = 1; $i <= 3; ++$i) {
            $group_1->team('Team ' . $i, $i);
        }

        // Create a final group
        $group = $final->group('Teams 1-2')->setInGame(2);

        $tournament->splitTeams($round);

        $progression = $group_1->progression($group, 0, 2);  // PROGRESS 2 BEST WINNING TEAMS

        $round->genGames();
        $round->simulate();
        $round->progress();

        self::assertCount(2, $group->getTeams());
        self::assertCount(2, $progression->getProgressedTeams());
        self::assertEquals($group->getTeams(), $progression->getProgressedTeams());
    }
}
