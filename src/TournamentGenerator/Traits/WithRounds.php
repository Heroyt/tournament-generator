<?php


namespace TournamentGenerator\Traits;


use TournamentGenerator\Interfaces\WithRounds as WithRoundsInterface;
use TournamentGenerator\Interfaces\WithSkipSetters;
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

	/** @var Round[] Rounds in a category */
	protected array $rounds = [];

	/**
	 * Adds round to the category
	 *
	 * @param Round ...$rounds One or more round objects
	 *
	 * @return $this
	 */
	public function addRound(Round ...$rounds) : WithRoundsInterface {
		foreach ($rounds as $round) {
			$this->rounds[] = $round;
		}
		return $this;
	}

	/**
	 * Creates a new round and adds it to the category
	 *
	 * @param string $name Round name
	 * @param null   $id   Round id - if omitted -> it is generated automatically as unique string
	 *
	 * @return Round The newly created round
	 */
	public function round(string $name = '', $id = null) : Round {
		$r = new Round($name, $id);
		if ($this instanceof WithSkipSetters) {
			$this->rounds[] = $r->setSkip($this->getSkip());
		}
		return $r;
	}

	/**
	 * Get all rounds in this category
	 *
	 * @return Round[]
	 */
	public function getRounds() : array {
		if ($this instanceof \TournamentGenerator\Interfaces\WithCategories && count($this->categories) > 0) {
			$rounds = [];
			foreach ($this->categories as $category) {
				$rounds[] = $category->getRounds();
			}
			$rounds[] = $this->rounds;
			return array_merge(...$rounds);
		}
		return $this->rounds;
	}
}