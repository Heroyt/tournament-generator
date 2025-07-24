<?php

declare(strict_types=1);

namespace TournamentGenerator;

use Exception;
use TournamentGenerator\Containers\GameContainer;
use TournamentGenerator\Containers\HierarchyContainer;
use TournamentGenerator\Containers\TeamContainer;
use TournamentGenerator\Interfaces\ProgressionInterface;
use TournamentGenerator\Interfaces\WithGames;
use TournamentGenerator\Interfaces\WithGeneratorSetters;
use TournamentGenerator\Interfaces\WithSkipSetters;
use TournamentGenerator\Interfaces\WithTeams;
use TournamentGenerator\Traits\WithGames as WithGamesTrait;
use TournamentGenerator\Traits\WithIterations;
use TournamentGenerator\Traits\WithTeams as WithTeamsTrait;

/**
 * Tournament group.
 *
 * Group is a collection of teams that play against each other. It defaults to Round-robin group where one team plays against every other team in a group.
 * Group can also be setup in such a way that teams play only one game against one other team (randomly selected). Teams from groups can be progressed (moved) to other groups.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.1
 */
class Group extends HierarchyBase implements WithGeneratorSetters, WithSkipSetters, WithTeams, WithGames
{
    use WithTeamsTrait;
    use WithGamesTrait;
    use WithIterations;

    /** @var Helpers\Generator Generator class to generate games of this group */
    protected Helpers\Generator $generator;

    /** @var int[]|string[] List of already progressed teams' id */
    protected array $progressed = [];

    /** @var string Ordering parameter */
    protected string $ordering = Constants::POINTS;

    /** @var ProgressionInterface[] List of progressions from this group */
    protected array $progressions = [];

    /** @var int Points acquired for winning */
    protected int $winPoints = 3;

    /** @var int Points acquired from draw */
    protected int $drawPoints = 1;

    /** @var int Points acquired from loss */
    protected int $lostPoints = 0;

    /** @var int Points acquired from placing second (only for 3 or 4 teams in one game) */
    protected int $secondPoints = 2;

    /** @var int Points acquired from placing third (only for 4 teams in one game) */
    protected int $thirdPoints = 1;

    /**
     * @var int Points acquired from progressing to the next round
     *
     * @details This can be useful when getting the total team order in a tournament. Ex: If you consider teams that
     *     progressed to the next round to be higher on the scoreboard even if they got less points in total than some
     *     other teams that did not progressed.
     */
    protected int $progressPoints = 50;

    /** @var int Group order in a round */
    protected int $order = 0;

    /**
     * Group constructor.
     *
     * @param string          $name Group name
     * @param null|int|string $id   Group id - if omitted -> it is generated automatically as unique string
     */
    public function __construct(string $name, null|int|string $id = null) {
        $this->setName($name);
        $this->generator = new Helpers\Generator($this);
        // @infection-ignore-all
        $this->setId($id ?? uniqid('', false));
        $this->games = new GameContainer($this->id);
        $this->teams = new TeamContainer($this->id);
        $this->container = new HierarchyContainer($this->id);
    }

    /**
     * Add one or more teams into the object.
     *
     * @param Team ...$teams Team objects
     *
     * @throws Exception
     */
    public function addTeam(Team ...$teams) : Group {
        foreach ($teams as $team) {
            $this->teams->insert($team);
            $team->addGroupResults($this);
        }

        return $this;
    }

    /**
     * Create a new team and add it into the object.
     *
     * @param string          $name Name of the new team
     * @param null|int|string $id   Id of the new team - if omitted -> it is generated automatically as unique string
     *
     * @return Team Newly created team
     *
     * @throws Exception
     */
    public function team(string $name = '', null|int|string $id = null) : Team {
        $team = new Team($name, $id);
        $this->teams->insert($team);
        $team->addGroupResults($this);

        return $team;
    }

    /**
     * Allows round skipping.
     */
    public function allowSkip() : Group {
        $this->generator->allowSkip();

        return $this;
    }

    /**
     * Set round skipping.
     */
    public function disallowSkip() : Group {
        $this->generator->disallowSkip();

        return $this;
    }

    /**
     * Set round skipping.
     */
    public function setSkip(bool $skip) : Group {
        $this->generator->setSkip($skip);

        return $this;
    }

    /**
     * Getter for round skipping.
     */
    public function getSkip() : bool {
        return $this->generator->getSkip();
    }

    /**
     * Get points for winning.
     */
    public function getWinPoints() : int {
        return $this->winPoints;
    }

