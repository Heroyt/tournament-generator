<?php

namespace Hierarchy;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TournamentGenerator\BlankTeam;
use TournamentGenerator\Constants;
use TournamentGenerator\Group;
use TournamentGenerator\Round;
use TournamentGenerator\Team;
use TypeError;

/**
 *
 */
class RoundTest extends TestCase
{

	/** @test */
	public function check_name_setup_round() : void {
		$round = new Round('Round name 1');

		self::assertEquals('Round name 1', $round->getName());
		self::assertEquals('Round name 1', (string) $round);

		$round->setName('Round name 2');

		self::assertEquals('Round name 2', $round->getName());
	}

	/** @test */
	public function check_id_setup_round() : void {
		$round = new Round('Round name 1', 123);

		self::assertEquals(123, $round->getId());

		$round->setId('ID2');

		self::assertEquals('ID2', $round->getId());

		$this->expectException(InvalidArgumentException::class);
		$round->setId(['This', 'is', 'not', 'a', 'valid' => 'id']);
	}

	/** @test */
	public function check_group_add_round() : void {

		$round = new Round('Name of round 1');

		$group = new Group('Group name');
		$group2 = new Group('Group name 2');
		$group3 = new Group('Group name 3');

		// Test adding a single group
		$output = $round->addGroup($group);
		self::assertCount(1, $round->getGroups());

		// Test adding multiple categories
		$round->addGroup($group2, $group3);
		self::assertCount(3, $round->getGroups());

		// Test adding not a group class
		$this->expectException(TypeError::class);
		$round->addGroup('totally not a Group class');

	}

	/** @test */
	public function check_group_creation_round() : void {

		$round = new Round('Name of round 1');

		$group = $round->group('Group name');

		// Test if the group was added
		self::assertCount(1, $round->getGroups());

	}

	/** @test */
	public function check_getting_group_ids_round() : void {

		$round = new Round('Name of round 1');

		$round->group('Group name', 'id1');
		$round->group('Group name', 'id2');
		$round->group('Group name', 12345);

		// Test if the group was added
		self::assertCount(3, $round->getGroupsIds());
		self::assertSame(['id1', 'id2', 12345], $round->getGroupsIds());

	}

	/** @test */
	public function check_ordering_groups_round() : void {

		$round = new Round('Name of round 1');

		$round->group('Group name', 1)->setOrder(2);
		$round->group('Group name', 2)->setOrder(3);
		$round->group('Group name', 3)->setOrder(1);

		// Test if the group was added
		$groups = array_map(static function($a) {
			return $a->getId();
		}, $round->orderGroups());
		self::assertSame([3, 1, 2], $groups);

	}

	/** @test */
	public function check_skip_round() : void {

		$round = new Round('Name of round');

		// Test allowSkip() method
		$round->allowSkip();
		self::assertTrue($round->getSkip());

		// Test disallowSkip() method
		$round->disallowSkip();
		self::assertFalse($round->getSkip());

		// Test setSkip() method
		$round->setSkip(true);
		self::assertTrue($round->getSkip());

	}

	/** @test */
	public function check_group_inherits_skip_round() : void {

		$round = new Round('Name of round');

		$round->allowSkip();
		$group = $round->group('Group name');

		self::assertTrue($group->getSkip());

	}

	/** @test */
	public function check_team_add_round() : void {

		$round = new Round('Name of round');

		$team = new Team('Team name');
		$team2 = new Team('Team name 2');
		$team3 = new Team('Team name 3');

		// Test adding a single team
		$output = $round->addTeam($team);
		self::assertCount(1, $round->getTeams());

		// Test if the output is $this
		self::assertInstanceOf(Round::class, $output);

		// Test adding multiple teams
		$round->addTeam($team2, $team3);
		self::assertCount(3, $round->getTeams());

		// Test adding not a team class
		$this->expectException(TypeError::class);
		$round->addTeam('totally not a Team class');

	}

	/** @test */
	public function check_team_creation_round() : void {

		$round = new Round('Name of round');

		$round->team('Team name');

		// Test if the team was added
		self::assertCount(1, $round->getTeams());

	}

	/** @test */
	public function check_split_teams_round() : void {

		$round = new Round('Name of round');

		for ($i = 1; $i <= 8; $i++) {
			$round->team('Team '.$i);
		}

		$group1 = $round->group('Group 1');
		$group2 = $round->group('Group 2');

		$round->splitTeams();

		self::assertCount(4, $group1->getTeams());
		self::assertCount(4, $group2->getTeams());

	}

	/** @test */
	public function check_split_teams_with_defined_groups_round() : void {

		$round = new Round('Name of tournament 1');

		for ($i = 1; $i <= 8; $i++) {
			$round->team('Team '.$i);
		}

		$group1 = $round->group('Group 1');
		$group2 = $round->group('Group 2');
		$group3 = $round->group('Group 3');

		$round->splitTeams($group1, $group2);

		self::assertCount(4, $group1->getTeams());
		self::assertCount(4, $group2->getTeams());
		self::assertCount(0, $group3->getTeams());

	}

