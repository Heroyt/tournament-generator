<?php

namespace TournamentGenerator;

/**
 *
 */
class Generator
{

	private $group = null;
	private $type = /** @scrutinizer ignore-all */ R_R; // TYPE OF ROUND TO CREATE A LAYOUT
	private $inGame = 2; // NUMBER OF TEAMS IN ONE GAME - 2/3/4
	private $maxSize = 4; // MAX SIZE OF GROUP BEFORE SPLIT
	private $allowSkip = false; // IF IS NUMBER OF TEAMS LESS THAN $this->inGame THEN SKIP PLAYING THIS GROUP
	private $games = []; // ARRAY OF GAME OBJECTS

	function __construct(Group $group) {
		$this->group = $group;
	}

	public function allowSkip(){
		$this->allowSkip = true;
		return $this;
	}
	public function disallowSkip(){
		$this->allowSkip = false;
		return $this;
	}
	public function setSkip(bool $skip) {
		$this->allowSkip = $skip;
		return $this;
	}
	public function getSkip() {
		return $this->allowSkip;
	}


	public function setType(/** @scrutinizer ignore-all */ string $type = R_R) {
		if (in_array($type, groupTypes)) $this->type = $type;
		else throw new \Exception('Unknown group type: '.$type);
		return $this;
	}
	public function getType() {
		return $this->type;
	}

	public function setInGame(int $inGame) {
		if ($inGame < 2 ||  $inGame > 4) throw new \Exception('Expected 2,3 or 4 as inGame '.$inGame.' given');
		$this->inGame = $inGame;
		return $this;
	}
	public function getInGame() {
		return $this->inGame;
	}

	public function setMaxSize(int $maxSize) {
		if ($maxSize < 2) throw new \Exception('Max group size has to be at least 2, '.$maxSize.' given');
		$this->maxSize = $maxSize;
		return $this;
	}
	public function getMaxSize() {
		return $this->maxSite;
	}

	public function genGames() {
		switch ($this->type) {
			case R_R:{
					$this->group->addGame($this->r_rGames());
				break;}
			case TWO_TWO:
				$teams = $this->group->getTeams();
				$discard = [];
				shuffle($teams);
				$count = count($teams);
				while (count($teams) % $this->inGame !== 0) { $discard[] = array_shift($teams); }

				while (count($teams) > 0) {
					$tInGame = [];
					for ($i=0; $i < $this->inGame; $i++) { $tInGame[] = array_shift($teams); }
					$this->group->game($tInGame);
				}

				if (count($discard) > 0 && !$this->allowSkip) throw new \Exception('Couldn\'t make games with all teams. Expected k*'.$this->inGame.' teams '.$count.' teams given - discarting '.count($discard).' teams ('.implode(', ', $discard).') in group '.$this.' - allow skip '.($this->allowSkip ? 'True' : 'False'));
				break;
			case COND_SPLIT:
				$games = [];
				$teams = $this->group->getTeams();
				if (count($teams) > $this->maxSize) {
					$groups = array_chunk($teams, /** @scrutinizer ignore-type */ ceil(count($teams)/ceil(count($teams)/$this->maxSize))); // SPLIT TEAMS INTO GROUP OF MAXIMUM SIZE OF $this->maxSize
					foreach ($groups as $group) { $games[] = $this->r_rGames($group); }
					$g = 0;
					foreach ($games as $group) {
						$g += count($group);
					}
					while ($g > 0) {
						foreach ($games as $key => $group) {
							$this->group->addGame(array_shift($games[$key]));
							if (count($games[$key]) === 0) unset($games[$key]);
							$g--;
						}
					}
				}
				else $this->group->addGame($this->r_rGames());
				break;
		}
		return $this->group->getGames();
	}
	public function r_rGames(array $teams = []) {
		$games = [];
		if (count($teams) === 0) $teams = $this->group->getTeams();
		switch ($this->inGame) {
			case 2:
				$games = Generator::circle_genGames2($teams, $this->group);
				break;
			case 3:{
				$teamsB = $teams;
				while (count($teamsB) >= 3) {
					$lockedTeam = array_shift($teamsB);
					$gamesTemp = Generator::circle_genGames2($teamsB, $this->group);
					foreach ($gamesTemp as $game) {
						$game->addTeam($lockedTeam);
					}
					$games = array_merge($games, $gamesTemp);
				}
				break;}
			case 4:{
				$teamsB = $teams;
				$lockedTeam1 = array_shift($teamsB);
				while (count($teamsB) >= 4) {
					$teamsB2 = $teamsB;
					while (count($teamsB2) >= 3) {
						$lockedTeam2 = array_shift($teamsB2);
						$gamesTemp = Generator::circle_genGames2($teamsB2, $this->group);
						foreach ($gamesTemp as $game) {
							$game->addTeam($lockedTeam1, $lockedTeam2);
						}
						$games = array_merge($games, $gamesTemp);
					}
					$lockedTeam1 = array_shift($teamsB);
				}
				$games[] = new Game(array_merge([$lockedTeam1], $teamsB), $this->group);
				break;}
		}
		return $games;
	}

