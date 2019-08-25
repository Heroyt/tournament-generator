<?php

namespace TournamentGenerator;

/**
 *
 */
class Filter
{

	private $groups;
	private $filters = [];

	function __construct(array $groups, array $filters) {
		$this->groups = array_filter($groups, function($a) {return $a instanceof Group;});
		$this->filters = $filters;
	}

	private function getGroupsIds() {
		return array_map(function($a){return $a->getId();}, $this->groups);
	}

	/**
	 * Apply filters
	 * @param array &$teams
	 */
	public function filter(array &$teams) {
		foreach ($this->filters as $key => $filter) {
			if (gettype($filter) === 'array') {
				$this->filterMulti($teams, $filter, $key);
				continue;
			}
			elseif ($filter instanceof TeamFilter) {
				$teams = array_filter($teams, function($team) use ($filter) {return $filter->validate($team, $this->getGroupsIds(), 'sum', $this->groups[0]); });
				continue;
			}
			throw new \Exception('Filter ['.$key.'] is not an instance of TeamFilter class');
		}
		return $teams;
	}

	private function filterMulti(array &$teams, array $filters, string $how = 'and') {
		if (is_int($how)) $how = 'and';
		switch (strtolower($how)) {
			case 'and':
				foreach ($teams as $tkey => $team) {
					if (!$this->filterAnd($team, $filters)) unset($teams[$tkey]); // IF FILTER IS NOT VALIDATED REMOVE TEAM FROM RETURN ARRAY
				}
				return true;
			case 'or':
				foreach ($teams as $tkey => $team) {
					if (!$this->filterOr($team, $filters)) unset($teams[$tkey]); // IF FILTER IS NOT VALIDATED REMOVE TEAM FROM RETURN ARRAY
				}
				return true;
		}
		throw new \Exception('Unknown opperand type "'.$how.'". Expected "and" or "or".');
	}

	private function filterAnd(Team $team, array $filters) {
		foreach ($filters as $key => $value) {
			if (is_array($value)) {
				if (is_int($key)) $key = 'and';
				switch (strtolower($key)) {
					case 'and':
						if (!$this->filterAnd($team, $value)) return false;
						break;
					case 'or':
						if (!$this->filterOr($team, $value)) return false;
						break;
					default:
						throw new \Exception('Unknown opperand type "'.$key.'". Expected "and" or "or".');
				}
				continue;
			}
			elseif ($value instanceof TeamFilter) {
				if (!$value->validate($team, $this->getGroupsIds(), 'sum', $this->groups[0])) return false;
				continue;
			}
			throw new \Exception('Filter ['.$key.'] is not an instance of TeamFilter class');
		}
		return true;
	}
	private function filterOr(Team $team, array $filters) {
		foreach ($filters as $key => $value) {
			if (is_int($key)) $key = 'and';
			if (is_array($value)) {
				switch (strtolower($key)) {
					case 'and':
						if ($this->filterAnd($team, $value)) return true;
						break;
					case 'or':
						if ($this->filterOr($team, $value)) return true;
						break;
					default:
						throw new \Exception('Unknown opperand type "'.$key.'". Expected "and" or "or".');
				}
				continue;
			}
			elseif ($value instanceof TeamFilter) {
				if ($value->validate($team, $this->getGroupsIds(), 'sum', $this->groups[0])) return true;
				continue;
			}
			throw new \Exception('Filter ['.$key.'] is not an instance of TeamFilter class');
		}
		return false;
	}
}
