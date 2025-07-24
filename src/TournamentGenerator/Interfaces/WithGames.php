<?php

declare(strict_types=1);

namespace TournamentGenerator\Interfaces;

use Exception;
use TournamentGenerator\Containers\GameContainer;
use TournamentGenerator\Game;

/**
 * Interface for objects that contain games.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.4
 */
interface WithGames
{
    /**
     * Get all tournament games.
     *
     * @return Game[]
     */
    public function getGames() : array;

    /**
     * Get the container for games.
     */
    public function getGameContainer() : GameContainer;

    /**
     * Sets a new autoincrement value (start) for the generated games.
     *
     * @param int $id Id - probably from the database
     *
     * @warning Do this on the top-level hierarchy element (Tournament class) or else, it might be reset later
     *
     * @post    Propagates the value to all child hierarchy objects
     *
     * @see     GameContainer::setAutoIncrement()
     * @since   0.5
     */
    public function setGameAutoincrementId(int $id) : WithGames;

    /**
     * Add a child container for games.
     */
    public function addGameContainer(GameContainer $gameContainer) : WithGames;

    /**
     * Set the game's results.
     *
     * Results is an array of [teamId => teamScore] key-value pairs. This method will look for a game with given teams
     * and try to set the first not played.
     *
     * @param int[] $results array of [teamId => teamScore] key-value pairs
     *
     * @throws Exception
     */
    public function setResults(array $results) : ?Game;
}
