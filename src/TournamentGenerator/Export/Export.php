<?php


namespace TournamentGenerator\Export;

use TournamentGenerator\HierarchyBase;

/**
 * Interface for exporters
 *
 * @package TournamentGenerator\Export
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.5
 */
interface Export
{

	/**
	 * Simple export query without any modifiers
	 *
	 * @param HierarchyBase $object
	 *
	 * @return array The query result
	 */
	public static function export(HierarchyBase $object) : array;

	/**
	 * Start an export query
	 *
	 * @param HierarchyBase $object
	 *
	 * @return Export
	 */
	public static function start(HierarchyBase $object) : Export;

	/**
	 * Finish the export query -> get the result
	 *
	 * @return array The query result
	 */
	public function get() : array;

	/**
	 * Gets the basic unmodified data
	 *
	 * @return array
	 */
	public function getBasic() : array;

}