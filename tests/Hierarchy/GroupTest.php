<?php

declare(strict_types=1);

namespace Hierarchy;

use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\BlankTeam;
use TournamentGenerator\Constants;
use TournamentGenerator\Game;
use TournamentGenerator\Group;
use TournamentGenerator\Helpers\Simulator;
use TournamentGenerator\Progression;
use TournamentGenerator\Team;
use TournamentGenerator\TeamFilter;
use TypeError;

/**
 * @internal
 *
 * @coversNothing
 */
class GroupTest extends TestCase
{
    #[Test]
    public function checkNameSetupGroup() : void {
        $group = new Group('Group name 1');

        self::assertEquals('Group name 1', $group->getName());
        self::assertEquals('Group name 1', (string) $group);

        $group->setName('Group name 2');

        self::assertEquals('Group name 2', $group->getName());
    }

    #[Test]
    public function checkIdSetupGroup() : void {
        $group = new Group('Group name 1', 123);

        self::assertEquals(123, $group->getId());

        $group->setId('ID2');

        self::assertEquals('ID2', $group->getId());

        $this->expectException(TypeError::class);
        $group->setId(['This', 'is', 'not', 'a', 'valid' => 'id']);
    }

    #[Test]
    public function checkSkipGroup() : void {

        $group = new Group('Name of group');

        // Test allowSkip() method
        $group->allowSkip();
        self::assertTrue($group->getSkip());

        // Test disallowSkip() method
        $group->disallowSkip();
        self::assertFalse($group->getSkip());

        // Test setSkip() method
        $group->setSkip(true);
        self::assertTrue($group->getSkip());

    }

    #[Test]
    public function checkTeamAddGroup() : void {

        $group = new Group('Name of group');

        $team = new Team('Team name');
        $team2 = new Team('Team name 2');
        $team3 = new Team('Team name 3');

        // Test adding a single team
        $group->addTeam($team);
        self::assertCount(1, $group->getTeams());
        self::assertArrayHasKey($group->getId(), $team->getGroupResults());

        // Test adding multiple teams
        $group->addTeam($team2, $team3);
        self::assertCount(3, $group->getTeams());
        self::assertArrayHasKey($group->getId(), $team2->getGroupResults());
        self::assertArrayHasKey($group->getId(), $team3->getGroupResults());

        // Test adding not a team class
        $this->expectException(TypeError::class);
        $group->addTeam('totally not a Team class');

    }

    /**
     * @throws Exception
     */
    #[Test]
    public function checkTeamFiltersGroup() : void {

        $group = new Group('Name of group');

        for ($i = 1; $i <= 4; ++$i) {
            $group->team('Team ' . $i);
        }

        $teams = $group->getTeams();

        /*
         *  Team 1 - Wins: 1 Draws: 0 Losses: 2 Score: 3300 By points: #3 By score: #2
         *  Team 2 - Wins: 2 Draws: 0 Losses: 1 Score: 7100 By points: #2 By score: #1
         *  Team 3 - Wins: 3 Draws: 0 Losses: 0 Score: 400  By points: #1 By score: #4
         *  Team 4 - Wins: 0 Draws: 0 Losses: 3 Score: 2099 By points: #4s By score: #3
         */
        $group->game([$teams[0], $teams[1]])->setResults([$teams[0]->getId() => 2000, $teams[1]->getId() => 2001]);
        $group->game([$teams[2], $teams[3]])->setResults([$teams[2]->getId() => 100, $teams[3]->getId() => 99]);
        $group->game([$teams[0], $teams[2]])->setResults([$teams[0]->getId() => 199, $teams[2]->getId() => 200]);
        $group->game([$teams[1], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[1]->getId() => 5000]);
        $group->game([$teams[0], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[0]->getId() => 1001]);
        $group->game([$teams[1], $teams[2]])->setResults([$teams[1]->getId() => 99, $teams[2]->getId() => 100]);

        $filter = new TeamFilter('score', '>', 3000);
        $filter2 = new TeamFilter('points', '!=', 3);

        $filtered = array_map(static fn ($a) : string => $a->getName(), $group->getTeams(false, null, [$filter]));
        self::assertCount(2, $filtered);
        self::assertSame(['Team 1', 'Team 2'], $filtered);

        $filtered = array_map(static fn ($a) : string => $a->getName(), $group->getTeams(false, null, [$filter2]));
        self::assertCount(3, $filtered);
        self::assertSame([1 => 'Team 2', 2 => 'Team 3', 3 => 'Team 4'], $filtered);

        $filtered = array_map(static fn ($a) : string => $a->getName(), $group->getTeams(false, null, [$filter, $filter2]));
        self::assertCount(1, $filtered);
        self::assertSame([1 => 'Team 2'], $filtered);

    }

