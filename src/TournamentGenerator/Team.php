<?php

namespace TournamentGenerator;

use Exception;
use InvalidArgumentException;
use TournamentGenerator\Export\ExporterInterface;
use TournamentGenerator\Export\Single\TeamExporter;
use TournamentGenerator\Interfaces\Exportable;
use TournamentGenerator\Traits\HasPositions;
use TournamentGenerator\Traits\HasScore;

/**
 * Class that identifies a team and holds the scores from the whole tournament.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.1
 * @package TournamentGenerator
 */
class Team extends Base implements Exportable
{
	use HasScore;
	use HasPositions;

	/**
	 * @var string     $name The name of the team
	 * @var string|int $id   The unique identifier of the team
	 */

	/** @var Game[] $games A list of games played by this team */
	protected array $games = [];
	/** @var array $gamesWith Multi-dimensional associative array of number of games together with other teams */
	protected array $gamesWith = [];
	/** @var int Seeding score */
	protected int $seed = 0;

	/**
	 * Initiates a team class
	 *
	 * @param string     $name Name of the team
	 * @param string|int $id   Unique identifier of the team
	 *
	 * @throws InvalidArgumentException if the provided argument id is not of type 'null' or 'string' or 'int'
	 */
	public function __construct(string $name = 'team', $id = null) {
		$this->setName($name);
		$this->setId($id ?? uniqid('', false));
	}

	public function seed(int $score) : Team {
		$this->seed = $score;
		return $this;
	}

	/**
	 * Gets team statistics from the given group without the group object
	 *
	 * @param string|int $groupId Unique identifier of the group to get its results
	 *
	 * @return array  All the statistics including points, score, wins, draws, losses, times being second, times being third
	 * @throws Exception if the group with given groupId doesn't exist
	 *
	 */
	public function getGamesInfo($groupId) : array {
		return array_filter($this->getGroupResults($groupId), static function($k) {
			return $k !== 'group';
		}, ARRAY_FILTER_USE_KEY);
	}

	/**
	 * Gets team statistics from the given group
	 *
	 * @param string|int|null $groupId Unique identifier of the group to get its results
	 *
	 * @return array  All the statistics including points, score, wins, draws, losses, times being second, times being third if the group id is set or all the statistics
	 * @throws Exception if the group with given groupId doesn't exist
	 *
	 */
	public function getGroupResults($groupId = null) : array {
		if (isset($groupId)) {
			if (!isset($this->groupResults[$groupId])) {
				throw new Exception('Trying to get nonexistent group results ('.$groupId.')');
			}
			return $this->groupResults[$groupId];
		}
		return $this->groupResults;
	}

	/**
	 * Creates a new data-array to store statistics for a new group
	 *
	 * Resets the statistics if the group was already added
	 *
	 * @param Group $group A group object to add its results
	 *
	 * @return $this
	 */
	public function addGroupResults(Group $group) : Team {
		$this->groupResults[$group->getId()] = [
			'group'  => $group,
			'points' => 0,
			'score'  => 0,
			'wins'   => 0,
			'draws'  => 0,
			'losses' => 0,
			'second' => 0,
			'third'  => 0
		];
		return $this;
	}

	/**
	 * Adds a record of a game with another team in a group
	 *
	 * @param Team  $team  A team that played with this team
	 * @param Group $group A group that the teams were playing in
	 *
	 * @return $this
	 */
	public function addGameWith(Team $team, Group $group) : Team {
		if (!isset($this->gamesWith[$group->getId()][$team->getId()])) {
			$this->gamesWith[$group->getId()][$team->getId()] = 0;
		}
		$this->gamesWith[$group->getId()][$team->getId()]++;
		return $this;
	}

	/**
	 * Gets a record of a game with another team or teams
	 *
	 * @param Team|null  $team  A team to get the games with
	 * @param Group|null $group A group from where to get the games
	 *
	 * @return array|int The number of games played with a team in a group if both arguments are given, array of all games with all teams from a group if only group is given, array of games with team from all groups if only a team argument is given or all games with all teams from all groups if no argument is given
	 */
	public function getGameWith(Team $team = null, Group $group = null) {
		if (isset($group)) {
			if (isset($team)) {
				return $this->gamesWith[$group->getId()][$team->getId()];
			}
			return $this->gamesWith[$group->getId()];
		}
		if (isset($team)) {
			$return = [];
			foreach ($this->gamesWith as $id => $games) {
				$filter = array_filter($games, static function($key) use ($team) {
					return $key === $team->getId();
				}, ARRAY_FILTER_USE_KEY);
				if (count($filter) > 0) {
					$return[$id] = $filter;
				}
			}
			return $return;
		}
		return $this->gamesWith;
	}

	/**
	 * Adds a group to a team and creates an array for all games to be played
	 *
	 * @param Group $group A group to add
	 *
	 * @return $this
	 */
	public function addGroup(Group $group) : Team {
		if (!isset($this->games[$group->getId()])) {
			$this->games[$group->getId()] = [];
		}
		return $this;
	}

	/**
	 * Adds a game to this team
	 *
	 * @param Game $game A game to add
	 *
	 * @return $this
	 */
	public function addGame(Game $game) : Team {
		$group = $game->getGroup();
		if (!isset($this->games[$group->getId()])) {
			$this->games[$group->getId()] = [];
		}
		$this->games[$group->getId()][] = $game;
		return $this;
	}

	/**
	 * Gets all game from given group
	 *
	 * @param Group|null      $group   A group to get its game from
	 * @param string|int|null $groupId An id of group to get its game from
	 *
	 * @return array Games from a group or all games if both arguments are null
	 */
	public function getGames(?Group $group = null, $groupId = null) {
		if (!is_null($group) && isset($this->games[$group->getId()])) {
			return $this->games[$group->getId()];
		}
		if (isset($groupId, $this->games[$groupId])) {
			return $this->games[$groupId];
		}
		return $this->games;
	}

	/**
	 * Prepares an export query for the object
	 *
	 * @return ExporterInterface Exporter for this class
	 */
	public function export() : ExporterInterface {
		return TeamExporter::start($this);
	}

	/**
	 * Get seeding score
	 *
	 * @return int
	 */
	public function getSeed() : int {
		return $this->seed;
	}
}
