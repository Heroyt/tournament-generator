<?php

namespace TournamentGenerator\Utilis\Sorter;

/**
 * TournamentGenerator sorter for teams
 *
 * @author Tomáš Vojík
 *
 * @property array $ids            Stores ids of groups get scores and points from
 *
 * @method array sortGroup         Sort teams in group by defined ordering type
 * @method array sortRound         Sort teams in round by defined ordering type
 * @method array sortTeamsByPoints Sorter function for uasort by points
 * @method array sortTeamsByScore  Sorter function for uasort by score
 */
class Teams
{

	/**
	* @var array $ids Stores ids of groups get scores and points from
	*/
	protected static $ids = [];

	/**
	* Sort teams in group by defined ordering type
	*
	* @param array &$teams                     Array of teams to be sorted
	* @param \TournamentGenerator\Group $group Group to get the results from
	* @param string $ordering                  What to order by (\TournamentGenerator\Constants::POINTS / \TournamentGenerator\Constants::SCORE)
	*
	* @return array Sorted array of teams
	*/
	public static function sortGroup(array &$teams, \TournamentGenerator\Group $group, string $ordering = \TournamentGenerator\Constants::POINTS) {
		if (!in_array($ordering, \TournamentGenerator\Constants::OrderingTypes)) throw new \Exception('Unknown ordering type `'.$ordering.'`');

		self::$ids = [$group->getId()];

		switch ($ordering) {
			case \TournamentGenerator\Constants::POINTS:
				usort($teams, ['\TournamentGenerator\Utilis\Sorter\Teams', 'sortTeamsByPoints']);
				break;
			case \TournamentGenerator\Constants::SCORE:
				usort($teams, ['\TournamentGenerator\Utilis\Sorter\Teams', 'sortTeamsByScore']);
				break;
		}

		return $teams;
	}

	/**
	* Sort teams in round by defined ordering type
	*
	* @param array &$teams                     Array of teams to be sorted
	* @param \TournamentGenerator\Round $round Round to get the results from
	* @param string $ordering                  What to order by (\TournamentGenerator\Constants::POINTS / \TournamentGenerator\Constants::SCORE)
	*
	* @return array Sorted array of teams
	*/
	public static function sortRound(array &$teams, \TournamentGenerator\Round $round, string $ordering = \TournamentGenerator\Constants::POINTS) {
		if (!in_array($ordering, \TournamentGenerator\Constants::OrderingTypes)) throw new \Exception('Unknown ordering type `'.$ordering.'`');

		self::$ids = $round->getGroupsIds();

		switch ($ordering) {
			case \TournamentGenerator\Constants::POINTS:
				usort($teams, ['\TournamentGenerator\Utilis\Sorter\Teams', 'sortTeamsByPoints']);
				break;
			case \TournamentGenerator\Constants::SCORE:
				usort($teams, ['\TournamentGenerator\Utilis\Sorter\Teams', 'sortTeamsByScore']);
				break;
		}

		return $teams;
	}

	/**
	* Sorter function for uasort by points
	*/
	private static function sortTeamsByPoints($a, $b) {
		$groupsIds = self::$ids;
		if ($a->sumPoints($groupsIds) === $b->sumPoints($groupsIds) && $a->sumScore($groupsIds) === $b->sumScore($groupsIds)) return 0;
		if ($a->sumPoints($groupsIds) === $b->sumPoints($groupsIds)) return ($a->sumScore($groupsIds) > $b->sumScore($groupsIds) ? -1 : 1);
		return ($a->sumPoints($groupsIds) > $b->sumPoints($groupsIds) ? -1 : 1);
	}
	/**
	* Sorter function for uasort by score
	*/
	private static function sortTeamsByScore($a, $b) {
		$groupsIds = self::$ids;
		if ($a->sumScore($groupsIds) === $b->sumScore($groupsIds)) return 0;
		return ($a->sumScore($groupsIds) > $b->sumScore($groupsIds) ? -1 : 1);
	}

}
