<?php


namespace TournamentGenerator\Export;


use JsonException;
use JsonSerializable;
use TournamentGenerator\Export\Modifiers\Modifier;
use TournamentGenerator\HierarchyBase;
use TournamentGenerator\Interfaces\WithId;

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
abstract class ExportBase implements Export, JsonSerializable
{

	/** @var HierarchyBase Hierarchy object to export */
	protected WithId $object;
	/** @var Modifier[] Modifiers to apply to exported data */
	protected array  $modifiers = [];

	public function __construct(WithId $object) {
		$this->object = $object;
	}

	/**
	 * Return result as json
	 *
	 * @return string
	 * @throws JsonException
	 * @see ExportBase::jsonSerialize()
	 * @see ExportBase::get()
	 *
	 */
	public function getJson() : string {
		return $this->jsonSerialize();
	}

	/**
	 * Serialize exported data as JSON
	 *
	 * @return string
	 * @throws JsonException
	 */
	public function jsonSerialize() : string {
		return json_encode($this->get(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
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
			$modifier::process($data);
		}
	}
}