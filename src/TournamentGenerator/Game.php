<?php

namespace TournamentGenerator;

use Exception;
use JsonSerializable;
use TournamentGenerator\Export\ExporterInterface;
use TournamentGenerator\Export\Single\GameExporter;
use TournamentGenerator\Interfaces\Exportable;
use TournamentGenerator\Interfaces\WithId;
use TypeError;

/**
 * Class Game
 *
 * @package TournamentGenerator
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.1
 */
class Game implements WithId, Exportable, JsonSerializable
{

	/** @var int Autoincrement game id */
	protected int $id;
	/** @var Team[] Teams playing this game */
	protected array $teams;
	/** @var array[] List of scores - [teamId => [score => value, type => win|loss|draw|second|third]] pairs */
	protected array $results = [];
	/** @var Group Group that the game belongs to */
	protected Group $group;
	/** @var int|string Id of the winning team */
	protected $winId;
	/** @var int|string Id of the losing team */
	protected $lossId;
	/** @var int|string Id of the second team */
	private $secondId;
	/** @var int|string Id of the third team */
	private $thirdId;
	/** @var int[]|string[] Ids of the teams that have drawn */
	private array $drawIds = [];

	/**
	 * Game constructor.
	 *
	 * @param Team[] $teams Teams that play in this game
	 * @param Group  $group Group that this game belongs to
	 */
	public function __construct(array $teams, Group $group) {
		$this->group = $group;
		$this->addTeam(...$teams);
	}

	/**
	 * Add teams to this game
	 *
	 * @param Team[] $teams
	 *
	 * @return $this
	 */
	public function addTeam(Team ...$teams) : Game {
		foreach ($teams as $team) {
			$this->teams[] = $team;
			$team->addGame($this);

			// Log games with this added teams to all teams added before
			foreach ($this->teams as $team2) {
				if ($team === $team2) {
					continue;
				}
				$team->addGameWith($team2, $this->group);
				$team2->addGameWith($team, $this->group);
			}
		}
		return $this;
	}

	/**
	 * Get the parent group object
	 *
	 * @return Group
	 */
	public function getGroup() : Group {
		return $this->group;
	}

	/**
	 * Get all teams from the game
	 *
	 * @return Team[]
	 */
	public function getTeams() : array {
		return $this->teams;
	}

	/**
	 * Get all team ids from this game
	 *
	 * @return string[]|int[]
	 */
	public function getTeamsIds() : array {
		return array_map(static function($a) {
			return $a->getId();
		}, $this->teams);
	}

	/**
	 * Get results
	 *
	 * @return array[]
	 */
	public function getResults() : array {
		ksort($this->results);
		return $this->results;
	}

	/**
	 * Set the game's results
	 *
	 * Results is an array of [teamId => teamScore] key-value pairs.
	 *
	 * @param int[] $results array of [teamId => teamScore] key-value pairs
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function setResults(array $results = []) : Game {
		if (count($this->results) === 0) {
			$this->resetResults();
		}
		arsort($results);
		$i = 1;
		foreach ($results as $id => $score) {
			if (!is_numeric($score)) {
				throw new TypeError('Score passed to TournamentGenerator\Game::setResults() must be of the type numeric, '.gettype($score).' given');
			}
			$team = $this->getTeam($id);
			if (!isset($team)) {
				throw new Exception('Couldn\'t find team with id of "'.$id.'"');
			}
			$this->setTeamScore($i, $team, $results, $score);
			$i++;
		}
		return $this;
	}

	/**
	 * Reset the game's results
	 *
	 * @post Scores are removed
	 * @post Team points are subtracted
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function resetResults() : Game {
		foreach ($this->results as $teamId => $score) {
			$this->resetTeamScore($teamId, $score);
		}
		$this->results = [];
		return $this;
	}

	/**
	 * Resets a score for a team
	 *
	 * @param string|int $teamId
	 * @param array      $score
	 *
	 * @throws Exception
	 * @noinspection NullPointerExceptionInspection
	 */
	protected function resetTeamScore($teamId, array $score) : void {
		$team = $this->getTeam($teamId);
		$team->groupResults[$this->group->getId()]['score'] -= $score['score'];
		$team->removeScore($score['score']);
		switch ($score['type']) {
			case 'win':
				$team->removeWin($this->group->getId());
				break;
			case 'draw':
				$team->removeDraw($this->group->getId());
				break;
			case 'loss':
				$team->removeLoss($this->group->getId());
				break;
			case 'second':
				$team->removeSecond($this->group->getId());
				break;
			case 'third':
				$team->removeThird($this->group->getId());
				break;
		}
	}

	/**
	 * Get team by ID
	 *
	 * @param string|int $id Team ID
	 *
	 * @return Team|null
	 */
	public function getTeam($id) : ?Team {
		$key = array_search($id, array_map(static function($a) {
			return $a->getId();
		}, $this->teams),   true);
		return ($key !== false ? $this->teams[$key] : null);
	}

	/**
	 * Set a score for a team
	 *
	 * @param int   $position Team's position
	 * @param Team  $team
	 * @param int[] $results  The whole results set
	 * @param int   $score    Team's score
	 *
	 * @throws Exception
	 */
	protected function setTeamScore(int $position, Team $team, array $results, int $score) : void {
		$this->results[$team->getId()] = ['score' => $score];
		$team->addScore($score);
		switch ($this->group->getInGame()) {
			case 2:
				$this->setResults2($position, $score, $results, $team);
				break;
			case 3:
				$this->setResults3($position, $team);
				break;
			case 4:
				$this->setResults4($position, $team);
				break;
		}
		$team->groupResults[$this->group->getId()]['score'] += $score;
	}

