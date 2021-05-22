<?php

use PHPUnit\Framework\TestCase;
use TournamentGenerator\Category;
use TournamentGenerator\Constants;
use TournamentGenerator\Round;
use TournamentGenerator\Team;
use TournamentGenerator\Tournament;

/**
 *
 */
class TournamentTest extends TestCase
{

	/** @test */
	public function check_if_name_is_setup_and_can_get_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		// Test getName() method
		self::assertEquals('Name of tournament 1', $tournament->getName());

		// Test __toString() method
		self::assertEquals('Name of tournament 1', (string) $tournament);

		// Test setName() method
		$tournament->setName('Name of tournament 2');
		self::assertEquals('Name of tournament 2', $tournament->getName());

	}

	/** @test */
	public function check_play_time_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		// Test getPlay() method
		$tournament->setPlay(123);
		self::assertEquals(123, $tournament->getPlay());

	}

	/** @test */
	public function check_game_wait_time_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		// Test getPlay() method
		$tournament->setGameWait(123);
		self::assertEquals(123, $tournament->getGameWait());

	}

	/** @test */
	public function check_round_wait_time_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		// Test getPlay() method
		$tournament->setRoundWait(123);
		self::assertEquals(123, $tournament->getRoundWait());

	}

	/** @test */
	public function check_category_wait_time_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		// Test getPlay() method
		$tournament->setCategoryWait(123);
		self::assertEquals(123, $tournament->getCategoryWait());

	}

	/** @test */
	public function check_skip_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		// Test allowSkip() method
		$tournament->allowSkip();
		self::assertTrue($tournament->getSkip());

		// Test disallowSkip() method
		$tournament->disallowSkip();
		self::assertFalse($tournament->getSkip());

		// Test setSkip() method
		$tournament->setSkip(true);
		self::assertTrue($tournament->getSkip());

	}

	/** @test */
	public function check_category_add_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		$category = new Category('Category name');
		$category2 = new Category('Category name 2');
		$category3 = new Category('Category name 3');

		// Test adding a single category
		$output = $tournament->addCategory($category);
		self::assertCount(1, $tournament->getCategories());

		// Test if the output is $this
		self::assertInstanceOf('\\TournamentGenerator\\Tournament', $output);

		// Test adding multiple categories
		$tournament->addCategory($category2, $category3);
		self::assertCount(3, $tournament->getCategories());

		// Test adding not a category class
		$this->expectException(TypeError::class);
		$tournament->addCategory('totally not a Category class');

	}

	/** @test */
	public function check_category_creation_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		$category = $tournament->category('Category name');

		// Test if Category class is really created
		self::assertInstanceOf('\\TournamentGenerator\\Category', $category);
		// Test if the category was added
		self::assertCount(1, $tournament->getCategories());

	}

	/** @test */
	public function check_category_inherits_skip_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		$tournament->allowSkip();
		$category = $tournament->category('Category name');

		self::assertTrue($category->getSkip());

	}

	/** @test */
	public function check_round_add_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		$round = new Round('Round name');
		$round2 = new Round('Round name 2');
		$round3 = new Round('Round name 3');

		// Test adding a single round
		$output = $tournament->addRound($round);
		self::assertCount(1, $tournament->getRounds());

		// Test if the output is $this
		self::assertInstanceOf('\\TournamentGenerator\\Tournament', $output);

		// Test adding multiple rounds
		$tournament->addRound($round2, $round3);
		self::assertCount(3, $tournament->getRounds());

		// Test adding not a round class
		$this->expectException(TypeError::class);
		$tournament->addRound('totally not a Round class');

	}

	/** @test */
	public function check_round_creation_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		$round = $tournament->round('Round name');

		// Test if Round class is really created
		self::assertInstanceOf('\\TournamentGenerator\\Round', $round);
		// Test if the round was added
		self::assertCount(1, $tournament->getRounds());

	}

	/** @test */
	public function check_round_inherits_skip_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		$tournament->allowSkip();
		$round = $tournament->round('Round name');

		self::assertTrue($round->getSkip());

	}

	/** @test */
	public function check_team_add_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		$team = new Team('Team name');
		$team2 = new Team('Team name 2');
		$team3 = new Team('Team name 3');

		// Test adding a single team
		$output = $tournament->addTeam($team);
		self::assertCount(1, $tournament->getTeams());

		// Test if the output is $this
		self::assertInstanceOf('\\TournamentGenerator\\Tournament', $output);

		// Test adding multiple teams
		$tournament->addTeam($team2, $team3);
		self::assertCount(3, $tournament->getTeams());

		// Test adding not a team class
		$this->expectException(TypeError::class);
		$tournament->addTeam('totally not a Team class');

	}

	/** @test */
	public function check_team_creation_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		$team = $tournament->team('Team name');

		// Test if Team class is really created
		self::assertInstanceOf('\\TournamentGenerator\\Team', $team);
		// Test if the team was added
		self::assertCount(1, $tournament->getTeams());

	}

	/** @test */
	public function check_rounds_from_categories_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		$category1 = $tournament->category('Category 1');
		$category2 = $tournament->category('Category 2');

		$category1->round('Round1');
		$category1->round('Round2');

		$category2->round('Round3');
		$category2->round('Round4');

		self::assertCount(4, $tournament->getRounds());

		$this->expectException(InvalidArgumentException::class);
		$tournament->round('Round5');
	}

	/** @test */
	public function check_teams_from_categories_and_rounds_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		$category1 = $tournament->category('Category 1');
		$category2 = $tournament->category('Category 2');

		$round1 = $category1->round('Round1');
		$round2 = $category2->round('Round1');

		$category1->addTeam(new Team('Team1'), new Team('Team2'), new Team('Team3'));
		$category2->addTeam(new Team('Team4'), new Team('Team5'), new Team('Team6'));

		$round1->addTeam(new Team('Team7'), new Team('Team8'), new Team('Team9'));
		$round2->addTeam(new Team('Team10'), new Team('Team11'), new Team('Team12'));

		$tournament->addTeam(new Team('Team13'), new Team('Team14'), new Team('Team15'));

		self::assertCount(15, $tournament->getTeams());
		// Test if teams does not duplicate
		self::assertCount(15, $tournament->getTeams());

	}

	/** @test */
	public function check_split_teams_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		for ($i = 1; $i <= 8; $i++) {
			$tournament->team('Team '.$i);
		}

		$round = $tournament->round('Round name');

		$group1 = $round->group('Group 1');
		$group2 = $round->group('Group 2');

		$tournament->splitTeams();

		self::assertCount(4, $group1->getTeams());
		self::assertCount(4, $group2->getTeams());

	}

	/** @test */
	public function check_split_teams_with_defined_groups_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		for ($i = 1; $i <= 8; $i++) {
			$tournament->team('Team '.$i);
		}

		$round = $tournament->round('Round name');
		$round2 = $tournament->round('Round 2 name');

		$group1 = $round->group('Group 1');
		$group2 = $round->group('Group 2');
		$group3 = $round2->group('Group 3');

		$tournament->splitTeams($round);

		self::assertCount(4, $group1->getTeams());
		self::assertCount(4, $group2->getTeams());
		self::assertCount(0, $group3->getTeams());

	}

	/** @test */
	public function check_gen_games_simulate_tournament() : void {

		$tournament = $this->gen_tournament();

		$games = $tournament->genGamesSimulate();

		self::assertCount(18, $games);
	}

	protected function gen_tournament() : Tournament {
		$tournament = new Tournament('Name of tournament 1');

		$tournament
			->setPlay(7)     // SET GAME TIME TO 7 MINUTES
			->setGameWait(2) // SET TIME BETWEEN GAMES TO 2 MINUTES
			->setRoundWait(0); // SET TIME BETWEEN ROUNDS TO 0 MINUTES

		for ($i = 1; $i <= 8; $i++) {
			$tournament->team('Team '.$i);
		}
		// Create a round and a final round
		$round = $tournament->round("First's round's name");
		$final = $tournament->round("Final's round's name");

		// Create 2 groups for the first round
		$group_1 = $round->group('Round 1')->setInGame(2);
		$group_2 = $round->group('Round 2')->setInGame(2);

		// Create a final group
		$final_group = $final->group('Finale')->setInGame(2);

		$tournament->splitTeams($round);

		$group_1->progression($final_group, 0, 2); // PROGRESS 2 BEST WINNING TEAMS
		$group_2->progression($final_group, 0, 2); // PROGRESS 2 BEST WINNING TEAMS

		return $tournament;
	}

	/** @test */
	public function check_gen_games_simulate_tournament_empty() : void {

		$tournament = new Tournament('Name of tournament 1');

		for ($i = 1; $i <= 8; $i++) {
			$tournament->team('Team '.$i);
		}

		$this->expectException(Exception::class);
		$tournament->genGamesSimulate();
	}

	/** @test */
	public function check_gen_games_simulate_real_tournament_empty() : void {

		$tournament = new Tournament('Name of tournament 1');

		for ($i = 1; $i <= 8; $i++) {
			$tournament->team('Team '.$i);
		}

		$this->expectException(Exception::class);
		$tournament->genGamesSimulateReal();
	}

	/** @test */
	public function check_gen_games_simulate_with_time_tournament() : void {

		$tournament = $this->gen_tournament();

		$time = $tournament->genGamesSimulate(true);

		self::assertEquals(160, $time);

	}

	/** @test */
	public function check_gen_games_simulate_real_tournament() : void {

		$tournament = $this->gen_tournament();

		$games = $tournament->genGamesSimulateReal();

		self::assertCount(18, $games);

	}

	/** @test */
	public function check_gen_games_simulate_real_with_time_tournament() : void {

		$tournament = $this->gen_tournament();

		$time = $tournament->genGamesSimulateReal(true);

		self::assertEquals(160, $time);

	}

	/** @test */
	public function check_gen_games_simulate_with_categories_tournament() : void {

		$tournament = $this->gen_tournament_with_categories();

		$games = $tournament->genGamesSimulate();

		self::assertCount(36, $games);

	}

	protected function gen_tournament_with_categories() : Tournament {
		$tournament = new Tournament('Name of tournament 1');

		$tournament
			->setPlay(7) // SET GAME TIME TO 7 MINUTES
			->setGameWait(2)  // SET TIME BETWEEN GAMES TO 2 MINUTES
			->setRoundWait(0) // SET TIME BETWEEN ROUNDS TO 0 MINUTES
			->setCategoryWait(10); // SET TIME BETWEEN CATEGORIES TO 10 MINUTES

		for ($i = 1; $i <= 16; $i++) {
			$tournament->team('Team '.$i);
		}

		// Create categories
		$category1 = $tournament->category('Category 1');
		$category2 = $tournament->category('Category 2');

		// Create a round and a final round for Category 1
		$round = $category1->round("First's round's name");
		$final = $category1->round("Final's round's name");

		// Create a round and a final round for Category 2
		$round2 = $category2->round("First's round's name");
		$final2 = $category2->round("Final's round's name");

		// Create 2 groups for the first round for Category 1
		$group_1 = $round->group('Group 1')->setInGame(2);
		$group_2 = $round->group('Group 2')->setInGame(2);

		// Create 2 groups for the first round fir Category 2
		$group2_1 = $round2->group('Group 1')->setInGame(2);
		$group2_2 = $round2->group('Group 2')->setInGame(2);

		// Create a final groups
		$final_group = $final->group('Finale')->setInGame(2);
		$final_group2 = $final2->group('Finale')->setInGame(2);

		$tournament->splitTeams($round, $round2);

		self::assertCount(8, $round->getTeams());
		self::assertCount(4, $group_1->getTeams());
		self::assertCount(4, $group_2->getTeams());
		self::assertCount(8, $round2->getTeams());
		self::assertCount(4, $group2_1->getTeams());
		self::assertCount(4, $group2_2->getTeams());

		$group_1->progression($final_group, 0, 2);   // PROGRESS 2 BEST WINNING TEAMS
		$group_2->progression($final_group, 0, 2);   // PROGRESS 2 BEST WINNING TEAMS
		$group2_1->progression($final_group2, 0, 2); // PROGRESS 2 BEST WINNING TEAMS
		$group2_2->progression($final_group2, 0, 2); // PROGRESS 2 BEST WINNING TEAMS

		return $tournament;
	}

	/** @test */
	public function check_gen_games_simulate_with_categories_with_time_tournament() : void {

		$tournament = $this->gen_tournament_with_categories();

		$time = $tournament->genGamesSimulate(true);

		self::assertEquals(161 * 2 + 10, $time);

	}

	/** @test */
	public function check_gen_games_simulate_with_categories_real_tournament() : void {

		$tournament = $this->gen_tournament_with_categories();

		$games = $tournament->genGamesSimulateReal();

		self::assertCount(36, $games);

	}

	/** @test */
	public function check_gen_games_simulate_with_categories_real_with_time_tournament() : void {

		$tournament = $this->gen_tournament_with_categories();

		$time = $tournament->genGamesSimulateReal(true);

		self::assertEquals(161 * 2 + 10, $time);

	}

	/** @test */
	public function check_tournament_time_tournament() : void {

		$tournament = $this->gen_tournament();

		$tournament->genGamesSimulate();

		self::assertEquals(160, $tournament->getTournamentTime());

	}

	/** @test */
	public function check_getting_games_tournament() : void {

		$tournament = $this->gen_tournament();

		$tournament->genGamesSimulate();

		self::assertCount(18, $tournament->getGames());

	}

	/** @test */
	public function check_sorting_teams_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		$round = $tournament->round('Round name');

		$group = $round->group('Group name');

		for ($i = 1; $i <= 4; $i++) {
			$tournament->team('Team '.$i);
		}

		$teams = $tournament->getTeams();

		$group->addTeam(...$teams);


		$group->game([$teams[0], $teams[1]])->setResults([$teams[0]->getId() => 2000, $teams[1]->getId() => 2001]);
		$group->game([$teams[2], $teams[3]])->setResults([$teams[2]->getId() => 100, $teams[3]->getId() => 99]);
		$group->game([$teams[0], $teams[2]])->setResults([$teams[0]->getId() => 199, $teams[2]->getId() => 200]);
		$group->game([$teams[1], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[1]->getId() => 5000]);
		$group->game([$teams[0], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[0]->getId() => 1001]);
		$group->game([$teams[1], $teams[2]])->setResults([$teams[1]->getId() => 99, $teams[2]->getId() => 100]);

		$teamsSorted = array_map(function($team) {
			return $team->getName();
		}, $tournament->sortTeams());

		self::assertSame(['Team 3', 'Team 2', 'Team 1', 'Team 4'], $teamsSorted);

	}

	/** @test */
	public function check_sorting_teams_from_getTeams_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		$round = $tournament->round('Round name');

		$group = $round->group('Group name');

		for ($i = 1; $i <= 4; $i++) {
			$tournament->team('Team '.$i);
		}

		$teams = $tournament->getTeams();

		$group->addTeam(...$teams);

		$group->game([$teams[0], $teams[1]])->setResults([$teams[0]->getId() => 2000, $teams[1]->getId() => 2001]);
		$group->game([$teams[2], $teams[3]])->setResults([$teams[2]->getId() => 100, $teams[3]->getId() => 99]);
		$group->game([$teams[0], $teams[2]])->setResults([$teams[0]->getId() => 199, $teams[2]->getId() => 200]);
		$group->game([$teams[1], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[1]->getId() => 5000]);
		$group->game([$teams[0], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[0]->getId() => 1001]);
		$group->game([$teams[1], $teams[2]])->setResults([$teams[1]->getId() => 99, $teams[2]->getId() => 100]);

		$teamsSorted = array_map(static function($team) {
			return $team->getName();
		}, $tournament->getTeams(true));

		self::assertSame(['Team 3', 'Team 2', 'Team 1', 'Team 4'], $teamsSorted);

	}

	/** @test */
	public function check_sorting_teams_by_score_tournament() : void {

		$tournament = new Tournament('Name of tournament 1');

		$round = $tournament->round('Round name');

		$group = $round->group('Group name');

		for ($i = 1; $i <= 4; $i++) {
			$tournament->team('Team '.$i);
		}

		$teams = $tournament->getTeams();

		$group->addTeam(...$teams);

		$group->game([$teams[0], $teams[1]])->setResults([$teams[0]->getId() => 1000, $teams[1]->getId() => 2001]);
		$group->game([$teams[2], $teams[3]])->setResults([$teams[2]->getId() => 100, $teams[3]->getId() => 99]);
		$group->game([$teams[0], $teams[2]])->setResults([$teams[0]->getId() => 199, $teams[2]->getId() => 200]);
		$group->game([$teams[1], $teams[3]])->setResults([$teams[3]->getId() => 1101, $teams[1]->getId() => 5000]);
		$group->game([$teams[0], $teams[3]])->setResults([$teams[3]->getId() => 1000, $teams[0]->getId() => 1001]);
		$group->game([$teams[1], $teams[2]])->setResults([$teams[1]->getId() => 99, $teams[2]->getId() => 100]);

		$teamsSorted = array_map(static function($team) {
			return $team->getName();
		}, $tournament->sortTeams(Constants::SCORE));

		self::assertSame(['Team 2', 'Team 1', 'Team 4', 'Team 3'], $teamsSorted);

		self::assertSame(
			[
				'points' => 3,
				'score'  => 2200,
				'wins'   => 1,
				'draws'  => 0,
				'losses' => 2,
				'second' => 0,
				'third'  => 0,
			],
			$teams[0]->getGamesInfo($group->getId())
		);

	}

}