    #[Test]
    public function checkTeamCreationGroup() : void {

        $group = new Group('Name of group');

        $team = $group->team('Team name');

        self::assertArrayHasKey($group->getId(), $team->getGroupResults());

        // Test if the team was added
        self::assertCount(1, $group->getTeams());

    }

    #[Test]
    public function checkWinPointSettingGroup() : void {
        $group = new Group('Group name 1');

        $group->setWinPoints(5);

        self::assertEquals(5, $group->getWinPoints());
    }

    #[Test]
    public function checkDrawPointSettingGroup() : void {
        $group = new Group('Group name 1');

        $group->setDrawPoints(5);

        self::assertEquals(5, $group->getDrawPoints());
    }

    #[Test]
    public function checkLostPointSettingGroup() : void {
        $group = new Group('Group name 1');

        $group->setLostPoints(5);

        self::assertEquals(5, $group->getLostPoints());
    }

    #[Test]
    public function checkSecondPointSettingGroup() : void {
        $group = new Group('Group name 1');

        $group->setSecondPoints(5);

        self::assertEquals(5, $group->getSecondPoints());
    }

    #[Test]
    public function checkThirdPointSettingGroup() : void {
        $group = new Group('Group name 1');

        $group->setThirdPoints(5);

        self::assertEquals(5, $group->getThirdPoints());
    }

    #[Test]
    public function checkProgressPointSettingGroup() : void {
        $group = new Group('Group name 1');

        $group->setProgressPoints(5);

        self::assertEquals(5, $group->getProgressPoints());
    }

    #[Test]
    public function checkSortingTeamsGroup() : void {

        $group = new Group('Name of group');

        for ($i = 1; $i <= 4; ++$i) {
            $group->team('Team ' . $i);
        }

        $teams = $group->getTeams();

        /*
         *  Team 1 - Wins: 1 Draws: 0 Losses: 2 Score: 3300 By points: #3 By score: #2
         *  Team 2 - Wins: 2 Draws: 0 Losses: 1 Score: 7100 By points: #2 By score: #1
         *  Team 3 - Wins: 3 Draws: 0 Losses: 0 Score: 400  By points: #1 By score: #4
         *  Team 4 - Wins: 0 Draws: 0 Losses: 3 Score: 2099 By points: #4 By score: #3
         */
        $group->game([$teams[0], $teams[1]])->setResults([$teams[0]->getId() => 2000, $teams[1]->getId() => 2001]);
        $group->game([$teams[2], $teams[3]])->setResults([$teams[2]->getId() => 100, $teams[3]->getId() => 99]);
        $group->game([$teams[0], $teams[2]])->setResults([$teams[0]->getId() => 199, $teams[2]->getId() => 200]);
        $group->game([$teams[1], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[1]->getId() => 5000]);
        $group->game([$teams[0], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[0]->getId() => 1001]);
        $group->game([$teams[1], $teams[2]])->setResults([$teams[1]->getId() => 99, $teams[2]->getId() => 100]);

        $teamsSorted = array_map(static fn ($team) : string => $team->getName(), $group->sortTeams());

        self::assertSame(['Team 3', 'Team 2', 'Team 1', 'Team 4'], $teamsSorted);

    }

