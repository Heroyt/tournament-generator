<?php

namespace TournamentGenerator\Preset;

use Exception;
use TournamentGenerator\Constants;
use TournamentGenerator\Group;
use TournamentGenerator\Helpers\Functions;
use TournamentGenerator\Round;
use TournamentGenerator\Team;
use TournamentGenerator\TeamFilter;
use TournamentGenerator\Tournament;

/**
 * Double elimination generator
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @package TournamentGenerator\Preset
 * @since   0.1
 */
class DoubleElimination extends Tournament implements Preset
{

	/**
	 * Generate all the games
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function generate() : DoubleElimination {
		$this->allowSkip();

		$countTeams = count($this->getTeams());

		if ($countTeams < 3) {
			throw new Exception('Double elimination is possible for minimum of 3 teams - '.$countTeams.' teams given.');
		}


		// CALCULATE BYES
		$nextPow = 0;
		$byes = $this->calcByes($countTeams, $nextPow);

		$startRound = $this->round('Start round');

		$roundsNum = log($nextPow, 2) * 2;

		$startGroups = ($countTeams + $byes) / 2;

		$previousGroups = [];
		$previousLosingGroups = [];
		$allGroups = [];

		for ($i = 1; $i <= $startGroups; $i++) {
			$g = $startRound->group('Start group ('.$i.')')->setInGame(2)->setType(Constants::ROUND_TWO);
			$allGroups[] = $g;
			$previousGroups[] = $g;
		}

		// SPLIT TEAMS EVENLY
		$this->splitTeams();

		$lastLosingGroup = null;
		$lastWinningGroup = end($allGroups);

		for ($r = 2; $r <= $roundsNum - 1; $r++) {
			$groups = [];
			$losingGroups = [];
			$round = $this->round('Round '.$r);

			// GENERATING LOSING AND WINNING SIDE
			$this->generateLosingSide($r, $round, $allGroups, $previousLosingGroups, $previousGroups, $losingGroups);
			$this->generateWinSide($r, $byes, $countTeams, $round, $allGroups, $groups, $lastWinningGroup, $previousGroups);

			if (count($losingGroups) > 0) {
				$lastLosingGroup = end($losingGroups);
			}
			$previousGroups = $groups;
			$previousLosingGroups = $losingGroups;
		}

		// LAST ROUND
		$round = $this->round('Round '.$roundsNum.' - Finale');
		$groupFinal = $round->group('Round '.$r.' - finale')->setInGame(2)->setType(Constants::ROUND_TWO)->setOrder(1);
		$allGroups[] = $groupFinal;
		if (isset($lastLosingGroup)) {
			$lastLosingGroup->progression($groupFinal, 0, 1);
		}
		if (isset($lastWinningGroup)) {
			$lastWinningGroup->progression($groupFinal, 0, 1);
		}

		// REPEAT GROUP IF LOSING TEAM WON
		$group = $round->group('Round '.$r.' - finale (2)')->setInGame(2)->setType(Constants::ROUND_TWO)->setOrder(1);
		$twoLoss = new TeamFilter('losses', '=', 1, $allGroups);
		$groupFinal->progression($group, 0, 2)->addFilter($twoLoss);

		return $this;

	}

	/**
	 * Calculate how many teams should skip the first round
	 *
	 * @param int $countTeams Total teams
	 * @param int $nextPow    Next power of 2
	 *
	 * @return float|int
	 */
	private function calcByes(int $countTeams, int &$nextPow) {
		$byes = 0;
		$nextPow = $countTeams;
		if (!Functions::isPowerOf2($countTeams)) {
			$nextPow = Functions::nextPowerOf2($countTeams);
			$byes = $nextPow - $countTeams;
		}
		return $byes;
	}

