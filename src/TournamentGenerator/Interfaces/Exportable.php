<?php


namespace TournamentGenerator\Interfaces;

use TournamentGenerator\Export\ExporterInterface;

/**
 * Interface Exportable
 *
 * Marks all classes that are exportable by some kind of exporter.
 *
 * @package TournamentGenerator\Interfaces
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.5
 */
interface Exportable
{

	/**
	 * Prepares an export query for the object
	 *
	 * @return ExporterInterface Exporter for this class
	 */
	public function export() : ExporterInterface;

}