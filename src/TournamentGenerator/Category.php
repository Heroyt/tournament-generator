<?php

namespace TournamentGenerator;

/**
 *
 */
class Category extends Base implements WithSkipSetters, WithTeams, WithRounds
{

	private $rounds = [];
	private $teams = [];
	private $allowSkip = false;

	public function __construct(string $name = '', $id = null) {
		$this->setName($name);
		$this->setId(isset($id) ? $id : uniqid());
	}

	public function addRound(Round ...$rounds){
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
	public function getRounds(){
		return $this->rounds;
	}
	public function getGroups() {
		$groups = [];
		foreach ($this->getRounds() as $round) {
			$groups = array_merge($groups, $round->getGroups());
		}
		return $groups;
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
		foreach ($this->rounds as $round) {
			$teams = \array_merge($teams, $round->getTeams());
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

	public function genGamesSimulate() {
		$games = Utilis\Simulator::simulateCategory($this);
		return $games;
	}
	public function genGamesSimulateReal() {
		$games = Utilis\Simulator::simulateCategoryReal($this);
		return $games;
	}
}
