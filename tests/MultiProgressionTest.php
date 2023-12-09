<?php

use PHPUnit\Framework\TestCase;
use TournamentGenerator\Group;
use TournamentGenerator\MultiProgression;
use TournamentGenerator\Tournament;

/**
 *
 */
class MultiProgressionTest extends TestCase
{

	public function testProgressedSetting(): void {
		$group1 = new Group('Group1');
		$group2 = new Group('Group2');
		$group3 = new Group('Group3');
		$progression = new MultiProgression([$group1, $group2], $group3);

		self::assertFalse($progression->isProgressed());
		$progression->setProgressed(true);
		self::assertTrue($progression->isProgressed());
	}

	public function testProgressing(): void {
		$tournament = new Tournament('Name of tournament 1');

		for ($i = 1; $i <= 10; $i++) {
			$tournament->team('Team ' . $i);
		}
		// Create a round and a final round
		$round = $tournament->round("First's round's name");
		$final = $tournament->round("Final's round's name");

		// Create 2 groups for the first round
		$group1 = $round->group('Round 1')->setInGame(2);
		$group2 = $round->group('Round 2')->setInGame(2);

		// Create a final group
		$final_group = $final->group('Teams 1-4')->setInGame(2);
		$second_group = $final->group('Teams 5-8')->setInGame(2);

		$tournament->splitTeams($round);

		$group1->progression($final_group, 0, 2);                        // PROGRESS 2 BEST WINNING TEAMS
		$group2->progression($final_group, 0, 2);                        // PROGRESS 2 BEST WINNING TEAMS
		$final_group->multiProgression([$group1, $group2], 2, 1, 1);     // Progress the best of the third teams
		$second_group->multiProgression([$group1, $group2], 2, 1, 1, 1); // Progress the worst of the third teams
		$group1->progression($second_group, 3, 2);                       // Progress all other teams
		$group2->progression($second_group, 3, 2);                       // Progress all other teams

		$round->genGames();
		$round->simulate();
		$round->progress();

		self::assertCount(5, $final_group->getTeams());
		self::assertCount(5, $second_group->getTeams());

		// Get third teams
		$team1 = $group1->getTeams(true)[2];
		$team1Points = $team1->sumPoints([$group1->getId()]);
		$team1Score = $team1->sumScore([$group1->getId()]);
		$team2 = $group2->getTeams(true)[2];
		$team2Points = $team2->sumPoints([$group2->getId()]);
		$team2Score = $team2->sumScore([$group2->getId()]);

		if ($team1Points > $team2Points) {
			$betterTeam = $team1;
			$worseTeam = $team2;
		}
		else {
			if ($team1Points < $team2Points) {
				$betterTeam = $team2;
				$worseTeam = $team1;
			}
			else {
				if ($team1Score > $team2Score) {
					$betterTeam = $team1;
					$worseTeam = $team2;
				}
				else {
					$betterTeam = $team2;
					$worseTeam = $team1;
				}
			}
		}

		$this->assertContains($betterTeam->getId(), $final_group->getTeamContainer()->ids()->get());
		$this->assertContains($worseTeam->getId(), $second_group->getTeamContainer()->ids()->get());
	}

	public function testGetProgressedTeams(): void {
		$tournament = new Tournament('Name of tournament 1');

		// Create a round and a final round
		$round = $tournament->round("First's round's name");
		$final = $tournament->round("Final's round's name");

		// Create 1 group for the first round
		$group1 = $round->group('Round 1')->setInGame(2);
		$group2 = $round->group('Round 2')->setInGame(2);

		for ($i = 1; $i <= 6; $i++) {
			$group1->team('Team ' . $i, $i);
		}

		// Create a final group
		$final_group = $final->group('Teams 1-2')->setInGame(2);

		$tournament->splitTeams($round);

		$progression = $final_group->multiProgression([$group1, $group2], 0, 2, 3);

		$round->genGames();
		$round->simulate();
		$round->progress();

		self::assertCount(3, $final_group->getTeams());
		self::assertCount(3, $progression->getProgressedTeams());
		self::assertEquals($final_group->getTeams(), $progression->getProgressedTeams());
	}
}
