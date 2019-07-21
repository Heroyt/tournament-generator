<?php

namespace TournamentGenerator;

require_once '../functions.php';

/**
 *
 */
class Group
{

	private $type = R_R; // TYPE OF ROUND TO CREATE A LAYOUT
	private $inGame = 2; // NUMBER OF TEAMS IN ONE GAME - 2/3/4
	private $teams = []; // ARRAY OF TEAMS
	private $progressed = []; // ARRAY OF TEAMS ALREADY PROGRESSED FROM THIS GROUP
	public $name = ''; // DISPLAYABLE NAME
	private $ordering = POINTS; // WHAT TO DECIDE ON WHEN ORDERING TEAMS
	private $progressions = []; // ARRAY OF PROGRESSION CONDITION OBJECTS
	private $games = []; // ARRAY OF GAME OBJECTS
	public $id = ''; // UNIQID OF GROUP FOR IDENTIFICATION
	private $maxSize = 4; // MAX SIZE OF GROUP BEFORE SPLIT
	public $winPoints = 3; // POINTS AQUIRED FROM WINNING
	public $drawPoints = 1; // POINTS AQUIRED FROM DRAW
	public $lostPoints = 0; // POINTS AQUIRED FROM LOOSING
	public $secondPoints = 2; // POINTS AQUIRED FROM BEING SECOND (APPLYES ONLY FOR 3 OR 4 INGAME VALUE)
	public $thirdPoints = 1; // POINTS AQUIRED FROM BEING THIRD (APPLYES ONLY FOR 4 INGAME VALUE)
	private $allowSkip = false; // IF IS NUMBER OF TEAMS LESS THAN $this->inGame THEN SKIP PLAYING THIS GROUP
	public $order = 0; // ORDER OF GROUPS IN ROUND