    #[Test]
    public function checkSortingTeamsByScoreGroup() : void {

        $group = new Group('Name of group');

        for ($i = 1; $i <= 4; ++$i) {
            $group->team('Team ' . $i);
        }

        $teams = $group->getTeams();

        /*
         *  Team 1 - Wins: 1 Draws: 0 Losses: 2 Score: 3300 By points: #3 By score: #2
         *  Team 2 - Wins: 2 Draws: 0 Losses: 1 Score: 7100 By points: #2 By score: #1
         *  Team 3 - Wins: 3 Draws: 0 Losses: 0 Score: 400  By points: #1 By score: #4
         *  Team 4 - Wins: 0 Draws: 0 Losses: 3 Score: 2099 By points: #4 By score: #3
         */
        $group->game([$teams[0], $teams[1]])->setResults([$teams[0]->getId() => 2000, $teams[1]->getId() => 2001]);
        $group->game([$teams[2], $teams[3]])->setResults([$teams[2]->getId() => 100, $teams[3]->getId() => 99]);
        $group->game([$teams[0], $teams[2]])->setResults([$teams[0]->getId() => 199, $teams[2]->getId() => 200]);
        $group->game([$teams[1], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[1]->getId() => 5000]);
        $group->game([$teams[0], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[0]->getId() => 1001]);
        $group->game([$teams[1], $teams[2]])->setResults([$teams[1]->getId() => 99, $teams[2]->getId() => 100]);

        $teamsSorted = array_map(fn ($team) : string => $team->getName(), $group->sortTeams(Constants::SCORE, []));

        self::assertSame(['Team 2', 'Team 1', 'Team 4', 'Team 3'], $teamsSorted);

    }

    #[Test]
    public function checkGamesGenerationGroup() : void {

        $group = new Group('Name of group');
        $group->setType(Constants::ROUND_ROBIN);

        for ($i = 1; $i <= 4; ++$i) {
            $group->team('Team ' . $i);
        }

        self::assertCount(6, $group->genGames());
    }

    #[Test]
    public function checkGamesGenerationTwoTwoGroup() : void {

        $group = new Group('Name of group');
        $group->setType(Constants::ROUND_TWO);

        for ($i = 1; $i <= 4; ++$i) {
            $group->team('Team ' . $i);
        }

        self::assertCount(2, $group->genGames());
    }

    #[Test]
    public function checkGamesGenerationSplitGroup() : void {

        $group = new Group('Name of group');
        $group->setType(Constants::ROUND_SPLIT)->setMaxSize(3);

        for ($i = 1; $i <= 6; ++$i) {
            $group->team('Team ' . $i);
        }

        self::assertCount(6, $group->genGames());
    }

    #[Test]
    public function checkGamesCreationGroup() : void {

        $group = new Group('Name of group');

        $team1 = $group->team('Team 1');
        $team2 = $group->team('Team 2');
        $team3 = $group->team('Team 3');

        $group->game([$team1, $team2]);
        $group->game([$team2, $team3]);
        $group->game([$team1, $team3]);

        self::assertCount(3, $group->getGames());
    }

    #[Test]
    public function checkGamesAddingGroup() : void {

        $group = new Group('Name of group');

        $team1 = $group->team('Team 1');
        $team2 = $group->team('Team 2');
        $team3 = $group->team('Team 3');
        $team4 = $group->team('Team 4');

        $game1 = new Game([$team1, $team2], $group);
        $game2 = new Game([$team3, $team2], $group);
        $game3 = new Game([$team1, $team3], $group);
        $game4 = new Game([$team4, $team1], $group);
        $game5 = new Game([$team4, $team2], $group);
        $game6 = new Game([$team4, $team3], $group);

        $group->addGame($game1);
        self::assertCount(1, $group->getGames());

        $group->addGame($game2, $game3);
        self::assertCount(3, $group->getGames());

        $group->addGame($game4, $game5, $game6);
        self::assertCount(6, $group->getGames());

        $ordered = array_map(static fn ($a) : array => array_map(static fn ($b) : string => $b->getName(), $a->getTeams()), $group->orderGames());

        self::assertSame([
            ['Team 1', 'Team 2'],
            ['Team 4', 'Team 3'],
            ['Team 3', 'Team 2'],
            ['Team 4', 'Team 1'],
            ['Team 1', 'Team 3'],
            ['Team 4', 'Team 2'],
        ], $ordered);
    }

    #[Test]
    public function checkPlayedGroup() : void {

        $group = new Group('Name of group');
        $group->setType(Constants::ROUND_ROBIN);

        for ($i = 1; $i <= 4; ++$i) {
            $group->team('Team ' . $i);
        }

        self::assertFalse($group->isPlayed());

        $group->genGames();

        self::assertFalse($group->isPlayed());

        $group->simulate([], false);

        self::assertTrue($group->isPlayed());

        $group->resetGames();

        self::assertFalse($group->isPlayed());
    }

