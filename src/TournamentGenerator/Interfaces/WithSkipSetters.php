<?php

declare(strict_types=1);

namespace TournamentGenerator\Interfaces;

/**
 * Interface that allows for setting skipping of not-playable games.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.4
 */
interface WithSkipSetters
{
    /**
     * Allows round skipping.
     */
    public function allowSkip() : WithSkipSetters;

    /**
     * Disallow round skipping.
     */
    public function disallowSkip() : WithSkipSetters;

    /**
     * Set round skipping.
     */
    public function setSkip(bool $skip) : WithSkipSetters;

    /**
     * Getter for round skipping.
     */
    public function getSkip() : bool;
}
