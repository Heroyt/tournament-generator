<?php

namespace TournamentGenerator;

/**
 *
 */
class TeamFilter
{

	/*
	* WHAT TO CONSIDER  *
	* * * * * * * * * * *
	* points
	* score
	* wins
	* losses
	* draws
	* second
	* third
	* team
	* notprogressed
	* progressed
	*/
	private $what = 'points';

	/*
	* HOW TO COMPARE  *
	* * * * * * * * * *
	* >
	* <
	* >=
	* <=
	* =
	* !=
	*/
	private $how = '>';

	/*
	* VALUE *
	*/
	private $val = 0;

	private $groups = [];

	function __construct(string $what = 'points', string $how = '>', $val = 0, $groups = []){
		if (in_array(strtolower($what), ['points', 'score', 'wins', 'draws', 'losses', 'second', 'third', 'team', 'notprogressed', 'progressed'])) $this->what = strtolower($what);
		if (in_array($how, ['>', '<', '>=', '<=', '=', '!='])) $this->how = $how;
		if ((gettype($val) === 'integer' && strtolower($what) !== 'team') || ($val instanceof Team && strtolower($what) === 'team')) $this->val = $val;
		foreach ($groups as $group) {
			if ($group instanceof Group) $this->groups[] =  $group->id;
		}
	}
	public function __toString() {
		return 'Filter: '.$this->what.' '.($this->what !== 'notprogressed' && $this->what !== 'progressed' ? $this->how.' '.$this->val : '');
	}

	public function validate(Team $team, $groupsId, string $operation = 'sum', Group $from = null) {
		if (count($this->groups) > 0) $groupsId = array_unique(array_merge($this->groups, (gettype($groupsId) === 'array' ? $groupsId : [$groupsId])), SORT_REGULAR);
		if ($this->what == 'team') {
			return $this->validateTeam($team);
		}
		elseif ($this->what == 'notprogressed') {
			return !$this->validateProgressed($team, $from);
		}
		elseif ($this->what == 'progressed') {
			return $this->validateProgressed($team, $from);
		}
		return $this->validateCalc($team, $groupsId, $operation, $from);
	}

	private function validateTeam(Team $team) {
		switch ($this->how) {
			case '=':
				if ($this->val === $team) return true;
				break;
			case '!=':
				if ($this->val !== $team) return true;
				break;
		}
		return false;
	}
	private function validateProgressed(Team $team, Group $from = null) {
		if ($from === null) throw new \Exception('Group $from was not defined.');
		return $from->isProgressed($team);
	}
	private function validateCalc(Team $team, $groupsId, string $operation = 'sum') {
		if (gettype($groupsId) === 'array' && !in_array(strtolower($operation), ['sum', 'avg', 'max', 'min'])) throw new \Exception('Unknown operation of '.$operation.'. Only "sum", "avg", "min", "max" possible.');
		$comp = 0;
		if (gettype($groupsId) === 'array' && count($groupsId) > 0) {
			switch (strtolower($operation)) {
				case 'sum': $comp = $this->calcSum($team, $groupsId);
				case 'avg': $comp = $this->calcSum($team, $groupsId)/count($groupsId);
				case 'max': $comp = $this->calcMax($team, $groupsId);
				case 'min': $comp = $this->calcMin($team, $groupsId);
			}
		}
		elseif (gettype($groupsId) === 'string' && isset($team->groupResults[$groupsId])) $comp = $team->groupResults[$groupsId][$this->what];
		else throw new \Exception("Couldn't find group of id ".print_r($groupsId, true));

		switch ($this->how) {
			case '>': return ($comp > $this->val);
			case '<': return ($comp < $this->val);
			case '<=': return ($comp <= $this->val);
			case '>=': return ($comp >= $this->val);
			case '=': return ($comp == $this->val);
			case '!=': return ($comp != $this->val);
		}
		return false;
	}

	private function calcSum(Team $team, $groupsId) {
		$sum = 0;
		foreach ($groupsId as $id) {
			if (!isset($team->groupResults[$id])) continue; // IF TEAM DIDN'T PLAY IN THAT GROUP -> SKIP
			$sum += $team->groupResults[$id][$this->what];
		}
		return $sum;
	}
	private function calcMax(Team $team, $groupsId) {
		$max = null;
		foreach ($groupsId as $id) {
			if (isset($team->groupResults[$id]) && ($team->groupResults[$id][$this->what] > $max || $max === null)) $max = $team->groupResults[$id][$this->what];
		}
		return $max;
	}
	private function calcMin(Team $team, $groupsId) {
		$min = null;
		foreach ($groupsId as $id) {
			if (isset($team->groupResults[$id]) && ($team->groupResults[$id][$this->what] < $min || $min === null)) $min = $team->groupResults[$id][$this->what];
		}
		return $min;
	}
}
