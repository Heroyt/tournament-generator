<?php

namespace TournamentGenerator\Utilis;

/**
 *
 */
class FilterComparator
{

	protected static $what = '';
	protected static $val = 0;

	public static function compare(string $operation, $val, string $how, string $what, \TournamentGenerator\Team $team, array $groupsId) {

		self::$what = $what;
		self::$val = $val;

		$return = false;

		$comp = self::calcComparationValue($operation, $team, $groupsId);

		switch ($how) {
			case '>': $return = ($comp > $val); break;
			case '<': $return = ($comp < $val); break;
			case '<=': $return = ($comp <= $val); break;
			case '>=': $return = ($comp >= $val); break;
			case '=': $return = ($comp == $val); break;
			case '!=': $return = ($comp != $val); break;
		}
		return $return;
	}

	private static function calcComparationValue(string $operation, \TournamentGenerator\Team $team, array $groupsId) {
		$return = 0;
		switch (strtolower($operation)) {
			case 'sum': $return = self::calcSum($team, $groupsId); break;
			case 'avg': $return = self::calcAvg($team, $groupsId); break;
			case 'max': $return = self::calcMax($team, $groupsId); break;
			case 'min': $return = self::calcMin($team, $groupsId); break;
		}
		return $return;
	}

	private static function calcAvg(\TournamentGenerator\Team $team, array $groupsId) {
		$games = 0;
		foreach ($groupsId as $id) {
			$games += count($team->getGames(null, $id));
		}
		return self::calcSum($team, $groupsId)/$games;
	}
	private static function calcSum(\TournamentGenerator\Team $team, array $groupsId) {
		$sum = 0;
		foreach ($groupsId as $id) {
			if (isset($team->groupResults[$id])) $sum += $team->groupResults[$id][self::$what];
		}
		return $sum;
	}
	private static function calcMax(\TournamentGenerator\Team $team, array $groupsId) {
		$max = null;
		if (count($groupsId) === 1 && in_array(self::$what, ['score', 'points'])) {
			$games = $team->getGames(null, reset($groupsId));
			foreach ($games as $game) {
				$results = $game->getResults()[$team->getId()];
				if (($results[self::$what] > $max || $max === null)) $max = $results[self::$what];
			}
			return $max;
		}
		foreach ($groupsId as $id) {
			if (isset($team->groupResults[$id]) && ($team->groupResults[$id][self::$what] > $max || $max === null)) $max = $team->groupResults[$id][self::$what];
		}
		return $max;
	}
	private static function calcMin(\TournamentGenerator\Team $team, array $groupsId) {
		$min = null;
		if (count($groupsId) === 1 && in_array(self::$what, ['score', 'points'])) {
			$games = $team->getGames(null, reset($groupsId));
			foreach ($games as $game) {
				$results = $game->getResults()[$team->getId()];
				if (($results[self::$what] < $min || $min === null)) $min = $results[self::$what];
			}
			return $min;
		}
		foreach ($groupsId as $id) {
			if (isset($team->groupResults[$id]) && ($team->groupResults[$id][self::$what] < $min || $min === null)) $min = $team->groupResults[$id][self::$what];
		}
		return $min;
	}
}
