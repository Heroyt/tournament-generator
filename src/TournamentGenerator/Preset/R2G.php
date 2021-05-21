<?php

namespace TournamentGenerator\Preset;

use Exception;
use TournamentGenerator\Constants;
use TournamentGenerator\TeamFilter;
use TournamentGenerator\Tournament;

/**
 * Special (testing) tournament type
 *
 * Split teams into 3 groups after 2 games based on how many games did they win - first group has teams with 2 wins and 0 losses, second: 1 win 1 loss, third: 0 wins 2 losses.
 * These groups then play round-robin.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @package TournamentGenerator\Preset
 * @since   0.1
 */
class R2G extends Tournament implements Preset
{

	/**
	 * Generate all the games
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function generate() : R2G {

		if (count($this->getTeams()) === 0) {
			throw new Exception('Couldn\'t generate 2R2G tournament because there are no teams in the tournament.');
		}


		$round1 = $this->round('Round 1');
		$round2 = $this->round('Round 2');
		$round3 = $this->round('Round 3');

		$group_0_0 = $round1->group('0/0')->setInGame(2)->setType(Constants::ROUND_TWO);
		$group_0_1 = $round2->group('0/1')->setInGame(2)->setType(Constants::ROUND_TWO);
		$group_1_0 = $round2->group('1/0')->setInGame(2)->setType(Constants::ROUND_TWO);
		$group_1_1 = $round3->group('1/1')->setInGame(2)->setType(Constants::ROUND_SPLIT)->setMaxSize(3);
		$group_0_2 = $round3->group('0/2')->setInGame(2); // Implicit Round-robin
		$group_2_0 = $round3->group('2/0')->setInGame(2); // Implicit Round-robin

		$filter_win_1 = new TeamFilter('wins', '=', 1);
		$filter_loss_1 = new TeamFilter('losses', '=', 1);
		$filter_notProgressed = new TeamFilter('not-progressed');

		$this->splitTeams($round1);

		if (count($this->getTeams()) % 4 === 2) {
			$group_top = $round2->group('TOP')->setType(Constants::ROUND_TWO);

			$filter_win_2 = new TeamFilter('wins', '=', 2, [$group_0_0, $group_top]);
			$filter_loss_2 = new TeamFilter('losses', '=', 2, [$group_0_0, $group_top]);
			$filter_win_1_both = new TeamFilter('wins', '=', 1, [$group_0_0, $group_top]);
			$filter_loss_1_both = new TeamFilter('losses', '=', 1, [$group_0_0, $group_top]);
			$group_0_0->progression($group_top, 0, 1)->addFilter($filter_win_1);  // PROGRESS THE BEST WINNING TEAM
			$group_0_0->progression($group_top, 0, 1)->addFilter($filter_loss_1); // PROGRESS THE BEST LOSING TEAM
			$group_top->progression($group_2_0)->addFilter($filter_win_2);
			$group_top->progression($group_0_2)->addFilter($filter_loss_2);
			$group_top->progression($group_1_1)->addFilter($filter_win_1_both, $filter_loss_1_both);
		}

		$group_0_0->progression($group_0_1)->addFilter($filter_loss_1)->addFilter($filter_notProgressed);
		$group_0_0->progression($group_1_0)->addFilter($filter_win_1)->addFilter($filter_notProgressed);
		$group_0_1->progression($group_0_2)->addFilter($filter_loss_1);
		$group_0_1->progression($group_1_1)->addFilter($filter_win_1);
		$group_1_0->progression($group_2_0)->addFilter($filter_win_1);
		$group_1_0->progression($group_1_1)->addFilter($filter_loss_1);

		return $this;

	}

}
