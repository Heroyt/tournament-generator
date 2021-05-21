<?php


namespace TournamentGenerator\Traits;

use Exception;
use TournamentGenerator\Constants;
use TournamentGenerator\Group;
use TournamentGenerator\Helpers\Filter;
use TournamentGenerator\Helpers\Sorter\Teams;
use TournamentGenerator\Interfaces\WithCategories;
use TournamentGenerator\Interfaces\WithGroups;
use TournamentGenerator\Interfaces\WithRounds;
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

	/** @var Team[] Teams in a object */
	protected array $teams = [];

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
		$teams = [$this->teams];
		if ($this instanceof WithCategories) {
			foreach ($this->getCategories() as $category) {
				$teams[] = $category->getTeams();
			}
		}
		if ($this instanceof WithRounds) {
			foreach ($this->getRounds() as $round) {
				$teams[] = $round->getTeams();
			}
		}
		elseif ($this instanceof WithGroups) {
			foreach ($this->getGroups() as $group) {
				$teams[] = $group->getTeams();
			}
		}
		$this->teams = $this->uniqueTeams(array_merge(...$teams));
		$returnTeams = $this->teams;
		if ($ordered) {
			$returnTeams = $this->sortTeams($ordering);
		}

		// APPLY FILTERS
		if (count($filters) > 0) {
			$this->filterTeams($returnTeams, $filters);
		}

		return $returnTeams;
	}

	/**
	 * @param Team[] $teams
	 *
	 * @return Team[]
	 */
	protected function uniqueTeams(array $teams) : array {
		$ids = [];
		foreach ($teams as $key => $team) {
			if (in_array($team->getId(), $ids, true)) {
				unset($teams[$key]);
				continue;
			}
			$ids[] = $team->getId();
		}
		return array_values($teams);
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
		$teams = [];
		if ($this instanceof WithRounds) {
			$rounds = $this->getRounds();
			for ($i = count($rounds) - 1; $i >= 0; $i--) {
				foreach ($rounds[$i]->getTeams(true, $ordering) as $team) {
					if (!isset($teams[$team->getId()])) {
						$teams[$team->getId()] = $team;
					}
				}
				$this->teams = array_values($teams);
			}
		}
		elseif ($this instanceof WithGroups) {
			foreach ($this->getGroups() as $group) {
				$teams[] = $group->getTeams(true);
			}
			$this->teams = array_merge(...$teams);
		}

		if ($this instanceof Round) {
			$teams = Teams::sortRound($this->teams, $this, $ordering);
		}
		elseif ($this instanceof Group) {
			$teams = Teams::sortGroup($this->teams, $this, $ordering);
		}
		else {
			$teams = $this->teams;
		}
		$this->teams = $teams;

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
		if ($this instanceof WithGroups) {
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
			$this->teams[] = $team;
		}
		return $this;
	}

	public function getRealTeamCount() : int {
		return count($this->teams);
	}
}