	public function orderGames() {

		$games = $this->group->getGames();

		if (count($games) <= 4) return $games;

		$this->games = [];

		// TEAMS FROM LAST 3 GAMES
		// 1 = PLAYED ONLY 3RD GAME FROM END
		// 2 = PLAYED ONLY 2ND GAME FROM END
		// 3 = PLAYED 3RD AND 2ND GAME FROM END
		// 4 = PLAYED ONLY THE LAST GAME
		// 5 = PLAYED 3RD AND 1ST GAME FROM END
		// 6 = PLAYED 2ND ANS 1ST GAME FROM END
		// 7 = PLAYED ALL 3 LAST GAMES
		$teams = [];
		foreach ($this->group->getTeams() as $team) { $teams[$team->id] = 0; }

		$this->moveCalculatedGames(array_shift($games), $teams);

		while (count($games) > 0) {
			$found = false;

			// CYCLE 1
			// TEAM WHICH DIDN'T PLAY IN LAST GAME (< 4)
			foreach ($games as $key => $game) {
				if ($this->orderCheckTeamsVal($game, $teams, [4,5,6,7])) {
					$this->moveCalculatedGames($game,$teams);
					unset($games[$key]);
					$found = true;
					break;
				}
			}
			if ($found) continue;

			// CYCLE 2
			// ! TEAM WHICH PLAYED IN LAST TWO GAMES (NOT 6 or 7)
			foreach ($games as $key => $game) {
				if ($this->orderCheckTeamsVal($game, $teams, [6,7])) {
					$this->moveCalculatedGames($game,$teams);
					unset($games[$key]);
					$found = true;
					break;
				}
			}
			if ($found) continue;

			// CYCLE 3
			// NOT TEAM WHICH PLAYED IN LAST THREE GAMES (NOT 7)
			// TEAMS THAT DIDN'T PLAY IN LAST GAME WILL PLAY THIS GAME (< 4)
			foreach ($games as $key => $game) {
				if ($this->orderCheckTeamsVal($game, $teams, [7], [1,2,3])) {
					$this->moveCalculatedGames($game,$teams);
					unset($games[$key]);
					$found = true;
					break;
				}
			}
			if ($found) continue;

			// CYCLE 4
			// NOT TEAM WHICH PLAYED IN LAST THREE GAMES (NOT 7)
			foreach ($games as $key => $game) {
				if ($this->orderCheckTeamsVal($game, $teams, [7])) {
					$this->moveCalculatedGames($game,$teams);
					unset($games[$key]);
					$found = true;
					break;
				}
			}
			if ($found) continue;

			// CYCLE 5
			// TEAMS THAT DIDN'T PLAY IN LAST GAME WILL PLAY THIS GAME (< 4)
			foreach ($games as $key => $game) {
				if ($this->orderCheckTeamsVal($game, $teams, [], [1,2,3])) {
					$this->moveCalculatedGames($game,$teams);
					unset($games[$key]);
					$found = true;
					break;
				}
			}
			if ($found) continue;

			// CYCLE 6
			// FIRST AVAILABLE GAME
			$this->moveCalculatedGames(array_shift($games),$teams);
		}

		return $this->games;
	}
	private function moveCalculatedGames(Game $game, array &$teams) {

		$this->games[] = $game;

		foreach (end($this->games)->getTeamsIds() as $tid) {
			$teams[$tid] += 4;
		}

		if (count($this->games) > 1) {
			foreach (prev($this->games)->getTeamsIds() as $tid) {
				$teams[$tid] -= 2;
			}
		}
		if (count($this->games) > 2) {
			foreach (prev($this->games)->getTeamsIds() as $tid) {
				$teams[$tid] -= 1;
			}
		}
		if (count($this->games) > 3) {
			foreach (prev($this->games)->getTeamsIds() as $tid) {
				$teams[$tid] -= 1;
			}
		}

		return $teams;

	}
	private function orderCheckTeamsVal(Game $game, array &$teams, array $checkVals, array $required = []) {

		$requiredTeams = array_filter($teams, function($a) use ($required) { return in_array($a, $required); });

		foreach ($game->getTeamsIds() as $tid) {
			if (in_array($teams[$tid], $checkVals)) return false;
			if (isset($requiredTeams[$tid])) unset($requiredTeams[$tid]);
		}

		if (count($requiredTeams) > 0) return false;

		return true;

	}

	// GENERATES A ROBIN-ROBIN BRACKET
	public static function circle_genGames2(array $teams = [], Group $group = null) {
		$bracket = []; // ARRAY OF GAMES

		if (count($teams) % 2 != 0) $teams[] = DUMMY_TEAM; // IF NOT EVEN NUMBER OF TEAMS, ADD DUMMY

		shuffle($teams); // SHUFFLE TEAMS FOR MORE RANDOMNESS

		for ($i=0; $i < count($teams)-1; $i++) {
			$bracket = array_merge($bracket, Generator::circle_saveBracket($teams, $group)); // SAVE CURRENT ROUND

			$teams = Generator::circle_rotateBracket($teams); // ROTATE TEAMS IN BRACKET
		}

		return $bracket;

	}
	// CREATE GAMES FROM BRACKET
	public static function circle_saveBracket(array $teams, Group $group = null) {

		$bracket = [];

		for ($i=0; $i < count($teams)/2; $i++) { // GO THROUGH HALF OF THE TEAMS

			$home = $teams[$i];
			$reverse = array_reverse($teams);
			$away = $reverse[$i];

			if (($home == DUMMY_TEAM || $away == DUMMY_TEAM)) continue; // SKIP WHEN DUMMY_TEAM IS PRESENT

			$bracket[] = new Game([$home, $away], $group);

		}

		return $bracket;

	}
	// ROTATE TEAMS IN BRACKET
	public static function circle_rotateBracket(array $teams) {

		$first = array_shift($teams); // THE FIRST TEAM REMAINS FIRST
		$last = array_shift($teams); // THE SECOND TEAM MOVES TO LAST PLACE

		$teams = array_merge([$first], $teams, [$last]); // MERGE BACK TOGETHER

		return $teams;

	}

}
