<?php
use PHPUnit\Framework\TestCase;

/**
 *
 */
class CategoryTest extends TestCase
{

	/** @test */
	public function check_name_setup_category() {
		$category = new \TournamentGenerator\Category('Category name 1');

		$this->assertEquals('Category name 1', $category->getName());
		$this->assertEquals('Category name 1', (string) $category);

		$category->setName('Category name 2');

		$this->assertEquals('Category name 2', $category->getName());
	}

	/** @test */
	public function check_id_category_round() {
		$category = new \TournamentGenerator\Category('Category name 1', 123);

		$this->assertEquals(123, $category->getId());

		$category->setId('ID2');

		$this->assertEquals('ID2', $category->getId());

		$this->expectException(Exception::class);
		$category->setId(['This', 'is', 'not', 'a', 'valid' => 'id']);
	}

	/** @test */
	public function check_round_add_category() {

		$category = new \TournamentGenerator\Category('Name of category 1');

		$round = new \TournamentGenerator\Round('Round name');
		$round2 = new \TournamentGenerator\Round('Round name 2');
		$round3 = new \TournamentGenerator\Round('Round name 3');

		// Test adding a single round
		$output = $category->addRound($round);
		$this->assertCount(1, $category->getRounds());

		// Test if the output is $this
		$this->assertInstanceOf('\\TournamentGenerator\\Category', $output);

		// Test adding multiple rounds
		$category->addRound($round2, $round3);
		$this->assertCount(3, $category->getRounds());

		// Test adding not a round class
		$this->expectException(TypeError::class);
		$category->addRound('totally not a Round class');

	}

	/** @test */
	public function check_round_creation_category() {

		$category = new \TournamentGenerator\Category('Name of category 1');

		$round = $category->round('Round name');

		// Test if Round class is really created
		$this->assertInstanceOf('\\TournamentGenerator\\Round', $round);
		// Test if the round was added
		$this->assertCount(1, $category->getRounds());

	}

	/** @test */
	public function check_skip_category() {

		$category = new \TournamentGenerator\Category('Name of category 1');

		// Test allowSkip() method
		$category->allowSkip();
		$this->assertTrue($category->getSkip());

		// Test disallowSkip() method
		$category->disallowSkip();
		$this->assertFalse($category->getSkip());

		// Test setSkip() method
		$category->setSkip(true);
		$this->assertTrue($category->getSkip());

	}

	/** @test */
	public function check_round_inherits_skip_category() {

		$category = new \TournamentGenerator\Category('Name of category 1');

		$category->allowSkip();
		$round = $category->round('Round name');

		$this->assertTrue($round->getSkip());

	}

	/** @test */
	public function check_team_add_category() {

		$category = new \TournamentGenerator\Category('Name of category 1');

		$team = new \TournamentGenerator\Team('Team name');
		$team2 = new \TournamentGenerator\Team('Team name 2');
		$team3 = new \TournamentGenerator\Team('Team name 3');

		// Test adding a single team
		$output = $category->addTeam($team);
		$this->assertCount(1, $category->getTeams());

		// Test if the output is $this
		$this->assertInstanceOf('\\TournamentGenerator\\Category', $output);

		// Test adding multiple teams
		$category->addTeam($team2, $team3);
		$this->assertCount(3, $category->getTeams());

		// Test adding not a team class
		$this->expectException(TypeError::class);
		$category->addTeam('totally not a Team class');

	}

	/** @test */
	public function check_team_creation_category() {

		$category = new \TournamentGenerator\Category('Name of category 1');

		$team = $category->team('Team name');

		// Test if Team class is really created
		$this->assertInstanceOf('\\TournamentGenerator\\Team', $team);
		// Test if the team was added
		$this->assertCount(1, $category->getTeams());

	}

	/** @test */
	public function check_teams_from_rounds_category() {

		$category = new \TournamentGenerator\Category('Name of category 1');

		$round1 = $category->round('Round1');
		$round2 = $category->round('Round1');

		$round1->addTeam(new \TournamentGenerator\Team('Team1'),new \TournamentGenerator\Team('Team2'),new \TournamentGenerator\Team('Team3'));
		$round2->addTeam(new \TournamentGenerator\Team('Team4'),new \TournamentGenerator\Team('Team5'),new \TournamentGenerator\Team('Team6'));

		$round1->addTeam(new \TournamentGenerator\Team('Team7'),new \TournamentGenerator\Team('Team8'),new \TournamentGenerator\Team('Team9'));
		$round2->addTeam(new \TournamentGenerator\Team('Team10'),new \TournamentGenerator\Team('Team11'),new \TournamentGenerator\Team('Team12'));

		$category->addTeam(new \TournamentGenerator\Team('Team13'),new \TournamentGenerator\Team('Team14'),new \TournamentGenerator\Team('Team15'));

		$this->assertCount(15, $category->getTeams());
		// Test if teams does not duplicate
		$this->assertCount(15, $category->getTeams());

	}

	/** @test */
	public function check_split_teams_category() {

		$category = new \TournamentGenerator\Category('Name of category 1');

		for ($i=1; $i <= 8; $i++) {
			$category->team('Team '.$i);
		}

		$round = $category->round('Round name');

		$group1 = $round->group('Group 1');
		$group2 = $round->group('Group 2');

		$category->splitTeams();

		$this->assertCount(4, $group1->getTeams());
		$this->assertCount(4, $group2->getTeams());

	}

