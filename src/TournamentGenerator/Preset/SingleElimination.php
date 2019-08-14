<?php

namespace TournamentGenerator\Preset;

/**
 *
 */
class SingleElimination extends \TournamentGenerator\Tournament
{

	public function generate() {

		$this->allowSkip();

		$countTeams = count($this->getTeams());

		// CALCULATE BYES
		$byes = 0;
		if ( !\TournamentGenerator\isPowerOf2($countTeams) ) {
			$nextPow = bindec(str_pad(1, strlen(decbin($countTeams))+1, 0, STR_PAD_RIGHT));
			$byes = $nextPow-$countTeams;
		}

		$roundsNum = log($countTeams+$byes, 2); // NUMBER OF ROUNDS

		$startRound = $this->round('Start');

		$previousGroups = [];

		for ($i=1; $i <= (($countTeams+$byes)/2); $i++) {
			$g = $startRound->group('Round 1 '.$i)->setInGame(2)->setType(\TournamentGenerator\Constants::ROUND_TWO);
			$previousGroups[] = $g;
		}

		$this->splitTeams();

		for ($r=2; $r <= $roundsNum; $r++) {
			$groups = [];
			$round = $this->round('Round '.$r);
			for ($g=1; $g <= (($countTeams+$byes)/pow(2, $r)); $g++) {
				$group = $round->group('Round '.$r.' - '.$g)->setInGame(2)->setType(\TournamentGenerator\Constants::ROUND_TWO);
				$groups[] = $group;
				array_shift($previousGroups)->progression($group, 0, 1); // PROGRESS FROM GROUP BEFORE
				array_shift($previousGroups)->progression($group, 0, 1); // PROGREESS FROM GROUP BEFORE
			}
			$previousGroups = $groups;
		}

		return $this;

	}

}
