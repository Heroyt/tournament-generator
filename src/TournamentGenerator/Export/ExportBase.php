<?php


namespace TournamentGenerator\Export;


use TournamentGenerator\HierarchyBase;

/**
 * Base class for exporters
 *
 * Exporters operate on some HierarchyBase class. They extract data and/or settings from these classes in a form of PHP array.
 * Exporters also allow of adding modifiers to the exported query - adding more data. These modifiers are added via specific methods (usually starting with "with" keyword).
 *
 * @package TournamentGenerator\Export
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.5
 */
abstract class ExportBase implements Export
{

	/** @var HierarchyBase Hierarchy object to export */
	protected HierarchyBase $object;
	protected array         $modifiers = [];

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