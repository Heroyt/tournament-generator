<?php

namespace TournamentGenerator;

/**
 *
 */
class Group
{

	private $generator = null;
	private $teams = []; // ARRAY OF TEAMS
	private $progressed = []; // ARRAY OF TEAMS ALREADY PROGRESSED FROM THIS GROUP
	public $name = ''; // DISPLAYABLE NAME
	private /** @scrutinizer ignore-all */ $ordering = POINTS; // WHAT TO DECIDE ON WHEN ORDERING TEAMS
	private $progressions = []; // ARRAY OF PROGRESSION CONDITION OBJECTS
	private $games = []; // ARRAY OF GAME OBJECTS
	public $id = ''; // UNIQID OF GROUP FOR IDENTIFICATIONT
	public $winPoints = 3; // POINTS AQUIRED FROM WINNING
	public $drawPoints = 1; // POINTS AQUIRED FROM DRAW
	public $lostPoints = 0; // POINTS AQUIRED FROM LOOSING
	public $secondPoints = 2; // POINTS AQUIRED FROM BEING SECOND (APPLIES ONLY FOR 3 OR 4 INGAME VALUE)
	public $thirdPoints = 1; // POINTS AQUIRED FROM BEING THIRD (APPLIES ONLY FOR 4 INGAME VALUE)
	public $progressPoints = 50; // POINTS AQUIRED FROM PROGRESSING TO THE NEXT ROUND
	public $order = 0; // ORDER OF GROUPS IN ROUND

	function __construct(array $settings = []) {
		$this->id = uniqid();
		$this->generator = new Generator($this);
		foreach ($settings as $key => $value) {
			switch ($key) {
				case 'name':
					if (gettype($value) !== 'string') throw new \Exception('Expected string as group name '.gettype($value).' given');
					$this->name = $value;
					break;
				case 'type':
					$this->generator->setType($value);
					break;
				case 'ordering':
					if (!in_array($value, orderingTypes)) throw new \Exception('Unknown group ordering: '.$value);
					$this->ordering = $value;
					break;
				case 'inGame':
					$this->generator->setInGame($value);
					break;
				case 'maxSize':
					$value = (int) $value;
					if ($value > 1) $this->generator->setMaxSize($value);
					break;
				case 'order':
					$this->order = (int) $value;
					break;
			}
		}
	}
	function __toString() {
		return 'Group '.$this->name;
	}

	public function allowSkip(){
		$this->generator->allowSkip();
		return $this;
	}
	public function disallowSkip(){
		$this->generator->disallowSkip();
		return $this;
	}
	public function setSkip(bool $skip) {
		$this->generator->setSkip($skip);
		return $this;
	}
	public function getSkip() {
		return $this->generator->getSkip();
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
	public function sortTeams($filters = [], $ordering = null) {
		if (!isset($ordering)) $ordering = $this->ordering;
		switch ($ordering) {
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

	public function setType(string $type = R_R) {
		$this->generator->setType($type);
		return $this;
	}
	public function getType() {
		return $this->generator->getType();
	}

	public function setOrdering(string $ordering = POINTS) {
		if (in_array($ordering, orderingTypes)) $this->ordering = $ordering;
		else throw new \Exception('Unknown group ordering: '.$ordering);
		return $this;
	}
	public function getOrdering() {
		return $this->ordering;
	}

	public function setInGame(int $inGame) {
		$this->generator->setInGame($inGame);
		return $this;
	}
	public function getInGame() {
		return $this->generator->getInGame();
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
	public function progress(bool $blank = false) {
		foreach ($this->progressions as $progression) {
			$progression->progress($blank);
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
	public function isProgressed(Team $team) {
		if (in_array($team->id, $this->progressed)) {
			return true;
		}
		return false;
	}

	public function genGames() {
		$this->generator->genGames();
		return $this->games;
	}

	public function game(array $teams = []) {
		$g = new Game($teams, $this);
		$this->games[] = $g;
		return $g;
	}
	public function addGame(...$games){
		foreach ($games as $key => $game) {
			if (gettype($game) === 'array') {
				unset($games[$key]);
				$games = array_merge($games, $game);
			}
		}
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
		if (count($this->games) <= 4) return $this->games;
		$this->games = $this->generator->orderGames();
		return $this->games;
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