    #[Test]
    public function checkProgressGroup() : void {

        $group = new Group('Name of group');
        $group2 = new Group('Name of group 2');

        for ($i = 1; $i <= 4; ++$i) {
            $group->team('Team ' . $i);
        }

        $group->progression($group2, 0, 2); // Progress 2 teams

        $teams = $group->getTeams();

        /*
         *  Team 1 - Wins: 1 Draws: 0 Losses: 2 Score: 3300 By points: #3 By score: #2
         *  Team 2 - Wins: 2 Draws: 0 Losses: 1 Score: 7100 By points: #2 By score: #1
         *  Team 3 - Wins: 3 Draws: 0 Losses: 0 Score: 400  By points: #1 By score: #4
         *  Team 4 - Wins: 0 Draws: 0 Losses: 3 Score: 2099 By points: #4s By score: #3
         */
        $group->game([$teams[0], $teams[1]])->setResults([$teams[0]->getId() => 2000, $teams[1]->getId() => 2001]);
        $group->game([$teams[2], $teams[3]])->setResults([$teams[2]->getId() => 100, $teams[3]->getId() => 99]);
        $group->game([$teams[0], $teams[2]])->setResults([$teams[0]->getId() => 199, $teams[2]->getId() => 200]);
        $group->game([$teams[1], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[1]->getId() => 5000]);
        $group->game([$teams[0], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[0]->getId() => 1001]);
        $group->game([$teams[1], $teams[2]])->setResults([$teams[1]->getId() => 99, $teams[2]->getId() => 100]);

        $group->progress();

        self::assertCount(2, $group2->getTeams());
        self::assertTrue($group->isProgressed($teams[2]));
        self::assertTrue($group->isProgressed($teams[1]));
        self::assertFalse($group->isProgressed($teams[0]));
        self::assertFalse($group->isProgressed($teams[3]));

    }

    #[Test]
    public function checkAddProgressGroup() : void {

        $group = new Group('Name of group');
        $group2 = new Group('Name of group 2');

        for ($i = 1; $i <= 4; ++$i) {
            $group->team('Team ' . $i);
        }

        $progression = new Progression($group, $group2, 0, 2);

        $group->addProgression($progression); // Progress 2 teams

        $teams = $group->getTeams();

        /*
         *  Team 1 - Wins: 1 Draws: 0 Losses: 2 Score: 3300 By points: #3 By score: #2
         *  Team 2 - Wins: 2 Draws: 0 Losses: 1 Score: 7100 By points: #2 By score: #1
         *  Team 3 - Wins: 3 Draws: 0 Losses: 0 Score: 400  By points: #1 By score: #4
         *  Team 4 - Wins: 0 Draws: 0 Losses: 3 Score: 2099 By points: #4s By score: #3
         */
        $group->game([$teams[0], $teams[1]])->setResults([$teams[0]->getId() => 2000, $teams[1]->getId() => 2001]);
        $group->game([$teams[2], $teams[3]])->setResults([$teams[2]->getId() => 100, $teams[3]->getId() => 99]);
        $group->game([$teams[0], $teams[2]])->setResults([$teams[0]->getId() => 199, $teams[2]->getId() => 200]);
        $group->game([$teams[1], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[1]->getId() => 5000]);
        $group->game([$teams[0], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[0]->getId() => 1001]);
        $group->game([$teams[1], $teams[2]])->setResults([$teams[1]->getId() => 99, $teams[2]->getId() => 100]);

        $group->progress();

        self::assertCount(2, $group2->getTeams());
        self::assertTrue($group->isProgressed($teams[2]));
        self::assertTrue($group->isProgressed($teams[1]));
        self::assertFalse($group->isProgressed($teams[0]));
        self::assertFalse($group->isProgressed($teams[3]));

    }

    #[Test]
    public function checkAddProgressedTeamGroup() : void {

        $group = new Group('Name of group');

        for ($i = 1; $i <= 4; ++$i) {
            $group->team('Team ' . $i);
        }

        $teams = $group->getTeams();

        $group->addProgressed($teams[0], $teams[3]);
        self::assertTrue($group->isProgressed($teams[0]));
        self::assertTrue($group->isProgressed($teams[3]));
        self::assertFalse($group->isProgressed($teams[1]));
        self::assertFalse($group->isProgressed($teams[2]));

    }

