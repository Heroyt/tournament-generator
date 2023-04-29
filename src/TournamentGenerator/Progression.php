<?php

namespace TournamentGenerator;

use Exception;

/**
 * Progression is a class that takes care of moving teams between groups.
 *
 * Progressions are used in order to move teams from one group to another. This can be used to progress the winning teams to semi-finals and finals round, but you can also progress teams between groups in different categories and even tournaments if you ever needed to.
 * Progressions use a similar syntax to php's array_slice() function or it can use defined filters.
 *
 * @package TournamentGenerator
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.1
 */
class Progression
{

	/** @var Group What group to progress from */
	protected Group $from;
    /** @var Group What group to progress to */
    protected Group $to;
    /** @var int Offset to start picking teams */
    protected int $start;
    /** @var int|null Maximum number of teams to progress */
    protected ?int $len;
    /** @var TeamFilter[] Filters to use */
    protected array $filters = [];
    /** @var bool If the progression was already called */
    protected bool $progressed = false;

    /**
     * @var int|null Custom points for progression
     * @package TournamentGenerator
     */
    protected ?int $points = null;

    /** @var Team[] */
    protected array $progressedTeams = [];

    /**
     * Progression constructor.
     *
     * @param Group $from What group to progress from
     * @param Group $to What group to progress to
     * @param int $start Offset to start picking teams
     * @param int|null $len Maximum number of teams to progress
     */
    public function __construct(Group $from, Group $to, int $start = 0, ?int $len = null) {
		$this->from = $from;
		$this->to = $to;
		$this->start = $start;
		$this->len = $len;
	}

	/**
	 * Gets a description
	 *
	 * @return string
	 */
	public function __toString() {
		return 'Team from '.$this->from;
	}

	/**
	 * Adds progression's filters
	 *
	 * @param TeamFilter[] $filters
	 *
	 * @return $this
	 */
	public function addFilter(TeamFilter ...$filters) : Progression {
		foreach ($filters as $filter) {
			$this->filters[] = $filter;
		}
		return $this;
	}

	/**
	 * Progress the teams using set rules
	 *
	 * @param bool $blank If true -> do not move the real team objects, but create new dummy teams
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function progress(bool $blank = false) : Progression {
		if ($this->progressed) {
			return $this;
		}

		if ($blank) {
			$teams = $this->from->isPlayed() ? $this->from->sortTeams(null, $this->filters) : $this->from->simulate($this->filters);
		}
		else {
			$teams = $this->from->sortTeams(null, $this->filters);
		}

		if ($this->start !== 0 || $this->len !== null) {
			$next = array_splice($teams, $this->start, ($this->len ?? count($teams)));
		}
		else {
			$next = $teams;
		}

		$i = 1;

		foreach ($next as $team) {
			if ($blank) {
				$this->to->addTeam(new BlankTeam($this.' - '.$i++, $team, $this->from, $this));
			}
			else {
                $this->progressedTeams[] = $team;
                $team->addPoints($this->points ?? $this->from->getProgressPoints());
			}
		}

		$this->from->addProgressed(...$next);
		if (!$blank) {
			$this->to->addTeam(...$next);
		}
		$this->progressed = true;
		return $this;
	}

	/**
	 * Reset progression
	 *
	 * @warning This does not remove the teams from the progressed groups!
	 *
	 * @return $this
	 */
	public function reset() : Progression {
		$this->progressed = false;
		return $this;
	}

	/**
	 * @return Group
	 */
	public function getFrom() : Group {
		return $this->from;
	}

	/**
	 * @return Group
	 */
	public function getTo() : Group {
		return $this->to;
	}

	/**
	 * @return int
	 */
	public function getStart() : int {
		return $this->start;
	}

	/**
	 * @return int|null
	 */
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
	 * Sets progression's filters
	 *
	 * @param TeamFilter[] $filters
	 *
	 * @return $this
	 */
	public function setFilters(array $filters) : Progression {
		$this->filters = $filters;
		return $this;
	}

	/**
	 * @return bool
	 */
    public function isProgressed(): bool {
        return $this->progressed;
    }

    /**
     * @param bool $progressed
     */
    public function setProgressed(bool $progressed): void {
        $this->progressed = $progressed;
    }

    /**
     * @return int|null
     */
    public function getPoints(): ?int {
        return $this->points;
    }

    /**
     * @param int|null $points
     * @return Progression
     */
    public function setPoints(?int $points): Progression {
        $this->points = $points;
        return $this;
    }

    /**
     * @return array
     */
    public function getProgressedTeams(): array {
        return $this->progressedTeams;
    }

}
