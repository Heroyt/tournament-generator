<?php

declare(strict_types=1);

namespace TournamentGenerator\Interfaces;

interface WithIterationSetters
{
    /**
     * Set how many iterations should be generated = how many times should each team play every other team.
     *
     * @return $this
     */
    public function setIterationCount(int $iterations) : static;

    /**
     * Set how many iterations should be generated = how many times should each team play every other team.
     */
    public function getIterationCount() : int;
}
