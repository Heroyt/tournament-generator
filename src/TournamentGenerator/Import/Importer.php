<?php


namespace TournamentGenerator\Import;

use Exception;
use JsonException;
use TournamentGenerator\Base;
use TournamentGenerator\Category;
use TournamentGenerator\Group;
use TournamentGenerator\Interfaces\WithSkipSetters;
use TournamentGenerator\Round;
use TournamentGenerator\Team;
use TournamentGenerator\TeamFilter;
use TournamentGenerator\Tournament;

/**
 * Basic importer
 *
 * Importer uses exported data and creates a new tournament objects from it.
 *
 * @package TournamentGenerator\Import
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.5
 */
class Importer
{

	/**
	 * Processes a JSON input and creates necessary objects from it
	 *
	 * @param string $data
	 *
	 * @return Base
	 * @throws JsonException
	 * @throws InvalidImportDataException
	 * @see Importer::import()
	 *
	 */
	public static function importJson(string $data) : Base {
		return self::import(json_decode($data, true, 512, JSON_THROW_ON_ERROR));
	}

	/**
	 * Processes an input array of data and creates necessary objects from it
	 *
	 * @param array $data
	 *
	 * @return Base
	 * @throws InvalidImportDataException
	 */
	public static function import(array $data) : Base {
		// Validate data
		ImportValidator::validate($data);

		$categories = [];
		$rounds = [];
		$groups = [];
		$teams = [];
		$games = [];

		$root = null;
		if (isset($data['tournament'])) {
			$setting = (array) $data['tournament'];
			if (empty($setting['type']) || $setting['type'] === 'general') {
				$tournament = new Tournament();
			}
			else {
				$tournament = new $setting['type'];
			}
			$root = $tournament;

			self::setTournament($tournament, $setting);
			foreach ($setting['categories'] ?? [] as $id) {
				$categories[$id] = $tournament;
			}
			foreach ($setting['rounds'] ?? [] as $id) {
				$rounds[$id] = $tournament;
			}
			foreach ($setting['groups'] ?? [] as $id) {
				$groups[$id] = $tournament;
			}
			foreach ($setting['teams'] ?? [] as $id) {
				$teams[$id] = $tournament;
			}
			if (isset($setting['games'])) {
				$tournament->getGameContainer()->setAutoIncrement(min($setting['games']));
			}
			foreach ($setting['games'] ?? [] as $id) {
				$games[$id] = $tournament;
			}
		}

		if (isset($data['categories'])) {
			foreach ($data['categories'] as $setting) {
				$setting = (array) $setting;
				$category = new Category($setting['name'] ?? '', $setting['id'] ?? null);
				if (!isset($root)) {
					$root = $category;
				}
				self::setSkip($category, $setting);
				if (isset($categories[$setting['id'] ?? $category->getId()])) {
					$categories[$setting['id'] ?? $category->getId()]->addCategory($category);
				}
				foreach ($setting['rounds'] ?? [] as $id) {
					$rounds[$id] = $category;
				}
				foreach ($setting['groups'] ?? [] as $id) {
					$groups[$id] = $category;
				}
				foreach ($setting['teams'] ?? [] as $id) {
					$teams[$id] = $category;
				}
				if (isset($setting['games']) && $root === $category) {
					$category->getGameContainer()->setAutoIncrement(min($setting['games']));
				}
				foreach ($setting['games'] ?? [] as $id) {
					$games[$id] = $category;
				}
			}
		}

		if (isset($data['rounds'])) {
			foreach ($data['rounds'] as $setting) {
				$setting = (array) $setting;
				$round = new Round($setting['name'] ?? '', $setting['id'] ?? null);
				if (!isset($root)) {
					$root = $round;
				}
				self::setSkip($round, $setting);
				if (isset($rounds[$setting['id'] ?? $round->getId()])) {
					$rounds[$setting['id'] ?? $round->getId()]->addRound($round);
				}
				foreach ($setting['groups'] ?? [] as $id) {
					$groups[$id] = $round;
				}
				foreach ($setting['teams'] ?? [] as $id) {
					$teams[$id] = $round;
				}
				if (isset($setting['games']) && $root === $round) {
					$round->getGameContainer()->setAutoIncrement(min($setting['games']));
				}
				foreach ($setting['games'] ?? [] as $id) {
					$games[$id] = $round;
				}
			}
		}

		$allGroups = [];
		if (isset($data['groups'])) {
			foreach ($data['groups'] as $setting) {
				$setting = (array) $setting;
				$group = new Group($setting['name'] ?? '', $setting['id'] ?? null);
				$allGroups[$group->getId()] = $group;
				if (!isset($root)) {
					$root = $group;
				}
				self::setSkip($group, $setting);
				self::setGroup($group, $setting);
				if (isset($groups[$setting['id'] ?? $group->getId()])) {
					$groups[$setting['id'] ?? $group->getId()]->addGroup($group);
				}
				foreach ($setting['teams'] ?? [] as $id) {
					$teams[$id] = $group;
				}
				if (isset($setting['games']) && $root === $group) {
					$group->getGameContainer()->setAutoIncrement(min($setting['games']));
				}
				foreach ($setting['games'] ?? [] as $id) {
					$games[$id] = $group;
				}
			}
		}

		if (isset($data['progressions'])) {
			foreach ($data['progressions'] as $setting) {
				$setting = (array) $setting;

				if (isset($setting['from'], $setting['to'], $allGroups[$setting['from']], $allGroups[$setting['to']])) {
					$progression = $allGroups[$setting['from']]->progression($allGroups[$setting['to']], $setting['offset'] ?? 0, $setting['length'] ?? null);
					if (isset($setting['filters'])) {
						foreach ($setting['filters'] as $filterSetting) {
							$filterSetting = (array) $filterSetting;
							$groups = array_map(static function($groupId) use ($allGroups) {
								return $allGroups[$groupId] ?? null;
							}, $filterSetting['groups']);
							$filter = new TeamFilter($filterSetting['what'], $filterSetting['how'], $filterSetting['val'], $groups);
							$progression->addFilter($filter);
						}
					}
					if (isset($setting['progressed'])) {
						$progression->setProgressed($setting['progressed']);
					}
				}
			}
		}

		$allTeams = [];
		if (isset($data['teams'])) {
			foreach ($data['teams'] as $setting) {
				$setting = (array) $setting;
				$team = new Team($setting['name'] ?? '', $setting['id'] ?? null);
				if (!isset($root)) {
					$root = $team;
				}
				$allTeams[$team->getId()] = $team;
				if (isset($teams[$setting['id'] ?? $team->getId()])) {
					$teams[$setting['id'] ?? $team->getId()]->addTeam($team);
				}
			}
		}

		if (isset($data['games'])) {
			foreach ($data['games'] as $setting) {
				$setting = (array) $setting;
				$gameTeams = [];
				foreach ($setting['teams'] ?? [] as $teamId) {
					$gameTeams[] = $allTeams[$teamId];
				}
				if (isset($setting['id'], $games[$setting['id']])) {
					$game = $games[$setting['id']]->game($gameTeams);
					if (isset($setting['scores'])) {
						$scores = array_map(static function($info) {
							return ((array) $info)['score'] ?? 0;
						}, $setting['scores']);
						$game->setResults($scores);
					}
				}
			}
		}

		return $root;
	}

