<?php

use PHPUnit\Framework\TestCase;

/**
 *
 */
class TournamentTest extends TestCase
{

	/** @test */
	public function check_if_name_is_setup_and_can_get_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		// Test getName() method
		$this->assertEquals('Name of tournament 1', $tournament->getName());

		// Test __toString() method
		$this->assertEquals('Name of tournament 1', (string) $tournament);

		// Test setName() method
		$tournament->setName('Name of tournament 2');
		$this->assertEquals('Name of tournament 2', $tournament->getName());

	}

	/** @test */
	public function check_play_time_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		// Test getPlay() method
		$tournament->setPlay(123);
		$this->assertEquals(123, $tournament->getPlay());

	}

	/** @test */
	public function check_game_wait_time_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		// Test getPlay() method
		$tournament->setGameWait(123);
		$this->assertEquals(123, $tournament->getGameWait());

	}

	/** @test */
	public function check_round_wait_time_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		// Test getPlay() method
		$tournament->setRoundWait(123);
		$this->assertEquals(123, $tournament->getRoundWait());

	}

	/** @test */
	public function check_category_wait_time_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		// Test getPlay() method
		$tournament->setCategoryWait(123);
		$this->assertEquals(123, $tournament->getCategoryWait());

	}

	/** @test */
	public function check_skip_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		// Test allowSkip() method
		$tournament->allowSkip();
		$this->assertTrue($tournament->getSkip());

		// Test disallowSkip() method
		$tournament->disallowSkip();
		$this->assertFalse($tournament->getSkip());

		// Test setSkip() method
		$tournament->setSkip(true);
		$this->assertTrue($tournament->getSkip());

	}

	/** @test */
	public function check_category_add_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		$category = new \TournamentGenerator\Category('Category name');
		$category2 = new \TournamentGenerator\Category('Category name 2');
		$category3 = new \TournamentGenerator\Category('Category name 3');

		// Test adding a single category
		$output = $tournament->addCategory($category);
		$this->assertCount(1, $tournament->getCategories());

		// Test if the output is $this
		$this->assertInstanceOf('\\TournamentGenerator\\Tournament', $output);

		// Test adding multiple categories
		$tournament->addCategory($category2, $category3);
		$this->assertCount(3, $tournament->getCategories());

		// Test adding not a category class
		$this->expectException(TypeError::class);
		$tournament->addCategory('totally not a Category class');

	}

	/** @test */
	public function check_category_creation_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		$category = $tournament->category('Category name');

		// Test if Category class is really created
		$this->assertInstanceOf('\\TournamentGenerator\\Category', $category);
		// Test if the category was added
		$this->assertCount(1, $tournament->getCategories());

	}

	/** @test */
	public function check_category_inherits_skip_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		$tournament->allowSkip();
		$category = $tournament->category('Category name');

		$this->assertTrue($category->getSkip());

	}

	/** @test */
	public function check_round_add_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		$round = new \TournamentGenerator\Round('Round name');
		$round2 = new \TournamentGenerator\Round('Round name 2');
		$round3 = new \TournamentGenerator\Round('Round name 3');

		// Test adding a single round
		$output = $tournament->addRound($round);
		$this->assertCount(1, $tournament->getRounds());

		// Test if the output is $this
		$this->assertInstanceOf('\\TournamentGenerator\\Tournament', $output);

		// Test adding multiple rounds
		$tournament->addRound($round2, $round3);
		$this->assertCount(3, $tournament->getRounds());

		// Test adding not a round class
		$this->expectException(TypeError::class);
		$tournament->addRound('totally not a Round class');

	}

	/** @test */
	public function check_round_creation_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		$round = $tournament->round('Round name');

		// Test if Round class is really created
		$this->assertInstanceOf('\\TournamentGenerator\\Round', $round);
		// Test if the round was added
		$this->assertCount(1, $tournament->getRounds());

	}

	/** @test */
	public function check_round_inherits_skip_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		$tournament->allowSkip();
		$round = $tournament->round('Round name');

		$this->assertTrue($round->getSkip());

	}

	/** @test */
	public function check_team_add_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		$team = new \TournamentGenerator\Team('Team name');
		$team2 = new \TournamentGenerator\Team('Team name 2');
		$team3 = new \TournamentGenerator\Team('Team name 3');

		// Test adding a single team
		$output = $tournament->addTeam($team);
		$this->assertCount(1, $tournament->getTeams());

		// Test if the output is $this
		$this->assertInstanceOf('\\TournamentGenerator\\Tournament', $output);

		// Test adding multiple teams
		$tournament->addTeam($team2, $team3);
		$this->assertCount(3, $tournament->getTeams());

		// Test adding not a team class
		$this->expectException(TypeError::class);
		$tournament->addTeam('totally not a Team class');

	}

	/** @test */
	public function check_team_creation_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		$team = $tournament->team('Team name');

		// Test if Team class is really created
		$this->assertInstanceOf('\\TournamentGenerator\\Team', $team);
		// Test if the team was added
		$this->assertCount(1, $tournament->getTeams());

	}

	/** @test */
	public function check_rounds_from_categories_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		$category1 = $tournament->category('Category 1');
		$category2 = $tournament->category('Category 2');

		$category1->round('Round1');
		$category1->round('Round2');

		$category2->round('Round3');
		$category2->round('Round4');

		$tournament->round('Round5');

		$this->assertCount(5, $tournament->getRounds());

	}

	/** @test */
	public function check_teams_from_categories_and_rounds_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		$category1 = $tournament->category('Category 1');
		$category2 = $tournament->category('Category 2');

		$round1 = $tournament->round('Round1');
		$round2 = $tournament->round('Round1');

		$category1->addTeam(new \TournamentGenerator\Team('Team1'),new \TournamentGenerator\Team('Team2'),new \TournamentGenerator\Team('Team3'));
		$category2->addTeam(new \TournamentGenerator\Team('Team4'),new \TournamentGenerator\Team('Team5'),new \TournamentGenerator\Team('Team6'));

		$round1->addTeam(new \TournamentGenerator\Team('Team7'),new \TournamentGenerator\Team('Team8'),new \TournamentGenerator\Team('Team9'));
		$round2->addTeam(new \TournamentGenerator\Team('Team10'),new \TournamentGenerator\Team('Team11'),new \TournamentGenerator\Team('Team12'));

		$tournament->addTeam(new \TournamentGenerator\Team('Team13'),new \TournamentGenerator\Team('Team14'),new \TournamentGenerator\Team('Team15'));

		$this->assertCount(15, $tournament->getTeams());
		// Test if teams does not duplicate
		$this->assertCount(15, $tournament->getTeams());

	}

	/** @test */
	public function check_split_teams_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		for ($i=1; $i <= 8; $i++) {
			$tournament->team('Team '.$i);
		}

		$round = $tournament->round('Round name');

		$group1 = $round->group('Group 1');
		$group2 = $round->group('Group 2');

		$tournament->splitTeams();

		$this->assertCount(4, $group1->getTeams());
		$this->assertCount(4, $group2->getTeams());

	}

	/** @test */
	public function check_split_teams_with_defined_groups_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		for ($i=1; $i <= 8; $i++) {
			$tournament->team('Team '.$i);
		}

		$round = $tournament->round('Round name');
		$round2 = $tournament->round('Round 2 name');

		$group1 = $round->group('Group 1');
		$group2 = $round->group('Group 2');
		$group3 = $round2->group('Group 3');

		$tournament->splitTeams($round);

		$this->assertCount(4, $group1->getTeams());
		$this->assertCount(4, $group2->getTeams());
		$this->assertCount(0, $group3->getTeams());

	}

	protected function gen_tournament() {
		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		$tournament
			->setPlay(7) // SET GAME TIME TO 7 MINUTES
			->setGameWait(2) // SET TIME BETWEEN GAMES TO 2 MINUTES
			->setRoundWait(0); // SET TIME BETWEEN ROUNDS TO 0 MINUTES

		for ($i=1; $i <= 8; $i++) {
			$tournament->team('Team '.$i);
		}
		// Create a round and a final round
		$round = $tournament->round("First's round's name");
		$final = $tournament->round("Final's round's name");

		// Create 2 groups for the first round
		$group_1 = $round->group('Round 1')->setInGame(2)->setType(TournamentGenerator\Constants::ROUND_ROBIN);
		$group_2 = $round->group('Round 2')->setInGame(2)->setType(TournamentGenerator\Constants::ROUND_ROBIN);

		// Create a final group
		$final_group = $final->group('Finale')->setInGame(2)->setType(TournamentGenerator\Constants::ROUND_ROBIN);

		$tournament->splitTeams($round);

		$group_1->progression($final_group, 0, 2); // PROGRESS 2 BEST WINNING TEAMS
		$group_2->progression($final_group, 0, 2); // PROGRESS 2 BEST WINNING TEAMS

		return $tournament;
	}

	/** @test */
	public function check_gen_games_simulate_tournament() {

		$tournament = $this->gen_tournament();

		$games = $tournament->genGamesSimulate();

		$this->assertCount(18, $games);

	}

	/** @test */
	public function check_gen_games_simulate_with_time_tournament() {

		$tournament = $this->gen_tournament();

		$time = $tournament->genGamesSimulate(true);

		$this->assertEquals(162, $time);

	}

	/** @test */
	public function check_gen_games_simulate_real_tournament() {

		$tournament = $this->gen_tournament();

		$games = $tournament->genGamesSimulateReal();

		$this->assertCount(18, $games);

	}

	/** @test */
	public function check_gen_games_simulate_real_with_time_tournament() {

		$tournament = $this->gen_tournament();

		$time = $tournament->genGamesSimulateReal(true);

		$this->assertEquals(162, $time);

	}

	/** @test */
	public function check_tournament_time_tournament() {

		$tournament = $this->gen_tournament();

		$tournament->genGamesSimulate();

		$this->assertEquals(162, $tournament->getTournamentTime());

	}

	/** @test */
	public function check_getting_games_tournament() {

		$tournament = $this->gen_tournament();

		$tournament->genGamesSimulate();

		$this->assertCount(18, $tournament->getGames());

	}

	/** @test */
	public function check_sorting_teams_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		$round = $tournament->round('Round name');

		$group = $round->group('Group name');

		for ($i=1; $i <= 4; $i++) {
			$tournament->team('Team '.$i);
		}

		$teams = $tournament->getTeams();

		$group->addTeam($teams);


		$group->game([$teams[0], $teams[1]])->setResults([$teams[0]->getId() => 2000, $teams[1]->getId() => 2001]);
		$group->game([$teams[2], $teams[3]])->setResults([$teams[2]->getId() => 100, $teams[3]->getId() => 99]);
		$group->game([$teams[0], $teams[2]])->setResults([$teams[0]->getId() => 199, $teams[2]->getId() => 200]);
		$group->game([$teams[1], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[1]->getId() => 5000]);
		$group->game([$teams[0], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[0]->getId() => 1001]);
		$group->game([$teams[1], $teams[2]])->setResults([$teams[1]->getId() => 99, $teams[2]->getId() => 100]);

		$teamsSorted = array_map(function($team) {
			return $team->getName();
		}, $tournament->sortTeams());

		$this->assertSame(['Team 3', 'Team 2', 'Team 1', 'Team 4'], $teamsSorted);

	}

	/** @test */
	public function check_sorting_teams_from_getTeams_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		$round = $tournament->round('Round name');

		$group = $round->group('Group name');

		for ($i=1; $i <= 4; $i++) {
			$tournament->team('Team '.$i);
		}

		$teams = $tournament->getTeams();

		$group->addTeam($teams);

		$group->game([$teams[0], $teams[1]])->setResults([$teams[0]->getId() => 2000, $teams[1]->getId() => 2001]);
		$group->game([$teams[2], $teams[3]])->setResults([$teams[2]->getId() => 100, $teams[3]->getId() => 99]);
		$group->game([$teams[0], $teams[2]])->setResults([$teams[0]->getId() => 199, $teams[2]->getId() => 200]);
		$group->game([$teams[1], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[1]->getId() => 5000]);
		$group->game([$teams[0], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[0]->getId() => 1001]);
		$group->game([$teams[1], $teams[2]])->setResults([$teams[1]->getId() => 99, $teams[2]->getId() => 100]);

		$teamsSorted = array_map(function($team) {
			return $team->getName();
		}, $tournament->getTeams(true));

		$this->assertSame(['Team 3', 'Team 2', 'Team 1', 'Team 4'], $teamsSorted);

	}

	/** @test */
	public function check_sorting_teams_by_score_tournament() {

		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

		$round = $tournament->round('Round name');

		$group = $round->group('Group name');

		for ($i=1; $i <= 4; $i++) {
			$tournament->team('Team '.$i);
		}

		$teams = $tournament->getTeams();

		$group->addTeam($teams);

		$group->game([$teams[0], $teams[1]])->setResults([$teams[0]->getId() => 2000, $teams[1]->getId() => 2001]);
		$group->game([$teams[2], $teams[3]])->setResults([$teams[2]->getId() => 100, $teams[3]->getId() => 99]);
		$group->game([$teams[0], $teams[2]])->setResults([$teams[0]->getId() => 199, $teams[2]->getId() => 200]);
		$group->game([$teams[1], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[1]->getId() => 5000]);
		$group->game([$teams[0], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[0]->getId() => 1001]);
		$group->game([$teams[1], $teams[2]])->setResults([$teams[1]->getId() => 99, $teams[2]->getId() => 100]);

		$teamsSorted = array_map(function($team) {
			return $team->getName();
		}, $tournament->sortTeams(\TournamentGenerator\Constants::SCORE));

		$this->assertSame(['Team 2', 'Team 1', 'Team 4', 'Team 3'], $teamsSorted);

	}

}
