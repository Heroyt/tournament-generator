<?php

namespace TournamentGenerator;

/**
 *
 */
class Tournament
{

	private $name = '';
	private $categories = [];
	private $rounds = [];
	private $teams = [];

	private $expectedPlay = 0;
	private $expectedGameWait = 0;
	private $expectedRoundWait = 0;
	private $expectedCategoryWait = 0;

	private $allowSkip = false;

	function __construct(string $name = ''){
		$this->name = $name;
	}
	public function __toString() {
		return $this->name;
	}

	public function setName(string $name) {
		$this->name = $name;
		return $this;
	}
	public function getName() {
		return $this->name;
	}

	public function setPlay(int $play) {
		$this->expectedPlay = $play;
		return $this;
	}
	public function getPlay() {
		return $this->expectedPlay;
	}
	public function setGameWait(int $wait) {
		$this->expectedGameWait = $wait;
		return $this;
	}
	public function getGameWait() {
		return $this->expectedGameWait;
	}
	public function setRoundWait(int $wait) {
		$this->expectedRoundWait = $wait;
		return $this;
	}
	public function getRoundWait() {
		return $this->expectedRoundWait;
	}
	public function setCategoryWait(int $wait) {
		$this->expectedCategoryWait = $wait;
		return $this;
	}
	public function getCategoryWait() {
		return $this->expectedCategoryWait;
	}
	public function getTournamentTime(){
		$games = count($this->getGames());
		return $games*$this->expectedPlay+$games*$this->expectedGameWait+count($this->getRounds())*$this->expectedRoundWait+count($this->getCategories())*$this->expectedCategoryWait;
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

	public function addCategory(Category ...$categories){
		foreach ($categories as $category) {
			if ($category instanceof Category) $this->categories[] = $category;
			else throw new \Exception('Trying to add category which is not an instance of the Category class.');
		}
		return $this;
	}
	public function category(string $name = '') {
		$c = new Category($name);
		$this->categories[] = $c->setSkip($this->allowSkip);
		return $c;
	}
	public function getCategories() {
		return $this->categories;
	}

	public function addRound(Round ...$rounds) {
		foreach ($rounds as $round) {
			if ($round instanceof Round) $this->rounds[] = $round;
			else throw new \Exception('Trying to add round which is not an instance of the Round class.');
		}
		return $this;
	}
	public function round(string $name = '') {
		$r = new Round($name);
		$this->rounds[] = $r->setSkip($this->allowSkip);
		return $r;
	}
	public function getRounds() {
		if (count($this->categories) > 0) {
			$rounds = [];
			foreach ($this->categories as $category) {
				$rounds = array_merge($rounds, $category->getRounds());
			}
			return array_merge($rounds, $this->rounds);
		}
		return $this->rounds;
	}

	public function addTeam(Team ...$teams) {
		foreach ($teams as $team) {
			$this->teams[] = $team;
		}
		return $this;
	}
	public function team(string $name = '') {
		$t = new Team($name);
		$this->teams[] = $t;
		return $t;
	}
	public function getTeams(bool $ordered = false, $ordering = \TournamentGenerator\Constants::POINTS) {
		$teams = $this->teams;
		foreach ($this->categories as $category) {
			$teams = array_merge($teams, $category->getTeams());
		}
		foreach ($this->rounds as $round) {
			$teams = array_merge($teams, $round->getTeams());
		}
		$this->teams = \array_unique($teams);
		if ($ordered) $this->sortTeams($ordering);
		return $this->teams;
	}
	public function sortTeams($ordering = \TournamentGenerator\Constants::POINTS) {
		$teams = [];
		for ($i = count($this->rounds)-1; $i >= 0; $i--) {
			$rTeams = array_filter($this->rounds[$i]->getTeams(true, $ordering), function($a) use ($teams) { return !in_array($a, $teams); });
			$teams = array_merge($teams, $rTeams);
		}
		$this->teams = $teams;
		return $this->teams;
	}

	public function getGames() {
		$games = [];
		foreach ($this->getRounds() as $round) {
			$games = array_merge($games, $round->getGames());
		}
		return $games;
	}

	public function splitTeams(Round ...$wheres) {

		if (count($wheres) === 0) $wheres = $this->getRounds();

		foreach ($wheres as $key => $value) {
			if (gettype($value) === 'array') {
				unset($wheres[$key]);
				$wheres = array_merge($wheres, $value);
				continue;
			}
		}

		$teams = $this->getTeams();
		shuffle($teams);

		while (count($teams) > 0) {
			foreach ($wheres as $where) {
				if (count($teams) > 0) $where->addTeam(array_shift($teams));
			}
		}
		foreach ($wheres as $where) {
			$where->splitTeams();
		}
		return $this;
	}

	public function genGamesSimulate(bool $returnTime = false) {
		$games = Utilis\Simulator::simulateTournament($this);

		if ($returnTime) return $this->getTournamentTime();
		return $games;
	}
	public function genGamesSimulateReal(bool $returnTime = false) {
		$games = Utilis\Simulator::simulateTournamentReal($this);

		if ($returnTime) return $this->getTournamentTime();
		return $games;
	}

}
