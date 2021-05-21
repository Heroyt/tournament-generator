<?php

namespace TournamentGenerator;

use Exception;
use TournamentGenerator\Interfaces\WithGames;
use TournamentGenerator\Interfaces\WithGeneratorSetters;
use TournamentGenerator\Interfaces\WithSkipSetters;
use TournamentGenerator\Interfaces\WithTeams;
use TournamentGenerator\Traits\WithGames as WithGamesTrait;
use TournamentGenerator\Traits\WithTeams as WithTeamsTrait;

/**
 * Tournament group
 *
 * Group is a collection of teams that play against each other. It defaults to Round-robin group where one team plays against every other team in a group.
 * Group can also be setup in such a way that teams play only one game against one other team (randomly selected). Teams from groups can be progressed (moved) to other groups.
 *
 * @package TournamentGenerator
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.1
 */
class Group extends Base implements WithGeneratorSetters, WithSkipSetters, WithTeams, WithGames
{
	use WithTeamsTrait;
	use WithGamesTrait;

	/** @var Helpers\Generator Generator class to generate games of this group */
	protected Helpers\Generator $generator;
	/** @var string[]|int[] List of already progressed teams' id */
	protected array $progressed = [];
	/** @var string Ordering parameter */
	protected string $ordering = Constants::POINTS;
	/** @var Progression[] List of progressions from this group */
	protected array $progressions = [];
	/** @var int Points acquired for winning */
	protected int $winPoints = 3;
	/** @var int Points acquired from draw */
	protected int $drawPoints = 1;
	/** @var int Points acquired from loss */
	protected int $lostPoints = 0;
	/** @var int Points acquired from placing second (only for 3 or 4 teams in one game) */
	protected int $secondPoints = 2;
	/** @var int Points acquired from placing third (only for 4 teams in one game) */
	protected int $thirdPoints = 1;
	/**
	 * @var int Points acquired from progressing to the next round
	 * @details This can be useful when getting the total team order in a tournament. Ex: If you consider teams that progressed to the next round to be higher on the scoreboard even if they got less points in total than some other teams that did not progressed.
	 */
	protected int $progressPoints = 50;
	/** @var int Group order in a round */
	protected int $order = 0;

	/**
	 * Group constructor.
	 *
	 * @param string $name Group name
	 * @param null   $id   Group id - if omitted -> it is generated automatically as unique string
	 */
	public function __construct(string $name, $id = null) {
		$this->setName($name);
		$this->generator = new Helpers\Generator($this);
		$this->setId($id ?? uniqid('', false));
	}

	/**
	 * Add one or more teams into the object.
	 *
	 * @param Team ...$teams Team objects
	 *
	 * @return $this
	 */
	public function addTeam(Team ...$teams) : Group {
		foreach ($teams as $team) {
			$this->teams[] = $team;
			$team->addGroupResults($this);
		}
		return $this;
	}

	/**
	 * Create a new team and add it into the object
	 *
	 * @param string $name Name of the new team
	 * @param null   $id   Id of the new team - if omitted -> it is generated automatically as unique string
	 *
	 * @return Team Newly created team
	 */
	public function team(string $name = '', $id = null) : Team {
		$t = new Team($name, $id);
		$this->teams[] = $t;
		$t->addGroupResults($this);
		return $t;
	}

	/**
	 * Allows round skipping
	 *
	 * @return $this
	 */
	public function allowSkip() : Group {
		$this->generator->allowSkip();
		return $this;
	}

	/**
	 * Set round skipping
	 *
	 * @param bool $skip
	 *
	 * @return $this
	 */
	public function disallowSkip() : Group {
		$this->generator->disallowSkip();
		return $this;
	}

	/**
	 * Set round skipping
	 *
	 * @param bool $skip
	 *
	 * @return $this
	 */
	public function setSkip(bool $skip) : Group {
		$this->generator->setSkip($skip);
		return $this;
	}

	/**
	 * Getter for round skipping
	 *
	 * @return bool
	 */
	public function getSkip() : bool {
		return $this->generator->getSkip();
	}

	/**
	 * Get points for winning
	 *
	 * @return int
	 */
	public function getWinPoints() : int {
		return $this->winPoints;
	}

