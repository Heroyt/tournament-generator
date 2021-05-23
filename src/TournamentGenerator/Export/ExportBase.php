<?php


namespace TournamentGenerator\Export;


use TournamentGenerator\Base;
use TournamentGenerator\Game;
use TournamentGenerator\HierarchyBase;
use TournamentGenerator\Interfaces\WithGames;

/**
 * Class ExportBase
 *
 * @package TournamentGenerator\Export
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.5
 */
abstract class ExportBase implements Export
{

	/** @var HierarchyBase Hierarchy object to export */
	protected HierarchyBase $object;
	protected array $modifiers = [];

	public function __construct(HierarchyBase $object) {
		$this->object = $object;
	}

	/**
	 * Finish the export query -> get the result
	 *
	 * @return array The query result
	 */
	public function get() : array {
		$data = $this->getBasic();
		$this->applyModifiers($data);
		return array_map(static function(object $object) {
			unset($object->object);
			return $object;
		}, $data);
	}

	/**
	 * Apply set modifiers to data array
	 *
	 * @param array $data
	 */
	protected function applyModifiers(array &$data) : void {
		foreach ($this->modifiers as $modifier) {
			$this->$modifier($data);
		}
	}
}