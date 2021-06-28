<?php


namespace TournamentGenerator\Export;

use JsonException;
use TournamentGenerator\Interfaces\WithId;

/**
 * Interface for exporters
 *
 * @package TournamentGenerator\Export
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.5
 */
interface ExporterInterface
{

	/**
	 * Simple export query without any modifiers
	 *
	 * @param WithId $object
	 *
	 * @return array The query result
	 */
	public static function export(WithId $object) : array;

	/**
	 * Start an export query
	 *
	 * @param WithId $object
	 *
	 * @return ExporterInterface
	 */
	public static function start(WithId $object) : ExporterInterface;

	/**
	 * Return result as json
	 *
	 * @return string
	 * @throws JsonException
	 */
	public function getJson() : string;

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