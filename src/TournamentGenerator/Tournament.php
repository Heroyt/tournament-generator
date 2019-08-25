<?php

namespace TournamentGenerator;

/**
 *
 */
class Tournament implements WithSkipSetters, WithTeams, WithRounds
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
		return $games*$this->expectedPlay+($games-1)*$this->expectedGameWait+(count($this->getRounds())-1)*$this->expectedRoundWait+(count($this->getCategories())-1)*$this->expectedCategoryWait;
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
			$this->categories[] = $category;
		}
		return $this;
	}
	public function category(string $name = '', $id = null) {
		$c = new Category($name, $id);
		$this->categories[] = $c->setSkip($this->allowSkip);
		return $c;
	}
	public function getCategories() {
		return $this->categories;
	}

	public function addRound(Round ...$rounds) {
		foreach ($rounds as $round) {
			$this->rounds[] = $round;
		}
		return $this;
	}
	public function round(string $name = '', $id = null) {
		$r = new Round($name, $id);
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
	public function getGroups() {
		$groups = [];
		foreach ($this->getRounds() as $round) {
			$groups = array_merge($groups, $round->getGroups());
		}
		return $groups;
	}

	public function addTeam(Team ...$teams) {
		foreach ($teams as $team) {
			$this->teams[] = $team;
		}
		return $this;
	}
	public function team(string $name = '', $id = null) {
		$t = new Team($name, $id);
		$this->teams[] = $t;
		return $t;
	}
	public function getTeams(bool $ordered = false, $ordering = \TournamentGenerator\Constants::POINTS, array $filters = []) {
		$teams = $this->teams;
		foreach ($this->categories as $category) {
			$teams = array_merge($teams, $category->getTeams());
		}
		foreach ($this->rounds as $round) {
			$teams = array_merge($teams, $round->getTeams());
		}
		$teams = \array_unique($teams);
		$this->teams = $teams;
		if ($ordered) $teams = $this->sortTeams($ordering);

		// APPLY FILTERS
		$filter = new Filter($this->getGroups(), $filters);
		$filter->filter($teams);

		return $teams;
	}
	public function sortTeams($ordering = \TournamentGenerator\Constants::POINTS, array $filters = []) {
		$teams = [];
		for ($i = count($this->rounds)-1; $i >= 0; $i--) {
			$rTeams = array_filter($this->rounds[$i]->getTeams(true, $ordering), function($a) use ($teams) { return !in_array($a, $teams); });
			$teams = array_merge($teams, $rTeams);
		}
		$this->teams = $teams;

		// APPLY FILTERS
		$filter = new Filter($this->getGroups(), $filters);
		$filter->filter($teams);

		return $teams;
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
