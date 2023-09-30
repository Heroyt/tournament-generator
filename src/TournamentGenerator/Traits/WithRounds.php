<?php


namespace TournamentGenerator\Traits;


use TournamentGenerator\Containers\ContainerQuery;
use TournamentGenerator\Interfaces\WithIterationSetters;
use TournamentGenerator\Interfaces\WithRounds as WithRoundsInterface;
use TournamentGenerator\Interfaces\WithSkipSetters as WithSkipSettersInterface;
use TournamentGenerator\Round;

/**
 * Trait WithRounds
 *
 * @package TournamentGenerator\Traits
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.4
 */
trait WithRounds
{

	/**
	 * Adds round to the category
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
	 * Creates a new round and adds it to the category
	 *
	 * @param string          $name Round name
	 * @param string|int|null $id   Round id - if omitted -> it is generated automatically as unique string
	 *
	 * @return Round The newly created round
	 */
	public function round(string $name = '', $id = null) : Round {
		$r = new Round($name, $id);
		if ($this instanceof WithSkipSettersInterface) {
			$r->setSkip($this->getSkip());
		}
		if ($this instanceof WithIterationSetters) {
			$r->setIterationCount($this->getIterationCount());
		}
		$this->insertIntoContainer($r);
		return $r;
	}

	/**
	 * Get all rounds in this category
	 *
	 * @return Round[]
	 */
	public function getRounds() : array {
		return $this->container->getHierarchyLevel(Round::class);
	}

	/**
	 * Get rounds container query
	 *
	 * @return ContainerQuery
	 */
	public function queryRounds() : ContainerQuery {
		return $this->container->getHierarchyLevelQuery(Round::class);
	}
}