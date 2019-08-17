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
	private $name = ''; // DISPLAYABLE NAME
	private $ordering = \TournamentGenerator\Constants::POINTS; // WHAT TO DECIDE ON WHEN ORDERING TEAMS
	private $progressions = []; // ARRAY OF PROGRESSION CONDITION OBJECTS
	private $games = []; // ARRAY OF GAME OBJECTS
	private $id = ''; // UNIQID OF GROUP FOR IDENTIFICATIONT
	public $winPoints = 3; // POINTS AQUIRED FROM WINNING
	public $drawPoints = 1; // POINTS AQUIRED FROM DRAW
	public $lostPoints = 0; // POINTS AQUIRED FROM LOOSING
	public $secondPoints = 2; // POINTS AQUIRED FROM BEING SECOND (APPLIES ONLY FOR 3 OR 4 INGAME VALUE)
	public $thirdPoints = 1; // POINTS AQUIRED FROM BEING THIRD (APPLIES ONLY FOR 4 INGAME VALUE)
	public $progressPoints = 50; // POINTS AQUIRED FROM PROGRESSING TO THE NEXT ROUND
	private $order = 0; // ORDER OF GROUPS IN ROUND

	function __construct(string $name, $id = null) {
		$this->setName($name);
		$this->generator = new Utilis\Generator($this);
		$this->setId(isset($id) ? $id : uniqid());
	}
	public function __toString() {
		return $this->name;
	}

	public function setName(string $name) {
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	public function setId($id) {
		if (!is_string($id) && !is_int($id)) {
			$this->id = uniqid();
			throw new \Exception('Unsupported id type ('.gettype($id).') - expected type of string or int');
		}
		$this->id = $id;
	}
	public function getId() {
		return $this->id;
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
			if (is_array($team)) {
				foreach ($team as $team2) {
					$this->setTeam($team2);
				}
				continue;
			}
			$this->setTeam($team);
		}
		return $this;
	}
	private function setTeam(Team $team) {
		$this->teams[] = $team;
		$team->addGroupResults($this);
		return $this;
	}
	public function getTeams(array $filters = []) {
		$teams = $this->teams;

		if (gettype($filters) !== 'array' && $filters instanceof TeamFilter) $filters = [$filters];
		elseif (gettype($filters) !== 'array') $filters = [];

		// APPLY FILTERS
		$filter = new Filter($this, $filters);
		$filter->filter($teams);

		return $teams;
	}

	public function team(string $name = '') {
		$t = new Team($name);
		$this->teams[] = $t;
		$t->addGroupResults($this);
		return $t;
	}
	public function sortTeams(array $filters = [], $ordering = null) {
		if (!isset($ordering)) $ordering = $this->ordering;
		Utilis\Sorter\Teams::sortGroup($this->teams, $this, $ordering);
		return $this->getTeams($filters);
	}

	public function setMaxSize(int $size) {
		$this->generator->setMaxSize($size);
		return $this;
	}
	public function getMaxSize() {
		return $this->generator->getMaxSize();
	}

	public function setType(string $type = \TournamentGenerator\Constants::ROUND_ROBIN) {
		$this->generator->setType($type);
		return $this;
	}
	public function getType() {
		return $this->generator->getType();
	}

	public function setOrder(int $order) {
		$this->order = $order;
		return $this;
	}
	public function getOrder() {
		return $this->order;
	}

	public function setOrdering(string $ordering = \TournamentGenerator\Constants::POINTS) {
		if (!in_array($ordering, \TournamentGenerator\Constants::OrderingTypes)) throw new \Exception('Unknown group ordering: '.$ordering);
		$this->ordering = $ordering;
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
			if ($team instanceOf Team) $this->progressed[] = $team;
			elseif (gettype($team) === 'array') {
				$this->progressed = array_merge($this->progressed, array_filter($team, function($a) {
					return ($a instanceof Team);
				}));
			}
		}
		return $this;
	}
	public function isProgressed(Team $team) {
		return in_array($team, $this->progressed);
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
				$this->games = array_merge($this->games, array_filter($game, function($a){ return ($a instanceof Game); }));
				continue;
			}
			if (!$game instanceof Game) throw new \Exception('Trying to add game which is not instance of Game object.');
			$this->games[] = $game;
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

	public function simulate(array $filters = [], bool $reset = true) {
		return Utilis\Simulator::simulateGroup($this, $filters, $reset);
	}
	public function resetGames() {
		foreach ($this->getGames() as $game) {
			$game->resetResults();
		}
		return $this;
	}
	public function isPlayed(){
		if (count($this->games) === 0) return false;
		foreach ($this->games as $game) {
			if (!$game->isPlayed()) return false;
		}
		return true;
	}

}
