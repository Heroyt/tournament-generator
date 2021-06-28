<?php


namespace TournamentGenerator\Export\Hierarchy;

use Error;
use TournamentGenerator\Export\ExporterInterface;
use TournamentGenerator\Export\ExporterBase;
use TournamentGenerator\HierarchyBase;
use TournamentGenerator\Interfaces\WithGames;
use TournamentGenerator\Interfaces\WithId;
use TournamentGenerator\Interfaces\WithTeams;

/**
 * Basic exporter
 *
 * Basic exporter class for exporting all data from HierarchyBase objects. It uses all other specialized exporters and also inherits their modifiers specific.
 *
 * @package TournamentGenerator\Export
 * @author  Tomáš Vojík <vojik@wboy.cz>
 * @since   0.5
 */
class Exporter extends ExporterBase
{

	/** @var ExporterBase[] Other exporters used */
	protected array $exporters = [];

	public function __construct(HierarchyBase $object) {
		if ($object instanceof WithTeams) {
			$this->exporters['teams'] = TeamsExporter::start($object);
		}
		if ($object instanceof WithGames) {
			$this->exporters['games'] = GamesExporter::start($object);
		}
		parent::__construct($object);
	}

	/**
	 * Simple export query without any modifiers
	 *
	 * @param HierarchyBase $object
	 *
	 * @return array
	 */
	public static function export(WithId $object) : array {
		return self::start($object)->get();
	}

	/**
	 * Start an export query
	 *
	 * @param HierarchyBase $object
	 *
	 * @return Exporter
	 */
	public static function start(WithId $object) : ExporterInterface {
		return new self($object);
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

		throw new Error('Call to undefined method '.__CLASS__.'::'.$name.'()');
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
			if ($name === 'setup') {
				$data += $exporter->get();
			}
			else {
				$data[$name] = $exporter->get();
			}
		}
		return $data;
	}

	/**
	 * Gets the basic unmodified data
	 *
	 * @return array
	 */
	public function getBasic() : array {
		return [];
	}

	/**
	 * @defgroup ExporterQueryModifiers Query modifiers
	 * @brief    Modifier methods for the query
	 */

	/**
	 * Query modifier, adding a setup exporter
	 *
	 * @return $this
	 * @ingroup ExporterQueryModifiers
	 */
	public function withSetup() : ExporterInterface {
		$this->exporters['setup'] = SetupExporter::start($this->object);
		return $this;
	}
}