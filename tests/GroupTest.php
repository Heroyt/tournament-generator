<?php
use PHPUnit\Framework\TestCase;

/**
 *
 */
class GroupTest extends TestCase
{

	/** @test */
	public function check_name_setup_group() {
		$group = new \TournamentGenerator\Group('Group name 1');

		$this->assertEquals('Group name 1', $group->getName());
		$this->assertEquals('Group name 1', (string) $group);

		$group->setName('Group name 2');

		$this->assertEquals('Group name 2', $group->getName());
	}

	/** @test */
	public function check_id_setup_group() {
		$group = new \TournamentGenerator\Group('Group name 1', 123);

		$this->assertEquals(123, $group->getId());

		$group->setId('ID2');

		$this->assertEquals('ID2', $group->getId());

		$this->expectException(InvalidArgumentException::class);
		$group->setId(['This', 'is', 'not', 'a', 'valid' => 'id']);
	}

	/** @test */
	public function check_skip_group() {

		$group = new \TournamentGenerator\Group('Name of group');

		// Test allowSkip() method
		$group->allowSkip();
		$this->assertTrue($group->getSkip());

		// Test disallowSkip() method
		$group->disallowSkip();
		$this->assertFalse($group->getSkip());

		// Test setSkip() method
		$group->setSkip(true);
		$this->assertTrue($group->getSkip());

	}

	/** @test */
	public function check_team_add_group() {

		$group = new \TournamentGenerator\Group('Name of group');

		$team = new \TournamentGenerator\Team('Team name');
		$team2 = new \TournamentGenerator\Team('Team name 2');
		$team3 = new \TournamentGenerator\Team('Team name 3');

		// Test adding a single team
		$output = $group->addTeam($team);
		$this->assertCount(1, $group->getTeams());
		$this->assertArrayHasKey($group->getId(), $team->getGroupResults());

		// Test if the output is $this
		$this->assertInstanceOf('\\TournamentGenerator\\Group', $output);

		// Test adding multiple teams
		$group->addTeam($team2, $team3);
		$this->assertCount(3, $group->getTeams());
		$this->assertArrayHasKey($group->getId(), $team2->getGroupResults());
		$this->assertArrayHasKey($group->getId(), $team3->getGroupResults());

		// Test adding not a team class
		$this->expectException(TypeError::class);
		$group->addTeam('totally not a Team class');

	}

