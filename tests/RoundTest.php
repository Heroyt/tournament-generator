<?php
use PHPUnit\Framework\TestCase;

/**
 *
 */
class RoundTest extends TestCase
{

	/** @test */
	public function check_name_setup_round() {
		$round = new \TournamentGenerator\Round('Round name 1');

		$this->assertEquals('Round name 1', $round->getName());
		$this->assertEquals('Round name 1', (string) $round);

		$round->setName('Round name 2');

		$this->assertEquals('Round name 2', $round->getName());
	}

	/** @test */
	public function check_id_setup_round() {
		$round = new \TournamentGenerator\Round('Round name 1', 123);

		$this->assertEquals(123, $round->getId());

		$round->setId('ID2');

		$this->assertEquals('ID2', $round->getId());

		$this->expectException(Exception::class);
		$round->setId(['This', 'is', 'not', 'a', 'valid' => 'id']);
	}

	/** @test */
	public function check_group_add_round() {

		$round = new \TournamentGenerator\Round('Name of round 1');

		$group = new \TournamentGenerator\Group('Group name');
		$group2 = new \TournamentGenerator\Group('Group name 2');
		$group3 = new \TournamentGenerator\Group('Group name 3');

		// Test adding a single group
		$output = $round->addGroup($group);
		$this->assertCount(1, $round->getGroups());

		// Test if the output is $this
		$this->assertInstanceOf('\\TournamentGenerator\\Round', $output);

		// Test adding multiple categories
		$round->addGroup($group2, $group3);
		$this->assertCount(3, $round->getGroups());

		// Test adding not a group class
		$this->expectException(TypeError::class);
		$round->addGroup('totally not a Group class');

	}

	/** @test */
	public function check_group_creation_round() {

		$round = new \TournamentGenerator\Round('Name of round 1');

		$group = $round->group('Group name');

		// Test if Group class is really created
		$this->assertInstanceOf('\\TournamentGenerator\\Group', $group);
		// Test if the group was added
		$this->assertCount(1, $round->getGroups());

	}

	/** @test */
	public function check_getting_group_ids_round() {

		$round = new \TournamentGenerator\Round('Name of round 1');

		$group = $round->group('Group name', 'id1');
		$group2 = $round->group('Group name', 'id2');
		$group3 = $round->group('Group name', 12345);

		// Test if the group was added
		$this->assertCount(3, $round->getGroupsIds());
		$this->assertSame(['id1', 'id2', 12345], $round->getGroupsIds());

	}

	/** @test */
	public function check_ordering_groups_round() {

		$round = new \TournamentGenerator\Round('Name of round 1');

		$group = $round->group('Group name', 1)->setOrder(2);
		$group2 = $round->group('Group name', 2)->setOrder(3);
		$group3 = $round->group('Group name', 3)->setOrder(1);

		// Test if the group was added
		$groups = array_map(function($a) {
			return $a->getId();
		}, $round->orderGroups());
		$this->assertSame([3, 1, 2], $groups);

	}

	/** @test */
	public function check_skip_round() {

		$round = new \TournamentGenerator\Round('Name of round');

		// Test allowSkip() method
		$round->allowSkip();
		$this->assertTrue($round->getSkip());

		// Test disallowSkip() method
		$round->disallowSkip();
		$this->assertFalse($round->getSkip());

		// Test setSkip() method
		$round->setSkip(true);
		$this->assertTrue($round->getSkip());

	}

	/** @test */
	public function check_group_inherits_skip_round() {

		$round = new \TournamentGenerator\Round('Name of round');

		$round->allowSkip();
		$group = $round->group('Group name');

		$this->assertTrue($group->getSkip());

	}

	/** @test */
	public function check_team_add_round() {

		$round = new \TournamentGenerator\Round('Name of round');

		$team = new \TournamentGenerator\Team('Team name');
		$team2 = new \TournamentGenerator\Team('Team name 2');
		$team3 = new \TournamentGenerator\Team('Team name 3');

		// Test adding a single team
		$output = $round->addTeam($team);
		$this->assertCount(1, $round->getTeams());

		// Test if the output is $this
		$this->assertInstanceOf('\\TournamentGenerator\\Round', $output);

		// Test adding multiple teams
		$round->addTeam($team2, $team3);
		$this->assertCount(3, $round->getTeams());

		// Test adding not a team class
		$this->expectException(TypeError::class);
		$round->addTeam('totally not a Team class');

	}

	/** @test */
	public function check_team_creation_round() {

		$round = new \TournamentGenerator\Round('Name of round');

		$team = $round->team('Team name');

		// Test if Team class is really created
		$this->assertInstanceOf('\\TournamentGenerator\\Team', $team);
		// Test if the team was added
		$this->assertCount(1, $round->getTeams());

	}

