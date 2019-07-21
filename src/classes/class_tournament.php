<?php

namespace TournamentGenerator;

require_once '../functions.php';

/**
 *
 */
class Tournament
{

	public $name = '';
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
			return $rounds;
		}
		return $this->rounds;
	}

	public function addTeam(...$teams) {
		foreach ($teams as $team) {
			if ($team instanceof Team)  {
				$this->teams[] = $team;
			}
			elseif (gettype($team) === 'array') {
				foreach ($team as $team2) {
					if ($team2 instanceof Team) $this->teams[] = $team2;
				}
			}
			else throw new \Exception('Trying to add team which is not an instance of Team class');
		}
		return $this;
	}
	public function team(string $name = '') {
		$t = new Team($name);
		$this->teams[] = $t;
		return $t;
	}
	public function getTeams() {
		if (count($this->teams) > 0) return $this->teams;
		$teams = [];
		foreach ($this->categories as $category) {
			$teams = array_merge($teams, $category->getTeams());
		}
		foreach ($this->rounds as $round) {
			$teams = array_merge($teams, $round->getTeams());
		}
		$this->teams = $teams;
		return $teams;
	}

	public function getGames() {
		$games = [];
		foreach ($this->getRounds() as $round) {
			$games = array_merge($games, $round->getGames());
		}
		return $games;
	}

	public function splitTeams(...$wheres) {

		if (count($wheres) === 0) $wheres = array_merge($this->getRounds(), $this->getCategories());

		foreach ($wheres as $key => $value) {
			if (gettype($value) === 'array') {
				unset($wheres[$key]);
				$wheres = array_merge($wheres, $value);
			}
		}

		$teams = $this->getTeams();
		shuffle($teams);

		while (count($teams) > 0) {
			foreach ($wheres as $where) {
				if ($where instanceof Round) {
					if (count($teams) > 0) $where->addTeam(array_shift($teams));
				}
				elseif ($where instanceof Category) {
					if (count($teams) > 0) $where->addTeam(array_shift($teams));
				}
			}
		}
		foreach ($wheres as $where) {
			$where->splitTeams();
		}
		return $this;
	}

	public function genGamesSimulate(bool $returnTime = false) {
		$games = [];
		if (count($this->categories) > 0) {
			foreach ($this->categories as $category) {
				$games = array_merge($games, $category->genGamesSimulate());
			}
		}
		elseif (count($this->rounds) > 0) {
			foreach ($this->rounds as $round) {
				$games = array_merge($games, $round->genGames());
				$round->simulate()->progressBlank();
			}
			foreach ($this->rounds as $round) {
				$round->resetGames();
			}
		}
		else {
			throw new \Exception('There are no rounds or categories to simulate games from.');
		}
		if ($returnTime) return $this->getTournamentTime();
		return $games;
	}
	public function genGamesSimulateReal(bool $returnTime = false) {
		$games = [];
		if (count($this->categories) > 0) {
			foreach ($this->categories as $category) {
				$games = array_merge($games, $category->genGamesSimulate());
			}
		}
		elseif (count($this->rounds) > 0) {
			foreach ($this->rounds as $round) {
				$games = array_merge($games, $round->genGames());
				$round->simulate();
				$round->progress();
			}
		}
		else {
			throw new \Exception('There are no rounds or categories to simulate games from.');
		}
		if ($returnTime) return $this->getTournamentTime();
		return $games;
	}

}
