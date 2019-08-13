<?php

namespace TournamentGenerator\Utilis\Sorter;

/**
 * TournamentGenerator sorter for teams
 */
class Teams
{

	public static function sortGroup(array &$teams, \TournamentGenerator\Group $group, string $ordering = \TournamentGenerator\Constants::POINTS) {
		if (!in_array($ordering, \TournamentGenerator\Constants::OrderingTypes)) throw new \Exception('Unknown ordering type `'.$ordering.'`');

		switch ($ordering) {
			case \TournamentGenerator\Constants::POINTS:{
				uasort($teams, function($a, $b) use ($group) {
					if ($a->groupResults[$group->id]["points"] === $b->groupResults[$group->id]["points"] && $a->groupResults[$group->id]["score"] === $b->groupResults[$group->id]["score"]) return 0;
					if ($a->groupResults[$group->id]["points"] === $b->groupResults[$group->id]["points"]) return ($a->groupResults[$group->id]["score"] > $b->groupResults[$group->id]["score"] ? -1 : 1);
					return ($a->groupResults[$group->id]["points"] > $b->groupResults[$group->id]["points"] ? -1 : 1);
				});
				break;}
			case \TournamentGenerator\Constants::SCORE:{
				uasort($teams, function($a, $b) use ($group) {
					if ($a->groupResults[$group->id]["score"] === $b->groupResults[$group->id]["score"]) return 0;
					return ($a->groupResults[$group->id]["score"] > $b->groupResults[$group->id]["score"] ? -1 : 1);
				});
				break;}
		}

		return $teams;
	}
	public static function sortRound(array &$teams, \TournamentGenerator\Round $round, string $ordering = \TournamentGenerator\Constants::POINTS) {
		if (!in_array($ordering, \TournamentGenerator\Constants::OrderingTypes)) throw new \Exception('Unknown ordering type `'.$ordering.'`');

		$groupsIds = $round->getGroupsIds();

		switch ($ordering) {
			case \TournamentGenerator\Constants::POINTS:{
				uasort($teams, function($a, $b) use ($groupsIds) {
					if ($a->sumPoints($groupsIds) === $b->sumPoints($groupsIds) && $a->sumScore($groupsIds) === $b->sumScore($groupsIds)) return 0;
					if ($a->sumPoints($groupsIds) === $b->sumPoints($groupsIds)) return ($a->sumScore($groupsIds) > $b->sumScore($groupsIds) ? -1 : 1);
					return ($a->sumPoints($groupsIds) > $b->sumPoints($groupsIds) ? -1 : 1);
				});
				break;}
			case \TournamentGenerator\Constants::SCORE:{
				uasort($teams, function($a, $b) use ($groupsIds) {
					if ($a->sumScore($groupsIds) === $b->sumScore($groupsIds)) return 0;
					return ($a->sumScore($groupsIds) > $b->sumScore($groupsIds) ? -1 : 1);
				});
				break;}
		}

		return $teams;
	}

}