    /**
     * Set points for winning.
     */
    public function setWinPoints(int $points) : Group {
        $this->winPoints = $points;

        return $this;
    }

    /**
     * Get points for draw.
     */
    public function getDrawPoints() : int {
        return $this->drawPoints;
    }

    /**
     * Set points for draw.
     */
    public function setDrawPoints(int $points) : Group {
        $this->drawPoints = $points;

        return $this;
    }

    /**
     * Get points for losing.
     */
    public function getLostPoints() : int {
        return $this->lostPoints;
    }

    /**
     * Set points for losing.
     */
    public function setLostPoints(int $points) : Group {
        $this->lostPoints = $points;

        return $this;
    }

    /**
     * Get points for being second.
     */
    public function getSecondPoints() : int {
        return $this->secondPoints;
    }

    /**
     * Set points for being second.
     */
    public function setSecondPoints(int $points) : Group {
        $this->secondPoints = $points;

        return $this;
    }

    /**
     * Get points for being third.
     */
    public function getThirdPoints() : int {
        return $this->thirdPoints;
    }

    /**
     * Set points for being third.
     */
    public function setThirdPoints(int $points) : Group {
        $this->thirdPoints = $points;

        return $this;
    }

    /**
     * Get points for progression.
     */
    public function getProgressPoints() : int {
        return $this->progressPoints;
    }

    /**
     * Set points for progression.
     */
    public function setProgressPoints(int $points) : Group {
        $this->progressPoints = $points;

        return $this;
    }

    /**
     * Set maximum group size.
     *
     * Does not disallow adding teams to this round!
     * This can be used to split teams in a group into "subgroups" if you need to limit the maximum number of games.
     *
     * @throws Exception
     */
    public function setMaxSize(int $size) : Group {
        $this->generator->setMaxSize($size);

        return $this;
    }

    /**
     * Get the maximum group size.
     *
     * Does not disallow adding teams to this round!
     * This can be used to split teams in a group into "subgroups" if you need to limit the maximum number of games.
     */
    public function getMaxSize() : int {
        return $this->generator->getMaxSize();
    }

    /**
     * Set group type.
     *
     * @throws Exception
     *
     * @see Constants::GroupTypes
     */
    public function setType(string $type = Constants::ROUND_ROBIN) : Group {
        $this->generator->setType($type);

        return $this;
    }

    /**
     * Get group type.
     *
     * @see Constants::GroupTypes
     */
    public function getType() : string {
        return $this->generator->getType();
    }

    /**
     * Get group order.
     */
    public function getOrder() : int {
        return $this->order;
    }

    /**
     * Set group order.
     */
    public function setOrder(int $order) : Group {
        $this->order = $order;

        return $this;
    }

    /**
     * Get parameter to order the teams by.
     */
    public function getOrdering() : string {
        return $this->ordering;
    }

    /**
     * Set parameter to order the teams by.
     *
     * @throws Exception
     *
     * @see Constants::OrderingTypes
     */
    public function setOrdering(string $ordering = Constants::POINTS) : Group {
        if (!in_array($ordering, Constants::OrderingTypes, true)) {
            throw new Exception('Unknown group ordering: ' . $ordering);
        }
        $this->ordering = $ordering;

        return $this;
    }

    /**
     * Set how many teams play in one game.
     *
     * @param int $inGame 2 / 3 / 4
     *
     * @throws Exception
     */
    public function setInGame(int $inGame) : Group {
        $this->generator->setInGame($inGame);

        return $this;
    }

    /**
     * Get how many teams play in one game.
     */
    public function getInGame() : int {
        return $this->generator->getInGame();
    }

    /**
     * Add a progression to this group.
     */
    public function addProgression(ProgressionInterface $progression) : Group {
        $this->progressions[] = $progression;

        return $this;
    }

    /**
     * Creates a new progression from this group.
     *
     * Progression uses a similar syntax to php's array_slice() function.
     *
     * @param Group    $group  Which group to progress to
     * @param int      $offset First index
     * @param null|int $len    Maximum number of teams to progress
     *
     * @see https://www.php.net/manual/en/function.array-slice.php
     */
    public function progression(Group $group, int $offset = 0, ?int $len = null) : Progression {
        $progression = new Progression($this, $group, $offset, $len);
        $this->progressions[] = $progression;

        return $progression;
    }