	/**
	 * Set points for winning
	 *
	 * @param int $points
	 *
	 * @return $this
	 */
	public function setWinPoints(int $points) : Group {
		$this->winPoints = $points;
		return $this;
	}

	/**
	 * Get points for draw
	 *
	 * @return int
	 */
	public function getDrawPoints() : int {
		return $this->drawPoints;
	}

	/**
	 * Set points for draw
	 *
	 * @param int $points
	 *
	 * @return $this
	 */
	public function setDrawPoints(int $points) : Group {
		$this->drawPoints = $points;
		return $this;
	}

	/**
	 * Get points for losing
	 *
	 * @return int
	 */
	public function getLostPoints() : int {
		return $this->lostPoints;
	}

	/**
	 * Set points for losing
	 *
	 * @param int $points
	 *
	 * @return $this
	 */
	public function setLostPoints(int $points) : Group {
		$this->lostPoints = $points;
		return $this;
	}

	/**
	 * Get points for being second
	 *
	 * @return int
	 */
	public function getSecondPoints() : int {
		return $this->secondPoints;
	}

	/**
	 * Set points for being second
	 *
	 * @param int $points
	 *
	 * @return $this
	 */
	public function setSecondPoints(int $points) : Group {
		$this->secondPoints = $points;
		return $this;
	}

	/**
	 * Get points for being third
	 *
	 * @return int
	 */
	public function getThirdPoints() : int {
		return $this->thirdPoints;
	}

	/**
	 * Set points for being third
	 *
	 * @param int $points
	 *
	 * @return $this
	 */
	public function setThirdPoints(int $points) : Group {
		$this->thirdPoints = $points;
		return $this;
	}

	/**
	 * Get points for progression
	 *
	 * @return int
	 */
	public function getProgressPoints() : int {
		return $this->progressPoints;
	}

	/**
	 * Set points for progression
	 *
	 * @param int $points
	 *
	 * @return Group
	 */
	public function setProgressPoints(int $points) : Group {
		$this->progressPoints = $points;
		return $this;
	}

	/**
	 * Set maximum group size
	 *
	 * Does not disallow adding teams to this round!
	 * This can be used to split teams in a group into "subgroups" if you need to limit the maximum number of games.
	 *
	 * @param int $size
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function setMaxSize(int $size) : Group {
		$this->generator->setMaxSize($size);
		return $this;
	}

	/**
	 * Get the maximum group size
	 *
	 * Does not disallow adding teams to this round!
	 * This can be used to split teams in a group into "subgroups" if you need to limit the maximum number of games.
	 *
	 * @return int
	 */
	public function getMaxSize() : int {
		return $this->generator->getMaxSize();
	}

	/**
	 * Set group type
	 *
	 * @param string $type
	 *
	 * @return $this
	 * @throws Exception
	 * @see Constants::GroupTypes
	 *
	 */
	public function setType(string $type = Constants::ROUND_ROBIN) : Group {
		$this->generator->setType($type);
		return $this;
	}

	/**
	 * Get group type
	 *
	 * @return string
	 * @see Constants::GroupTypes
	 *
	 */
	public function getType() : string {
		return $this->generator->getType();
	}

	/**
	 * Get group order
	 *
	 * @return int
	 */
	public function getOrder() : int {
		return $this->order;
	}

	/**
	 * Set group order
	 *
	 * @param int $order
	 *
	 * @return $this
	 */
	public function setOrder(int $order) : Group {
		$this->order = $order;
		return $this;
	}

	/**
	 * Get parameter to order the teams by
	 *
	 * @return string
	 */
	public function getOrdering() : string {
		return $this->ordering;
	}

	/**
	 * Set parameter to order the teams by
	 *
	 * @param string $ordering
	 *
	 * @return $this
	 * @throws Exception
	 * @see Constants::OrderingTypes
	 *
	 */
	public function setOrdering(string $ordering = Constants::POINTS) : Group {
		if (!in_array($ordering, Constants::OrderingTypes, true)) {
			throw new Exception('Unknown group ordering: '.$ordering);
		}
		$this->ordering = $ordering;
		return $this;
	}

