<?php

namespace TournamentGenerator;

/**
 *
 */
class Filter
{

	private $group;
	private $filters = [];

	function __construct(Group $group, array $filters) {
		$this->group = $group;
		$this->filters = $filters;
	}

	/**
	 * Apply filters
	 * @param array &$teams
	 */
	public function filter(array &$teams) {
		foreach ($this->filters as $key => $filter) {
			if (gettype($filter) === 'array') {
				switch (strtolower($key)) {
					case 'and':
						foreach ($teams as $tkey => $team) {
							if (!$this->filterAnd($team, $filter)) unset($teams[$tkey]); // IF FILTER IS NOT VALIDATED REMOVE TEAM FROM RETURN ARRAY
						}
						break;
					case 'or':
						foreach ($teams as $tkey => $team) {
							if (!$this->filterOr($team, $filter)) unset($teams[$tkey]); // IF FILTER IS NOT VALIDATED REMOVE TEAM FROM RETURN ARRAY
						}
						break;
					default:
						throw new \Exception('Unknown opperand type "'.$key.'". Expected "and" or "or".');
						break;
				}
			}
			elseif ($filter instanceof TeamFilter) {
				foreach ($teams as $tkey => $team) {
					if (!$filter->validate($team, $this->id, 'sum', $this)) {
						unset($teams[$tkey]); // IF FILTER IS NOT VALIDATED REMOVE TEAM FROM RETURN ARRAY
					}
				}
			}
			else {
				throw new \Exception('Filer ['.$key.'] is not an instance of TeamFilter class');
			}
		}
		return $teams;
	}

	private function filterAnd(Team $team, array $filters) {
		foreach ($filters as $key => $value) {
			if (gettype($value) === 'array') {
				switch (strtolower($key)) {
					case 'and':
						if ($this->filterAnd($team, $value)) return false;
						break;
					case 'or':
						if ($this->filterOr($team, $value)) return false;
						break;
					default:
						throw new \Exception('Unknown opperand type "'.$key.'". Expected "and" or "or".');
						break;
				}
			}
			elseif ($value instanceof TeamFilter) {
				if (!$value->validate($team, $this->id, 'sum', $this)) return false;
			}
			else {
				throw new \Exception('Filer ['.$key.'] is not an instance of TeamFilter class');
			}
		}
		return true;
	}
	private function filterOr(Team $team, array $filters) {
		foreach ($filters as $key => $value) {
			if (gettype($value) === 'array') {
				switch (strtolower($key)) {
					case 'and':
						if ($this->filterAnd($team, $value)) return true;
						break;
					case 'or':
						if ($this->filterOr($team, $value)) return true;
						break;
					default:
						throw new \Exception('Unknown opperand type "'.$key.'". Expected "and" or "or".');
						break;
				}
			}
			elseif ($value instanceof TeamFilter) {
				if (!$value->validate($team, $this->id, 'sum', $this)) return true;
			}
			else {
				throw new \Exception('Filer ['.$key.'] is not an instance of TeamFilter class');
			}
		}
		return false;
	}
}