	function __construct(array $settings = []) {
		$this->id = uniqid();
		foreach ($settings as $key => $value) {
			switch ($key) {
				case 'name':
					if (gettype($value) !== 'string') throw new \Exception('Expected string as group name '.gettype($value).' given');
					$this->name = $value;
					break;
				case 'type':
					if (!in_array($value, groupTypes)) throw new \Exception('Unknown group type: '.$value);
					$this->type = $value;
					break;
				case 'ordering':
					if (!in_array($value, orderingTypes)) throw new \Exception('Unknown group ordering: '.$value);
					$this->ordering = $value;
					break;
				case 'inGame':
					if (gettype($value) !== 'integer') throw new \Exception('Expected integer as inGame '.gettype($value).' given');
					else if ($value < 2 || $value > 4) throw new \Exception('Expected 2,3 or 4 as inGame '.$value.' given');
					$this->inGame = $value;
					break;
				case 'maxSize':
					if (gettype($value) === 'integer') $this->maxSize = $value;
					break;
				case 'order':
					if (gettype($value) === 'integer') $this->order = $value;
					break;
			}
		}
	}
	function __toString() {
		return 'Group '.$this->name;
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

	public function addTeam(...$teams) {
		foreach ($teams as $team) {
			if ($team instanceof Team)  {
				$this->teams[] = $team;
				$team->groupResults[$this->id] = [
					'group' => $this,
					'points' => 0,
					'score'  => 0,
					'wins'   => 0,
					'draws'  => 0,
					'losses' => 0,
					'second' => 0,
					'third'  => 0
				];
			}
			elseif (gettype($team) === 'array') {
				foreach ($team as $team2) {
					if ($team2 instanceof Team) $this->teams[] = $team2;
					$team2->groupResults[$this->id] = [
						'group' => $this,
						'points' => 0,
						'score'  => 0,
						'wins'   => 0,
						'draws'  => 0,
						'losses' => 0,
						'second' => 0,
						'third'  => 0
					];
				}
			}
			else throw new \Exception('Trying to add team which is not an instance of Team class');
		}
		return $this;
	}
	public function getTeams($filters = []) {
		$teams = $this->teams;

		if (gettype($filters) !== 'array' && $filters instanceof TeamFilter) $filters = [$filters];
		elseif (gettype($filters) !== 'array') $filters = [];

		// APPLY FILTERS
		foreach ($filters as $key => $filter) {
			if (gettype($filter) === 'array') {
				switch (strtolower($key)) {
					case 'and':
						foreach ($teams as $tkey => $team) {
							if (!$this->filterAnd($team, $filter)) unset($teams[$tkey]); // IF FILTER IS NOT VALIDATED REMOVE TEAM FROM RETURN ARRAY
						}
						break;
					case 'or':
						foreach ($teams as $tkey => $team) {
							if (!$this->filterOr($team, $filter)) unset($teams[$tkey]); // IF FILTER IS NOT VALIDATED REMOVE TEAM FROM RETURN ARRAY
						}
						break;
					default:
						throw new \Exception('Unknown opperand type "'.$key.'". Expected "and" or "or".');
						break;
				}
			}
			elseif ($filter instanceof TeamFilter) {
				foreach ($teams as $tkey => $team) {
					if (!$filter->validate($team, $this->id, 'sum', $this)) {
						unset($teams[$tkey]); // IF FILTER IS NOT VALIDATED REMOVE TEAM FROM RETURN ARRAY
					}
				}
			}
			else {
				throw new \Exception('Filer ['.$key.'] is not an instance of TeamFilter class');
			}
		}
		return $teams;
	}
	public function filterAnd(Team $team, array $filters) {
		foreach ($filters as $key => $value) {
			if (gettype($value) === 'array') {
				switch (strtolower($key)) {
					case 'and':
						if ($this->filterAnd($team, $value)) return false;
						break;
					case 'or':
						if ($this->filterOr($team, $value)) return false;
						break;
					default:
						throw new \Exception('Unknown opperand type "'.$key.'". Expected "and" or "or".');
						break;
				}
			}
			elseif ($value instanceof TeamFilter) {
				if (!$value->validate($team, $this->id, 'sum', $this)) return false;
			}
			else {
				throw new \Exception('Filer ['.$key.'] is not an instance of TeamFilter class');
			}
		}
		return true;
	}
	public function filterOr(Team $team, array $filters) {
		foreach ($filters as $key => $value) {
			if (gettype($value) === 'array') {
				switch (strtolower($key)) {
					case 'and':
						if ($this->filterAnd($team, $value)) return true;
						break;
					case 'or':
						if ($this->filterOr($team, $value)) return true;
						break;
					default:
						throw new \Exception('Unknown opperand type "'.$key.'". Expected "and" or "or".');
						break;
				}
			}
			elseif ($value instanceof TeamFilter) {
				if (!$value->validate($team, $this->id, 'sum', $this)) return true;
			}
			else {
				throw new \Exception('Filer ['.$key.'] is not an instance of TeamFilter class');
			}
		}
		return false;
	}
	public function team(string $name = '') {
		$t = new Team($name);
		$this->teams[] = $t;
		$t->groupResults[$this->id] = [
			'group' => $this,
			'points' => 0,
			'score'  => 0,
			'wins'   => 0,
			'draws'  => 0,
			'losses' => 0,
			'second' => 0,
			'third'  => 0
		];
		return $t;
	}
	public function setType(string $type = R_R) {
		if (in_array($type, groupTypes)) $this->type = $type;
		else throw new \Exception('Unknown group type: '.$type);
		return $this;
	}
	public function getType() {
		return $this->type;
	}
	public function setOrdering(string $ordering = POINTS) {
		if (in_array($ordering, orderingTypes)) $this->ordering = $ordering;
		else throw new \Exception('Unknown group ordering: '.$ordering);
		return $this;
	}
	public function getOrdering() {
		return $this->ordering;
	}
	public function sortTeams($filters = []) {
		switch ($this->ordering) {
			case POINTS:{
				usort($this->teams, function($a, $b) {
					if ($a->groupResults[$this->id]["points"] === $b->groupResults[$this->id]["points"] && $a->groupResults[$this->id]["score"] === $b->groupResults[$this->id]["score"]) return 0;
					if ($a->groupResults[$this->id]["points"] === $b->groupResults[$this->id]["points"]) return ($a->groupResults[$this->id]["score"] > $b->groupResults[$this->id]["score"] ? -1 : 1);
					return ($a->groupResults[$this->id]["points"] > $b->groupResults[$this->id]["points"] ? -1 : 1);
				});
				break;}
			case SCORE:{
				usort($this->teams, function($a, $b) {
					if ($a->groupResults[$this->id]["score"] === $b->groupResults[$this->id]["score"]) return 0;
					return ($a->groupResults[$this->id]["score"] > $b->groupResults[$this->id]["score"] ? -1 : 1);
				});
				break;}
		}
		return $this->getTeams($filters);
	}
	public function setInGame(int $inGame) {
		if (gettype($inGame) === 'integer') {
			if ($inGame === 2 || $inGame === 3 || $inGame === 4) $this->inGame = $inGame;
			else throw new \Exception('Expected 2,3 or 4 as inGame '.$inGame.' given');
		}
		else throw new \Exception('Expected integer as inGame '.gettype($inGame).' given');
	}
	public function getInGame() {
		return $this->inGame;
	}
	public function addProgression(Progression $progression) {
		$this->progressions[] = $progression;
		return $this;
	}
	public function progression(Group $to, int $start = 0, int $len = null) {
		$p = new Progression($this, $to, $start, $len);
		$this->progressions[] = $p;
		return $p;
	}
	public function progress() {
		foreach ($this->progressions as $progression) {
			$progression->progress();
		}
	}
	public function progressBlank() {
		foreach ($this->progressions as $progression) {
			$progression->progressBlank();
		}
	}
	public function addProgressed(...$teams) {
		foreach ($teams as $team) {
			if ($team instanceOf Team) {
				$this->progressed[] = $team->id;
			}
			elseif (gettype($team) === 'string' || gettype($team) === 'integer') {
				$this->progressed[] = $team;
			}
			elseif (gettype($team) === 'array') {
				foreach ($team as $teamInner) {
					if ($teamInner instanceOf Team) {
						$this->progressed[] = $teamInner->id;
					}
					elseif (gettype($teamInner) === 'string' || gettype($teamInner) === 'integer') {
						$this->progressed[] = $teamInner;
					}
				}
			}
		}
		return $this;
	}
	public function progressed(Team $team) {
		if (in_array($team->id, $this->progressed)) {
			return true;
		}
		return false;
	}
	public function genGames() {
		switch ($this->type) {
			case R_R:{
					$this->games = $this->r_rGames();
				break;}
			case TWO_TWO:
				$teams = $this->teams;
				$discard = [];
				shuffle($teams);
				$count = count($teams);
				while (count($teams) % $this->inGame !== 0) {
					$discard[] = array_shift($teams);
				}

				while (count($teams) > 0) {
					$tInGame = [];
					for ($i=0; $i < $this->inGame; $i++) {
						$tInGame[] = array_shift($teams);
					}
					$this->game($tInGame);
				}

				if (count($discard) > 0 && !$this->allowSkip) throw new \Exception('Couldn\'t make games with all teams. Expected k*'.$this->inGame.' teams '.$count.' teams given - discarting '.count($discard).' teams ('.implode(', ', $discard).') in group '.$this.' - allow skip '.($this->allowSkip ? 'True' : 'False'));
				break;
			case COND_SPLIT:
				$games = [];
				if (count($this->teams) > $this->maxSize) {
					$groups = array_chunk($this->teams, /** @scrutinizer ignore-type */ ceil(count($this->teams)/ceil(count($this->teams)/$this->maxSize))); // SPLIT TEAMS INTO GROUP OF MAXIMUM SIZE OF $this->maxSize
					foreach ($groups as $group) {
						$games[] = $this->r_rGames($group);
					}
					$g = 0;
					foreach ($games as $group) {
						$g += count($group);
					}
					while ($g > 0) {
						foreach ($games as $key => $group) {
							$this->games[] = array_shift($games[$key]);
							if (count($games[$key]) === 0) unset($games[$key]);
							$g--;
						}
					}
				}
				else $this->games = $this->r_rGames();
				break;
		}
		return $this->games;
	}
	public function game(array $teams = []) {
		$g = new Game($teams, $this);
		$this->games[] = $g;
		return $g;
	}
	public function addGame(Game ...$games){
		foreach ($games as $game) {
			if ($game instanceof Game) $this->games[] = $game;
			else throw new \Exception('Trying to add game which is not instance of Game object.');
		}
		return $this;
	}
	public function getGames() {
		return $this->games;
	}
	public function orderGames() {
		$games = $this->games;
		$this->games = [];

		$this->games[] = array_shift($games); // FIRST GAME IS FIRST

		while (count($games) > 0) {
			$found = false;
			// TEAMS FROM LAST 3 GAMES
			// 1 = PLAYED ONLY 3RD GAME FROM END
			// 2 = PLAYED ONLY 2ND GAME FROM END
			// 3 = PLAYED 3RD AND 2ND GAME FROM END
			// 4 = PLAYED ONLY THE LAST GAME
			// 5 = PLAYED 3RD AND 1ST GAME FROM END
			// 6 = PLAYED 2ND ANS 1ST GAME FROM END
			// 7 = PLAYED ALL 3 LAST GAMES
			$teams = [];
			foreach ($this->teams as $team) {
				$teams[$team->id] = 0;
			}
			foreach (end($this->games)->getTeams() as $team) {
				if (!isset($teams[$team->id])) $teams[$team->id] = 4;
				else $teams[$team->id] += 4;
			}
			$g = prev($this->games);
			if ($g instanceof Game) {
				foreach ($g->getTeams() as $team) {
					if (!isset($teams[$team->id])) $teams[$team->id] = 2;
					else $teams[$team->id] += 2;
				}
			}
			$g = prev($this->games);
			if ($g instanceof Game) {
				foreach ($g->getTeams() as $team) {
					if (!isset($teams[$team->id])) $teams[$team->id] = 1;
					else $teams[$team->id]++;
				}
			}

			// CYCLE 1
			// TEAM WHICH DIDN'T PLAY IN LAST GAME (< 4)
			foreach ($games as $key => $game) {
				$gTeams = $game->getTeamsIds();
				$suitable = true;
				foreach ($gTeams as $tid) {
					$plays = isset($teams[$tid]) ? $teams[$tid] : 0;
					if ($plays >= 4) {
						$suitable = false;
					}
				}
				if ($suitable) {
					$this->games[] = $game;
					unset($games[$key]);
					$found = true;
					break;
				}
			}
			if ($found) continue;

			// CYCLE 2
			// ! TEAM WHICH PLAYED IN LAST TWO GAMES (NOT 6 or 7)
			foreach ($games as $key => $game) {
				$gTeams = $game->getTeamsIds();
				$suitable = true;
				foreach ($gTeams as $tid) {
					$plays = isset($teams[$tid]) ? $teams[$tid] : 0;
					if ($plays === 6 || $plays === 7) {
						$suitable = false;
						break;
					}
				}
				if ($suitable) {
					$this->games[] = $game;
					unset($games[$key]);
					$found = true;
					break;
				}
			}
			if ($found) continue;

			// CYCLE 3
			// ! TEAM WHICH PLAYED IN LAST THREE GAMES (NOT 7)
			// TEAMS THAT DIDN'T PLAY IN LAST GAME WILL PLAY THIS GAME (< 4)
			foreach ($games as $key => $game) {
				$gTeams = $game->getTeamsIds();
				$suitable = true;
				$requiredTeams = array_filter($teams, function($a){
					return $a < 4;
				});
				foreach ($gTeams as $tid) {
					$plays = isset($teams[$tid]) ? $teams[$tid] : 0;
					if ($plays === 7) {
						$suitable = false;
						break;
					}
					if (in_array($tid, array_keys($requiredTeams))) {
						unset($requiredTeams[$tid]);
					}
				}
				if (count($requiredTeams) > 0) {
					$suitable = false;
				}
				if ($suitable) {
					$this->games[] = $game;
					unset($games[$key]);
					$found = true;
					break;
				}
			}
			if ($found) continue;

			// CYCLE 4
			// ! TEAM WHICH PLAYED IN LAST THREE GAMES (NOT 7)
			foreach ($games as $key => $game) {
				$gTeams = $game->getTeamsIds();
				$suitable = true;
				foreach ($gTeams as $tid) {
					$plays = isset($teams[$tid]) ? $teams[$tid] : 0;
					if ($plays === 7) {
						$suitable = false;
						break;
					}
				}
				if ($suitable) {
					$this->games[] = $game;
					unset($games[$key]);
					$found = true;
					break;
				}
			}
			if ($found) continue;

			// CYCLE 5
			// TEAMS THAT DIDN'T PLAY IN LAST GAME WILL PLAY THIS GAME (< 4)
			foreach ($games as $key => $game) {
				$gTeams = $game->getTeamsIds();
				$suitable = false;
				$requiredTeams = array_filter($teams, function($a){
					return $a < 4;
				});
				foreach ($gTeams as $tid) {
					if (in_array($tid, array_keys($requiredTeams))) {
						unset($requiredTeams[$tid]);
					}
				}
				if (count($requiredTeams) > 0) {
					$suitable = false;
				}
				if ($suitable) {
					$this->games[] = $game;
					unset($games[$key]);
					$found = true;
					break;
				}
			}
			if ($found) continue;

			// CYCLE 6
			// FIRST AVAILABLE GAME
			$this->games[] = array_shift($games);
		}

		return $this->games;
	}
	public function r_rGames(array $teams = []) {
		$games = [];
		if (count($teams) === 0) $teams = $this->teams;
		switch ($this->inGame) {
			case 2:
				$games = circle_genGames2($teams, $this);
				break;
			case 3:{
				$teamsB = $teams;
				while (count($teamsB) >= 3) {
					$lockedTeam = array_shift($teamsB);
					$games = circle_genGames2($teamsB, $this);
					foreach ($games as $game) {
						$game->addTeam($lockedTeam);
					}
					$games = array_merge($games, $games);
				}
				// $this->orderGames();
				break;}
			case 4:{
				$teamsB = $teams;
				$lockedTeam1 = array_shift($teamsB);
				while (count($teamsB) >= 4) {
					$teamsB2 = $teamsB;
					while (count($teamsB2) >= 3) {
						$lockedTeam2 = array_shift($teamsB2);
						$games = circle_genGames2($teamsB2, $this);
						foreach ($games as $game) {
							$game->addTeam($lockedTeam1, $lockedTeam2);
						}
						$games = array_merge($this->games, $games);
					}
					$lockedTeam1 = array_shift($teamsB);
				}
				$games[] = new Game(array_merge([$lockedTeam1], $teamsB), $this);
				// $this->orderGames();
				break;}
		}
		return $games;
	}
	public function simulate($filters = [], bool $reset = true) {
		foreach ($this->getGames() as $game) {
			$teams = $game->getTeams();
			$results = [];
			foreach ($teams as $team) {
				$results[$team->id] = floor(rand(-1000, 5000));
			}
			$game->setResults($results);
		}
		$return = $this->sortTeams($filters);
		if (!$reset) return $return;
		foreach ($this->getGames() as $game) {
			$game->resetResults();
		}
		return $return;
	}
	public function resetGames() {
		foreach ($this->getGames() as $game) {
			if (isset($game)) $game->resetResults();
		}
		return $this;
	}
	public function isPlayed(){
		foreach ($this->games as $game) {
			if ((isset($game) || !$this->getSkip()) && !$game->isPlayed()) return false;
		}
		return true;
	}

}