    /**
     * Creates a new multi-progression INTO this group.
     *
     * Progression uses a similar syntax to php's array_slice() function.
     *
     * @warning The logic is reversed from the Group::progression() method. This will create a progression INTO this
     *     group.
     *
     * @param Group[]  $from
     * @param int      $offset First index
     * @param null|int $len    Maximum number of teams to progress
     *
     * @see     https://www.php.net/manual/en/function.array-slice.php
     */
    public function multiProgression(
        array $from,
        int $offset = 0,
        ?int $len = null,
        ?int $totalCount = null,
        int $totalStart = 0
    ) : MultiProgression {
        $multiProgression = new MultiProgression($from, $this, $offset, $len, $totalCount, $totalStart);
        foreach ($from as $group) {
            $group->addProgression($multiProgression);
        }

        return $multiProgression;
    }

    /**
     * Progress all teams using already setup progression.
     *
     * @pre  All progressions are setup
     *
     * @post All teams have been moved into their next groups
     *
     * @param bool $blank If true - create dummy teams instead of progressing the real objects
     *
     * @throws Exception
     */
    public function progress(bool $blank = false) : Group {
        foreach ($this->progressions as $progression) {
            $progression->progress($blank);
        }

        return $this;
    }

    /**
     * Add teams to the `progressed` list.
     *
     * @param Team[] $teams
     */
    public function addProgressed(Team ...$teams) : Group {
        $this->progressed = array_merge(
            $this->progressed,
            array_map(static fn ($a) : int|string => $a->getId(), $teams)
        );

        return $this;
    }

    /**
     * Check if a given team is progressed from this group.
     */
    public function isProgressed(Team $team) : bool {
        return in_array($team->getId(), $this->progressed, true);
    }

    /**
     * Generate all games.
     *
     * @throws Exception
     */
    public function genGames() : array {
        return $this->generator->genGames();
    }

    /**
     * Create a new game and add it to the group.
     *
     * @param Team[] $teams Teams that are playing
     *
     * @post The game's id is set to the current auto-incremented value
     *
     * @throws Exception
     */
    public function game(array $teams = []) : Game {
        $game = new Game($teams, $this);
        $game->setId($this->games->getAutoIncrement());
        $this->games->incrementId();
        $this->games->insert($game);

        return $game;
    }

    /**
     * Add games to this group.
     *
     * @param Game[] $games
     *
     * @post The games' id is set to the current auto-incremented value
     *
     * @throws Exception
     */
    public function addGame(Game ...$games) : Group {
        $this->games->insert(...$games);
        // Set the game id's
        foreach ($games as $game) {
            $game->setId($this->games->getAutoIncrement());
            // @noinspection DisconnectedForeachInstructionInspection
            $this->games->incrementId();
        }

        return $this;
    }

    /**
     * Order generated games to minimize teams playing multiple games after one other.
     *
     * @post The game ids are reset according to their new order
     *
     * @return Game[]
     *
     * @throws Exception
     */
    public function orderGames() : array {
        if (count($this->games) < 5) {
            return $this->games->get();
        }
        $this->games->resetAutoIncrement();

        return $this->generator->orderGames();
    }

    /**
     * Simulate all games in this group as they would be played for real.
     *
     * @param TeamFilter[]|TeamFilter[][] $filters Filters to teams returned from the group
     * @param bool                        $reset   If true - the scores will be reset after simulation
     *
     * @return Team[]
     *
     * @throws Exception
     */
    public function simulate(array $filters = [], bool $reset = true) : array {
        return Helpers\Simulator::simulateGroup($this, $filters, $reset);
    }

    /**
     * Reset all game results as if they were not played.
     *
     * @post All games in this group are marked as "not played"
     * @post All scores in this group are deleted
     *
     * @throws Exception
     */
    public function resetGames() : Group {
        foreach ($this->getGames() as $game) {
            $game->resetResults();
        }

        return $this;
    }

    /**
     * Check if all games in this group has been played.
     */
    public function isPlayed() : bool {
        if (0 === count($this->games)) {
            return false;
        }

        return count(array_filter($this->getGames(), static fn ($a) : bool => $a->isPlayed())) === count($this->games);
    }

    /**
     * Get all progressions.
     *
     * @return ProgressionInterface[]
     */
    public function getProgressions() : array {
        return $this->progressions;
    }

    /**
     * @throws Exception
     */
    public function jsonSerialize() : array {
        return [
            'id'    => $this->getId(),
            'name'  => $this->getName(),
            'games' => $this->getGames(),
            'teams' => $this->teams->ids(),
        ];
    }

    public function setIterationCount(int $iterations) : static {
        $this->generator->setIterationCount($iterations);
        $this->iterations = $iterations;

        return $this;
    }
}