	/** @test */
	public function check_team_filters_group() {

		$group = new \TournamentGenerator\Group('Name of group');

		for ($i=1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$teams = $group->getTeams();

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

		$filter = new \TournamentGenerator\TeamFilter('score', '>', 3000);
		$filter2 = new \TournamentGenerator\TeamFilter('points', '!=', 3);

		$filtered = array_map(function($a){return $a->getName();},$group->getTeams(false, null, [$filter]));
		$this->assertCount(2, $filtered);
		$this->assertSame(['Team 1', 'Team 2'], $filtered);

		$filtered = array_map(function($a){return $a->getName();},$group->getTeams(false, null, [$filter2]));
		$this->assertCount(3, $filtered);
		$this->assertSame([1 => 'Team 2', 2 => 'Team 3', 3 => 'Team 4'], $filtered);

		$filtered = array_map(function($a){return $a->getName();},$group->getTeams(false, null, [$filter, $filter2]));
		$this->assertCount(1, $filtered);
		$this->assertSame([1 => 'Team 2'], $filtered);

	}

	/** @test */
	public function check_team_creation_group() {

		$group = new \TournamentGenerator\Group('Name of group');

		$team = $group->team('Team name');

		$this->assertArrayHasKey($group->getId(), $team->getGroupResults());

		// Test if Team class is really created
		$this->assertInstanceOf('\\TournamentGenerator\\Team', $team);
		// Test if the team was added
		$this->assertCount(1, $group->getTeams());

	}

	/** @test */
	public function check_win_point_setting_group() {
		$group = new \TournamentGenerator\Group('Group name 1');

		$group->setWinPoints(5);

		$this->assertEquals(5, $group->getWinPoints());
	}

	/** @test */
	public function check_draw_point_setting_group() {
		$group = new \TournamentGenerator\Group('Group name 1');

		$group->setDrawPoints(5);

		$this->assertEquals(5, $group->getDrawPoints());
	}

	/** @test */
	public function check_lost_point_setting_group() {
		$group = new \TournamentGenerator\Group('Group name 1');

		$group->setLostPoints(5);

		$this->assertEquals(5, $group->getLostPoints());
	}

	/** @test */
	public function check_second_point_setting_group() {
		$group = new \TournamentGenerator\Group('Group name 1');

		$group->setSecondPoints(5);

		$this->assertEquals(5, $group->getSecondPoints());
	}

	/** @test */
	public function check_third_point_setting_group() {
		$group = new \TournamentGenerator\Group('Group name 1');

		$group->setThirdPoints(5);

		$this->assertEquals(5, $group->getThirdPoints());
	}

	/** @test */
	public function check_progress_point_setting_group() {
		$group = new \TournamentGenerator\Group('Group name 1');

		$group->setProgressPoints(5);

		$this->assertEquals(5, $group->getProgressPoints());
	}

	/** @test */
	public function check_sorting_teams_group() {

		$group = new \TournamentGenerator\Group('Name of group');

		for ($i=1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$teams = $group->getTeams();

		/**
		*	Team 1 - Wins: 1 Draws: 0 Losses: 2 Score: 3300 By points: #3 By score: #2
		*	Team 2 - Wins: 2 Draws: 0 Losses: 1 Score: 7100 By points: #2 By score: #1
		*	Team 3 - Wins: 3 Draws: 0 Losses: 0 Score: 400  By points: #1 By score: #4
		*	Team 4 - Wins: 0 Draws: 0 Losses: 3 Score: 2099 By points: #4 By score: #3
		*/
		$group->game([$teams[0], $teams[1]])->setResults([$teams[0]->getId() => 2000, $teams[1]->getId() => 2001]);
		$group->game([$teams[2], $teams[3]])->setResults([$teams[2]->getId() => 100, $teams[3]->getId() => 99]);
		$group->game([$teams[0], $teams[2]])->setResults([$teams[0]->getId() => 199, $teams[2]->getId() => 200]);
		$group->game([$teams[1], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[1]->getId() => 5000]);
		$group->game([$teams[0], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[0]->getId() => 1001]);
		$group->game([$teams[1], $teams[2]])->setResults([$teams[1]->getId() => 99, $teams[2]->getId() => 100]);

		$teamsSorted = array_map(function($team) {
			return $team->getName();
		}, $group->sortTeams());

		$this->assertSame(['Team 3', 'Team 2', 'Team 1', 'Team 4'], $teamsSorted);

	}

	/** @test */
	public function check_sorting_teams_by_score_group() {

		$group = new \TournamentGenerator\Group('Name of group');

		for ($i=1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$teams = $group->getTeams();

		/**
		*	Team 1 - Wins: 1 Draws: 0 Losses: 2 Score: 3300 By points: #3 By score: #2
		*	Team 2 - Wins: 2 Draws: 0 Losses: 1 Score: 7100 By points: #2 By score: #1
		*	Team 3 - Wins: 3 Draws: 0 Losses: 0 Score: 400  By points: #1 By score: #4
		*	Team 4 - Wins: 0 Draws: 0 Losses: 3 Score: 2099 By points: #4 By score: #3
		*/
		$group->game([$teams[0], $teams[1]])->setResults([$teams[0]->getId() => 2000, $teams[1]->getId() => 2001]);
		$group->game([$teams[2], $teams[3]])->setResults([$teams[2]->getId() => 100, $teams[3]->getId() => 99]);
		$group->game([$teams[0], $teams[2]])->setResults([$teams[0]->getId() => 199, $teams[2]->getId() => 200]);
		$group->game([$teams[1], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[1]->getId() => 5000]);
		$group->game([$teams[0], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[0]->getId() => 1001]);
		$group->game([$teams[1], $teams[2]])->setResults([$teams[1]->getId() => 99, $teams[2]->getId() => 100]);

		$teamsSorted = array_map(function($team) {
			return $team->getName();
		}, $group->sortTeams(\TournamentGenerator\Constants::SCORE, []));

		$this->assertSame(['Team 2', 'Team 1', 'Team 4', 'Team 3'], $teamsSorted);

	}

	/** @test */
	public function check_games_generation_group() {

		$group = new \TournamentGenerator\Group('Name of group');
		$group->setType(\TournamentGenerator\Constants::ROUND_ROBIN);

		for ($i=1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$this->assertCount(6, $group->genGames());
	}

	/** @test */
	public function check_games_generation_two_two_group() {

		$group = new \TournamentGenerator\Group('Name of group');
		$group->setType(\TournamentGenerator\Constants::ROUND_TWO);

		for ($i=1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$this->assertCount(2, $group->genGames());
	}

	/** @test */
	public function check_games_generation_split_group() {

		$group = new \TournamentGenerator\Group('Name of group');
		$group->setType(\TournamentGenerator\Constants::ROUND_SPLIT)->setMaxSize(3);

		for ($i=1; $i <= 6; $i++) {
			$group->team('Team '.$i);
		}

		$this->assertCount(6, $group->genGames());
	}

	/** @test */
	public function check_games_creation_group() {

		$group = new \TournamentGenerator\Group('Name of group');

		$team1 = $group->team('Team 1');
		$team2 = $group->team('Team 2');
		$team3 = $group->team('Team 3');

		$group->game([$team1, $team2]);
		$group->game([$team2, $team3]);
		$group->game([$team1, $team3]);

		$this->assertCount(3, $group->getGames());
	}

	/** @test */
	public function check_games_adding_group() {

		$group = new \TournamentGenerator\Group('Name of group');

		$team1 = $group->team('Team 1');
		$team2 = $group->team('Team 2');
		$team3 = $group->team('Team 3');
		$team4 = $group->team('Team 4');

		$game1 = new \TournamentGenerator\Game([$team1, $team2], $group);
		$game2 = new \TournamentGenerator\Game([$team3, $team2], $group);
		$game3 = new \TournamentGenerator\Game([$team1, $team3], $group);
		$game4 = new \TournamentGenerator\Game([$team4, $team1], $group);
		$game5 = new \TournamentGenerator\Game([$team4, $team2], $group);
		$game6 = new \TournamentGenerator\Game([$team4, $team3], $group);

		$group->addGame($game1);
		$this->assertCount(1, $group->getGames());

		$group->addGame($game2, $game3);
		$this->assertCount(3, $group->getGames());

		$group->addGame([$game4, $game5, $game6]);
		$this->assertCount(6, $group->getGames());

		$ordered = array_map(function($a){return array_map(function($b){return $b->getName();}, $a->getTeams());}, $group->orderGames());

		$this->assertSame([
			['Team 1', 'Team 2'],
			['Team 4', 'Team 3'],
			['Team 3', 'Team 2'],
			['Team 4', 'Team 1'],
			['Team 1', 'Team 3'],
			['Team 4', 'Team 2'],
		], $ordered);
	}

	/** @test */
	public function check_played_group() {

		$group = new \TournamentGenerator\Group('Name of group');
		$group->setType(\TournamentGenerator\Constants::ROUND_ROBIN);

		for ($i=1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$this->assertFalse($group->isPlayed());

		$group->genGames();

		$this->assertFalse($group->isPlayed());

		$group->simulate([], false);

		$this->assertTrue($group->isPlayed());

		$group->resetGames();

		$this->assertFalse($group->isPlayed());
	}

	/** @test */
	public function check_progress_group() {

		$group = new \TournamentGenerator\Group('Name of group');
		$group2 = new \TournamentGenerator\Group('Name of group 2');

		for ($i=1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$group->progression($group2, 0, 2); // Progress 2 teams

		$teams = $group->getTeams();

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

		$group->progress();

		$this->assertCount(2, $group2->getTeams());
		$this->assertTrue($group->isProgressed($teams[2]));
		$this->assertTrue($group->isProgressed($teams[1]));
		$this->assertFalse($group->isProgressed($teams[0]));
		$this->assertFalse($group->isProgressed($teams[3]));

	}

	/** @test */
	public function check_add_progress_group() {

		$group = new \TournamentGenerator\Group('Name of group');
		$group2 = new \TournamentGenerator\Group('Name of group 2');

		for ($i=1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$progression = new \TournamentGenerator\Progression($group, $group2, 0, 2);

		$group->addProgression($progression); // Progress 2 teams

		$teams = $group->getTeams();

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

		$group->progress();

		$this->assertCount(2, $group2->getTeams());
		$this->assertTrue($group->isProgressed($teams[2]));
		$this->assertTrue($group->isProgressed($teams[1]));
		$this->assertFalse($group->isProgressed($teams[0]));
		$this->assertFalse($group->isProgressed($teams[3]));

	}

	/** @test */
	public function check_add_progressed_team_group() {

		$group = new \TournamentGenerator\Group('Name of group');

		for ($i=1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$teams = $group->getTeams();

		$group->addProgressed($teams[0], $teams[3]);
		$this->assertTrue($group->isProgressed($teams[0]));
		$this->assertTrue($group->isProgressed($teams[3]));
		$this->assertFalse($group->isProgressed($teams[1]));
		$this->assertFalse($group->isProgressed($teams[2]));

	}

	/** @test */
	public function check_progress_blank_group() {

		$group = new \TournamentGenerator\Group('Name of group');
		$group2 = new \TournamentGenerator\Group('Name of group 2');

		for ($i=1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$group->progression($group2, 0, 2); // Progress 2 teams

		$group->genGames();
		$group->simulate([], false);
		$group->progress(true);

		$this->assertCount(2, $group2->getTeams());

		foreach ($group2->getTeams() as $team) {
			$this->assertInstanceOf('\\TournamentGenerator\\BlankTeam', $team);
		}

	}

	/** @test */
	public function check_setting_in_game_group() {

		$group = new \TournamentGenerator\Group('Group name');

		$group->setInGame(3);
		$this->assertEquals(3, $group->getInGame());

		$group->setInGame(2);
		$this->assertEquals(2, $group->getInGame());

		$group->setInGame(4);
		$this->assertEquals(4, $group->getInGame());

		$this->expectException(Exception::class);
		$group->setInGame(1);

	}

	/** @test */
	public function check_setting_ordering_group() {

		$group = new \TournamentGenerator\Group('Group name');

		$group->setOrdering(\TournamentGenerator\Constants::POINTS);
		$this->assertEquals(\TournamentGenerator\Constants::POINTS, $group->getOrdering());

		$group->setOrdering(\TournamentGenerator\Constants::SCORE);
		$this->assertEquals(\TournamentGenerator\Constants::SCORE, $group->getOrdering());

		$this->expectException(Exception::class);
		$group->setOrdering('Totally not a valid ordering');

	}

	/** @test */
	public function check_setting_order_group() {

		$group = new \TournamentGenerator\Group('Group name');

		$group->setOrder(2);
		$this->assertEquals(2, $group->getOrder());

		$group->setOrder(10);
		$this->assertEquals(10, $group->getOrder());

		$this->expectException(TypeError::class);
		$group->setOrder('Totally not a valid order');

	}

	/** @test */
	public function check_setting_type_group() {

		$group = new \TournamentGenerator\Group('Group name');

		$group->setType(\TournamentGenerator\Constants::ROUND_TWO);
		$this->assertEquals(\TournamentGenerator\Constants::ROUND_TWO, $group->getType());

		$group->setType(\TournamentGenerator\Constants::ROUND_SPLIT);
		$this->assertEquals(\TournamentGenerator\Constants::ROUND_SPLIT, $group->getType());

		$group->setType(\TournamentGenerator\Constants::ROUND_ROBIN);
		$this->assertEquals(\TournamentGenerator\Constants::ROUND_ROBIN, $group->getType());

		$this->expectException(Exception::class);
		$group->setType('Totally not a valid type');
		$this->expectException(TypeError::class);
		$group->setType(2);

	}

	/** @test */
	public function check_setting_max_size_group() {

		$group = new \TournamentGenerator\Group('Group name');

		$group->setMaxSize(2);
		$this->assertEquals(2, $group->getMaxSize());

		$group->setMaxSize(10);
		$this->assertEquals(10, $group->getMaxSize());

		$this->expectException(TypeError::class);
		$group->setMaxSize('Totally not a valid max size');

		$this->expectException(Exception::class);
		$group->setMaxSize(1);

	}

	/** @test */
	public function check_gen_games_simulate_group() {

		$group = new \TournamentGenerator\Group('Group name');

		for ($i=1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$games = $group->genGames();
		$this->assertCount(6, $games);

		$teams = $group->simulate([], false);
		$this->assertCount(4, $teams);

		$this->assertTrue($group->isPlayed());

	}

	/** @test */
	public function check_gen_games_simulate_with_reset_group() {

		$group = new \TournamentGenerator\Group('Group name');

		for ($i=1; $i <= 4; $i++) {
			$group->team('Team '.$i);
		}

		$games = $group->genGames();
		$this->assertCount(6, $games);

		$teams = $group->simulate([], true);
		$this->assertCount(4, $teams);

		$this->assertFalse($group->isPlayed());

	}
}
