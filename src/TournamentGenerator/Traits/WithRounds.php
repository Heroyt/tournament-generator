<?php

declare(strict_types=1);

namespace TournamentGenerator\Traits;

use TournamentGenerator\Containers\ContainerQuery;
use TournamentGenerator\Interfaces\WithIterationSetters;
use TournamentGenerator\Interfaces\WithRounds as WithRoundsInterface;
use TournamentGenerator\Interfaces\WithSkipSetters as WithSkipSettersInterface;
use TournamentGenerator\Round;

/**
 * Trait WithRounds.
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 *
 * @since   0.4
 */
trait WithRounds
{
    /**
     * Adds round to the category.
     *
     * @param Round ...$rounds One or more round objects
     *
     * @return $this
     */
    public function addRound(Round ...$rounds) : WithRoundsInterface {
        foreach ($rounds as $round) {
            $this->insertIntoContainer($round);
        }

        return $this;
    }

    /**
     * Creates a new round and adds it to the category.
     *
     * @param string          $name Round name
     * @param null|int|string $id   Round id - if omitted -> it is generated automatically as unique string
     *
     * @return Round The newly created round
     */
    public function round(string $name = '', null|int|string $id = null) : Round {
        $round = new Round($name, $id);
        if ($this instanceof WithSkipSettersInterface) {
            $round->setSkip($this->getSkip());
        }
        if ($this instanceof WithIterationSetters) {
            $round->setIterationCount($this->getIterationCount());
        }
        $this->insertIntoContainer($round);

        return $round;
    }

    /**
     * Get all rounds in this category.
     *
     * @return Round[]
     */
    public function getRounds() : array {
        return $this->container->getHierarchyLevel(Round::class);
    }

    /**
     * Get rounds container query.
     */
    public function queryRounds() : ContainerQuery {
        return $this->container->getHierarchyLevelQuery(Round::class);
    }
}