	/**
	 * Set how many teams play in one game
	 *
	 * @param int $inGame 2 / 3 / 4
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function setInGame(int $inGame) : Group {
		$this->generator->setInGame($inGame);
		return $this;
	}

	/**
	 * Get how many teams play in one game
	 *
	 * @return int
	 */
	public function getInGame() : int {
		return $this->generator->getInGame();
	}

	/**
	 * Add a progression to this group
	 *
	 * @param Progression $progression
	 *
	 * @return $this
	 */
	public function addProgression(Progression $progression) : Group {
		$this->progressions[] = $progression;
		return $this;
	}

	/**
	 * Creates a new progression from this group
	 *
	 * Progression uses a similar syntax to php's array_slice() function.
	 *
	 * @param Group    $to     Which group to progress to
	 * @param int      $offset First index
	 * @param int|null $len    Maximum number of teams to progress
	 *
	 * @return Progression
	 *
	 * @see https://www.php.net/manual/en/function.array-slice.php
	 */
	public function progression(Group $to, int $offset = 0, int $len = null) : Progression {
		$p = new Progression($this, $to, $offset, $len);
		$this->progressions[] = $p;
		return $p;
	}

	/**
	 * Progress all teams using already setup progression
	 *
	 * @pre  All progressions are setup
	 * @post All teams have been moved into their next groups
	 *
	 * @param bool $blank If true - create dummy teams instead of progressing the real objects
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function progress(bool $blank = false) : Group {
		foreach ($this->progressions as $progression) {
			$progression->progress($blank);
		}
		return $this;
	}

	/**
	 * Add teams to the `progressed` list
	 *
	 * @param Team[] $teams
	 *
	 * @return $this
	 */
	public function addProgressed(Team ...$teams) : Group {
		$this->progressed = array_merge($this->progressed, array_map(static function($a) {
			return $a->getId();
		}, $teams));
		return $this;
	}

	/**
	 * Check if a given team is progressed from this group
	 *
	 * @param Team $team
	 *
	 * @return bool
	 */
	public function isProgressed(Team $team) : bool {
		return in_array($team->getId(), $this->progressed, true);
	}

	/**
	 * Generate all games
	 *
	 * @return array
	 * @throws Exception
	 */
	public function genGames() : array {
		$this->generator->genGames();
		return $this->games;
	}

	/**
	 * Create a new game and add it to the group
	 *
	 * @param Team[] $teams Teams that are playing
	 *
	 * @return Game
	 * @throws Exception
	 */
	public function game(array $teams = []) : Game {
		$g = new Game($teams, $this);
		$this->games[] = $g;
		return $g;
	}

	/**
	 * Add games to this group
	 *
	 * @param Game[] $games
	 *
	 * @return $this
	 */
	public function addGame(Game ...$games) : Group {
		$this->games = array_merge($this->games, $games);
		return $this;
	}

	/**
	 * Order generated games to minimize teams playing multiple games after one other.
	 *
	 * @return Game[]
	 */
	public function orderGames() : array {
		if (count($this->games) <= 4) {
			return $this->games;
		}
		$this->games = $this->generator->orderGames();
		return $this->games;
	}

	/**
	 * Simulate all games in this group as they would be played for real
	 *
	 * @param TeamFilter[]|TeamFilter[][] $filters Filters to teams returned from the group
	 * @param bool                        $reset   If true - the scores will be reset after simulation
	 *
	 * @return Team[]
	 * @throws Exception
	 */
	public function simulate(array $filters = [], bool $reset = true) : array {
		return Helpers\Simulator::simulateGroup($this, $filters, $reset);
	}

	/**
	 * Reset all game results as if they were not played
	 *
	 * @post All games in this group are marked as "not played"
	 * @post All scores in this group are deleted
	 *
	 * @return $this
	 */
	public function resetGames() : Group {
		foreach ($this->getGames() as $game) {
			$game->resetResults();
		}
		return $this;
	}

	/**
	 * Check if all games in this group has been played
	 *
	 * @return bool
	 */
	public function isPlayed() : bool {
		if (count($this->games) === 0) {
			return false;
		}
		return count(array_filter($this->games, static function($a) {
				return $a->isPlayed();
			})) !== 0;
	}

}
