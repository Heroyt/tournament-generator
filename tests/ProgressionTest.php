<?php
use PHPUnit\Framework\TestCase;

/**
 *
 */
class ProgressionTest extends TestCase
{

	/** @test */
	public function check_progressing() {
		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

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
		$final_group = $final->group('Teams 1-4')->setInGame(2)->setType(TournamentGenerator\Constants::ROUND_ROBIN);
		$second_group = $final->group('Teams 5-8')->setInGame(2)->setType(TournamentGenerator\Constants::ROUND_ROBIN);

		$tournament->splitTeams($round);

		$group_1->progression($final_group, 0, 2); // PROGRESS 2 BEST WINNING TEAMS
		$group_2->progression($final_group, 0, 2); // PROGRESS 2 BEST WINNING TEAMS
		$group_1->progression($second_group, 2, 2); // PROGRESS 2 BEST WINNING TEAMS
		$group_2->progression($second_group, 2, 2); // PROGRESS 2 BEST WINNING TEAMS

		$round->simulate();

		$round->progress();

		$this->assertCount(4, $final_group->getTeams());
		$this->assertCount(4, $second_group->getTeams());
	}

	/** @test */
	public function check_progressing_blank() {
		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

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
		$final_group = $final->group('Teams 1-4')->setInGame(2)->setType(TournamentGenerator\Constants::ROUND_ROBIN);
		$second_group = $final->group('Teams 5-8')->setInGame(2)->setType(TournamentGenerator\Constants::ROUND_ROBIN);

		$tournament->splitTeams($round);

		$group_1->progression($final_group, 0, 2); // PROGRESS 2 BEST WINNING TEAMS
		$group_2->progression($final_group, 0, 2); // PROGRESS 2 BEST WINNING TEAMS
		$group_1->progression($second_group, 2, 2); // PROGRESS 2 BEST WINNING TEAMS
		$group_2->progression($second_group, 2, 2); // PROGRESS 2 BEST WINNING TEAMS

		$round->simulate();

		$round->progress(true);

		$this->assertCount(4, $final_group->getTeams());
		$this->assertCount(4, $second_group->getTeams());

		$this->assertInstanceOf('\\TournamentGenerator\\BlankTeam', $final_group->getTeams()[0]);
		$this->assertInstanceOf('\\TournamentGenerator\\BlankTeam', $second_group->getTeams()[0]);
	}

	/** @test */
	public function check_progressing_with_filters() {
		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

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
		$final_group = $final->group('Teams 1-4')->setInGame(2)->setType(TournamentGenerator\Constants::ROUND_ROBIN);
		$second_group = $final->group('Teams 5-8')->setInGame(2)->setType(TournamentGenerator\Constants::ROUND_ROBIN);

		$tournament->splitTeams($round);

		$filter1 = new \TournamentGenerator\TeamFilter('points', '>', 3, [$group_1, $group_2]);
		$filter2 = new \TournamentGenerator\TeamFilter('losses', '<=', 3, [$group_1, $group_2]);

		$group_1->progression($final_group)->addfilter($filter1);
		$group_2->progression($final_group)->addFilter($filter1);
		$group_1->progression($second_group)->addFilter($filter2);
		$group_2->progression($second_group)->addFilter($filter2);

		$round->genGames();

		$round->simulate();

		$round->progress();

		$filtered1 = $round->getTeams(false, null, [$filter1]);
		$filtered2 = $round->getTeams(false, null, [$filter2]);

		$this->assertCount(count($filtered1), $final_group->getTeams());
		$this->assertCount(count($filtered2), $second_group->getTeams());
	}

	/** @test */
	public function check_progressing_blank_wint_filters() {
		$tournament = new \TournamentGenerator\Tournament('Name of tournament 1');

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
		$final_group = $final->group('Teams 1-4')->setInGame(2)->setType(TournamentGenerator\Constants::ROUND_ROBIN);
		$second_group = $final->group('Teams 5-8')->setInGame(2)->setType(TournamentGenerator\Constants::ROUND_ROBIN);

		$tournament->splitTeams($round);

		$filter1 = new \TournamentGenerator\TeamFilter('points', '>', 3, [$group_1, $group_2]);
		$filter2 = new \TournamentGenerator\TeamFilter('losses', '<=', 3, [$group_1, $group_2]);

		$group_1->progression($final_group)->addfilter($filter1);
		$group_2->progression($final_group)->addFilter($filter1);
		$group_1->progression($second_group)->addFilter($filter2);
		$group_2->progression($second_group)->addFilter($filter2);

		$round->genGames();

		$round->simulate();

		$round->progress(true);

		$this->assertCount(count($round->getTeams(false, null, [$filter1])), $final_group->getTeams());
		$this->assertCount(count($round->getTeams(false, null, [$filter2])), $second_group->getTeams());

		$this->assertInstanceOf('\\TournamentGenerator\\BlankTeam', $final_group->getTeams()[0]);
		$this->assertInstanceOf('\\TournamentGenerator\\BlankTeam', $second_group->getTeams()[0]);
	}
}
