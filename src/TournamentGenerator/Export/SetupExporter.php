<?php


namespace TournamentGenerator\Export;


use TournamentGenerator\Category;
use TournamentGenerator\Group;
use TournamentGenerator\HierarchyBase;
use TournamentGenerator\Interfaces\WithCategories;
use TournamentGenerator\Interfaces\WithGames;
use TournamentGenerator\Interfaces\WithGroups;
use TournamentGenerator\Interfaces\WithRounds;
use TournamentGenerator\Interfaces\WithTeams;
use TournamentGenerator\Preset\Preset;
use TournamentGenerator\Progression;
use TournamentGenerator\Round;
use TournamentGenerator\TeamFilter;
use TournamentGenerator\Tournament;

/**
 * Class SetupExporter
 *
 * @package TournamentGenerator\Export
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.5
 */
class SetupExporter extends ExportBase
{

	/**
	 * @inheritDoc
	 */
	public static function export(HierarchyBase $object) : array {
		return self::start($object)->get();
	}

	/**
	 * @inheritDoc
	 */
	public static function start(HierarchyBase $object) : Export {
		return new self($object);
	}

	/**
	 * Finish the export query -> get the result
	 *
	 * @return array The query result
	 */
	public function get() : array {
		$data = $this->getBasic();
		$this->applyModifiers($data);
		return $data;
	}

	/**
	 * @inheritDoc
	 */
	public function getBasic() : array {
		$data = [];
		$this->getTournamentData($data);
		$this->getCategoriesData($data);
		$this->getRoundsData($data);
		$this->getGroupsData($data);
		return $data;
	}

	/**
	 * Get all setup information from a Tournament class
	 *
	 * @param array $data
	 */
	protected function getTournamentData(array &$data) : void {
		if (!$this->object instanceof Tournament) {
			return;
		}
		$data['tournament'] = (object) [
			'type'       => $this->object instanceof Preset ? get_class($this->object) : 'general',
			'name'       => $this->object->getName(),
			'skip'       => $this->object->getSkip(),
			'timing'     => (object) [
				'play'         => $this->object->getPlay(),
				'gameWait'     => $this->object->getGameWait(),
				'categoryWait' => $this->object->getCategoryWait(),
				'roundWait'    => $this->object->getRoundWait(),
				'expectedTime' => $this->object->getTournamentTime(),
			],
			'categories' => $this->object instanceof WithCategories ? $this->object->queryCategories()->ids()->get() : [],
			'rounds'     => $this->object instanceof WithRounds ? $this->object->queryRounds()->ids()->get() : [],
			'groups'     => $this->object instanceof WithGroups ? $this->object->queryGroups()->ids()->get() : [],
			'teams'      => $this->object instanceof WithTeams ? $this->object->getTeamContainer()->ids()->unique()->get() : [],
			'games'      => $this->object instanceof WithGames ? $this->object->getGameContainer()->ids()->get() : [],
		];
	}

	/**
	 * Get all setup information for categories
	 *
	 * @param array $data
	 */
	protected function getCategoriesData(array &$data) : void {
		if ($this->object instanceof Category) {
			$data['categories'] = [
				$this->object->getId() => $this->getCategoryData($this->object),
			];
		}
		elseif ($this->object instanceof WithCategories) {
			$data['categories'] = [];
			foreach ($this->object->getCategories() as $category) {
				$data['categories'][$category->getId()] = $this->getCategoryData($category);
			}
		}
	}

	/**
	 * Get all setup information from a Category class
	 *
	 * @param Category $category Category class to export
	 *
	 * @return object
	 */
	protected function getCategoryData(Category $category) : object {
		return (object) [
			'id'     => $category->getId(),
			'name'   => $category->getName(),
			'skip'   => $category->getSkip(),
			'rounds' => $category instanceof WithRounds ? $category->queryRounds()->ids()->get() : [],
			'groups' => $category instanceof WithGroups ? $category->queryGroups()->ids()->get() : [],
			'teams'  => $category instanceof WithTeams ? $category->getTeamContainer()->ids()->unique()->get() : [],
			'games'  => $category instanceof WithGames ? $category->getGameContainer()->ids()->get() : [],
		];
	}

