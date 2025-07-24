<?php

declare(strict_types=1);

namespace TournamentGenerator\Traits;

use Exception;
use TournamentGenerator\Constants;
use TournamentGenerator\Containers\TeamContainer;
use TournamentGenerator\Group;
use TournamentGenerator\Helpers\Filter;
use TournamentGenerator\Helpers\Functions;
use TournamentGenerator\Helpers\Sorter\TeamSorter;
use TournamentGenerator\Interfaces\WithGroups as WithGroupsInterface;
use TournamentGenerator\Interfaces\WithTeams as WithTeamsInterface;
use TournamentGenerator\Round;
use TournamentGenerator\Team;
use TournamentGenerator\TeamFilter;

/**
 * Trait WithTeams.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.4
 */
trait WithTeams
{
    /** @var TeamContainer Teams in a object */
    protected TeamContainer $teams;

    /**
     * Create a new team and add it into the object.
     *
     * @param string          $name Name of the new team
     * @param null|int|string $id   Id of the new team - if omitted -> it is generated automatically as unique string
     *
     * @return Team Newly created team
     *
     * @throws Exception
     */
    public function team(string $name = '', null|int|string $id = null) : Team {
        $team = new Team($name, $id);
        $this->teams->insert($team);

        return $team;
    }

    /**
     * Split teams into its Groups.
     *
     * @return $this
     *
     * @throws Exception
     *
     * @noinspection CallableParameterUseCaseInTypeContextInspection
     */
    public function splitTeams(Round ...$wheres) : WithTeamsInterface {
        if (0 === count($wheres)) {
            $wheres = $this->getRounds();
        }

        $teams = $this->getTeams(true, Constants::SEED);
        if ($this::isSeeded($teams)) {
            Functions::sortAlternate($teams);
        } else {
            shuffle($teams);
        }

        $split = (int) ceil(count($teams) / count($wheres));
        foreach ($wheres as $where) {
            if (count($teams) > 0) {
                $where->addTeam(...array_splice($teams, 0, $split));
            }
        }
        foreach ($wheres as $where) {
            $where->splitTeams();
        }

        return $this;
    }

    /**
     * Split teams into its Groups.
     *
     * @return $this
     *
     * @throws Exception
     *
     * @noinspection CallableParameterUseCaseInTypeContextInspection
     */
    public function splitTeamsEvenly(Round ...$wheres) : WithTeamsInterface {
        if (0 === count($wheres)) {
            $wheres = $this->getRounds();
        }

        $teams = $this->getTeams(true, Constants::SEED);
        if ($this::isSeeded($teams)) {
            Functions::sortAlternate($teams);
        } else {
            shuffle($teams);
        }

        while (count($teams) > 0) {
            foreach ($wheres as $where) {
                if (count($teams) > 0) {
                    $where->addTeam(array_pop($teams));
                }
            }
        }
        foreach ($wheres as $where) {
            $where->splitTeamsEvenly();
        }

        return $this;
    }

    /**
     * Get all teams in the object.
     *
     * @param bool                        $ordered  If true - order the teams by their score/points
     * @param null|string                 $ordering What to order the teams by - Constants::POINTS, Constants::SCORE
     * @param TeamFilter[]|TeamFilter[][] $filters  Filters to filter the returned teams (ex. if you only want to get
     *                                              the first 3 teams)
     *
     * @return Team[]
     *
     * @throws Exception
     */
    public function getTeams(
        bool $ordered = false,
        ?string $ordering = Constants::POINTS,
        array $filters = []
    ) : array {
        if (is_null($ordering)) {
            $ordering = Constants::POINTS;
        }
        $returnTeams = $ordered ? $this->sortTeams($ordering) : $this->teams->unique()->get();

        // APPLY FILTERS
        if (count($filters) > 0) {
            $this->filterTeams($returnTeams, $filters);
        }

        return $returnTeams;
    }

    /**
     * Sort the teams by their score/points.
     *
     * @param null|string                 $ordering What to order the teams by - Constants::POINTS, Constants::SCORE
     * @param TeamFilter[]|TeamFilter[][] $filters  Filters to filter the returned teams (ex. if you only want to get
     *                                              the first 3 teams)
     *
     * @return Team[]
     *
     * @throws Exception
     */
    public function sortTeams(?string $ordering = Constants::POINTS, array $filters = []) : array {
        if (is_null($ordering)) {
            $ordering = Constants::POINTS;
        }
        $teamSorter = new TeamSorter($this->getContainer(), $ordering);
        $teams = $this->teams->addSorter($teamSorter)->unique()->get();

        // APPLY FILTERS
        if (count($filters) > 0) {
            $this->filterTeams($teams, $filters);
        }

        return $teams;
    }

    /**
     * Filter teams using the specified filters.
     *
     * @param array                       $teams   Teams to filter through
     * @param TeamFilter[]|TeamFilter[][] $filters Filters to use
     *
     * @throws Exception
     */
    public function filterTeams(array &$teams, array $filters) : array {
        // APPLY FILTERS
        if ($this instanceof WithGroupsInterface) {
            $filter = new Filter($filters, $this->getGroups());
            $filter->filter($teams);
        } elseif ($this instanceof Group) {
            $filter = new Filter($filters, [$this]);
            $filter->filter($teams);
        }

        return $teams;
    }

    /**
     * @param Team[] $teams
     */
    public static function isSeeded(array $teams) : bool {
        foreach ($teams as $team) {
            if ($team->getSeed() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add one or more teams into the object.
     *
     * @param Team ...$teams Team objects
     *
     * @throws Exception
     */
    public function addTeam(Team ...$teams) : WithTeamsInterface {
        foreach ($teams as $team) {
            $this->teams->insert($team);
        }

        return $this;
    }

    /**
     * Get the container for games.
     */
    public function getTeamContainer() : TeamContainer {
        return $this->teams;
    }

    /**
     * Add a child container for games.
     *
     * @throws Exception
     */
    public function addTeamContainer(TeamContainer $teamContainer) : WithTeamsInterface {
        $this->teams->addChild($teamContainer);

        return $this;
    }
}
