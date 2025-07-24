<?php

declare(strict_types=1);

namespace TournamentGenerator\Traits;

use TournamentGenerator\Interfaces\WithSkipSetters as WithSkipSettersInterface;

/**
 * Trait WithSkipSetters.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.4
 */
trait WithSkipSetters
{
    /** @var bool If the number of teams is less than $this->inGame, then skip playing this round */
    private bool $allowSkip = false;

    /**
     * Allows round skipping.
     *
     * @return $this
     */
    public function allowSkip() : WithSkipSettersInterface {
        $this->allowSkip = true;

        return $this;
    }

    /**
     * Disallow round skipping.
     *
     * @return $this
     */
    public function disallowSkip() : WithSkipSettersInterface {
        $this->allowSkip = false;

        return $this;
    }

    /**
     * Set round skipping.
     *
     * @return $this
     */
    public function setSkip(bool $skip) : WithSkipSettersInterface {
        $this->allowSkip = $skip;

        return $this;
    }

    /**
     * Getter for round skipping.
     */
    public function getSkip() : bool {
        return $this->allowSkip;
    }
}
