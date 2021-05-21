<?php

namespace TournamentGenerator\Preset;

use Exception;
use TournamentGenerator\Constants;
use TournamentGenerator\Helpers\Functions;
use TournamentGenerator\Tournament;

/**
 * Single elimination generator
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @package TournamentGenerator\Preset
 * @since   0.1
 */
class SingleElimination extends Tournament implements Preset
{

	/**
	 * Generate all games
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function generate() : SingleElimination {

		$this->allowSkip();

		$countTeams = count($this->getTeams());

		// CALCULATE BYES
		$byes = 0;
		if (!Functions::isPowerOf2($countTeams)) {
			$nextPow = Functions::nextPowerOf2($countTeams);
			$byes = $nextPow - $countTeams;
		}

		$roundsNum = log($countTeams + $byes, 2); // NUMBER OF ROUNDS

		$startRound = $this->round('Start');

		$previousGroups = [];

		for ($i = 1; $i <= (($countTeams + $byes) / 2); $i++) {
			$g = $startRound->group('Round 1 '.$i)->setInGame(2)->setType(Constants::ROUND_TWO);
			$previousGroups[] = $g;
		}

		$this->splitTeams();

		for ($r = 2; $r <= $roundsNum; $r++) {
			$groups = [];
			$round = $this->round('Round '.$r);
			for ($g = 1; $g <= (($countTeams + $byes) / (2 ** $r)); $g++) {
				$group = $round->group('Round '.$r.' - '.$g)->setInGame(2)->setType(Constants::ROUND_TWO);
				$groups[] = $group;
				array_shift($previousGroups)->progression($group, 0, 1); // PROGRESS FROM GROUP BEFORE
				array_shift($previousGroups)->progression($group, 0, 1); // PROGRESS FROM GROUP BEFORE
			}
			$previousGroups = $groups;
		}

		return $this;

	}

}