	/**
	 * @param Tournament $tournament
	 * @param array      $setting
	 */
	protected static function setTournament(Tournament $tournament, array $setting) : void {
		foreach ($setting as $key => $value) {
			switch ($key) {
				case 'name':
					$tournament->setName($value);
					break;
				case 'skip':
					$tournament->setSkip($value);
					break;
				case 'timing':
					self::setTiming($tournament, (array) $value);
					break;
			}
		}
	}

	/**
	 * @param Tournament $object
	 * @param array      $setting
	 */
	protected static function setTiming(Tournament $object, array $setting) : void {
		foreach ($setting as $key2 => $value2) {
			switch ($key2) {
				case 'play':
					$object->setPlay($value2);
					break;
				case 'gameWait':
					$object->setGameWait($value2);
					break;
				case 'categoryWait':
					$object->setCategoryWait($value2);
					break;
				case 'roundWait':
					$object->setRoundWait($value2);
					break;
			}
		}
	}

	protected static function setSkip(WithSkipSetters $category, array $setting) : void {
		if (isset($setting['skip'])) {
			$category->setSkip($setting['skip']);
		}
	}

	/**
	 * @param Group $group
	 * @param array $setting
	 *
	 * @throws Exception
	 */
	protected static function setGroup(Group $group, array $setting) : void {
		foreach ($setting as $key => $value) {
			switch ($key) {
				case 'type':
					$group->setType($value);
					break;
				case 'points':
					self::setPoints($group, (array) $value);
					break;
				case 'inGame':
					$group->setInGame($value);
					break;
				case 'maxSize':
					$group->setMaxSize($value);
					break;
			}
		}
	}

	protected static function setPoints(Group $object, array $setting) : void {
		foreach ($setting as $key2 => $value2) {
			switch ($key2) {
				case 'win':
					$object->setWinPoints($value2);
					break;
				case 'loss':
					$object->setLostPoints($value2);
					break;
				case 'draw':
					$object->setDrawPoints($value2);
					break;
				case 'second':
					$object->setSecondPoints($value2);
					break;
				case 'third':
					$object->setThirdPoints($value2);
					break;
				case 'progression':
					$object->setProgressPoints($value2);
					break;
			}
		}
	}

}