	/**
	 * Generate the "losing side" - same as Single elimination
	 *
	 * @param int     $roundNum  Round number
	 * @param Round   $round     Round object
	 * @param Group[] $allGroups Array of all groups
	 * @param Group[] $previousLosingGroups
	 * @param array   $previousGroups
	 * @param array   $losingGroups
	 *
	 * @return void Last losing group
	 */
	private function generateLosingSide(int $roundNum, Round $round, array &$allGroups, array $previousLosingGroups = [], array $previousGroups = [], array &$losingGroups = []) : void {
		$losingGroupTeamsCount = count($previousLosingGroups) + count($previousGroups);
		$order = 2;
		if (Functions::isPowerOf2($losingGroupTeamsCount)) { // IF THE NUMBER OF TEAMS IS A POWER OF 2, GENERATE GROUPS WITHOUT BYES
			for ($g = 1; $g <= $losingGroupTeamsCount / 2; $g++) {
				$group = $round
					->group('Round '.$roundNum.' (loss '.$g.')')
					->setInGame(2)
					->setType(Constants::ROUND_TWO)
					->setOrder($order);
				$allGroups[] = $group;
				$order += 2;
				$losingGroups[] = $group;
				if ($roundNum === 2) {                                                   // FIRST LOSING ROUND
					$previousGroups[2 * ($g - 1)]->progression($group, 1, 1);              // PROGRESS FROM STARTING GROUP
					$previousGroups[(2 * ($g - 1)) + 1]->progression($group, 1, 1);        // PROGRESS FROM STARTING GROUP
				}
				elseif ($losingGroupTeamsCount >= 2) {
					$previousLosingGroups[$g - 1]->progression($group, 0, 1); // PROGRESS FROM LOSING GROUP BEFORE
					if (isset(array_reverse($previousGroups)[$g - 1])) {
						array_reverse($previousGroups)[$g - 1]->progression($group, 1, 1);
					} // PROGRESS FROM WINNING GROUP BEFORE
					else {
						$previousLosingGroups[$g]->progression($group, 0, 1);
					} // PROGRESS OTHER TEAM FROM LOSING GROUP BEFORE
				}
			}
		}
		else { // IF THE NUMBER OF TEAMS IS NOT A POWER OF 2, GENERATE GROUPS WITH BYES
			// LOOK FOR THE CLOSEST LOWER POWER OF 2
			$losingByes = $losingGroupTeamsCount - Functions::previousPowerOf2($losingGroupTeamsCount);
			$n = (floor(count($previousLosingGroups) / 2) + $losingByes);
			$byesGroupsNums = [];
			$byesProgressed = 0;
			for ($i = 0; $i < $losingByes; $i++) {
				$byesGroupsNums[] = (int) $n - ($i * 2);
			}
			$lastGroup = 0;
			for ($g = 1; $g <= ((count($previousLosingGroups) / 2) + $losingByes); $g++) {
				$group = $round
					->group('Round '.$roundNum.' (loss '.$g.')')
					->setInGame(2)
					->setType(Constants::ROUND_TWO)
					->setOrder($order);
				$allGroups[] = $group;
				$order += 2;
				$losingGroups[] = $group;
				if (isset($previousGroups[$byesProgressed]) && in_array($lastGroup, $byesGroupsNums, true)) {  // EMPTY GROUP FROM BYE
					$previousGroups[$byesProgressed]->progression($group, 1, 1);                                 // PROGRESS FROM WINNING GROUP BEFORE
					$byesProgressed++;
				}
				else {
					$previousLosingGroups[$lastGroup]->progression($group, 0, 1); // PROGRESS FROM LOSING GROUP BEFORE
					if (isset($previousLosingGroups[$lastGroup + 1])) {                         // PROGRESS FROM LOSING GROUP BEFORE
						$previousLosingGroups[$lastGroup + 1]->progression($group, 0, 1);
					}
					$lastGroup += 2;
				}
			}
		}
	}

	/**
	 * Generate the winning side (Single elimination with progressions into the losing side)
	 *
	 * @param int        $roundNum         Round number
	 * @param int        $byes             Initial byes
	 * @param int        $countTeams       Total teams
	 * @param Round      $round            Round object
	 * @param Group[]    $allGroups        All groups
	 * @param Group[]    $groups           Output groups
	 * @param Group|null $lastWinningGroup Last group
	 * @param Group[]    $previousGroups   Losing side groups
	 *
	 * @return void
	 */
	private function generateWinSide(int $roundNum, int $byes, int $countTeams, Round $round, array &$allGroups, array &$groups, Group &$lastWinningGroup = null, array $previousGroups = []) : void {
		$order = 1;
		for ($g = 1; $g <= (($countTeams + $byes) / (2 ** $roundNum)); $g++) {
			$group = $round
				->group('Round '.$roundNum.' (win '.$g.')')
				->setInGame(2)
				->setType(Constants::ROUND_TWO)
				->setOrder($order);
			$allGroups[] = $group;
			$order += 2;
			$groups[] = $group;
			$lastWinningGroup = $group;                                                   // KEEP THE LAST GROUP FOR FINALE
			$previousGroups[2 * ($g - 1)]->progression($group, 0, 1);                     // PROGRESS FROM GROUP BEFORE
			$previousGroups[(2 * ($g - 1)) + 1]->progression($group, 0, 1);               // PROGRESS FROM GROUP BEFORE
		}
	}

	/**
	 * @return string
	 */
	public function printBracket() : string {
		$str = '';
		foreach ($this->getRounds() as $round) {
			$name = $round->getName();
			$len = strlen($name);
			$str .= "\n| ---------------------------------------- |\n| ".str_repeat('-', floor((40 - $len) / 2) - 1).' '.$name.' '.str_repeat('-', ceil((40 - $len) / 2) - 1)." |\n| ---------------------------------------- |\n\n";
			foreach ($round->getGroups() as $group) {
				$str .= '-- '.$group->getName().PHP_EOL;
				foreach ($group->getGames() as $game) {
					$str .= '| '.implode(' | ', array_map(static function(Team $team) {
							return $team->getName();
						}, $game->getTeams())).' |'.PHP_EOL;
				}
			}
		}
		return $str;
	}

}
