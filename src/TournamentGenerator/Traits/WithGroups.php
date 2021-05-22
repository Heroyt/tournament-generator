<?php


namespace TournamentGenerator\Traits;


use TournamentGenerator\Group;
use TournamentGenerator\Round;

/**
 * Trait WithGroups
 *
 * @package TournamentGenerator\Traits
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.4
 */
trait WithGroups
{

	/**
	 * Get all groups in this object
	 *
	 * @return Group[]
	 */
	public function getGroups() : array {
		return $this->container->getHierarchyLevel(Group::class);
	}
}