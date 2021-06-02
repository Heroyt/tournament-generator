<?php


namespace TournamentGenerator\Export;

use TournamentGenerator\Interfaces\WithId;

/**
 * Class SingleExportBase
 *
 * Base class for all "Single" exporters = exporting only one specific class (Team, Game) and not a hierarchy class (Tournament, Category, Round, Group).
 *
 * @package TournamentGenerator\Export
 * @author Tomáš Vojík <vojik@wboy.cz>
 * @since 0.5
 */
abstract class SingleExportBase extends ExportBase implements SingleExport
{

	/**
	 * Finish the export query -> get the result
	 *
	 * @return array The query result
	 */
	public function get() : array {
		$data = $this->getBasic();
		$this->applyModifiers($data);
		unset($data['object']);
		return $data;
	}

	/**
	 * Finish the export query -> get the result including an object reference
	 *
	 * @return array
	 */
	public function getWithObject() : array {
		$data = $this->getBasic();
		$this->applyModifiers($data);
		return $data;
	}

}