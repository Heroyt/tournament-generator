<?php


namespace TournamentGenerator\Traits;

use Exception;
use TournamentGenerator\Constants;
use TournamentGenerator\Containers\TeamContainer;
use TournamentGenerator\Group;
use TournamentGenerator\Helpers\Filter;
use TournamentGenerator\Helpers\Sorter\TeamSorter;
use TournamentGenerator\Interfaces\WithGroups as WithGroupsInterface;
use TournamentGenerator\Interfaces\WithTeams as WithTeamsInterface;
use TournamentGenerator\Round;
use TournamentGenerator\Team;
use TournamentGenerator\TeamFilter;

/**
 * Trait WithTeams
 *
 * @package TournamentGenerator\Traits
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.4
 */
trait WithTeams
{

	/** @var TeamContainer Teams in a object */
	protected TeamContainer $teams;

	/**
	 * Create a new team and add it into the object
	 *
	 * @param string          $name Name of the new team
	 * @param string|int|null $id   Id of the new team - if omitted -> it is generated automatically as unique string
	 *
	 * @return Team Newly created team
	 */
	public function team(string $name = '', $id = null) : Team {
		$t = new Team($name, $id);
		$this->teams->insert($t);
		return $t;
	}

	/**
	 * Split teams into its Groups
	 *
	 * @param Round ...$wheres
	 *
	 * @return $this
	 * @throws Exception
	 * @noinspection CallableParameterUseCaseInTypeContextInspection
	 */
	public function splitTeams(Round ...$wheres) : WithTeamsInterface {
		if (count($wheres) === 0) {
			$wheres = $this->getRounds();
		}

		$teams = $this->getTeams();
		shuffle($teams);

		while (count($teams) > 0) {
			foreach ($wheres as $where) {
				if (count($teams) > 0) {
					$where->addTeam(array_shift($teams));
				}
			}
		}
		foreach ($wheres as $where) {
			$where->splitTeams();
		}
		return $this;
	}

	/**
	 * Get all teams in the object
	 *
	 * @param bool                        $ordered  If true - order the teams by their score/points
	 * @param string|null                 $ordering What to order the teams by - Constants::POINTS, Constants::SCORE
	 * @param TeamFilter[]|TeamFilter[][] $filters  Filters to filter the returned teams (ex. if you only want to get the first 3 teams)
	 *
	 * @return Team[]
	 * @throws Exception
	 */
	public function getTeams(bool $ordered = false, ?string $ordering = Constants::POINTS, array $filters = []) : array {
		if (is_null($ordering)) {
			$ordering = Constants::POINTS;
		}
		if ($ordered) {
			$returnTeams = $this->sortTeams($ordering);
		}
		else {
			$returnTeams = $this->teams->unique()->get();
		}

		// APPLY FILTERS
		if (count($filters) > 0) {
			$this->filterTeams($returnTeams, $filters);
		}

		return $returnTeams;
	}

	/**
	 * Sort the teams by their score/points
	 *
	 * @param string|null                 $ordering What to order the teams by - Constants::POINTS, Constants::SCORE
	 * @param TeamFilter[]|TeamFilter[][] $filters  Filters to filter the returned teams (ex. if you only want to get the first 3 teams)
	 *
	 * @return Team[]
	 * @throws Exception
	 */
	public function sortTeams(?string $ordering = Constants::POINTS, array $filters = []) : array {
		if (is_null($ordering)) {
			$ordering = Constants::POINTS;
		}
		$sorter = new TeamSorter($this->getContainer(), $ordering);
		$teams = $this->teams->addSorter($sorter)->unique()->get();

		// APPLY FILTERS
		if (count($filters) > 0) {
			$this->filterTeams($teams, $filters);
		}

		return $teams;
	}

	/**
	 * Filter teams using the specified filters
	 *
	 * @param array                       $teams   Teams to filter through
	 * @param TeamFilter[]|TeamFilter[][] $filters Filters to use
	 *
	 * @return array
	 * @throws Exception
	 */
	public function filterTeams(array &$teams, array $filters) : array {
		// APPLY FILTERS
		if ($this instanceof WithGroupsInterface) {
			$filter = new Filter($filters, $this->getGroups());
			$filter->filter($teams);
		}
		else if ($this instanceof Group) {
			$filter = new Filter($filters, [$this]);
			$filter->filter($teams);
		}
		return $teams;
	}

	/**
	 * Add one or more teams into the object.
	 *
	 * @param Team ...$teams Team objects
	 *
	 * @return WithTeamsInterface
	 */
	public function addTeam(Team ...$teams) : WithTeamsInterface {
		foreach ($teams as $team) {
			$this->teams->insert($team);
		}
		return $this;
	}

	/**
	 * Get the container for games
	 *
	 * @return TeamContainer
	 */
	public function getTeamContainer() : TeamContainer {
		return $this->teams;
	}

	/**
	 * Add a child container for games
	 *
	 * @param TeamContainer $container
	 *
	 * @return WithTeamsInterface
	 */
	public function addTeamContainer(TeamContainer $container) : WithTeamsInterface {
		$this->teams->addChild($container);
		return $this;
	}
}