	/**
	 * Get all setup information for rounds
	 *
	 * @param array $data
	 */
	protected function getRoundsData(array &$data) : void {
		if ($this->object instanceof Round) {
			$data['rounds'] = [
				$this->object->getId() => $this->getRoundData($this->object),
			];
		}
		elseif ($this->object instanceof WithRounds) {
			$data['rounds'] = [];
			foreach ($this->object->getRounds() as $round) {
				$data['rounds'][$round->getId()] = $this->getRoundData($round);
			}
		}
	}

	/**
	 * Get all setup information from a Round class
	 *
	 * @param Round $round Round class to export
	 *
	 * @return object
	 */
	protected function getRoundData(Round $round) : object {
		return (object) [
			'id'     => $round->getId(),
			'name'   => $round->getName(),
			'skip'   => $round->getSkip(),
			'played' => $round->isPlayed(),
			'groups' => $round instanceof WithGroups ? $round->queryGroups()->ids()->get() : [],
			'teams'  => $round instanceof WithTeams ? $round->getTeamContainer()->ids()->unique()->get() : [],
			'games'  => $round instanceof WithGames ? $round->getGameContainer()->ids()->get() : [],
		];
	}

	/**
	 * Get all setup information for groups and progressions
	 *
	 * @param array $data
	 */
	protected function getGroupsData(array &$data) : void {
		$data['groups'] = [];
		$data['progressions'] = [];
		if ($this->object instanceof Group) {
			$data['groups'][$this->object->getId()] = $this->getGroupData($this->object);
			foreach ($this->object->getProgressions() as $progression) {
				$data['progressions'][] = $this->getProgressionData($progression);
			}
		}
	elseif ($this->object instanceof WithGroups) {
			foreach ($this->object->getGroups() as $group) {
				$data['groups'][$group->getId()] = $this->getGroupData($group);
				foreach ($group->getProgressions() as $progression) {
					$data['progressions'][] = $this->getProgressionData($progression);
				}
			}
		}
	}

	/**
	 * Get all setup information from a Group class
	 *
	 * @param Group $group Group class to export
	 *
	 * @return object
	 */
	protected function getGroupData(Group $group) : object {
		return (object) [
			'id'     => $group->getId(),
			'name'   => $group->getName(),
			'type' => $group->getType(),
			'skip'   => $group->getSkip(),
			'points' => (object) [
				'win' => $group->getWinPoints(),
				'loss' => $group->getLostPoints(),
				'draw' => $group->getDrawPoints(),
				'second' => $group->getSecondPoints(),
				'third' => $group->getThirdPoints(),
				'progression' => $group->getProgressPoints(),
			],
			'played' => $group->isPlayed(),
			'inGame' => $group->getInGame(),
			'maxSize' => $group->getMaxSize(),
			'teams'  => $group instanceof WithTeams ? $group->getTeamContainer()->ids()->unique()->get() : [],
			'games'  => $group instanceof WithGames ? $group->getGameContainer()->ids()->get() : [],
		];
	}

	/**
	 * Get all setup information from a Progression class
	 *
	 * @param Progression $progression Progression class to export
	 *
	 * @return object
	 */
	protected function getProgressionData(Progression $progression) : object {
		return (object) [
			'from'     => $progression->getFrom()->getId(),
			'to'     => $progression->getTo()->getId(),
			'offset' => $progression->getStart(),
			'length' => $progression->getLen(),
			'progressed' => $progression->isProgressed(),
			'filters' => array_map([$this, 'getTeamFilterData'], $progression->getFilters()),
		];
	}

	/**
	 * Get all setup information from a TeamFilter class
	 *
	 * @param TeamFilter $filter TeamFilter class to export
	 *
	 * @return object
	 */
	protected function getTeamFilterData(TeamFilter $filter) : object {
		return (object) [
			'what'     => $filter->getWhat(),
			'how'     => $filter->getHow(),
			'val' => $filter->getVal(),
			'groups' => $filter->getGroups(),
		];
	}
}