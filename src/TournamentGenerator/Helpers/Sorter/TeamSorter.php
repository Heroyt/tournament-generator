<?php

namespace TournamentGenerator\Helpers\Sorter;

use Exception;
use InvalidArgumentException;
use TournamentGenerator\Constants;
use TournamentGenerator\Containers\BaseContainer;
use TournamentGenerator\Team;

/**
 * TournamentGenerator sorter for teams
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @package TournamentGenerator\Helpers\Sorter
 * @since   0.3
 */
class TeamSorter implements BaseSorter
{
	/** @var int[]|string[] Array of Group ids */
	protected static array $ids;

	/** @var string What to sort by */
	protected string $ordering;
	/** @var BaseContainer Container that contains the data to sort */
	protected BaseContainer $container;

	/**
	 * TeamSorter constructor.
	 *
	 * @param BaseContainer $container
	 * @param string        $ordering What to order by (\TournamentGenerator\Constants::POINTS / \TournamentGenerator\Constants::SCORE)
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct(BaseContainer $container, string $ordering = Constants::POINTS) {
		if (!in_array($ordering, Constants::OrderingTypes, true)) {
			throw new InvalidArgumentException('Unknown ordering type `'.$ordering.'`');
		}
		$this->container = $container;
		$this->ordering = $ordering;
	}

	/**
	 * Sorter function for usort by points
	 *
	 * @param Team $a First team
	 * @param Team $b Second team
	 */
	protected static function sortTeamsByPoints(Team $a, Team $b) : int {
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
	protected static function sortTeamsByScore(Team $a, Team $b) : int {
		$groupsIds = self::$ids;
		if ($a->sumScore($groupsIds) === $b->sumScore($groupsIds)) {
			return 0;
		}
		return ($a->sumScore($groupsIds) > $b->sumScore($groupsIds) ? -1 : 1);
	}

	/**
	 * Sort function to call
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function sort(array $data) : array {
		$this::$ids = $this->container->getLeafIds();
		switch ($this->ordering) {
			case Constants::POINTS:
				usort($data, [__CLASS__, 'sortTeamsByPoints']);
				break;
			case Constants::SCORE:
				usort($data, [__CLASS__, 'sortTeamsByScore']);
				break;
		}
		return $data;
	}
}