	/** @test */
	public function check_split_teams_round() {

		$round = new \TournamentGenerator\Round('Name of round');

		for ($i=1; $i <= 8; $i++) {
			$round->team('Team '.$i);
		}

		$group1 = $round->group('Group 1');
		$group2 = $round->group('Group 2');

		$round->splitTeams();

		$this->assertCount(4, $group1->getTeams());
		$this->assertCount(4, $group2->getTeams());

	}

	/** @test */
	public function check_split_teams_with_defined_groups_round() {

		$round = new \TournamentGenerator\Round('Name of tournament 1');

		for ($i=1; $i <= 8; $i++) {
			$round->team('Team '.$i);
		}

		$group1 = $round->group('Group 1');
		$group2 = $round->group('Group 2');
		$group3 = $round->group('Group 3');

		$round->splitTeams($group1, $group2);

		$this->assertCount(4, $group1->getTeams());
		$this->assertCount(4, $group2->getTeams());
		$this->assertCount(0, $group3->getTeams());

	}

	/** @test */
	public function check_sorting_teams_round() {

		$round = new \TournamentGenerator\Round('Name of round');

		$group = $round->group('Group name', 'group1');

		for ($i=1; $i <= 4; $i++) {
			$round->team('Team '.$i);
		}

		$teams = $round->getTeams();

		$group->addTeam($teams);

		/**
		*	Team 1 - Wins: 1 Draws: 0 Losses: 2 Score: 3300 By points: #3 By score: #2
		*	Team 2 - Wins: 2 Draws: 0 Losses: 1 Score: 7100 By points: #2 By score: #1
		*	Team 3 - Wins: 3 Draws: 0 Losses: 0 Score: 400  By points: #1 By score: #4
		*	Team 4 - Wins: 0 Draws: 0 Losses: 3 Score: 2099 By points: #4s By score: #3
		*/
		$group->game([$teams[0], $teams[1]])->setResults([$teams[0]->getId() => 2000, $teams[1]->getId() => 2001]);
		$group->game([$teams[2], $teams[3]])->setResults([$teams[2]->getId() => 100, $teams[3]->getId() => 99]);
		$group->game([$teams[0], $teams[2]])->setResults([$teams[0]->getId() => 199, $teams[2]->getId() => 200]);
		$group->game([$teams[1], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[1]->getId() => 5000]);
		$group->game([$teams[0], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[0]->getId() => 1001]);
		$group->game([$teams[1], $teams[2]])->setResults([$teams[1]->getId() => 99, $teams[2]->getId() => 100]);

		$teamsSorted = array_map(function($team) {
			return $team->getName();
		}, $round->sortTeams());

		$this->assertSame(['Team 3', 'Team 2', 'Team 1', 'Team 4'], $teamsSorted);

	}

	/** @test */
	public function check_sorting_teams_from_getTeams_round() {

		$round = new \TournamentGenerator\Round('Name of round');

		$group = $round->group('Group name');

		for ($i=1; $i <= 4; $i++) {
			$round->team('Team '.$i);
		}

		$teams = $round->getTeams();

		$group->addTeam($teams);

		/**
		*	Team 1 - Wins: 1 Draws: 0 Losses: 2 Score: 3300 By points: #3 By score: #2
		*	Team 2 - Wins: 2 Draws: 0 Losses: 1 Score: 7100 By points: #2 By score: #1
		*	Team 3 - Wins: 3 Draws: 0 Losses: 0 Score: 400  By points: #1 By score: #4
		*	Team 4 - Wins: 0 Draws: 0 Losses: 3 Score: 2099 By points: #4s By score: #3
		*/
		$group->game([$teams[0], $teams[1]])->setResults([$teams[0]->getId() => 2000, $teams[1]->getId() => 2001]);
		$group->game([$teams[2], $teams[3]])->setResults([$teams[2]->getId() => 100, $teams[3]->getId() => 99]);
		$group->game([$teams[0], $teams[2]])->setResults([$teams[0]->getId() => 199, $teams[2]->getId() => 200]);
		$group->game([$teams[1], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[1]->getId() => 5000]);
		$group->game([$teams[0], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[0]->getId() => 1001]);
		$group->game([$teams[1], $teams[2]])->setResults([$teams[1]->getId() => 99, $teams[2]->getId() => 100]);

		$teamsSorted = array_map(function($team) {
			return $team->getName();
		}, $round->getTeams(true));

		$this->assertSame(['Team 3', 'Team 2', 'Team 1', 'Team 4'], $teamsSorted);

	}

