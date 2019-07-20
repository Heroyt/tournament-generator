<?php

/**
 *
 */
class Tournament_2R2G extends Tournament
{

	public function generate() {

		if (count($this->getTeams()) === 0) throw new Exception('Couldn\'t generate 2R2G tournament because there are no teams in the tournament.');


		$round1 = $this->round('Round 1');
		$round2 = $this->round('Round 2');
		$round3 = $this->round('Round 3');

		$group_0_0 = $round1->group([
			'name' => '0/0',
			'inGame' => 2,
			'type' => TWO_TWO,
		]);
		$group_0_1 = $round2->group([
			'name' => '0/1',
			'type' => TWO_TWO
		]);
		$group_1_0 = $round2->group([
			'name' => '1/0',
			'type' => TWO_TWO
		]);
		$group_1_1 = $round3->group([
			'name' => '1/1',
			'type' => COND_SPLIT,
			'maxSize' => 3
		]);
		$group_0_2 = $round3->group([
			'name' => '0/2',
			'type' => R_R
		]);
		$group_2_0 = $round3->group([
			'name' => '2/0',
			'type' => R_R
		]);

		$filter_win_1 = new TeamFilter('wins', '=', 1);
		$filter_loss_1 = new TeamFilter('losses', '=', 1);
		$filter_notProgressed = new TeamFilter('notprogressed');

		$this->splitTeams($round1);

		if (count($this->getTeams()) % 4 == 2) {
			$group_top = $round2->group([
				'name' => 'TOP',
				'type' => TWO_TWO
			]);

			$filter_win_2 = new TeamFilter('wins', '=', 2, [$group_0_0, $group_top]);
			$filter_loss_2 = new TeamFilter('losses', '=', 2, [$group_0_0, $group_top]);
			$filter_win_1_both = new TeamFilter('wins', '=', 1, [$group_0_0, $group_top]);
			$filter_loss_1_both = new TeamFilter('losses', '=', 1, [$group_0_0, $group_top]);
			$group_0_0->progression($group_top, 0, 1)->addFilter($filter_win_1); // PROGRESS THE BEST WINNING TEAM
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


?>
