<?php

declare(strict_types=1);

namespace TournamentGenerator\Import;

use Exception;
use JsonException;
use TournamentGenerator\Base;
use TournamentGenerator\Category;
use TournamentGenerator\Group;
use TournamentGenerator\Interfaces\WithGames;
use TournamentGenerator\Interfaces\WithIterationSetters;
use TournamentGenerator\Interfaces\WithSkipSetters;
use TournamentGenerator\Round;
use TournamentGenerator\Team;
use TournamentGenerator\TeamFilter;
use TournamentGenerator\Tournament;

/**
 * Basic importer.
 *
 * Importer uses exported data and creates a new tournament objects from it.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.5
 */
class Importer
{
    /** @var array List of => */
    protected static array $categories = [];

    /** @var array List of => */
    protected static array $rounds = [];

    /** @var array List of => */
    protected static array $groups = [];

    /** @var array List of => */
    protected static array $teams = [];

    /** @var array List of => */
    protected static array $games = [];

    /** @var null|Base Root object - returned from import */
    protected static ?Base $root = null;

    /**
     * Processes a JSON input and creates necessary objects from it.
     *
     * @throws InvalidImportDataException
     * @throws JsonException
     *
     * @see Importer::import()
     */
    public static function importJson(string $data) : ?Base {
        return self::import(json_decode($data, true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * Processes an input array of data and creates necessary objects from it.
     *
     * @return null|Base Imported root object or null if nothing was created
     *
     * @throws InvalidImportDataException
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
     * Creates a tournament object.
     */
    protected static function createTournament(array $setting) : void {
        if ([] !== $setting) {
            // Check tournament type (can be a preset)
            if (empty($setting['type']) || 'general' === $setting['type']) {
                $tournament = new Tournament();
            } else {
                $tournament = new $setting['type']();
            }
            self::$root = $tournament; // If set - Tournament is always root

            self::setTournament($tournament, $setting);
            self::logAllIds($setting, $tournament);
        }
    }

    /**
     * Setup a tournament with all its settings.
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

                case 'iteration':
                    $tournament->setIterationCount($value);

                    break;

                case 'timing':
                    self::setTiming($tournament, (array) $value);

                    break;
            }
        }
    }

    /**
     * Set timing setting to a tournament object.
     */
    protected static function setTiming(Tournament $tournament, array $setting) : void {
        foreach ($setting as $key2 => $value2) {
            switch ($key2) {
                case 'play':
                    $tournament->setPlay($value2);

                    break;

                case 'gameWait':
                    $tournament->setGameWait($value2);

                    break;

                case 'categoryWait':
                    $tournament->setCategoryWait($value2);

                    break;

                case 'roundWait':
                    $tournament->setRoundWait($value2);

                    break;
            }
        }
    }

    /**
     * Log all set ids and objects into helper arrays.
     *
     * Adds an $id => $object pair for each id and group. Sets an game autoincrement value to the lowest possible game
     * id if the object is root.
     *
     * @param array $setting Object's settings
     * @param Base  $base    Object that is logged
     *
     * @see Importer::addIds()
     */
    protected static function logAllIds(array $setting, Base $base) : void {
        self::addIds(self::$categories, $base, $setting['categories'] ?? []);
        self::addIds(self::$rounds, $base, $setting['rounds'] ?? []);
        self::addIds(self::$groups, $base, $setting['groups'] ?? []);
        self::addIds(self::$teams, $base, $setting['teams'] ?? [], true);
        self::addIds(self::$games, $base, $setting['games'] ?? []);
        // @noinspection NotOptimalIfConditionsInspection
        if (self::$root === $base && !empty($setting['games']) && $base instanceof WithGames) {
            $base->getGameContainer()->setAutoIncrement(min($setting['games']));
        }
    }

    /**
     * Log an object as parent to other object.
     *
     * Adds an $id => $object pairs for each id into $group.
     *
     * @param array $group Group to log the object into
     * @param Base  $base  Object to log
     * @param array $ids   List of child object ids
     */
    protected static function addIds(array &$group, Base $base, array $ids, bool $multiple = false) : void {
        foreach ($ids as $id) {
            if ($multiple) {
                if (!isset($group[$id])) {
                    $group[$id] = [];
                }
                $group[$id][] = $base;

                continue;
            }
            $group[$id] = $base;
        }
    }

    /**
     * Create category objects.
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
     * Set skip setting to an object.
     */
    protected static function setSkip(WithSkipSetters $withSkipSetters, array $setting) : void {
        if (isset($setting['skip'])) {
            $withSkipSetters->setSkip($setting['skip']);
        }
    }

    /**
     * Set skip setting to an object.
     */
    protected static function setIterations(WithIterationSetters $withIterationSetters, array $setting) : void {
        if (isset($setting['iterations'])) {
            $withIterationSetters->setIterationCount($setting['iterations']);
        }
    }

    /**
     * Create round objects.
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
            self::setIterations($round, $setting);

            // Set parent if exists
            if (isset(self::$rounds[$setting['id'] ?? $round->getId()])) {
                self::$rounds[$setting['id'] ?? $round->getId()]->addRound($round);
            }
            self::logAllIds($setting, $round);
        }
    }

    /**
     * Create group objects.
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
            self::setIterations($group, $setting);

            // Set parent if exists
            if (isset(self::$groups[$setting['id'] ?? $group->getId()])) {
                self::$groups[$setting['id'] ?? $group->getId()]->addGroup($group);
            }
            self::logAllIds($setting, $group);
        }
    }

    /**
     * Setup a group with all its settings.
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
     * Set points setting to an object.
     */
    protected static function setPoints(Group $group, array $setting) : void {
        foreach ($setting as $key2 => $value2) {
            switch ($key2) {
                case 'win':
                    $group->setWinPoints($value2);

                    break;

                case 'loss':
                    $group->setLostPoints($value2);

                    break;

                case 'draw':
                    $group->setDrawPoints($value2);

                    break;

                case 'second':
                    $group->setSecondPoints($value2);

                    break;

                case 'third':
                    $group->setThirdPoints($value2);

                    break;

                case 'progression':
                    $group->setProgressPoints($value2);

                    break;
            }
        }
    }

    /**
     * Create all progressions.
     */
    protected static function createProgressions(array $progressions, array $allGroups) : void {
        foreach ($progressions as $setting) {
            // Typecast settings
            $setting = (array) $setting;

            if (isset($setting['from'], $setting['to'], $allGroups[$setting['from']], $allGroups[$setting['to']])) {
                $progression = $allGroups[$setting['from']]->progression(
                    $allGroups[$setting['to']],
                    $setting['offset'] ?? 0,
                    $setting['length'] ?? null
                );

                // Setup filters
                foreach ($setting['filters'] ?? [] as $filterSetting) {
                    // Typecast settings
                    $filterSetting = (array) $filterSetting;

                    self::$groups = array_map(
                        static fn ($groupId) => $allGroups[$groupId] ?? null,
                        $filterSetting['groups'] ?? []
                    );

                    $filter = new TeamFilter($filterSetting['what'] ?? 'points', $filterSetting['how'] ?? '>', $filterSetting['val'] ?? 0, self::$groups);
                    $progression->addFilter($filter);
                }

                if (isset($setting['progressed'])) {
                    $progression->setProgressed($setting['progressed']);
                }

                if (isset($settings['points'])) {
                    $progression->setPoints($settings['points']);
                }
            }
        }
    }

    /**
     * Create all team objects.
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
     * Create all game objects.
     */
    protected static function createGames(mixed $games, array $allTeams) : void {
        foreach ($games as $setting) {
            // Typecast settings
            $setting = (array) $setting;

            $gameTeams = array_map(static fn ($teamId) => $allTeams[$teamId] ?? null, $setting['teams'] ?? []);

            // Check if parent group exists
            if (isset($setting['id'], self::$games[$setting['id']])) {

                $game = self::$games[$setting['id']]->game($gameTeams);

                // Set results
                if (isset($setting['scores'])) {
                    $scores = array_map(static fn ($info) => ((array) $info)['score'] ?? 0, $setting['scores']);
                    $game->setResults($scores);
                }

            }
        }
    }
}
