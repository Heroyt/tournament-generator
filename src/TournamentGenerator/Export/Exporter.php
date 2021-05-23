<?php


namespace TournamentGenerator\Export;

use TournamentGenerator\HierarchyBase;
use TournamentGenerator\Interfaces\WithGames;
use TournamentGenerator\Interfaces\WithTeams;

/**
 * Class Exporter
 *
 * Basic exporter class
 *
 * @package TournamentGenerator\Export
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.5
 */
class Exporter extends ExportBase
{

	/** @var ExportBase[] Other exporters used */
	protected array $exporters = [];

	public function __construct(HierarchyBase $object) {
		if ($object instanceof WithTeams) {
			$this->exporters['teams'] = TeamExporter::start($object);
		}
		if ($object instanceof WithGames) {
			$this->exporters['games'] = GameExporter::start($object);
		}
		parent::__construct($object);
	}

	/**
	 * Try to call a modifier method on other used exporters
	 *
	 * @param string $name
	 * @param array  $arguments
	 *
	 * @return Exporter
	 */
	public function __call(string $name, array $arguments) {
		$called = false;
		foreach ($this->exporters as $exporter) {
			if (method_exists($exporter, $name)) {
				$exporter->$name(...$arguments);
				$called = true;
			}
		}
		if ($called) {
			return $this;
		}

		throw new \Error('Call to undefined method '.__CLASS__.'::'.$name.'()');
	}

	/**
	 * @param HierarchyBase $object
	 *
	 * @return array
	 */
	public static function export(HierarchyBase $object) : array {
		return self::start($object)->get();
	}

	/**
	 * Start an export query
	 *
	 * @param HierarchyBase $object
	 *
	 * @return Export
	 */
	public static function start(HierarchyBase $object) : Export {
		return new self($object);
	}

	/**
	 * Finish the export query -> get the result
	 *
	 * @return array The query result
	 */
	public function get() : array {
		$data = $this->getBasic();
		$this->applyModifiers($data);
		foreach ($this->exporters as $name => $exporter) {
			$data[$name] = $exporter->get();
		}
		return $data;
	}

	/**
	 * Gets the basic unmodified data
	 *
	 * @return array
	 */
	public function getBasic() : array {
		$data = [];
		return $data;
	}
}