	/** @test */
	public function check_split_teams_with_defined_rounds_category() {

		$category = new \TournamentGenerator\Category('Name of category 1');

		for ($i=1; $i <= 8; $i++) {
			$category->team('Team '.$i);
		}

		$round = $category->round('Round name');
		$round2 = $category->round('Round 2 name');

		$group1 = $round->group('Group 1');
		$group2 = $round->group('Group 2');
		$group3 = $round2->group('Group 3');

		$category->splitTeams($round);

		$this->assertCount(4, $group1->getTeams());
		$this->assertCount(4, $group2->getTeams());
		$this->assertCount(0, $group3->getTeams());

	}

	protected function gen_category() {
		$category = new \TournamentGenerator\Category('Name of category 1');

		for ($i=1; $i <= 8; $i++) {
			$category->team('Team '.$i);
		}
		// Create a round and a final round
		$round = $category->round("First's round's name");
		$final = $category->round("Final's round's name");

		// Create 2 groups for the first round
		$group_1 = $round->group('Round 1')->setInGame(2)->setType(TournamentGenerator\Constants::ROUND_ROBIN);
		$group_2 = $round->group('Round 2')->setInGame(2)->setType(TournamentGenerator\Constants::ROUND_ROBIN);

		// Create a final group
		$final_group = $final->group('Finale')->setInGame(2)->setType(TournamentGenerator\Constants::ROUND_ROBIN);

		$category->splitTeams($round);

		$group_1->progression($final_group, 0, 2); // PROGRESS 2 BEST WINNING TEAMS
		$group_2->progression($final_group, 0, 2); // PROGRESS 2 BEST WINNING TEAMS

		return $category;
	}

	/** @test */
	public function check_gen_games_simulate_category() {

		$category = $this->gen_category();

		$games = $category->genGamesSimulate();

		$this->assertCount(18, $games);

		$this->assertCount(18, $category->getGames());

	}

	/** @test */
	public function check_gen_games_simulate_real_category() {

		$category = $this->gen_category();

		$games = $category->genGamesSimulateReal();

		$this->assertCount(18, $games);

		$this->assertCount(18, $category->getGames());

	}

	/** @test */
	public function check_sorting_teams_category() {

		$category = new \TournamentGenerator\Category('Name of category 1');

		$round = $category->round('Round name');

		$group = $round->group('Group name');

		for ($i=1; $i <= 4; $i++) {
			$category->team('Team '.$i);
		}

		$teams = $category->getTeams();

		$group->addTeam($teams);


		$group->game([$teams[0], $teams[1]])->setResults([$teams[0]->getId() => 2000, $teams[1]->getId() => 2001]);
		$group->game([$teams[2], $teams[3]])->setResults([$teams[2]->getId() => 100, $teams[3]->getId() => 99]);
		$group->game([$teams[0], $teams[2]])->setResults([$teams[0]->getId() => 199, $teams[2]->getId() => 200]);
		$group->game([$teams[1], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[1]->getId() => 5000]);
		$group->game([$teams[0], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[0]->getId() => 1001]);
		$group->game([$teams[1], $teams[2]])->setResults([$teams[1]->getId() => 99, $teams[2]->getId() => 100]);

		$teamsSorted = array_map(function($team) {
			return $team->getName();
		}, $category->sortTeams());

		$this->assertSame(['Team 3', 'Team 2', 'Team 1', 'Team 4'], $teamsSorted);

	}

	/** @test */
	public function check_sorting_teams_from_getTeams_category() {

		$category = new \TournamentGenerator\Category('Name of category 1');

		$round = $category->round('Round name');

		$group = $round->group('Group name');

		for ($i=1; $i <= 4; $i++) {
			$category->team('Team '.$i);
		}

		$teams = $category->getTeams();

		$group->addTeam($teams);

		$group->game([$teams[0], $teams[1]])->setResults([$teams[0]->getId() => 2000, $teams[1]->getId() => 2001]);
		$group->game([$teams[2], $teams[3]])->setResults([$teams[2]->getId() => 100, $teams[3]->getId() => 99]);
		$group->game([$teams[0], $teams[2]])->setResults([$teams[0]->getId() => 199, $teams[2]->getId() => 200]);
		$group->game([$teams[1], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[1]->getId() => 5000]);
		$group->game([$teams[0], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[0]->getId() => 1001]);
		$group->game([$teams[1], $teams[2]])->setResults([$teams[1]->getId() => 99, $teams[2]->getId() => 100]);

		$teamsSorted = array_map(function($team) {
			return $team->getName();
		}, $category->getTeams(true));

		$this->assertSame(['Team 3', 'Team 2', 'Team 1', 'Team 4'], $teamsSorted);

	}

	/** @test */
	public function check_sorting_teams_by_score_category() {

		$category = new \TournamentGenerator\Category('Name of category 1');

		$round = $category->round('Round name');

		$group = $round->group('Group name');

		for ($i=1; $i <= 4; $i++) {
			$category->team('Team '.$i);
		}

		$teams = $category->getTeams();

		$group->addTeam($teams);

		$group->game([$teams[0], $teams[1]])->setResults([$teams[0]->getId() => 2000, $teams[1]->getId() => 2001]);
		$group->game([$teams[2], $teams[3]])->setResults([$teams[2]->getId() => 100, $teams[3]->getId() => 99]);
		$group->game([$teams[0], $teams[2]])->setResults([$teams[0]->getId() => 199, $teams[2]->getId() => 200]);
		$group->game([$teams[1], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[1]->getId() => 5000]);
		$group->game([$teams[0], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[0]->getId() => 1001]);
		$group->game([$teams[1], $teams[2]])->setResults([$teams[1]->getId() => 99, $teams[2]->getId() => 100]);

		$teamsSorted = array_map(function($team) {
			return $team->getName();
		}, $category->sortTeams(\TournamentGenerator\Constants::SCORE));

		$this->assertSame(['Team 2', 'Team 1', 'Team 4', 'Team 3'], $teamsSorted);

	}

}
