<?php

namespace TournamentGenerator\Helpers;

use TournamentGenerator\Team;

/**
 * Class responsible for processing filters
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @package TournamentGenerator\Helpers
 * @since   0.3
 */
class FilterComparator
{

	protected static string $what = '';
	protected static int    $val  = 0;

	/**
	 * Compare a given team metric
	 *
	 * @param string    $operation How to aggregate the values (sum, avg, max, min)
	 * @param int|float $val       Value to compare to
	 * @param string    $how       How to compare (<, >, <=, >=, =, !=)
	 * @param string    $what      What team's metric to consider
	 * @param Team      $team      Team to get the value from
	 * @param array     $groupsId  What groups to consider
	 *
	 * @return bool
	 */
	public static function compare(string $operation, $val, string $how, string $what, Team $team, array $groupsId) : bool {

		self::$what = $what;
		self::$val = $val;

		$return = false;

		$comp = self::calcComparisonValue($operation, $team, $groupsId);

		switch ($how) {
			case '>':
				$return = ($comp > $val);
				break;
			case '<':
				$return = ($comp < $val);
				break;
			case '<=':
				$return = ($comp <= $val);
				break;
			case '>=':
				$return = ($comp >= $val);
				break;
			case '=':
				$return = ($comp === $val);
				break;
			case '!=':
				$return = ($comp !== $val);
				break;
		}
		return $return;
	}

	/**
	 * Get the team's value to compare
	 *
	 * @param string $operation How to aggregate the values (sum, avg, max, min)
	 * @param Team   $team      Team to get the value from
	 * @param array  $groupsId  What groups to consider
	 *
	 * @return float|int|null
	 */
	private static function calcComparisonValue(string $operation, Team $team, array $groupsId) {
		$return = 0;
		switch (strtolower($operation)) {
			case 'sum':
				$return = self::calcSum($team, $groupsId);
				break;
			case 'avg':
				$return = self::calcAvg($team, $groupsId);
				break;
			case 'max':
				$return = self::calcMax($team, $groupsId);
				break;
			case 'min':
				$return = self::calcMin($team, $groupsId);
				break;
		}
		return $return;
	}

	/**
	 * Calculate a sum of given metric
	 *
	 * @param Team  $team     Team to get the values from
	 * @param array $groupsId What groups to consider
	 *
	 * @return int|float
	 */
	private static function calcSum(Team $team, array $groupsId) {
		$sum = 0;
		foreach ($groupsId as $id) {
			if (isset($team->groupResults[$id])) {
				$sum += $team->groupResults[$id][self::$what];
			}
		}
		return $sum;
	}

	/**
	 * Calculate a average of given metric
	 *
	 * @param Team  $team     Team to get the values from
	 * @param array $groupsId What groups to consider
	 *
	 * @return int|float
	 */
	private static function calcAvg(Team $team, array $groupsId) {
		$games = 0;
		foreach ($groupsId as $id) {
			$games += count($team->getGames(null, $id));
		}
		return self::calcSum($team, $groupsId) / $games;
	}

	/**
	 * Find a maximum of given metric
	 *
	 * @param Team  $team     Team to get the values from
	 * @param array $groupsId What groups to consider
	 *
	 * @return int|float
	 */
	private static function calcMax(Team $team, array $groupsId) {
		$max = null;
		if (count($groupsId) === 1 && in_array(self::$what, ['score', 'points'])) {
			$games = $team->getGames(null, reset($groupsId));
			foreach ($games as $game) {
				$results = $game->getResults()[$team->getId()];
				if (($results[self::$what] > $max || $max === null)) {
					$max = $results[self::$what];
				}
			}
			return $max;
		}
		foreach ($groupsId as $id) {
			if (isset($team->groupResults[$id]) && ($team->groupResults[$id][self::$what] > $max || $max === null)) {
				$max = $team->groupResults[$id][self::$what];
			}
		}
		return $max;
	}

	/**
	 * Find a minimum of given metric
	 *
	 * @param Team  $team     Team to get the values from
	 * @param array $groupsId What groups to consider
	 *
	 * @return int|float
	 */
	private static function calcMin(Team $team, array $groupsId) {
		$min = null;
		if (count($groupsId) === 1 && in_array(self::$what, ['score', 'points'])) {
			$games = $team->getGames(null, reset($groupsId));
			foreach ($games as $game) {
				$results = $game->getResults()[$team->getId()];
				if (($results[self::$what] < $min || $min === null)) {
					$min = $results[self::$what];
				}
			}
			return $min;
		}
		foreach ($groupsId as $id) {
			if (isset($team->groupResults[$id]) && ($team->groupResults[$id][self::$what] < $min || $min === null)) {
				$min = $team->groupResults[$id][self::$what];
			}
		}
		return $min;
	}
}
