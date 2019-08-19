<?php

namespace TournamentGenerator\Utilis;

/**
 *
 */
class Generator implements \TournamentGenerator\WithGeneratorSetters, \TournamentGenerator\WithSkipSetters
{

	private $group = null;
	private $type = \TournamentGenerator\Constants::ROUND_ROBIN; // TYPE OF ROUND TO CREATE A LAYOUT
	private $inGame = 2; // NUMBER OF TEAMS IN ONE GAME - 2/3/4
	private $maxSize = 4; // MAX SIZE OF GROUP BEFORE SPLIT
	private $allowSkip = false; // IF IS NUMBER OF TEAMS LESS THAN $this->inGame THEN SKIP PLAYING THIS GROUP

	function __construct(\TournamentGenerator\Group $group) {
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


	public function setType(/** @scrutinizer ignore-all */ string $type = \TournamentGenerator\Constants::ROUND_ROBIN) {
		if (in_array($type, \TournamentGenerator\Constants::GroupTypes)) $this->type = $type;
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
		return $this->maxSize;
	}

	public function genGames() {
		switch ($this->type) {
			case \TournamentGenerator\Constants::ROUND_ROBIN:
					$this->group->addGame($this->r_rGames());
				break;
			case \TournamentGenerator\Constants::ROUND_TWO:
					$this->two_twoGames();
				break;
			case \TournamentGenerator\Constants::ROUND_SPLIT:
				$this->cond_splitGames();
				break;
		}
		return $this->group->getGames();
	}
	private function r_rGames(array $teams = []) {
		$games = [];
		if (count($teams) === 0) $teams = $this->group->getTeams();
		switch ($this->inGame) {
			case 2:
				$games = Generator::circle_genGames2($teams, $this->group);
				break;
			case 3:
				$games = $this->r_r3Games($teams, $games);
				break;
			case 4:
				$games = $this->r_r4Games($teams, $games);
				break;
		}
		return $games;
	}
	private function r_r3Games(array $teams, array &$games, \TournamentGenerator\Team $lockedTeam1 = null) {
		$teamsB = $teams;
		while (count($teamsB) >= 3) {
			$lockedTeam = array_shift($teamsB);
			$gamesTemp = Generator::circle_genGames2($teamsB, $this->group);
			foreach ($gamesTemp as $game) {
				if (isset($lockedTeam1)) $game->addTeam($lockedTeam1);
				$game->addTeam($lockedTeam);
			}
			$games = array_merge($games, $gamesTemp);
		}
		return $games;
	}
	private function r_r4Games(array $teams, array &$games) {
		$teamsB = $teams;
		$lockedTeam1 = array_shift($teamsB);
		while (count($teamsB) >= 4) {
			$this->r_r3Games($teamsB, $games, $lockedTeam1);
			$lockedTeam1 = array_shift($teamsB);
		}
		$games[] = new \TournamentGenerator\Game(array_merge([$lockedTeam1], $teamsB), $this->group);
		return $games;
	}
	private function two_twoGames(array $teams = []) {
		if (count($teams) === 0) $teams = $this->group->getTeams();
		$discard = [];
		shuffle($teams);
		$count = count($teams);
		while (count($teams) % $this->inGame !== 0) { $discard[] = array_shift($teams); }

		while (count($teams) > 0) {
			$tInGame = [];
			for ($i=0; $i < $this->inGame; $i++) { $tInGame[] = array_shift($teams); }
			$this->group->game($tInGame);
		}

		if (count($discard) > 0 && !$this->allowSkip) throw new \Exception('Couldn\'t make games with all teams. Expected k*'.$this->inGame.' teams '.$count.' teams given - discarting '.count($discard).' teams ('.implode(', ', $discard).') in group '.$this->group.' - allow skip '.($this->allowSkip ? 'True' : 'False'));

		return $this;
	}
	private function cond_splitGames(array $teams = []) {
		$games = [];
		if (count($teams) === 0) $teams = $this->group->getTeams();

		if (count($teams) > $this->maxSize) {
			$groups = array_chunk($teams, /** @scrutinizer ignore-type */ ceil(count($teams)/ceil(count($teams)/$this->maxSize))); // SPLIT TEAMS INTO GROUP OF MAXIMUM SIZE OF $this->maxSize
			foreach ($groups as $group) { $games[] = $this->r_rGames($group); }
			$g = 0;
			foreach ($games as $group) {	$g += count($group); }
			while ($g > 0) {
				foreach ($games as $key => $group) {
					$this->group->addGame(array_shift($games[$key]));
					if (count($games[$key]) === 0) unset($games[$key]);
					$g--;
				}
			}
			return $this;
		}
		$this->group->addGame($this->r_rGames());

		return $this;
	}

	public function orderGames() {
		$sorter = new Sorter\Games($this->group);

		return $sorter->orderGames();
	}

	// GENERATES A ROBIN-ROBIN BRACKET
	public static function circle_genGames2(array $teams = [], \tournamentGenerator\Group $group) {
		$bracket = []; // ARRAY OF GAMES

		if (count($teams) % 2 != 0) $teams[] = \TournamentGenerator\Constants::DUMMY_TEAM; // IF NOT EVEN NUMBER OF TEAMS, ADD DUMMY

		shuffle($teams); // SHUFFLE TEAMS FOR MORE RANDOMNESS

		for ($i=0; $i < count($teams)-1; $i++) {
			$bracket = array_merge($bracket, Generator::circle_saveBracket($teams, $group)); // SAVE CURRENT ROUND

			$teams = Generator::circle_rotateBracket($teams); // ROTATE TEAMS IN BRACKET
		}

		return $bracket;

	}
	// CREATE GAMES FROM BRACKET
	public static function circle_saveBracket(array $teams, \tournamentGenerator\Group $group) {

		$bracket = [];

		for ($i=0; $i < count($teams)/2; $i++) { // GO THROUGH HALF OF THE TEAMS

			$home = $teams[$i];
			$reverse = array_reverse($teams);
			$away = $reverse[$i];

			if (($home == \TournamentGenerator\Constants::DUMMY_TEAM || $away == \TournamentGenerator\Constants::DUMMY_TEAM)) continue; // SKIP WHEN DUMMY_TEAM IS PRESENT

			$bracket[] = new \TournamentGenerator\Game([$home, $away], $group);

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
