<?php


namespace TournamentGenerator\Interfaces;


use TournamentGenerator\Containers\ContainerQuery;
use TournamentGenerator\Group;

/**
 * Interface for objects that contain groups
 *
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @package TournamentGenerator\Interfaces
 * @since   0.4
 */
interface WithGroups
{

	/**
	 * Get all groups in this category
	 *
	 * @return Group[]
	 */
	public function getGroups() : array;

	/**
	 * Get groups container query
	 *
	 * @return ContainerQuery
	 */
	public function queryGroups() : ContainerQuery;
}