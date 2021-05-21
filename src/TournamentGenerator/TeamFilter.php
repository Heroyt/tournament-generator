<?php

namespace TournamentGenerator;

use Exception;
use InvalidArgumentException;
use TournamentGenerator\Helpers\FilterComparator;

/**
 * TeamFilter is a wrapper class for rules that filter teams
 *
 * Filtering teams can be useful in progressions, or in getting teams that need to pass some condition.
 *
 * @package TournamentGenerator
 * @since   0.1
 * @author  Tomáš Vojík <vojik@wboy.cz>
 */
class TeamFilter
{

	/**
	 * @var string What to consider
	 * @details Available values:
	 * * points
	 * * score
	 * * wins
	 * * losses
	 * * draws
	 * * second
	 * * third
	 * * team
	 * * not-progressed
	 * * progressed
	 */
	private string $what;

	/**
	 * @var string How to compare values
	 * @details Available values:
	 * * >
	 * * <
	 * * >=
	 * * <=
	 * * =
	 * * !=
	 */
	private string $how;

	/** @var int|Team Value */
	private $val;

	private array $groups;

	/**
	 * TeamFilter constructor.
	 *
	 * @param string   $what   What to compare
	 * @param string   $how    How to compare values
	 * @param int|Team $val    Value to compare to
	 * @param Group[]  $groups Groups to get the statistics from
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct(string $what = 'points', string $how = '>', $val = 0, array $groups = []) {
		if (!in_array(strtolower($what), ['points', 'score', 'wins', 'draws', 'losses', 'second', 'third', 'team', 'not-progressed', 'progressed'])) {
			throw new InvalidArgumentException('Trying to filter nonexistent type ('.$what.')');
		}
		$this->what = strtolower($what);
		if (!in_array($how, ['>', '<', '>=', '<=', '=', '!='])) {
			throw new InvalidArgumentException('Trying to filter with nonexistent operator ('.$how.')');
		}
		$this->how = $how;
		if (!(is_int($val) && strtolower($what) !== 'team') && !($val instanceof Team && strtolower($what) === 'team')) {
			throw new InvalidArgumentException('Unsupported filter value type ('.gettype($val).')');
		}
		$this->val = $val;
		$this->groups = array_map(static function($a) {
			return $a->getId();
		}, array_filter($groups, static function($a) {
			return ($a instanceof Group);
		}));
	}

	/**
	 * Returns filter description
	 *
	 * @return string
	 */
	public function __toString() {
		return 'Filter: '.$this->what.' '.($this->what !== 'not-progressed' && $this->what !== 'progressed' ? $this->how.' '.$this->val : '');
	}

	/**
	 * Check if a team passes the filter
	 *
	 * @param Team           $team      Team to check
	 * @param int[]|string[] $groupsId  Ids of groups to consider
	 * @param string         $operation Aggregate operation (sum, avg, max, min)
	 * @param Group|null     $from      If checking for progression -> what group to check progression from
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function validate(Team $team, array $groupsId, string $operation = 'sum', ?Group $from = null) : bool {
		if (count($this->groups) > 0) {
			$groupsId = array_unique(array_merge($this->groups, (is_array($groupsId) ? $groupsId : [$groupsId])), SORT_REGULAR);
		}

		if ($this->what === 'team') {
			return ($this->how === '!=' ? !$this->validateTeam($team) : $this->validateTeam($team));
		}

		if ($this->what === 'not-progressed') {
			return !$this->validateProgressed($team, $from);
		}

		if ($this->what === 'progressed') {
			return $this->validateProgressed($team, $from);
		}

		return $this->validateCalc($team, $groupsId, $operation);
	}

	/**
	 * Validate a specific team
	 *
	 * @param Team $team
	 *
	 * @return bool
	 */
	protected function validateTeam(Team $team) : bool {
		return $this->val === $team;
	}

	/**
	 * Check if a team is progressed from some group
	 *
	 * @param Team       $team
	 * @param Group|null $from
	 *
	 * @return bool
	 * @throws Exception
	 */
	protected function validateProgressed(Team $team, Group $from = null) : bool {
		if ($from === null) {
			throw new Exception('Group $from was not defined.');
		}
		return $from->isProgressed($team);
	}

	/**
	 * Check a value using an aggregate operation
	 *
	 * @param Team           $team      Team to check
	 * @param string[]|int[] $groupsId  Groups' ids to aggregate from
	 * @param string         $operation Aggregate operation (sum, avg, max, min)
	 *
	 * @return bool
	 * @throws Exception
	 * @see FilterComparator::compare()
	 *
	 */
	private function validateCalc(Team $team, array $groupsId, string $operation = 'sum') : bool {
		if (is_array($groupsId) && !in_array(strtolower($operation), ['sum', 'avg', 'max', 'min'])) {
			throw new Exception('Unknown operation of '.$operation.'. Only "sum", "avg", "min", "max" possible.');
		}

		return FilterComparator::compare($operation, $this->val, $this->how, $this->what, $team, $groupsId);

	}

}
