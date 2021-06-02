<?php


namespace TournamentGenerator\Export;

use TournamentGenerator\Interfaces\WithId;

/**
 * Interface SingleExport
 *
 * @package TournamentGenerator\Export
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.5
 */
interface SingleExporter
{

	/**
	 * Simple export query without any modifiers
	 *
	 * @param WithId $object
	 *
	 * @return array The query result including the object reference
	 */
	public static function exportBasic(WithId $object) : array;

}