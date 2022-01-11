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
		/** If an extra winning round is generated first */
		$extraStart = $byes > 0;

		$startRound = $this->round('Start round');

		/** Total round count (minus final rounds) */
		$roundsNum = log($nextPow, 2) * 2;

		/** How many groups are in the first round */
		$startGroups = ($countTeams + $byes) / 2;

		/** How many losing teams there are after the first winning rounds */
		$losingTeams = (($countTeams - $byes) / 2) + ($extraStart ? $startGroups / 2 : 0);
		/** If an extra losing round is generated first */
		$extraLosingStart = !Functions::isPowerOf2($losingTeams);

		if ($extraLosingStart) {
			$roundsNum++;
		}

		$previousLosingGroups = [];
		$previousWinningGroups = [];
		$allGroups = [];

		// First round's groups
		for ($i = 1; $i <= $startGroups; $i++) {
			$g = $startRound->group('Start group ('.$i.')')->setInGame(2)->setType(Constants::ROUND_TWO);
			$allGroups[] = $g;
		}
		$previousGroups = $allGroups;

		// Split teams
		$this->splitTeamsEvenly();

		/** Counter for winning rounds only */
		$winR = 2;

		// Create an extra starting winning round.
		// This needs to be created because the first round will skip a lot of games if there are any byes.
		if ($extraStart) {
			$startRound = $this->round('Start round (2)');
			$groups = [];
			$winningGroups = [];
			$this->generateWinSide(2, $winR++, $byes, $countTeams, $startRound, $allGroups, $groups, $winningGroups, $previousGroups);
			$previousWinningGroups = $winningGroups;
		}

		$previousGroups = $allGroups;

		/** @var Group|null $lastLosingGroup The last group from the loser's side, to progress to the final round */
		$lastLosingGroup = null;
		/** @var Group $lastLosingGroup The last group from the winner's side, to progress to the final round */
		$lastWinningGroup = end($allGroups);

		// Create all rounds
		for ($r = $winR; $r <= $roundsNum - 1; $r++) {
			$groups = [];
			$losingGroups = [];
			$winningGroups = [];
			$round = $this->round('Round '.$r);

			// Always generate a losing side
			$this->generateLosingSide($r, $extraStart, $round, $allGroups, $previousLosingGroups, $previousGroups, $losingGroups);

			// Skip some winning rounds - losing side will have more rounds
			$rr = $r - ($extraStart ? 1 : 0) + ($extraLosingStart ? 1 : 0);
			if (($rr < 3 || $rr % 2 === 0) && (!$extraStart || count($previousWinningGroups) > 1)) {
				// First round after the starting rounds
				if ($extraStart && $r === 3) {
					$previousGroups = $previousWinningGroups;
				}
				/** @noinspection SlowArrayOperationsInLoopInspection */
				$previousGroups = array_merge($previousGroups, $previousWinningGroups);
				$this->generateWinSide($r, $winR++, $byes, $countTeams, $round, $allGroups, $groups, $winningGroups, $previousGroups);
				$previousWinningGroups = $winningGroups;
			}

			// Save last generated groups for next round's
			if (count($winningGroups) > 0) {
				$lastWinningGroup = end($winningGroups);
			}
			if (count($losingGroups) > 0) {
				$lastLosingGroup = end($losingGroups);
			}

			$previousGroups = $groups;
			$previousLosingGroups = $losingGroups;
		}

		// Final round
		$round = $this->round('Round '.$roundsNum.' - Finale');
		$groupFinal = $round->group('Round '.$r.' - finale')->setInGame(2)->setType(Constants::ROUND_TWO)->setOrder(1);
		$allGroups[] = $groupFinal;
		if (isset($lastLosingGroup)) {
			$lastLosingGroup->progression($groupFinal, 0, 1);
		}
		if (isset($lastWinningGroup)) {
			$lastWinningGroup->progression($groupFinal, 0, 1);
		}

		// Repeat the game if the winning team loses
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
	 * Generate the winning side (Single elimination with progressions into the losing side)
	 *
	 * @param int     $roundNum              Round number
	 * @param int     $winRoundNum           Real winning side round counter
	 * @param int     $byes                  Initial byes
	 * @param int     $countTeams            Total teams
	 * @param Round   $round                 Round object
	 * @param Group[] $allGroups             All groups
	 * @param Group[] $groups                Output groups
	 * @param Group[] $previousWinningGroups Winning side groups
	 * @param Group[] $previousGroups        Losing side groups
	 *
	 * @return void
	 * @throws Exception
	 */
	private function generateWinSide(int $roundNum, int $winRoundNum, int $byes, int $countTeams, Round $round, array &$allGroups, array &$groups, array &$previousWinningGroups = [], array $previousGroups = []) : void {
		$order = 1;
		// All groups
		for ($g = 1; $g <= (($countTeams + $byes) / (2 ** $winRoundNum)); $g++) {
			$group = $round
				->group('Round '.$roundNum.' (win '.$g.')')
				->setInGame(2)
				->setType(Constants::ROUND_TWO)
				->setOrder($order);
			$allGroups[] = $group;
			$order += 2;
			$groups[] = $group;

			// Save the last winning groups for the final round
			$previousWinningGroups[] = $group;

			// Progress from winning groups before
			$previousGroups[2 * ($g - 1)]->progression($group, 0, 1);
			$previousGroups[(2 * ($g - 1)) + 1]->progression($group, 0, 1);
		}
	}

	/**
	 * Generate the "losing side" - same as Single elimination
	 *
	 * @param int     $roundNum              Round number
	 * @param bool    $extraStart            If there was an extra starting round (because of byes)
	 * @param Round   $round                 Round object
	 * @param Group[] $allGroups             Array of all groups
	 * @param Group[] $previousLosingGroups  Last losing round's groups
	 * @param Group[] $previousWinningGroups Last winning round's groups
	 * @param Group[] $losingGroups          Array to save generated groups for later reference
	 *
	 * @return void
	 * @throws Exception
	 */
	private function generateLosingSide(int $roundNum, bool $extraStart, Round $round, array &$allGroups, array $previousLosingGroups = [], array $previousWinningGroups = [], array &$losingGroups = []) : void {
		// Filter winning groups - remove the ones without a game
		foreach ($previousWinningGroups as $key => $group) {
			if (count($group->getTeams()) === 1) {
				unset($previousWinningGroups[$key]);
			}
		}
		// Reset keys
		$previousWinningGroups = array_values($previousWinningGroups);

		// Save counts
		$losingCount = count($previousLosingGroups);
		$winningCount = count($previousWinningGroups);
		$teamsTotal = $losingCount + $winningCount;

		// Merge all groups in an alternating order for progressions
		/** @var array[] $progressGroups 0: Group, 1: int - progression offset */
		$progressGroups = [];
		$losingKey = 0;
		$winningKey = 0;
		while (count($progressGroups) < $teamsTotal && ($losingCount > $losingKey || $winningCount > $winningKey)) {
			if ($losingCount > $losingKey) {
				$progressGroups[] = [$previousLosingGroups[$losingKey++], 0];
			}
			if ($winningCount > $winningKey) {
				$progressGroups[] = [$previousWinningGroups[$winningKey++], 1];
			}
		}

		$order = 2;
		// Check byes
		if (Functions::isPowerOf2($teamsTotal)) {
			for ($g = 1; $g <= $teamsTotal / 2; $g++) {
				$group = $round
					->group('Round '.$roundNum.' (loss '.$g.')')
					->setInGame(2)
					->setType(Constants::ROUND_TWO)
					->setOrder($order);
				$allGroups[] = $group;
				$order += 2;
				$losingGroups[] = $group;

				// First losing round
				// Progress from winning teams only
				if (($roundNum === 2 && !$extraStart) || ($roundNum === 3 && $extraStart)) {
					$previousWinningGroups[2 * ($g - 1)]->progression($group, 1, 1);
					$previousWinningGroups[(2 * ($g - 1)) + 1]->progression($group, 1, 1);
				}
				elseif ($teamsTotal >= 2) {
					$key = 2 * ($g - 1);
					$progressGroups[$key][0]->progression($group, $progressGroups[$key][1], 1);
					$key++;
					$progressGroups[$key][0]->progression($group, $progressGroups[$key][1], 1);
				}
			}
		}
		else {
			// Calculate byes
			$nextPowerOf2 = Functions::nextPowerOf2($teamsTotal);
			$losingByes = $nextPowerOf2 - $teamsTotal;

			// Counters
			$byesProgressed = 0;
			$teamCounter = 0;

			// Generate groups
			$groupCount = $nextPowerOf2 / 2;
			for ($g = 1; $g <= $groupCount; $g++) {
				$group = $round
					->group('Round '.$roundNum.' (loss '.$g.')')
					->setInGame(2)
					->setType(Constants::ROUND_TWO)
					->setOrder($order);
				$allGroups[] = $group;
				$order += 2;
				$losingGroups[] = $group;

				// Create progressions from groups before
				$teamCounter++;
				$progressGroups[$byesProgressed][0]->progression($group, $progressGroups[$byesProgressed++][1], 1);
				if (isset($progressGroups[$byesProgressed]) && $teamCounter < $teamsTotal - $losingByes) {
					$teamCounter++;
					$progressGroups[$byesProgressed][0]->progression($group, $progressGroups[$byesProgressed++][1], 1);
				}
			}
		}
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function printBracket() : string {
		$str = '';
		foreach ($this->getRounds() as $round) {
			$name = $round->getName();
			$len = strlen($name);
			$str .= "\n| ---------------------------------------- |\n| ".str_repeat('-', floor((40 - $len) / 2) - 1).' '.$name.' '.str_repeat('-', ceil((40 - $len) / 2) - 1)." |\n| ---------------------------------------- |\n\n";
			foreach ($round->getGroups() as $group) {
				$str .= '-- '.$group->getName().PHP_EOL;
				if (count($group->getGames()) === 0) {
					$str .= '| '.implode(' | ', array_map(static function(Team $team) {
							return $team->getName();
						}, $group->getTeams())).' |'.PHP_EOL;
				}
				else {
					foreach ($group->getGames() as $game) {
						$str .= '| '.implode(' | ', array_map(static function(Team $team) use ($game) {
								return ($team->getId() === $game->getWin() ? "\e[1m\e[4m" : '').$team->getName()."\e[0m";
							}, $game->getTeams())).' |'.(count($game->getDraw()) > 0 ? ' - draw' : '').PHP_EOL;
					}
				}
			}
		}
		return $str;
	}

}
