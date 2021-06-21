<?php


namespace TournamentGenerator\Import;

use Exception;
use JsonException;
use TournamentGenerator\Base;
use TournamentGenerator\Category;
use TournamentGenerator\Group;
use TournamentGenerator\Interfaces\WithGames;
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
	/** @var array List of $categoryId => $parent */
	protected static array $categories = [];
	/** @var array List of $roundId => $parent */
	protected static array $rounds = [];
	/** @var array List of $groupId => $parent */
	protected static array $groups = [];
	/** @var array List of $teamId => $parent */
	protected static array $teams = [];
	/** @var array List of $gameId => $parent */
	protected static array $games = [];

	/** @var Base|null $root Root object - returned from import */
	protected static ?Base $root = null;

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
	public static function importJson(string $data) : ?Base {
		return self::import(json_decode($data, true, 512, JSON_THROW_ON_ERROR));
	}

	/**
	 * Processes an input array of data and creates necessary objects from it
	 *
	 * @param array $data
	 *
	 * @return Base Imported root object or null if nothing was created
	 *
	 * @throws InvalidImportDataException
	 * @throws Exception
	 */
	public static function import(array $data) : ?Base {
		// Validate data
		ImportValidator::validate($data);

		// Groups for parent logging
		// This allows setting a parent to other objects. The latest parent in hierarchy.
		self::$categories = [];
		self::$rounds = [];
		self::$groups = [];
		self::$teams = [];
		self::$games = [];

		// Reset root
		self::$root = null;

		// Helper array - $id => $object reference
		$allGroups = [];
		$allTeams = [];

		// Try setting up the tournament object
		self::createTournament((array) ($data['tournament'] ?? []));

		// Try setting up all category objects
		self::createCategories($data['categories'] ?? []);

		// Try setting up all round objects
		self::createRounds($data['rounds'] ?? []);

		// Try setting up all group objects
		self::createGroups($data['groups'] ?? [], $allGroups);

		// Try setting up all progression objects
		self::createProgressions($data['progressions'] ?? [], $allGroups);

		// Try setting up all team objects
		self::createTeams($data['teams'] ?? [], $allTeams);

		// Try setting up all game objects
		self::createGames($data['games'] ?? [], $allTeams);

		return self::$root;
	}

	/**
	 * Creates a tournament object
	 *
	 * @param array $setting
	 */
	protected static function createTournament(array $setting) : void {
		if (!empty($setting)) {
			// Check tournament type (can be a preset)
			if (empty($setting['type']) || $setting['type'] === 'general') {
				$tournament = new Tournament();
			}
			else {
				$tournament = new $setting['type'];
			}
			self::$root = $tournament; // If set - Tournament is always root

			self::setTournament($tournament, $setting);
			self::logAllIds($setting, $tournament);
		}
	}

	/**
	 * Setup a tournament with all its settings
	 *
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
	 * Set timing setting to a tournament object
	 *
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

	/**
	 * Log all set ids and objects into helper arrays
	 *
	 * Adds an $id => $object pair for each id and group. Sets an game autoincrement value to the lowest possible game id if the object is root.
	 *
	 * @param array $setting Object's settings
	 * @param Base  $object  Object that is logged
	 *
	 * @see Importer::addIds()
	 */
	protected static function logAllIds(array $setting, Base $object) : void {
		self::addIds(self::$categories, $object, $setting['categories'] ?? []);
		self::addIds(self::$rounds, $object, $setting['rounds'] ?? []);
		self::addIds(self::$groups, $object, $setting['groups'] ?? []);
		self::addIds(self::$teams, $object, $setting['teams'] ?? [], true);
		self::addIds(self::$games, $object, $setting['games'] ?? []);
		/** @noinspection NotOptimalIfConditionsInspection */
		if (self::$root === $object && isset($setting['games']) && $object instanceof WithGames) {
			$object->getGameContainer()->setAutoIncrement(min($setting['games']));
		}
	}

	/**
	 * Log an object as parent to other object
	 *
	 * Adds an $id => $object pairs for each id into $group.
	 *
	 * @param array $group  Group to log the object into
	 * @param Base  $object Object to log
	 * @param array $ids    List of child object ids
	 */
	protected static function addIds(array &$group, Base $object, array $ids, bool $multiple = false) : void {
		foreach ($ids as $id) {
			if ($multiple) {
				if (!isset($group[$id])) {
					$group[$id] = [];
				}
				$group[$id][] = $object;
				continue;
			}
			$group[$id] = $object;
		}
	}

	/**
	 * Create category objects
	 *
	 * @param array $categories
	 */
	protected static function createCategories(array $categories) : void {
		foreach ($categories as $setting) {
			// Typecast settings
			$setting = (array) $setting;
			$category = new Category($setting['name'] ?? '', $setting['id'] ?? null);

			if (!isset(self::$root)) {
				self::$root = $category;
			}

			self::setSkip($category, $setting);

			// Set parent if exists
			if (isset(self::$categories[$setting['id'] ?? $category->getId()])) {
				self::$categories[$setting['id'] ?? $category->getId()]->addCategory($category);
			}

			self::logAllIds($setting, $category);
		}
	}

	/**
	 * Set skip setting to an object
	 *
	 * @param WithSkipSetters $category
	 * @param array           $setting
	 */
	protected static function setSkip(WithSkipSetters $category, array $setting) : void {
		if (isset($setting['skip'])) {
			$category->setSkip($setting['skip']);
		}
	}

	/**
	 * Create round objects
	 *
	 * @param array $rounds
	 */
	protected static function createRounds(array $rounds) : void {
		foreach ($rounds as $setting) {
			// Typecast settings
			$setting = (array) $setting;
			$round = new Round($setting['name'] ?? '', $setting['id'] ?? null);

			if (!isset(self::$root)) {
				self::$root = $round;
			}

			self::setSkip($round, $setting);

			// Set parent if exists
			if (isset(self::$rounds[$setting['id'] ?? $round->getId()])) {
				self::$rounds[$setting['id'] ?? $round->getId()]->addRound($round);
			}
			self::logAllIds($setting, $round);
		}
	}

	/**
	 * Create group objects
	 *
	 * @param array $groups
	 * @param array $allGroups
	 *
	 * @throws Exception
	 */
	protected static function createGroups(array $groups, array &$allGroups) : void {
		foreach ($groups as $setting) {
			// Typecast settings
			$setting = (array) $setting;
			$group = new Group($setting['name'] ?? '', $setting['id'] ?? null);
			$allGroups[$group->getId()] = $group;

			if (!isset(self::$root)) {
				self::$root = $group;
			}

			self::setSkip($group, $setting);
			self::setGroup($group, $setting);

			// Set parent if exists
			if (isset(self::$groups[$setting['id'] ?? $group->getId()])) {
				self::$groups[$setting['id'] ?? $group->getId()]->addGroup($group);
			}
			self::logAllIds($setting, $group);
		}
	}

	/**
	 * Setup a group with all its settings
	 *
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

	/**
	 * Set points setting to an object
	 *
	 * @param Group $object
	 * @param array $setting
	 */
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

	/**
	 * Create all progressions
	 *
	 * @param array $progressions
	 * @param array $allGroups
	 */
	protected static function createProgressions(array $progressions, array $allGroups) : void {
		foreach ($progressions as $setting) {
			// Typecast settings
			$setting = (array) $setting;

			if (isset($setting['from'], $setting['to'], $allGroups[$setting['from']], $allGroups[$setting['to']])) {
				$progression = $allGroups[$setting['from']]->progression($allGroups[$setting['to']], $setting['offset'] ?? 0, $setting['length'] ?? null);

				// Setup filters
				foreach ($setting['filters'] ?? [] as $filterSetting) {
					// Typecast settings
					$filterSetting = (array) $filterSetting;

					self::$groups = array_map(static function($groupId) use ($allGroups) {
						return $allGroups[$groupId] ?? null;
					}, $filterSetting['groups'] ?? []);

					$filter = new TeamFilter($filterSetting['what'] ?? 'points', $filterSetting['how'] ?? '>', $filterSetting['val'] ?? 0, self::$groups);
					$progression->addFilter($filter);
				}

				if (isset($setting['progressed'])) {
					$progression->setProgressed($setting['progressed']);
				}
			}
		}
	}

	/**
	 * Create all team objects
	 *
	 * @param array $teams
	 * @param array $allTeams
	 */
	protected static function createTeams(array $teams, array &$allTeams) : void {
		foreach ($teams as $setting) {
			// Typecast settings
			$setting = (array) $setting;
			$team = new Team($setting['name'] ?? '', $setting['id'] ?? null);
			$allTeams[$team->getId()] = $team;

			if (!isset(self::$root)) {
				self::$root = $team;
			}

			// Set parent if exists
			if (isset(self::$teams[$setting['id'] ?? $team->getId()])) {
				foreach (self::$teams[$setting['id'] ?? $team->getId()] as $object) {
					$object->addTeam($team);
				}
			}
		}
	}

	/**
	 * Create all game objects
	 *
	 * @param       $games
	 * @param array $allTeams
	 */
	protected static function createGames($games, array $allTeams) : void {
		foreach ($games as $setting) {
			// Typecast settings
			$setting = (array) $setting;

			$gameTeams = array_map(static function($teamId) use ($allTeams) {
				return $allTeams[$teamId] ?? null;
			}, $setting['teams'] ?? []);

			// Check if parent group exists
			if (isset($setting['id'], self::$games[$setting['id']])) {

				$game = self::$games[$setting['id']]->game($gameTeams);

				// Set results
				if (isset($setting['scores'])) {
					$scores = array_map(static function($info) {
						return ((array) $info)['score'] ?? 0;
					}, $setting['scores']);
					$game->setResults($scores);
				}

			}
		}
	}

}