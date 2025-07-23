<?php

declare(strict_types=1);

namespace TournamentGenerator\Traits;

use TournamentGenerator\Group;
use TournamentGenerator\Team;
use TournamentGenerator\TeamFilter;

trait ProgressionTrait
{
    /** @var Group What group to progress to */
    protected Group $to;

    /** @var int Offset to start picking teams */
    protected int $start;

    /** @var null|int Maximum number of teams to progress */
    protected ?int $len;

    /** @var TeamFilter[] Filters to use */
    protected array $filters = [];

    /** @var bool If the progression was already called */
    protected bool $progressed = false;

    /**
     * @var null|int Custom points for progression
     */
    protected ?int $points = null;

    /** @var Team[] */
    protected array $progressedTeams = [];

    /**
     * Adds progression's filters.
     *
     * @param TeamFilter[] $filters
     *
     * @return $this
     */
    public function addFilter(TeamFilter ...$filters) : static {
        foreach ($filters as $filter) {
            $this->filters[] = $filter;
        }

        return $this;
    }

    /**
     * Reset progression.
     *
     * @warning This does not remove the teams from the progressed groups!
     *
     * @return $this
     */
    public function reset() : static {
        $this->progressed = false;

        return $this;
    }

    public function getTo() : Group {
        return $this->to;
    }

    public function getStart() : int {
        return $this->start;
    }

    public function getLen() : ?int {
        return $this->len;
    }

    /**
     * @return TeamFilter[]
     */
    public function getFilters() : array {
        return $this->filters;
    }

    /**
     * Sets progression's filters.
     *
     * @param TeamFilter[] $filters
     *
     * @return $this
     */
    public function setFilters(array $filters) : static {
        $this->filters = $filters;

        return $this;
    }

    public function isProgressed() : bool {
        return $this->progressed;
    }

    public function setProgressed(bool $progressed) : void {
        $this->progressed = $progressed;
    }

    public function getPoints() : ?int {
        return $this->points;
    }

    /**
     * @return $this
     */
    public function setPoints(?int $points) : static {
        $this->points = $points;

        return $this;
    }

    public function getProgressedTeams() : array {
        return $this->progressedTeams;
    }
}
