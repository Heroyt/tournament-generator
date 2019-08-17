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
		$this->groups = array_map(function($a) { return $a->getId(); }, array_filter($groups, function($a) {return ($a instanceof Group);}));
	}
	public function __toString() {
		return 'Filter: '.$this->what.' '.($this->what !== 'notprogressed' && $this->what !== 'progressed' ? $this->how.' '.$this->val : '');
	}

	public function validate(Team $team, $groupsId, string $operation = 'sum', Group $from = null) {
		if (count($this->groups) > 0) $groupsId = array_unique(array_merge($this->groups, (gettype($groupsId) === 'array' ? $groupsId : [$groupsId])), SORT_REGULAR);

		if ($this->what == 'team') return ($this->how === '!=' ? !$this->validateTeam($team) : $this->validateTeam($team));
		elseif ($this->what == 'notprogressed') return !$this->validateProgressed($team, $from);
		elseif ($this->what == 'progressed') return $this->validateProgressed($team, $from);

		return $this->validateCalc($team, $groupsId, $operation);
	}

	private function validateTeam(Team $team) {
		return $this->val === $team;
	}
	private function validateProgressed(Team $team, Group $from = null) {
		if ($from === null) throw new \Exception('Group $from was not defined.');
		return $from->isProgressed($team);
	}
	private function validateCalc(Team $team, $groupsId, string $operation = 'sum') {
		if (gettype($groupsId) === 'array' && !in_array(strtolower($operation), ['sum', 'avg', 'max', 'min'])) throw new \Exception('Unknown operation of '.$operation.'. Only "sum", "avg", "min", "max" possible.');
		$comp = 0;
		if (gettype($groupsId) === 'string') $groupsId = [$groupsId];
		switch (strtolower($operation)) {
			case 'sum': $comp = $this->calcSum($team, $groupsId); break;
			case 'avg': $comp = $this->calcSum($team, $groupsId)/count($groupsId); break;
			case 'max': $comp = $this->calcMax($team, $groupsId); break;
			case 'min': $comp = $this->calcMin($team, $groupsId); break;
		}

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
			if (isset($team->groupResults[$id])) $sum += $team->groupResults[$id][$this->what];
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
