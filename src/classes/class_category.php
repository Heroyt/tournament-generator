<?php

namespace TournamentGenerator;

require_once '../functions.php';

/**
 *
 */
class Category
{

	public $name = '';
	public $id = '';
	private $rounds = [];
	private $teams = [];
	private $allowSkip = false;

	public function __construct(string $name = '') {
		$this->id = uniqid();
		$this->name = $name;
	}

	public function addRound(Round ...$rounds){
		foreach ($rounds as $round) {
			if ($round instanceof Round) $this->rounds[] = $round;
			else throw new \Exception('Trying to add round which is not an instance of Round class.');
		}
		return $this;
	}
	public function round(string $name = '') {
		$r = new Round($name);
		$this->rounds[] = $r->setSkip($this->allowSkip);
		return $r;
	}
	public function getRounds(){
		return $this->rounds;
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

	public function splitTeams(...$rounds) {

		if (count($rounds) === 0) $rounds = $this->getRounds();

		$teams = $this->getTeams();
		shuffle($teams);

		while (count($teams) > 0) {
			foreach ($rounds as $round) {
				if ($round instanceof Round) {
					$round->addTeam(array_shift($teams));
				}
			}
		}
		foreach ($rounds as $round) {
			$round->splitTeams();
		}
		return $this;
	}

	public function genGamesSimulate() {
		$games = [];
		if (count($this->rounds) <= 0) throw new \Exception('There are no rounds to simulate games from.');
		foreach ($this->rounds as $round) {
			$games = array_merge($games, $round->genGames());
			$round->simulate()->progressBlank()->resetGames();
		}
		return $games;
	}
}
