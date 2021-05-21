<?php

namespace TournamentGenerator\Helpers\Sorter;

use Exception;
use TournamentGenerator\Constants;
use TournamentGenerator\Group;
use TournamentGenerator\Round;
use TournamentGenerator\Team;

/**
 * TournamentGenerator sorter for teams
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @package TournamentGenerator\Helpers\Sorter
 * @since   0.3
 */
class Teams
{

	/** @var int|string[] $ids Stores ids of groups get scores and points from */
	protected static array $ids = [];

	/**
	 * Sort teams in group by defined ordering type
	 *
	 * @param Team[] &                    $teams    Array of teams to be sorted
	 * @param Group                       $group    Group to get the results from
	 * @param string                      $ordering What to order by (\TournamentGenerator\Constants::POINTS / \TournamentGenerator\Constants::SCORE)
	 *
	 * @return Team[] Sorted array of teams
	 * @throws Exception
	 */
	public static function sortGroup(array &$teams, Group $group, string $ordering = Constants::POINTS) : array {
		if (!in_array($ordering, Constants::OrderingTypes, true)) {
			throw new Exception('Unknown ordering type `'.$ordering.'`');
		}

		self::$ids = [$group->getId()];

		switch ($ordering) {
			case Constants::POINTS:
				usort($teams, [__CLASS__, 'sortTeamsByPoints']);
				break;
			case Constants::SCORE:
				usort($teams, [__CLASS__, 'sortTeamsByScore']);
				break;
		}

		return $teams;
	}

	/**
	 * Sort teams in round by defined ordering type
	 *
	 * @param Team[] &                    $teams    Array of teams to be sorted
	 * @param Round                       $round    Round to get the results from
	 * @param string                      $ordering What to order by (\TournamentGenerator\Constants::POINTS / \TournamentGenerator\Constants::SCORE)
	 *
	 * @return Team[] Sorted array of teams
	 * @throws Exception
	 */
	public static function sortRound(array &$teams, Round $round, string $ordering = Constants::POINTS) : array {
		if (!in_array($ordering, Constants::OrderingTypes, true)) {
			throw new Exception('Unknown ordering type `'.$ordering.'`');
		}

		self::$ids = $round->getGroupsIds();

		switch ($ordering) {
			case Constants::POINTS:
				usort($teams, [__CLASS__, 'sortTeamsByPoints']);
				break;
			case Constants::SCORE:
				usort($teams, [__CLASS__, 'sortTeamsByScore']);
				break;
		}

		return $teams;
	}

	/**
	 * Sorter function for usort by points
	 *
	 * @param Team $a First team
	 * @param Team $b Second team
	 */
	private static function sortTeamsByPoints(Team $a, Team $b) : int {
		$groupsIds = self::$ids;
		if ($a->sumPoints($groupsIds) === $b->sumPoints($groupsIds) && $a->sumScore($groupsIds) === $b->sumScore($groupsIds)) {
			return 0;
		}
		if ($a->sumPoints($groupsIds) === $b->sumPoints($groupsIds)) {
			return ($a->sumScore($groupsIds) > $b->sumScore($groupsIds) ? -1 : 1);
		}
		return ($a->sumPoints($groupsIds) > $b->sumPoints($groupsIds) ? -1 : 1);
	}

	/**
	 * Sorter function for usort by score
	 *
	 * @param Team $a First team
	 * @param Team $b Second team
	 */
	private static function sortTeamsByScore(Team $a, Team $b) : int {
		$groupsIds = self::$ids;
		if ($a->sumScore($groupsIds) === $b->sumScore($groupsIds)) {
			return 0;
		}
		return ($a->sumScore($groupsIds) > $b->sumScore($groupsIds) ? -1 : 1);
	}

}
