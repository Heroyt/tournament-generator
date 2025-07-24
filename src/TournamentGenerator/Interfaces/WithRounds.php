<?php

declare(strict_types=1);

namespace TournamentGenerator\Interfaces;

use TournamentGenerator\Containers\ContainerQuery;
use TournamentGenerator\Round;

/**
 * Interface for objects that contain rounds.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.4
 */
interface WithRounds
{
    /**
     * Adds round to the Object.
     *
     * @param Round ...$rounds One or more round objects
     */
    public function addRound(Round ...$rounds) : WithRounds;

    /**
     * Creates a new round and adds it to the category.
     *
     * @param string          $name Round name
     * @param null|int|string $id   Round id - if omitted -> it is generated automatically as unique string
     *
     * @return Round The newly created round
     */
    public function round(string $name = '', null|int|string $id = null) : Round;

    /**
     * Get all rounds in this category.
     *
     * @return Round[]
     */
    public function getRounds() : array;

    /**
     * Get rounds container query.
     */
    public function queryRounds() : ContainerQuery;
}