	/** @test */
	public function check_sorting_teams_round() : void {

		$round = new Round('Name of round');

		$group = $round->group('Group name', 'group1');

		for ($i = 1; $i <= 4; $i++) {
			$round->team('Team '.$i);
		}

		$teams = $round->getTeams();

		$group->addTeam(...$teams);

		/**
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

		$teamsSorted = array_map(static function($team) {
			return $team->getName();
		}, $round->sortTeams());

		self::assertSame(['Team 3', 'Team 2', 'Team 1', 'Team 4'], $teamsSorted);

	}

	/** @test */
	public function check_sorting_teams_from_getTeams_round() : void {

		$round = new Round('Name of round');

		$group = $round->group('Group name');

		for ($i = 1; $i <= 4; $i++) {
			$round->team('Team '.$i);
		}

		$teams = $round->getTeams();

		$group->addTeam(...$teams);

		/**
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

		$teamsSorted = array_map(static function($team) {
			return $team->getName();
		}, $round->getTeams(true));

		self::assertSame(['Team 3', 'Team 2', 'Team 1', 'Team 4'], $teamsSorted);

	}

	/** @test */
	public function check_sorting_teams_by_score_round() : void {

		$round = new Round('Name of round');

		$group = $round->group('Group name');

		for ($i = 1; $i <= 4; $i++) {
			$round->team('Team '.$i);
		}

		$teams = $round->getTeams();

		$group->addTeam(...$teams);

		/**
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

		$teamsSorted = array_map(static function($team) {
			return $team->getName();
		}, $round->sortTeams(Constants::SCORE));

		self::assertSame(['Team 2', 'Team 1', 'Team 4', 'Team 3'], $teamsSorted);

	}

	/** @test */
	public function check_games_generation_round() : void {

		$round = new Round('Name of round');

		$round->group('Group name');
		$round->group('Group name');

		for ($i = 1; $i <= 8; $i++) {
			$round->team('Team '.$i);
		}

		$round->splitTeams();

		self::assertCount(12, $round->genGames());
		self::assertCount(12, $round->getGames());
	}

	/** @test */
	public function check_played_round() : void {

		$round = new Round('Name of round');

		$round->group('Group name');
		$round->group('Group name');

		for ($i = 1; $i <= 8; $i++) {
			$round->team('Team '.$i);
		}

		$round->splitTeams();

		self::assertFalse($round->isPlayed());

		$round->genGames();

		self::assertFalse($round->isPlayed());

		$round->simulate();

		self::assertTrue($round->isPlayed());

		// Try to simulate again

		$round->simulate();

		self::assertTrue($round->isPlayed());

		$round->resetGames();

		self::assertFalse($round->isPlayed());
	}

	/** @test */
	public function check_progress_round() : void {

		$round = new Round('Name of round');
		$round2 = new Round('Name of round 2');

		$group = $round->group('Group 1');
		$group2 = $round2->group('Group 2 - wins');
		$group3 = $round2->group('Group 3 - losses');

		for ($i = 1; $i <= 4; $i++) {
			$round->team('Team '.$i);
		}

		$round->splitTeams();

		$group->progression($group2, 0, 2);  // Progress 2 teams
		$group->progression($group3, -2, 2); // Progress 2 teams

		$round->genGames();
		$round->simulate();
		$round->progress();

		self::assertCount(4, $round2->getTeams());
		self::assertCount(2, $group2->getTeams());
		self::assertCount(2, $group3->getTeams());

	}

	/** @test */
	public function check_progress_blank_round() : void {

		$round = new Round('Name of round');
		$round2 = new Round('Name of round 2');

		$group = $round->group('Group 1');
		$group2 = $round2->group('Group 2 - wins');
		$group3 = $round2->group('Group 3 - losses');

		for ($i = 1; $i <= 4; $i++) {
			$round->team('Team '.$i);
		}

		$round->splitTeams();

		self::assertCount(4, $group->getTeams());

		$group->progression($group2, 0, 2);  // Progress 2 teams
		$group->progression($group3, -2, 2); // Progress 2 teams

		$round->genGames();
		$round->simulate();
		$round->progress(true);

		self::assertCount(2, $group2->getTeams());
		self::assertCount(2, $group3->getTeams());
		self::assertCount(4, $round2->getTeams());

		foreach ($round2->getTeams() as $team) {
			self::assertInstanceOf(BlankTeam::class, $team);
		}

	}

	/** @test */
	public function check_adding_teams() : void {

		$round = new Round('Name of round');

		$group1 = $round->group('Group name');
		$group2 = $round->group('Group name');

		for ($i = 1; $i <= 8; $i++) {
			if ($i <= 4) {
				$group1->team('Team '.$i);
			}
			else {
				$group2->team('Team '.$i);
			}
		}

		self::assertCount(8, $round->getTeams());
	}
}