    #[Test]
    public function checkProgressBlankGroup() : void {

        $group = new Group('Name of group');
        $group2 = new Group('Name of group 2');

        for ($i = 1; $i <= 4; ++$i) {
            $group->team('Team ' . $i);
        }

        $group->progression($group2, 0, 2); // Progress 2 teams

        $group->genGames();
        $group->simulate([], false);
        $group->progress(true);

        self::assertCount(2, $group2->getTeams());

        foreach ($group2->getTeams() as $team) {
            self::assertInstanceOf(BlankTeam::class, $team);
        }

    }

    #[Test]
    public function checkSettingInGameGroup() : void {

        $group = new Group('Group name');

        $group->setInGame(3);
        self::assertEquals(3, $group->getInGame());

        $group->setInGame(2);
        self::assertEquals(2, $group->getInGame());

        $group->setInGame(4);
        self::assertEquals(4, $group->getInGame());

        $this->expectException(Exception::class);
        $group->setInGame(1);

    }

    #[Test]
    public function checkSettingOrderingGroup() : void {

        $group = new Group('Group name');

        $group->setOrdering(Constants::POINTS);
        self::assertEquals(Constants::POINTS, $group->getOrdering());

        $group->setOrdering(Constants::SCORE);
        self::assertEquals(Constants::SCORE, $group->getOrdering());

        $this->expectException(Exception::class);
        $group->setOrdering('Totally not a valid ordering');

    }

    #[Test]
    public function checkSettingOrderGroup() : void {

        $group = new Group('Group name');

        $group->setOrder(2);
        self::assertEquals(2, $group->getOrder());

        $group->setOrder(10);
        self::assertEquals(10, $group->getOrder());

        $this->expectException(TypeError::class);
        $group->setOrder('Totally not a valid order');

    }

    #[Test]
    public function checkSettingTypeGroup() : void {

        $group = new Group('Group name');

        $group->setType(Constants::ROUND_TWO);
        self::assertEquals(Constants::ROUND_TWO, $group->getType());

        $group->setType(Constants::ROUND_SPLIT);
        self::assertEquals(Constants::ROUND_SPLIT, $group->getType());

        $group->setType(Constants::ROUND_ROBIN);
        self::assertEquals(Constants::ROUND_ROBIN, $group->getType());

        $this->expectException(Exception::class);
        $group->setType('Totally not a valid type');
        $this->expectException(TypeError::class);
        $group->setType(2);

    }

    #[Test]
    public function checkSettingMaxSizeGroup() : void {

        $group = new Group('Group name');

        $group->setMaxSize(2);
        self::assertEquals(2, $group->getMaxSize());

        $group->setMaxSize(10);
        self::assertEquals(10, $group->getMaxSize());

        $this->expectException(TypeError::class);
        $group->setMaxSize('Totally not a valid max size');

        $this->expectException(Exception::class);
        $group->setMaxSize(1);

    }

    #[Test]
    public function checkSettingMaxSizeGroupInvalid() : void {

        $group = new Group('Group name');

        $this->expectException(Exception::class);
        $group->setMaxSize(1);

    }

    #[Test]
    public function checkGenGamesSimulateGroup() : void {

        $group = new Group('Group name');

        for ($i = 1; $i <= 4; ++$i) {
            $group->team('Team ' . $i);
        }

        $games = $group->genGames();
        self::assertCount(6, $games);

        $teams = $group->simulate([], false);
        self::assertCount(4, $teams);

        self::assertTrue($group->isPlayed());

    }

    #[Test]
    public function checkGenGamesSimulateWithResetGroup() : void {

        $group = new Group('Group name');

        for ($i = 1; $i <= 4; ++$i) {
            $group->team('Team ' . $i);
        }

        $games = $group->genGames();
        self::assertCount(6, $games);

        $teams = $group->simulate([], true);
        self::assertCount(4, $teams);

        self::assertFalse($group->isPlayed());

    }

    public function testIsPlayed() : void {
        $group = new Group('Group name');

        for ($i = 1; $i <= 4; ++$i) {
            $group->team('Team ' . $i);
        }

        $games = $group->genGames();
        self::assertCount(6, $games);

        foreach ($games as $game) {
            self::assertFalse($group->isPlayed());
            Simulator::simulateGame($game);
            self::assertTrue($game->isPlayed());
        }

        self::assertTrue($group->isPlayed());
    }
}
