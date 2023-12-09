<?php

namespace TournamentGenerator;

use Exception;
use TournamentGenerator\Interfaces\ProgressionInterface;
use TournamentGenerator\Traits\ProgressionTrait;

/**
 * Special type of progression that can progress teams from multiple groups into one
 *
 * Progressions are used in order to move teams from one group to another. This can be used to progress the winning teams to semi-finals and finals round, but you can also progress teams between groups in different categories and even tournaments if you ever needed to.
 * Progressions use a similar syntax to php's array_slice() function, or it can use defined filters.
 *
 * @package TournamentGenerator
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.1
 */
class MultiProgression implements ProgressionInterface
{
	use ProgressionTrait;

	/** @var Group[] What group to progress from */
	protected array $from;
	protected ?int  $totalCount = null;
	private int     $totalStart;

	/**
	 * Progression constructor.
	 *
	 * @param Group[]  $from       What groups to progress from
	 * @param Group    $to         What group to progress to
	 * @param int      $start      Offset to start picking teams
	 * @param int|null $len        Maximum number of teams to progress from each $from groups
	 * @param int|null $totalCount Maximum total count to progress from the $from groups.
	 *                             If set, the final teams from each input group will be sorted only the first
	 *                             $totalCount will be progressed.
	 * @param int      $totalStart Offset to start picking teams. The $totalCount must be set.
	 */
	public function __construct(array $from, Group $to, int $start = 0, ?int $len = null, ?int $totalCount = null, int $totalStart = 0) {
		$this->from = $from;
		$this->to = $to;
		$this->start = $start;
		$this->len = $len;
		$this->totalCount = $totalCount;
		$this->totalStart = $totalStart;
	}

	/**
	 * Gets a description
	 *
	 * @return string
	 */
	public function __toString() {
		return 'Team from ' . implode(', ', array_map(static fn(Group $group) => $group->getName(), $this->from));
	}


	/**
	 * Progress the teams using set rules
	 *
	 * @param bool $blank If true -> do not move the real team objects, but create new dummy teams
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function progress(bool $blank = false): static {
		if ($this->progressed) {
			return $this;
		}

		$fromIds = [];

		/** @var Team[][] $teams */
		$teams = [];
		foreach ($this->from as $key => $from) {
			$fromIds[] = $from->getId();
			if ($blank) {
				$teams[$key] = $from->isPlayed() ?
					$from->sortTeams(null, $this->filters) :
					$from->simulate($this->filters);
			}
			else {
				$teams[$key] = $from->sortTeams(null, $this->filters);
			}
		}

		/** @var Team[][] $next */
		$next = [];
		foreach ($teams as $key => $groupTeams) {
			if ($this->start !== 0 || $this->len !== null) {
				$next[$key] = array_splice($groupTeams, $this->start, ($this->len ?? count($groupTeams)));
			}
			else {
				$next[$key] = $groupTeams;
			}
		}

		// Only $totalCount teams should progress from all groups
		if ($this->totalCount !== null) {
			$allTeams = array_merge(...$next);
			usort($allTeams, static function (Team $a, Team $b) use ($fromIds) {
				$aPoints = $a->sumPoints($fromIds);
				$bPoints = $b->sumPoints($fromIds);
				if ($aPoints === $bPoints) {
					return $b->sumScore($fromIds) - $a->sumScore($fromIds);
				}
				return $bPoints - $aPoints;
			});
			$allTeams = array_slice($allTeams, $this->totalStart, $this->totalCount);
			foreach ($next as $key => $groupTeams) {
				foreach ($groupTeams as $key2 => $team) {
					if (!in_array($team, $allTeams, true)) {
						unset($next[$key][$key2]);
					}
				}
			}
		}

		$i = 1;
		foreach ($next as $key => $groupTeams) {
			foreach ($groupTeams as $team) {
				if ($blank) {
					$this->to->addTeam(new BlankTeam($this . ' - ' . $i++, $team, $this->from[$key], $this));
				}
				else {
					$this->progressedTeams[] = $team;
					$team->addPoints($this->points ?? $this->from[$key]->getProgressPoints());
				}
			}
		}

		foreach ($this->from as $key => $from) {
			if (count($next[$key]) === 0) {
				continue;
			}
			$from->addProgressed(...$next[$key]);
			if (!$blank) {
				$this->to->addTeam(...$next[$key]);
			}
		}
		$this->progressed = true;
		return $this;
	}


	/**
	 * @return Group[]
	 */
	public function getFrom(): array {
		return $this->from;
	}

	/**
	 * @return int|null
	 */
	public function getTotalCount(): ?int {
		return $this->totalCount;
	}

	/**
	 * @param int|null $totalCount
	 *
	 * @return $this
	 */
	public function setTotalCount(?int $totalCount): static {
		$this->totalCount = $totalCount;
		return $this;
	}

	public function getTotalStart(): int {
		return $this->totalStart;
	}

	public function setTotalStart(int $totalStart): static {
		$this->totalStart = $totalStart;
		return $this;
	}

}
