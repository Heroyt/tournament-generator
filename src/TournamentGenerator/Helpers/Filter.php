<?php

namespace TournamentGenerator\Helpers;

use Exception;
use TournamentGenerator\Group;
use TournamentGenerator\Team;
use TournamentGenerator\TeamFilter;

/**
 * Filter class
 *
 * Filter is a helper class to apply TeamFilter classes to an array of teams.
 *
 * @package TournamentGenerator\Helpers
 * @since   0.2
 * @author  Tomáš Vojík <vojik@wboy.cz>
 */
class Filter
{

	/** @var Group[] Groups to consider stats from */
	private array $groups;
	/** @var TeamFilter[]|TeamFilter[][] Filters to use */
	private array $filters;

	/**
	 * Filter constructor.
	 *
	 * @param TeamFilter[]|TeamFilter[][] $filters Filter classes - can be structured in an hierarchical array with "and" / "or" keys.
	 * @param Group[]                     $groups  Groups to consider when getting team stats.
	 */
	public function __construct(array $filters, array $groups) {
		$this->groups = array_filter($groups, static function($a) {
			return $a instanceof Group;
		});
		$this->filters = $filters;
	}

	/**
	 * Apply filters
	 *
	 * @param Team[] &$teams Team array to filter
	 *
	 * @return Team[]
	 * @throws Exception
	 */
	public function filter(array &$teams) : array {
		foreach ($this->filters as $key => $filter) {
			if (is_array($filter)) {
				$this->filterMulti($teams, $filter, $key);
				continue;
			}

			if ($filter instanceof TeamFilter) {
				$teams = array_filter($teams, function($team) use ($filter) {
					return $filter->validate($team, $this->getGroupsIds(), 'sum', $this->groups[0]);
				});
				continue;
			}

			throw new Exception('Filter ['.$key.'] is not an instance of TeamFilter class');
		}
		return $teams;
	}

	/**
	 * Apply "multiplied" filters
	 *
	 * Filters can be structured in an array with "and" / "or" keys, that will be parsed.
	 *
	 * @param Team[]                      $teams
	 * @param TeamFilter[]|TeamFilter[][] $filters
	 * @param string                      $how Logical operator - AND, OR (case insensitive).
	 *
	 * @throws Exception
	 */
	protected function filterMulti(array &$teams, array $filters, string $how = 'and') : void {
		switch (strtolower($how)) {
			case 'and':
				foreach ($teams as $tKey => $team) {
					if (!$this->filterAnd($team, $filters)) { // IF FILTER IS NOT VALIDATED REMOVE TEAM FROM RETURN ARRAY
						unset($teams[$tKey]);
					}
				}
				return;
			case 'or':
				foreach ($teams as $tKey => $team) {
					if (!$this->filterOr($team, $filters)) { // IF FILTER IS NOT VALIDATED REMOVE TEAM FROM RETURN ARRAY
						unset($teams[$tKey]);
					}
				}
				return;
		}
		throw new Exception('Unknown operand type "'.$how.'". Expected "and" or "or".');
	}

	/**
	 * Apply filters using and AND operand
	 *
	 * @param Team                        $team
	 * @param TeamFilter[]|TeamFilter[][] $filters
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function filterAnd(Team $team, array $filters) : bool {
		foreach ($filters as $key => $value) {
			if (is_array($value)) {
				if (is_int($key)) {
					$key = 'and';
				}
				switch (strtolower($key)) {
					case 'and':
						if (!$this->filterAnd($team, $value)) {
							return false;
						}
						break;
					case 'or':
						if (!$this->filterOr($team, $value)) {
							return false;
						}
						break;
					default:
						throw new Exception('Unknown opperand type "'.$key.'". Expected "and" or "or".');
				}
				continue;
			}

			if ($value instanceof TeamFilter) {
				if (!$value->validate($team, $this->getGroupsIds(), 'sum', $this->groups[0])) {
					return false;
				}
				continue;
			}
			throw new Exception('Filter ['.$key.'] is not an instance of TeamFilter class');
		}
		return true;
	}

	/**
	 * Apply filters using and OR operand
	 *
	 * @param Team                        $team
	 * @param TeamFilter[]|TeamFilter[][] $filters
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function filterOr(Team $team, array $filters) : bool {
		foreach ($filters as $key => $value) {
			if (is_int($key)) {
				$key = 'and';
			}
			if (is_array($value)) {
				switch (strtolower($key)) {
					case 'and':
						if ($this->filterAnd($team, $value)) {
							return true;
						}
						break;
					case 'or':
						if ($this->filterOr($team, $value)) {
							return true;
						}
						break;
					default:
						throw new Exception('Unknown opperand type "'.$key.'". Expected "and" or "or".');
				}
				continue;
			}

			if ($value instanceof TeamFilter) {
				if ($value->validate($team, $this->getGroupsIds(), 'sum', $this->groups[0])) {
					return true;
				}
				continue;
			}
			throw new Exception('Filter ['.$key.'] is not an instance of TeamFilter class');
		}
		return false;
	}

	/**
	 * Get ids of the considered groups
	 *
	 * @return string[]|int[]
	 */
	private function getGroupsIds() : array {
		return array_map(static function($a) {
			return $a->getId();
		}, $this->groups);
	}
}
