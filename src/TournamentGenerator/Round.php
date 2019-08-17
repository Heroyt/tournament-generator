<?php

namespace TournamentGenerator;

/**
 *
 */
class Round
{

	private $name = '';
	private $id = '';
	private $groups = [];
	private $games = [];
	private $teams = [];
	private $allowSkip = false;

	function __construct(string $name = '', $id = null) {
		$this->setName($name);
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

	public function addGroup(Group ...$groups){
		foreach ($groups as $group) {
			$this->groups[] = $group;
		}
		return $this;
	}
	public function group(string $name, $id = null) {
		$g = new Group($name, $id);
		$this->groups[] = $g->setSkip($this->allowSkip);
		return $g;
	}
	public function getGroups(){
		$this->orderGroups();
		return $this->groups;
	}
	public function getGroupsIds() {
		$this->orderGroups();
		return array_map(function($a) { return $a->getId(); }, $this->groups);
	}
	public function orderGroups() {
		usort($this->groups, function($a, $b){
			return $a->getOrder() - $b->getOrder();
		});
		return $this->groups;
	}

	public function allowSkip(){
		$this->allowSkip = true;
		return $this;
	}
	public function disallowSkip(){
		$this->allowSkip = false;
		return $this;
	}
	public function setSkip(bool $skip = false) {
		$this->allowSkip = $skip;
		return $this;
	}
	public function getSkip() {
		return $this->allowSkip;
	}

	public function genGames(){
		foreach ($this->groups as $group) {
			$group->genGames();
			$this->games = array_merge($this->games, $group->orderGames());
		}
		return $this->games;
	}
	public function getGames() {
		return $this->games;
	}
	public function isPlayed(){
		if (count($this->games) === 0) return false;
		foreach ($this->groups as $group) {
			if (!$group->isPlayed()) return false;
		}
		return true;
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
	public function getTeams(bool $ordered = false, $ordering = \TournamentGenerator\Constants::POINTS) {
		if (count($this->teams) == 0) {
			$teams = [];
			foreach ($this->groups as $group) {
				$teams = array_merge($teams, $group->getTeams());
			}
			$this->teams = $teams;
		}
		if ($ordered) {
			$this->sortTeams($ordering);
		}
		return $this->teams;
	}
	public function sortTeams($ordering = \TournamentGenerator\Constants::POINTS) {
		Utilis\Sorter\Teams::sortRound($this->teams, $this, $ordering);
		return $this->teams;
	}

	public function splitTeams(Group ...$groups) {

		if (count($groups) === 0) $groups = $this->getGroups();

		foreach ($groups as $key => $value) {
			if (gettype($value) === 'array') {
				unset($groups[$key]);
				$groups = array_merge($groups, $value);
			}
		}

		$teams = $this->getTeams();
		shuffle($teams);

		while (count($teams) > 0) {
			foreach ($groups as $group) {
				if (count($teams) > 0) $group->addTeam(array_shift($teams));
			}
		}
		return $this;
	}

	public function progress(bool $blank = false){
		foreach ($this->groups as $group) {
			$group->progress($blank);
		}
		return $this;
	}

	public function simulate() {
		Utilis\Simulator::simulateRound($this);
		return $this;
	}
	public function resetGames() {
		foreach ($this->groups as $group) {
			$group->resetGames();
		}
		return $this;
	}
}
