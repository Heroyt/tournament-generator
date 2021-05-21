<?php


namespace TournamentGenerator\Traits;


use TournamentGenerator\Group;

/**
 * Trait WithGroups
 *
 * @package TournamentGenerator\Traits
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.4
 */
trait WithGroups
{

	/** @var array Group objects */
	protected array $groups = [];

	/**
	 * Get all groups in this object
	 *
	 * @return Group[]
	 */
	public function getGroups() : array {
		if ($this instanceof \TournamentGenerator\Interfaces\WithRounds) {
			$groups = [];
			foreach ($this->getRounds() as $round) {
				$groups[] = $round->getGroups();
			}
			return array_merge(...$groups);
		}
		return $this->groups;
	}
}