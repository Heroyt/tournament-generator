<?php

namespace TournamentGenerator\Preset;

/**
 *
 */
class Tournament_DoubleElimination extends \TournamentGenerator\Tournament
{

	public function generate() {
		$this->allowSkip();

		$countTeams = count($this->getTeams());

		if ($countTeams < 3) throw new Exception('Double elimination is possible for minimum of 3 teams - '.$countTeams.' teams given.');


		// CALCULATE BYES
		$byes = 0;
		$nextPow = $countTeams;
		if ( !\TournamentGenerator\isPowerOf2($countTeams) ) {
			$nextPow = bindec(str_pad(1, strlen(decbin($countTeams))+1, 0, STR_PAD_RIGHT));
			$byes = $nextPow-$countTeams;
		}

		$startRound = $this->round('Start round');

		$teams = $this->getTeams();
		// shuffle($teams);

		$roundsNum = log($nextPow, 2)*2;

		$startGroups = ($countTeams+$byes)/2;

		$previousGroups = [];
		$previousLosingGroups = [];
		$groupIds = [];
		$allGroups = [];

		for ($i=1; $i <= $startGroups; $i++) {
			$g = $startRound->group([
				'name' => 'Start group - '.$i,
				'inGame' => 2,
				'type' => TWO_TWO,
			]);
			$allGroups[] = $g;
			$groupIds[] = $g->id;
			$previousGroups[] = $g;
		}

		// SPLIT TEAMS EVENLY
		$this->splitTeams();

		for ($r=2; $r <= $roundsNum-1; $r++) {
			$groups = [];
			$losingGroups = [];
			$round = $this->round('Round '.$r);

			// GENERATE GROUPS AND PROGRESSIONS

			// LOSING SIDE
			$losingGroupTeamsCount = count($previousLosingGroups)+count($previousGroups);
			$order = 2;
			if (\TournamentGenerator\isPowerOf2($losingGroupTeamsCount)) { // IF THE NUMBER OF TEAMS IS A POWER OF 2, GENERATE GROUPS WITHOUT BYES
				for ($g=1; $g <= $losingGroupTeamsCount/2; $g++) {
					$group = $round->group([
						'name' => 'Round '.$r.' - loss '.$g,
						'inGame' => 2,
						'type' => TWO_TWO,
						'order' => $order,
					]);
					$allGroups[] = $group;
					$order += 2;
					$losingGroups[] = $group;
					$lastLosingGroup = $group; // KEEP THE LAST GROUP FOR FINALE
					if ($r === 2) { // FIRST LOSING ROUND
						$previousGroups[2*($g-1)]->progression($group, 1, 1); // PROGRESS FROM STARTING GROUP
						$previousGroups[(2*($g-1))+1]->progression($group, 1, 1); // PROGREESS FROM STARTING GROUP
					}
					elseif ($losingGroupTeamsCount >= 2) {
						$previousLosingGroups[$g-1]->progression($group, 0, 1); // PROGRESS FROM LOSING GROUP BEFORE
						if (isset(array_reverse($previousGroups)[$g-1])) array_reverse($previousGroups)[$g-1]->progression($group, 1, 1); // PROGREESS FROM WINNING GROUP BEFORE
						else $previousLosingGroups[$g]->progression($group, 0, 1); // PROGRESS OTHER TEAM FROM LOSING GROUP BEEFORE
					}
				}
			}
			else { // IF THE NUMBER OF TEAMS IS NOT A POWER OF 2, GENERATE GROUPS WITH BYES
				// LOOK FOR THE CLOSEST LOWER POWER OF 2
				$losingByes = $losingGroupTeamsCount-bindec(str_pad(1, strlen(decbin($losingGroupTeamsCount)), 0, STR_PAD_RIGHT));
				$n = (floor(count($previousLosingGroups)/2)+$losingByes);
				$byesGroupsNums = [];
				$byesProgressed = 0;
				for ($i=0; $i < $losingByes; $i++) {
					$byesGroupsNums[] = $n-($i*2);
				}
				$lastGroup = 0;
				for ($g=1; $g <= ((count($previousLosingGroups)/2)+$losingByes); $g++) {
					$group = $round->group([
						'name' => 'Round '.$r.' - loss '.$g,
						'inGame' => 2,
						'type' => TWO_TWO,
						'order' => $order,
					]);
					$allGroups[] = $group;
					$order += 2;
					$losingGroups[] = $group;
					$lastLosingGroup = $group; // KEEP THE LAST GROUP FOR FINALE
					if (in_array($g, $byesGroupsNums) && isset($previousGroups[$byesProgressed])) { // EMPTY GROUP FROM BYE
						$previousGroups[$byesProgressed]->progression($group, 1, 1); // PROGRESS FROM WINNING GROUP BEFORE
						$byesProgressed++;
					}
					else {
						$previousLosingGroups[$lastGroup]->progression($group, 0, 1); // PROGRESS FROM LOSING GROUP BEFORE
						if (isset($previousLosingGroups[$lastGroup + 1])) $previousLosingGroups[$lastGroup + 1]->progression($group, 0, 1); // PROGREESS FROM LOSING GROUP BEFORE
						$lastGroup += 2;
					}
				}
			}
			// WINNING SIDE LIKE SINGLE ELIMINATION
			$order = 1;
			for ($g=1; $g <= (($countTeams+$byes)/pow(2, $r)); $g++) {
				$group = $round->group([
					'name' => 'Round '.$r.' - win '.$g,
					'inGame' => 2,
					'type' => TWO_TWO,
					'order' => $order,
				]);
				$allGroups[] = $group;
				$order += 2;
				$groups[] = $group;
				$lastWinningGroup = $group; // KEEP THE LAST GROUP FOR FINALE
				$previousGroups[2*($g-1)]->progression($group, 0, 1); // PROGRESS FROM GROUP BEFORE
				$previousGroups[(2*($g-1))+1]->progression($group, 0, 1); // PROGREESS FROM GROUP BEFORE
			}

			$previousGroups = $groups;
			$previousLosingGroups = $losingGroups;
		}

		// LAST ROUND
		$round = $this->round('Round '.$roundsNum.' - Finale');
		$groupFinal = $round->group([
			'name' => 'Round '.$r.' - finale',
			'inGame' => 2,
			'type' => TWO_TWO,
			'order' => 1,
		]);
		$allGroups[] = $groupFinal;
		$lastLosingGroup->progression($groupFinal, 0, 1);
		$lastWinningGroup->progression($groupFinal, 0, 1);

		// REPEAT GROUP IF LOSING TEAM WON
		$group = $round->group([
			'name' => 'Round '.$r.' - finale (2)',
			'inGame' => 2,
			'type' => TWO_TWO,
			'order' => 1,
		]);
		$twoLoss = new \TournamentGenerator\TeamFilter('losses', '=', 1, $allGroups);
		$groupFinal->progression($group, 0, 2)->addFilter($twoLoss);

		return $this;

	}

}


?>