	/**
	 * Set results for 2 team game
	 *
	 * @param int   $teamPosition Team's position (first = 1, second = 2)
	 * @param int   $score        Team's score
	 * @param int[] $results      Results array (for draw checking)
	 * @param Team  $team         Team object
	 *
	 * @return $this
	 * @throws Exception
	 */
	protected function setResults2(int $teamPosition, int $score, array $results, Team $team) : Game {
		if (count(array_filter($results, static function($a) use ($score) {
				return $a === $score;
			})) > 1) {
			$this->drawIds[] = $team->getId();
			$team->addDraw($this->group->getId());
			$this->results[$team->getId()] += ['points' => $this->group->getDrawPoints(), 'type' => 'draw'];
		}
		elseif ($teamPosition === 1) {
			$this->winId = $team->getId();
			$team->addWin($this->group->getId());
			$this->results[$team->getId()] += ['points' => $this->group->getWinPoints(), 'type' => 'win'];
		}
		else {
			$this->lossId = $team->getId();
			$team->addLoss($this->group->getId());
			$this->results[$team->getId()] += ['points' => $this->group->getLostPoints(), 'type' => 'loss'];
		}
		return $this;
	}

	/**
	 * Set results for 3 team game
	 *
	 * @param int  $teamPosition Team's position (first = 1, second = 2, third = 3)
	 * @param Team $team         Team object
	 *
	 * @return $this
	 * @throws Exception
	 */
	protected function setResults3(int $teamPosition, Team $team) : Game {
		switch ($teamPosition) {
			case 1:
				$this->winId = $team->getId();
				$team->addWin($this->group->getId());
				$this->results[$team->getId()] += ['points' => $this->group->getWinPoints(), 'type' => 'win'];
				break;
			case 2:
				$this->secondId = $team->getId();
				$team->addSecond($this->group->getId());
				$this->results[$team->getId()] += ['points' => $this->group->getSecondPoints(), 'type' => 'second'];
				break;
			case 3:
				$this->lossId = $team->getId();
				$team->addLoss($this->group->getId());
				$this->results[$team->getId()] += ['points' => $this->group->getLostPoints(), 'type' => 'loss'];
				break;
		}
		return $this;
	}

	/**
	 * Set results for 4 team game
	 *
	 * @param int  $teamPosition Team's position (first = 1, second = 2, third = 3, fourth = 4)
	 * @param Team $team         Team object
	 *
	 * @return Game
	 * @throws Exception
	 */
	protected function setResults4(int $teamPosition, Team $team) : Game {
		switch ($teamPosition) {
			case 1:
				$this->winId = $team->getId();
				$team->addWin($this->group->getId());
				$this->results[$team->getId()] += ['points' => $this->group->getWinPoints(), 'type' => 'win'];
				break;
			case 2:
				$this->secondId = $team->getId();
				$team->addSecond($this->group->getId());
				$this->results[$team->getId()] += ['points' => $this->group->getSecondPoints(), 'type' => 'second'];
				break;
			case 3:
				$this->thirdId = $team->getId();
				$team->addThird($this->group->getId());
				$this->results[$team->getId()] += ['points' => $this->group->getThirdPoints(), 'type' => 'third'];
				break;
			case 4:
				$this->lossId = $team->getId();
				$team->addLoss($this->group->getId());
				$this->results[$team->getId()] += ['points' => $this->group->getLostPoints(), 'type' => 'loss'];
				break;
		}
		return $this;
	}

	/**
	 * Get the winning team's id
	 *
	 * @return int|string
	 */
	public function getWin() {
		return $this->winId;
	}

	/**
	 * Get the losing team's id
	 *
	 * @return int|string
	 */
	public function getLoss() {
		return $this->lossId;
	}

	/**
	 * Get the second team's id
	 *
	 * @return int|string
	 */
	public function getSecond() {
		return $this->secondId;
	}

	/**
	 * Get the third team's id
	 *
	 * @return int|string
	 */
	public function getThird() {
		return $this->thirdId;
	}

	/**
	 * Get the draws teams' id
	 *
	 * @return int[]|string[]
	 */
	public function getDraw() : array {
		return $this->drawIds;
	}

	/**
	 * Check if the game has been played
	 *
	 * @return bool
	 */
	public function isPlayed() : bool {
		return count($this->results) > 0;
	}

	/**
	 * @return int
	 * @since 0.5
	 */
	public function getId() : int {
		return $this->id;
	}

	/**
	 * @param int $id
	 *
	 * @return Game
	 * @since 0.5
	 */
	public function setId($id) : Game {
		if (!is_int($id)) {
			throw new TypeError('Game\'s ID needs to be an integer.');
		}
		$this->id = $id;
		return $this;
	}

	/**
	 * @inheritDoc
	 * @return array
	 */
	public function jsonSerialize() : array {
		return $this->export()->get();
	}

	/**
	 * Prepares an export query for the object
	 *
	 * @return ExporterInterface Exporter for this class
	 */
	public function export() : ExporterInterface {
		return GameExporter::start($this);
	}
}
