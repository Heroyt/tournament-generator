<?php

declare(strict_types=1);

namespace TournamentGenerator\Interfaces;

use TournamentGenerator\Constants;

/**
 * Interface for objects that can generate games.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.4
 */
interface WithGeneratorSetters extends WithIterationSetters
{
    /**
     * Sets a generator type.
     */
    public function setType(string $type = Constants::ROUND_ROBIN) : WithGeneratorSetters;

    /**
     * Get generator type.
     */
    public function getType() : string;

    /**
     * Set max group size.
     */
    public function setMaxSize(int $size) : WithGeneratorSetters;

    /**
     * Get max group size.
     */
    public function getMaxSize() : int;

    /**
     * Set how many teams play in each game.
     *
     * @param int $inGame 2/3/4
     */
    public function setInGame(int $inGame) : WithGeneratorSetters;

    /**
     * Get how many teams play in each game.
     */
    public function getInGame() : int;
}