	/** @test */
	public function check_sorting_teams_by_score_round() {

		$round = new \TournamentGenerator\Round('Name of round');

		$group = $round->group('Group name');

		for ($i=1; $i <= 4; $i++) {
			$round->team('Team '.$i);
		}

		$teams = $round->getTeams();

		$group->addTeam($teams);

		/**
		*	Team 1 - Wins: 1 Draws: 0 Losses: 2 Score: 3300 By points: #3 By score: #2
		*	Team 2 - Wins: 2 Draws: 0 Losses: 1 Score: 7100 By points: #2 By score: #1
		*	Team 3 - Wins: 3 Draws: 0 Losses: 0 Score: 400  By points: #1 By score: #4
		*	Team 4 - Wins: 0 Draws: 0 Losses: 3 Score: 2099 By points: #4s By score: #3
		*/
		$group->game([$teams[0], $teams[1]])->setResults([$teams[0]->getId() => 2000, $teams[1]->getId() => 2001]);
		$group->game([$teams[2], $teams[3]])->setResults([$teams[2]->getId() => 100, $teams[3]->getId() => 99]);
		$group->game([$teams[0], $teams[2]])->setResults([$teams[0]->getId() => 199, $teams[2]->getId() => 200]);
		$group->game([$teams[1], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[1]->getId() => 5000]);
		$group->game([$teams[0], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[0]->getId() => 1001]);
		$group->game([$teams[1], $teams[2]])->setResults([$teams[1]->getId() => 99, $teams[2]->getId() => 100]);

		$teamsSorted = array_map(function($team) {
			return $team->getName();
		}, $round->sortTeams(\TournamentGenerator\Constants::SCORE));

		$this->assertSame(['Team 2', 'Team 1', 'Team 4', 'Team 3'], $teamsSorted);

	}

	/** @test */
	public function check_games_generation_round() {

		$round = new \TournamentGenerator\Round('Name of round');

		$group = $round->group('Group name')->setType(\TournamentGenerator\Constants::ROUND_ROBIN);
		$group2 = $round->group('Group name')->setType(\TournamentGenerator\Constants::ROUND_ROBIN);

		for ($i=1; $i <= 8; $i++) {
			$round->team('Team '.$i);
		}

		$round->splitTeams();

		$this->assertCount(12, $round->genGames());
		$this->assertCount(12, $round->getGames());
	}

	/** @test */
	public function check_played_round() {

		$round = new \TournamentGenerator\Round('Name of round');

		$group = $round->group('Group name')->setType(\TournamentGenerator\Constants::ROUND_ROBIN);
		$group2 = $round->group('Group name')->setType(\TournamentGenerator\Constants::ROUND_ROBIN);

		for ($i=1; $i <= 8; $i++) {
			$round->team('Team '.$i);
		}

		$round->splitTeams();

		$this->assertFalse($round->isPlayed());

		$round->genGames();

		$this->assertFalse($round->isPlayed());

		$round->simulate();

		$this->assertTrue($round->isPlayed());

		$round->resetGames();

		$this->assertFalse($round->isPlayed());
	}

	/** @test */
	public function check_progress_round() {

		$round = new \TournamentGenerator\Round('Name of round');
		$round2 = new \TournamentGenerator\Round('Name of round 2');

		$group = $round->group('Group 1');
		$group2 = $round2->group('Group 2 - wins');
		$group3 = $round2->group('Group 3 - losses');

		for ($i=1; $i <= 4; $i++) {
			$round->team('Team '.$i);
		}

		$round->splitTeams();

		$group->progression($group2, 0, 2); // Progress 2 teams
		$group->progression($group3, -2, 2); // Progress 2 teams

		$round->genGames();
		$round->simulate();
		$round->progress();

		$this->assertCount(4, $round2->getTeams());
		$this->assertCount(2, $group2->getTeams());
		$this->assertCount(2, $group3->getTeams());

	}

	/** @test */
	public function check_progress_blank_round() {

		$round = new \TournamentGenerator\Round('Name of round');
		$round2 = new \TournamentGenerator\Round('Name of round 2');

		$group = $round->group('Group 1');
		$group2 = $round2->group('Group 2 - wins');
		$group3 = $round2->group('Group 3 - losses');

		for ($i=1; $i <= 4; $i++) {
			$round->team('Team '.$i);
		}

		$round->splitTeams();

		$group->progression($group2, 0, 2); // Progress 2 teams
		$group->progression($group3, -2, 2); // Progress 2 teams

		$round->genGames();
		$round->simulate();
		$round->progress(true);

		$this->assertCount(4, $round2->getTeams());
		$this->assertCount(2, $group2->getTeams());
		$this->assertCount(2, $group3->getTeams());

		foreach ($round2->getTeams() as $team) {
			$this->assertInstanceOf('\\TournamentGenerator\\BlankTeam', $team);
		}

	}

}
